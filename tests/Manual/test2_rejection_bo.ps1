<#
.SYNOPSIS
  Test 2 — Full rejection creates BO with ALL original items
  - Creates a DO with 2 items
  - Branch rejects everything (qty_received=0 for all)
  - Asserts: BO created with ALL original items (not just shortage)
  - Asserts: bo_created notification do_id points to BO
  - Asserts: rejection_alert notification do_id points to original DO
#>

$BASE = "http://localhost:8000"

function Api($method, $path, $body, $token) {
    $h = @{ Authorization = "Bearer $token"; Accept = "application/json" }
    if ($body) { $h["Content-Type"] = "application/json" }
    try {
        $b = if ($body) { $body | ConvertTo-Json -Depth 5 } else { $null }
        return Invoke-RestMethod -Uri "$BASE$path" -Method $method -Headers $h -Body $b -ErrorAction Stop
    } catch {
        Write-Host "  [HTTP ERROR] $($_.Exception.Message) — $($_.ErrorDetails.Message)"
        return $null
    }
}

function Assert($label, $cond) {
    if ($cond) { Write-Host "  PASS  $label" }
    else        { Write-Host "  FAIL  $label"; $script:failed++ }
}

$script:failed = 0

Write-Host "`n=== TEST 2: Full rejection → BO with all original items ==="

# ── Setup: login ──────────────────────────────────────────────
$adminToken  = (Api POST "/api/auth/quick-login" @{employee_code="EMP001"} $null).token
$branchToken = (Api POST "/api/auth/quick-login" @{employee_code="EMP004"} $null).token

# ── Create DO ─────────────────────────────────────────────────
$do = Api POST "/api/delivery-orders" @{
    branch_id=1; vehicle_id=1; scheduled_date="2026-06-21"
    items=@(
        @{product_name="สินค้า-T2-A"; qty_ordered=8; unit="ลัง"}
        @{product_name="สินค้า-T2-B"; qty_ordered=3; unit="ถุง"}
    )
} $adminToken

Assert "DO created" ($do -ne $null)
$doId    = $do.id
$item1Id = $do.items[0].id
$item2Id = $do.items[1].id
$qty1    = $do.items[0].qty_ordered
$qty2    = $do.items[1].qty_ordered
Write-Host "  DO id=$doId  item1=$item1Id(qty=$qty1)  item2=$item2Id(qty=$qty2)"

# Move to in_transit
Api PUT "/api/delivery-orders/$doId" @{status="in_transit"} $adminToken | Out-Null

# ── Full rejection: all qty_received = 0 ──────────────────────
$recv = Api POST "/api/receive/$doId" @{
    items=@(
        @{id=$item1Id; qty_received=0; remark="สินค้าเสียหายทั้งหมด"}
        @{id=$item2Id; qty_received=0; remark=""}
    )
    signature=""
} $branchToken

Assert "Receive call succeeded"       ($recv -ne $null)
Assert "ReceiveRecord status=rejected" ($recv.status -eq "rejected")

# ── Verify DO state ───────────────────────────────────────────
$detail = Api GET "/api/delivery-orders/$doId" $null $adminToken
Assert "DO status=problem"       ($detail.status    -eq "problem")
Assert "DO sub_status=rejected"  ($detail.sub_status -eq "rejected")

# ── Verify BO created ─────────────────────────────────────────
$bos = $detail.backorders
Assert "Exactly 1 BO created"    ($bos.Count -eq 1)

if ($bos.Count -gt 0) {
    $bo = $bos[0]
    Assert "BO is_backorder=true"     ($bo.is_backorder -eq $true)
    Assert "BO sub_status=bo_pending" ($bo.sub_status   -eq "bo_pending")
    Assert "BO bo_round=1"            ($bo.bo_round     -eq 1)
    Assert "BO parent_do_id=$doId"    ($bo.parent_do_id -eq $doId)

    # ALL original items must be in BO (not just shortage)
    $boItems = $bo.items
    Assert "BO has ALL 2 items (full rejection)"  ($boItems.Count -eq 2)
    Assert "BO item1 qty=$qty1 (original qty)"    (($boItems | Where-Object {$_.qty_ordered -eq $qty1}).Count -gt 0)
    Assert "BO item2 qty=$qty2 (original qty)"    (($boItems | Where-Object {$_.qty_ordered -eq $qty2}).Count -gt 0)

    Write-Host "  BO id=$($bo.id) do_number=$($bo.do_number)"
    $boItems | ForEach-Object { Write-Host "    item: $($_.product_name) qty=$($_.qty_ordered) note=$($_.item_note)" }

    # ── Verify notifications ──────────────────────────────────
    $notifs    = Api GET "/api/notifications" $null $adminToken
    $boCreated  = $notifs | Where-Object { $_.type -eq "bo_created"      -and $_.do_id -eq $bo.id }
    $rejectAlert= $notifs | Where-Object { $_.type -eq "rejection_alert" -and $_.do_id -eq $doId  }

    Assert "bo_created notification exists"              ($boCreated.Count   -gt 0)
    Assert "bo_created do_id=BO.id (not original DO)"   ($boCreated[0].do_id -eq $bo.id)
    Assert "rejection_alert do_id=original DO"           ($rejectAlert.Count  -gt 0)
    Assert "rejection_alert do_id=$doId"                 ($rejectAlert[0].do_id -eq $doId)

    Write-Host "  rejection_alert do_id=$($rejectAlert[0].do_id) (original DO=$doId)"
    Write-Host "  bo_created      do_id=$($boCreated[0].do_id)   (BO=$($bo.id))"
}

# ── Result ────────────────────────────────────────────────────
Write-Host ""
if ($script:failed -eq 0) { Write-Host "RESULT: PASS (all assertions)" }
else                       { Write-Host "RESULT: FAIL ($($script:failed) assertion(s) failed)" }
