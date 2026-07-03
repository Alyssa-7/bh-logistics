<#
.SYNOPSIS
  Test 1 — Partial receive creates BO automatically
  - Creates a DO with 3 items
  - Branch receives only part of items (2 items short)
  - Asserts: BO created with shortage items only
  - Asserts: bo_created notification do_id points to BO (not original DO)
#>

$BASE = "http://localhost:8000"

function Api($method, $path, $body, $token) {
    $h = @{ Authorization = "Bearer $token"; Accept = "application/json" }
    if ($body) { $h["Content-Type"] = "application/json" }
    try {
        $b = if ($body) { $body | ConvertTo-Json -Depth 5 } else { $null }
        return Invoke-RestMethod -Uri "$BASE$path" -Method $method -Headers $h -Body $b -ErrorAction Stop
    } catch {
        $msg = $_.ErrorDetails.Message
        Write-Host "  [HTTP ERROR] $($_.Exception.Message) — $msg"
        return $null
    }
}

function Assert($label, $cond) {
    if ($cond) { Write-Host "  PASS  $label" }
    else        { Write-Host "  FAIL  $label"; $script:failed++ }
}

$script:failed = 0

Write-Host "`n=== TEST 1: Partial receive -> BO auto-creation ==="

# ── Setup: login ──────────────────────────────────────────────
$adminToken  = (Api POST "/api/auth/quick-login" @{employee_code="EMP001"} $null).token
$branchToken = (Api POST "/api/auth/quick-login" @{employee_code="EMP004"} $null).token

# ── Create DO ─────────────────────────────────────────────────
$do = Api POST "/api/delivery-orders" @{
    branch_id=1; vehicle_id=1; scheduled_date="2026-06-20"
    items=@(
        @{product_name="สินค้า-T1-A"; qty_ordered=10; unit="ลัง"}
        @{product_name="สินค้า-T1-B"; qty_ordered=6;  unit="ถุง"}
        @{product_name="สินค้า-T1-C"; qty_ordered=4;  unit="กล่อง"}
    )
} $adminToken

Assert "DO created" ($do -ne $null)
$doId = $do.id
Write-Host "  DO id=$doId do_number=$($do.do_number)"

# Move to in_transit
Api PUT "/api/delivery-orders/$doId" @{status="in_transit"} $adminToken | Out-Null
$item1Id = $do.items[0].id
$item2Id = $do.items[1].id
$item3Id = $do.items[2].id

# ── Partial receive: item1 short=4, item2 full, item3 short=2 ─
$recv = Api POST "/api/receive/$doId" @{
    items=@(
        @{id=$item1Id; qty_received=6; remark=""}   # short 4
        @{id=$item2Id; qty_received=6; remark=""}   # full
        @{id=$item3Id; qty_received=2; remark=""}   # short 2
    )
    signature=""
} $branchToken

Assert "Receive call succeeded"    ($recv -ne $null)
Assert "ReceiveRecord status=partial" ($recv.status -eq "partial")

# ── Verify DO state ───────────────────────────────────────────
$detail = Api GET "/api/delivery-orders/$doId" $null $adminToken
Assert "DO status=problem"      ($detail.status    -eq "problem")
Assert "DO sub_status=incomplete" ($detail.sub_status -eq "incomplete")

# ── Verify BO created ─────────────────────────────────────────
$bos = $detail.backorders
Assert "Exactly 1 BO created"   ($bos.Count -eq 1)

if ($bos.Count -gt 0) {
    $bo = $bos[0]
    Assert "BO is_backorder=true"   ($bo.is_backorder -eq $true)
    Assert "BO sub_status=bo_pending" ($bo.sub_status -eq "bo_pending")
    Assert "BO bo_round=1"          ($bo.bo_round -eq 1)
    Assert "BO parent_do_id=$doId"  ($bo.parent_do_id -eq $doId)
    Assert "BO vehicle_id=null"     ($bo.vehicle_id -eq $null)

    # Only shortage items (item1 and item3) should be in BO — NOT item2
    $boItems = $bo.items
    Assert "BO has 2 shortage items (not 3)" ($boItems.Count -eq 2)
    $boQtys = $boItems | ForEach-Object { $_.qty_ordered }
    Assert "BO item qty=4 (shortage of item1)" ($boQtys -contains 4)
    Assert "BO item qty=2 (shortage of item3)" ($boQtys -contains 2)

    Write-Host "  BO id=$($bo.id) do_number=$($bo.do_number)"
    $boItems | ForEach-Object { Write-Host "    item: $($_.product_name) qty=$($_.qty_ordered)" }

    # ── Verify notifications ──────────────────────────────────
    $notifs = Api GET "/api/notifications" $null $adminToken
    $boCreated  = $notifs | Where-Object { $_.type -eq "bo_created"     -and $_.do_id -eq $bo.id }
    $shortAlert = $notifs | Where-Object { $_.type -eq "shortage_alert" -and $_.do_id -eq $doId  }

    Assert "bo_created notification exists"              ($boCreated.Count  -gt 0)
    Assert "bo_created do_id=BO.id (not original DO)"   ($boCreated[0].do_id -eq $bo.id)
    Assert "shortage_alert do_id=original DO"            ($shortAlert.Count  -gt 0)

    Write-Host "  shortage_alert do_id=$($shortAlert[0].do_id) (original DO=$doId)"
    Write-Host "  bo_created     do_id=$($boCreated[0].do_id)  (BO=$($bo.id))"
}

# ── Result ────────────────────────────────────────────────────
Write-Host ""
if ($script:failed -eq 0) { Write-Host "RESULT: PASS (all assertions)" }
else                       { Write-Host "RESULT: FAIL ($($script:failed) assertion(s) failed)" }
