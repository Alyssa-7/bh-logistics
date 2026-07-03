<#
.SYNOPSIS
  Test 3 — Driver sees only their own DOs + approved BOs only
  - Driver CAN view their own DO via GET /delivery-orders/{id}
  - Driver CANNOT view a DO assigned to a different vehicle
  - Driver CAN view a BO that is bo_approved and assigned to them
  - Driver CANNOT view a BO that is bo_pending (not yet approved)
  - GET /driver/orders returns only driver's own orders (no pending BOs)
#>

$BASE = "http://localhost:8000"

function Api($method, $path, $body, $token) {
    $h = @{ Authorization = "Bearer $token"; Accept = "application/json" }
    if ($body) { $h["Content-Type"] = "application/json" }
    try {
        $b = if ($body) { $body | ConvertTo-Json -Depth 5 } else { $null }
        return Invoke-RestMethod -Uri "$BASE$path" -Method $method -Headers $h -Body $b -ErrorAction Stop
    } catch {
        return @{ __httpError = $true; status = $_.Exception.Response.StatusCode.Value__; msg = $_.ErrorDetails.Message }
    }
}

function Assert($label, $cond) {
    if ($cond) { Write-Host "  PASS  $label" }
    else        { Write-Host "  FAIL  $label"; $script:failed++ }
}

$script:failed = 0

Write-Host "`n=== TEST 3: Driver access control ==="

# ── Login ─────────────────────────────────────────────────────
$adminToken  = (Api POST "/api/auth/quick-login" @{employee_code="EMP001"} $null).token
# EMP003 = driver bound to vehicle_id=1
$driverToken = (Api POST "/api/auth/quick-login" @{employee_code="EMP003"} $null).token
$branchToken = (Api POST "/api/auth/quick-login" @{employee_code="EMP004"} $null).token

# ── Create a DO assigned to driver's vehicle (vehicle_id=1) ───
$do = Api POST "/api/delivery-orders" @{
    branch_id=1; vehicle_id=1; scheduled_date="2026-06-22"
    items=@(@{product_name="สินค้า-T3-A"; qty_ordered=5; unit="ลัง"})
} $adminToken
$doId    = $do.id
$item1Id = $do.items[0].id
Write-Host "  DO for driver: id=$doId"

# Move to in_transit and partial-receive to create a pending BO
Api PUT "/api/delivery-orders/$doId" @{status="in_transit"} $adminToken | Out-Null
Api POST "/api/receive/$doId" @{
    items=@(@{id=$item1Id; qty_received=2; remark=""})
    signature=""
} $branchToken | Out-Null

$detail  = Api GET "/api/delivery-orders/$doId" $null $adminToken
$pendingBO = $detail.backorders | Where-Object { $_.sub_status -eq "bo_pending" } | Select-Object -First 1
$pendingBOId = $pendingBO.id
Write-Host "  Pending BO id=$pendingBOId"

# ── 3a: Driver can view their own (non-BO) DO ─────────────────
$r = Api GET "/api/delivery-orders/$doId" $null $driverToken
Assert "3a: Driver can view own DO"  ($r.id -eq $doId)

# ── 3b: Driver CANNOT view a DO assigned to another vehicle ───
# Find any DO NOT assigned to vehicle_id=1
$allOrders = Api GET "/api/delivery-orders?per_page=50" $null $adminToken
$otherDO = $allOrders.data | Where-Object { $_.vehicle_id -ne 1 -and $_.vehicle_id -ne $null } | Select-Object -First 1
if ($otherDO) {
    $r2 = Api GET "/api/delivery-orders/$($otherDO.id)" $null $driverToken
    Assert "3b: Driver blocked from other vehicle's DO (403)" ($r2.__httpError -eq $true -and $r2.status -eq 403)
    Write-Host "  Other DO id=$($otherDO.id) vehicle_id=$($otherDO.vehicle_id) → HTTP $($r2.status)"
} else {
    Write-Host "  SKIP 3b: no other-vehicle DO found to test"
}

# ── 3c: Driver CANNOT view a pending BO ───────────────────────
$r3 = Api GET "/api/delivery-orders/$pendingBOId" $null $driverToken
Assert "3c: Driver blocked from pending BO (403)" ($r3.__httpError -eq $true -and $r3.status -eq 403)
Write-Host "  Pending BO (bo_pending) id=$pendingBOId → HTTP $($r3.status)"

# ── 3d: Driver CAN view an approved BO assigned to them ───────
$approveBody = @{
    driver_id      = 3
    scheduled_date = "2026-06-25"
    items          = @(@{ id = ($pendingBO.items[0].id); qty_ordered = 3 })
}
Api POST "/api/delivery-orders/$pendingBOId/approve-backorder" $approveBody $adminToken | Out-Null

$r4 = Api GET "/api/delivery-orders/$pendingBOId" $null $driverToken
Assert "3d: Driver CAN view approved BO"             ($r4.id -eq $pendingBOId)
Assert "3d: BO sub_status=bo_approved"               ($r4.sub_status -eq "bo_approved")
Write-Host "  Approved BO id=$pendingBOId → HTTP 200 sub_status=$($r4.sub_status)"

# ── 3e: GET /driver/orders — no pending BOs in list ───────────
$driverOrders = Api GET "/api/driver/orders" $null $driverToken
$pendingBOsInList = $driverOrders | Where-Object { $_.is_backorder -eq $true -and $_.sub_status -ne "bo_approved" }
Assert "3e: Driver orders list excludes pending BOs" ($pendingBOsInList.Count -eq 0)
$approvedBOsInList = $driverOrders | Where-Object { $_.is_backorder -eq $true -and $_.sub_status -eq "bo_approved" }
Assert "3e: Approved BO appears in driver orders"    ($approvedBOsInList.Count -gt 0)
Write-Host "  Driver orders total=$($driverOrders.Count)  pending_BOs=$($pendingBOsInList.Count)  approved_BOs=$($approvedBOsInList.Count)"

# ── Result ────────────────────────────────────────────────────
Write-Host ""
if ($script:failed -eq 0) { Write-Host "RESULT: PASS (all assertions)" }
else                       { Write-Host "RESULT: FAIL ($($script:failed) assertion(s) failed)" }
