<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BH-Logistic — ระบบบริหารจัดการขนส่ง</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
  <style>
    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:'Segoe UI',Tahoma,sans-serif;background:#f0f4f8;color:#2d3748;font-size:14px;}

    /* ---- LOGIN ---- */
    #login-screen{display:flex;align-items:center;justify-content:center;min-height:100vh;background:linear-gradient(135deg,#1e3a5f 0%,#2a5298 100%);}
    .login-card{background:#fff;border-radius:16px;padding:40px;width:360px;box-shadow:0 20px 60px rgba(0,0,0,0.3);}
    .login-logo{text-align:center;margin-bottom:28px;}
    .login-logo .logo-icon{width:56px;height:56px;background:#1e3a5f;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;}
    .login-logo .logo-icon i{color:#fff;font-size:24px;}
    .login-logo h1{font-size:22px;color:#1e3a5f;font-weight:700;}
    .login-logo p{color:#718096;font-size:13px;margin-top:4px;}
    .form-group{margin-bottom:16px;}
    .form-group label{display:block;font-size:12px;font-weight:600;color:#4a5568;margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px;}
    .form-group input,.form-group select,.form-group textarea{width:100%;border:1.5px solid #e2e8f0;border-radius:8px;padding:10px 14px;font-size:14px;transition:border .2s;font-family:inherit;}
    .form-group input:focus,.form-group select:focus,.form-group textarea:focus{outline:none;border-color:#2a5298;}
    .btn{display:inline-flex;align-items:center;gap:6px;padding:10px 18px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;border:none;transition:all .2s;font-family:inherit;}
    .btn-primary{background:#1e3a5f;color:#fff;}
    .btn-primary:hover{background:#2a5298;}
    .btn-success{background:#38a169;color:#fff;}
    .btn-success:hover{background:#2f855a;}
    .btn-danger{background:#e53e3e;color:#fff;}
    .btn-danger:hover{background:#c53030;}
    .btn-secondary{background:#718096;color:#fff;}
    .btn-secondary:hover{background:#4a5568;}
    .btn-warning{background:#d69e2e;color:#fff;}
    .btn-warning:hover{background:#b7791f;}
    .btn-sm{padding:6px 12px;font-size:12px;}
    .btn-block{width:100%;justify-content:center;}
    .alert{padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:12px;}
    .alert-error{background:#fff5f5;color:#c53030;border:1px solid #fed7d7;}
    .alert-success{background:#f0fff4;color:#276749;border:1px solid #c6f6d5;}

    /* ---- QUICK ACCESS ROLE CARDS ---- */
    .qa-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
    .qa-card{border:2px solid #e2e8f0;border-radius:12px;padding:22px 12px;text-align:center;cursor:pointer;transition:all .2s;display:flex;flex-direction:column;align-items:center;gap:7px;background:#f7fafc;}
    .qa-card:hover{border-color:#2a5298;background:#ebf4ff;transform:translateY(-2px);box-shadow:0 4px 14px rgba(42,82,152,.15);}
    .qa-card:active{transform:translateY(0);box-shadow:none;}
    .qa-icon{font-size:34px;line-height:1;}
    .qa-label{font-size:13px;font-weight:700;color:#1e3a5f;}

    /* ---- LAYOUT ---- */
    #app{display:none;min-height:100vh;}
    .sidebar{position:fixed;top:0;left:0;bottom:0;width:240px;background:#1e3a5f;display:flex;flex-direction:column;z-index:100;}
    .sidebar-logo{padding:20px;border-bottom:1px solid rgba(255,255,255,.1);}
    .sidebar-logo .logo-wrap{display:flex;align-items:center;gap:12px;}
    .sidebar-logo .logo-box{width:36px;height:36px;background:rgba(255,255,255,.15);border-radius:8px;display:flex;align-items:center;justify-content:center;}
    .sidebar-logo .logo-box i{color:#fff;font-size:16px;}
    .sidebar-logo h2{color:#fff;font-size:15px;font-weight:700;}
    .sidebar-logo p{color:rgba(255,255,255,.5);font-size:11px;margin-top:2px;}
    .sidebar-nav{flex:1;overflow-y:auto;padding:12px 0;}
    .nav-section{padding:8px 16px 4px;font-size:10px;text-transform:uppercase;letter-spacing:1px;color:rgba(255,255,255,.4);}
    .nav-item{display:flex;align-items:center;gap:10px;padding:10px 20px;color:rgba(255,255,255,.75);cursor:pointer;transition:all .2s;border-left:3px solid transparent;}
    .nav-item:hover{background:rgba(255,255,255,.1);color:#fff;}
    .nav-item.active{background:rgba(255,255,255,.15);color:#fff;border-left-color:#63b3ed;}
    .nav-item i{width:18px;text-align:center;font-size:14px;}
    .sidebar-footer{padding:16px;border-top:1px solid rgba(255,255,255,.1);}
    .user-card{display:flex;align-items:center;gap:10px;}
    .user-avatar{width:36px;height:36px;border-radius:50%;background:#2a5298;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:14px;flex-shrink:0;}
    .user-info .name{color:#fff;font-size:13px;font-weight:600;}
    .user-info .role{color:rgba(255,255,255,.5);font-size:11px;text-transform:uppercase;}
    .logout-btn{margin-left:auto;background:rgba(255,255,255,.1);border:none;color:rgba(255,255,255,.7);cursor:pointer;padding:7px 9px;border-radius:6px;font-size:13px;}
    .logout-btn:hover{background:rgba(255,255,255,.2);color:#fff;}
    .main{margin-left:240px;min-height:100vh;}
    .topbar{background:#fff;padding:0 24px;height:60px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #e2e8f0;position:sticky;top:0;z-index:50;}
    .topbar h1{font-size:17px;font-weight:700;color:#1e3a5f;}
    .content{padding:24px;}

    /* ---- CARDS ---- */
    .card{background:#fff;border-radius:12px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,.06);margin-bottom:20px;}
    .card-title{font-size:15px;font-weight:700;color:#1e3a5f;margin-bottom:16px;padding-bottom:10px;border-bottom:2px solid #ebf4ff;display:flex;align-items:center;gap:8px;}
    .card-title i{color:#4a90d9;}
    .stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px;}
    .stat-card{background:#fff;border-radius:12px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,.06);display:flex;align-items:center;gap:16px;}
    .stat-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;}
    .stat-icon.blue{background:#ebf8ff;color:#2b6cb0;}
    .stat-icon.green{background:#f0fff4;color:#276749;}
    .stat-icon.yellow{background:#fffbeb;color:#b7791f;}
    .stat-icon.red{background:#fff5f5;color:#c53030;}
    .stat-icon.purple{background:#faf5ff;color:#6b46c1;}
    .stat-value{font-size:26px;font-weight:700;color:#1e3a5f;}
    .stat-label{font-size:12px;color:#718096;margin-top:2px;}

    /* ---- TABLE ---- */
    .table-wrap{overflow-x:auto;}
    table{width:100%;border-collapse:collapse;}
    thead tr{background:#f7fafc;}
    th{padding:10px 14px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#718096;white-space:nowrap;}
    td{padding:10px 14px;border-bottom:1px solid #f0f4f8;vertical-align:middle;}
    tr:last-child td{border-bottom:none;}
    tr:hover td{background:#f7faff;}
    .badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;}
    .badge-pending{background:#e2e8f0;color:#4a5568;}
    .badge-in_transit{background:#bee3f8;color:#2a4a7f;}
    .badge-delivered{background:#bee3f8;color:#2a4a7f;}
    .badge-problem{background:#fbd38d;color:#7b341e;}
    .badge-completed{background:#c6f6d5;color:#22543d;}
    .badge-cancelled{background:#fed7d7;color:#742a2a;}
    .badge-incomplete{background:#fed7aa;color:#7b341e;}
    .badge-rejected{background:#feb2b2;color:#742a2a;}
    .badge-bo_pending{background:#fefcbf;color:#744210;}
    .badge-bo_approved{background:#c6f6d5;color:#22543d;}
    .badge-bo_rejected{background:#e2e8f0;color:#4a5568;}
    .badge-admin{background:#fed7d7;color:#742a2a;}
    /* ---- NOTIFICATION BELL ---- */
    .notif-bell{position:relative;background:none;border:none;cursor:pointer;padding:8px 10px;color:#1e3a5f;font-size:18px;border-radius:8px;transition:background .15s;}
    .notif-bell:hover{background:#ebf4ff;}
    .notif-unread-dot{position:absolute;top:6px;right:6px;background:#e53e3e;color:#fff;border-radius:50%;min-width:16px;height:16px;font-size:10px;font-weight:700;display:flex;align-items:center;justify-content:center;padding:0 3px;line-height:1;}
    .notif-dropdown{position:absolute;right:0;top:44px;width:340px;background:#fff;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,.18);z-index:300;border:1px solid #e2e8f0;overflow:hidden;}
    .notif-hdr{padding:12px 16px;border-bottom:1px solid #e2e8f0;font-weight:700;font-size:13px;color:#1e3a5f;display:flex;align-items:center;justify-content:space-between;}
    .notif-item{padding:10px 16px;border-bottom:1px solid #f0f4f8;cursor:pointer;transition:background .15s;}
    .notif-item:last-child{border-bottom:none;}
    .notif-item:hover{background:#f7fafc;}
    .notif-item.unread{background:#ebf8ff;}
    .notif-msg{font-size:12px;color:#2d3748;line-height:1.45;}
    .notif-time{font-size:11px;color:#a0aec0;margin-top:3px;}
    .notif-empty{padding:28px;text-align:center;color:#a0aec0;font-size:13px;}
    .badge-hq{background:#bee3f8;color:#2a4a7f;}
    .badge-driver{background:#c6f6d5;color:#22543d;}
    .badge-branch{background:#e9d8fd;color:#553c9a;}
    .badge-active{background:#c6f6d5;color:#22543d;}
    .badge-inactive{background:#fed7d7;color:#742a2a;}

    /* ---- MODAL ---- */
    .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:200;align-items:center;justify-content:center;}
    .modal-overlay.open{display:flex;}
    .modal{background:#fff;border-radius:16px;padding:28px;width:560px;max-width:95vw;max-height:90vh;overflow-y:auto;}
    .modal-title{font-size:17px;font-weight:700;color:#1e3a5f;margin-bottom:20px;display:flex;align-items:center;gap:8px;}
    .modal-title i{color:#4a90d9;}
    .modal-footer{display:flex;gap:10px;justify-content:flex-end;margin-top:20px;padding-top:16px;border-top:1px solid #e2e8f0;}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;}

    /* ---- MISC ---- */
    .empty-state{text-align:center;padding:48px;color:#a0aec0;}
    .empty-state i{font-size:40px;margin-bottom:12px;display:block;}
    .empty-state p{font-size:14px;}
    .loading{text-align:center;padding:32px;color:#718096;}
    .action-bar{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;}
    .search-box{display:flex;gap:8px;align-items:center;flex-wrap:wrap;}
    .search-box input,.search-box select{border:1.5px solid #e2e8f0;border-radius:8px;padding:8px 12px;font-size:13px;font-family:inherit;}
    .search-box input{width:220px;}
    .page-section{display:none;}
    .page-section.active{display:block;}
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px;}
    .info-item span.label{color:#718096;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:2px;}
    .info-item span.val{font-size:14px;}
    .scan-found{border:1.5px solid #bee3f8;border-radius:10px;padding:16px;background:#ebf8ff;}
    .scan-found .scan-title{font-weight:700;color:#2a4a7f;font-size:15px;margin-bottom:12px;}
  </style>
</head>
<body>

<!-- LOGIN -->
<div id="login-screen">
  <div class="login-card">
    <div class="login-logo">
      <div class="logo-icon"><i class="fa-solid fa-truck"></i></div>
      <h1>BH-Logistic</h1>
      <p>ระบบบริหารจัดการขนส่งสินค้า</p>
    </div>

    <!-- Step 1: เลือกประเภทผู้ใช้ -->
    <div id="qa-step-1">
      <p style="text-align:center;font-size:11px;font-weight:700;color:#a0aec0;letter-spacing:.8px;text-transform:uppercase;margin-bottom:14px;">เลือกประเภทผู้ใช้งาน</p>
      <div class="qa-grid">
        <div class="qa-card" onclick="selectRole('ครัวกลาง','🏢')">
          <span class="qa-icon">🏢</span>
          <span class="qa-label">ครัวกลาง</span>
        </div>
        <div class="qa-card" onclick="selectRole('ไรเดอร์','🚛')">
          <span class="qa-icon">🚛</span>
          <span class="qa-label">ไรเดอร์</span>
        </div>
        <div class="qa-card" onclick="selectRole('หน้าสาขา','🏪')">
          <span class="qa-icon">🏪</span>
          <span class="qa-label">หน้าสาขา</span>
        </div>
        <div class="qa-card" onclick="selectRole('แอดมิน','⚙️')">
          <span class="qa-icon">⚙️</span>
          <span class="qa-label">แอดมิน</span>
        </div>
      </div>
    </div>

    <!-- Step 2: ใส่รหัสพนักงาน -->
    <div id="qa-step-2" style="display:none;">
      <div style="text-align:center;margin-bottom:22px;">
        <div id="qa-role-icon" style="font-size:46px;line-height:1;margin-bottom:10px;">🏢</div>
        <div id="qa-role-name" style="font-size:20px;font-weight:700;color:#1e3a5f;">ครัวกลาง</div>
        <div style="font-size:12px;color:#a0aec0;margin-top:5px;">ใส่รหัสพนักงานเพื่อเข้าระบบ</div>
      </div>
      <div id="quick-error"></div>
      <div class="form-group">
        <label>รหัสพนักงาน</label>
        <input type="text" id="employee-code" placeholder="เช่น EMP001" autocomplete="off">
      </div>
      <button class="btn btn-primary btn-block" onclick="doQuickLogin()" style="margin-bottom:10px;">
        <i class="fa-solid fa-right-to-bracket"></i> เข้าระบบ
      </button>
      <button class="btn btn-block" onclick="backToRoleSelect()" style="background:#edf2f7;color:#4a5568;justify-content:center;">
        <i class="fa-solid fa-arrow-left"></i> กลับ
      </button>
    </div>
  </div>
</div>

<!-- APP -->
<div id="app">
  <div class="sidebar">
    <div class="sidebar-logo">
      <div class="logo-wrap">
        <div class="logo-box"><i class="fa-solid fa-truck"></i></div>
        <div>
          <h2>BH-Logistic</h2>
          <p>ระบบขนส่งสินค้า</p>
        </div>
      </div>
    </div>
    <nav class="sidebar-nav" id="sidebar-nav"></nav>
    <div class="sidebar-footer">
      <div class="user-card">
        <div class="user-avatar" id="avatar-initial">A</div>
        <div class="user-info">
          <div class="name" id="sidebar-name">Loading...</div>
          <div class="role" id="sidebar-role">-</div>
        </div>
        <button class="logout-btn" onclick="doLogout()" title="ออกจากระบบ">
          <i class="fa-solid fa-right-from-bracket"></i>
        </button>
      </div>
    </div>
  </div>

  <div class="main">
    <div class="topbar">
      <h1 id="page-title">หน้าหลัก</h1>
      <div style="display:flex;align-items:center;gap:8px;">
        <div id="notif-wrap" style="position:relative;display:none;">
          <button class="notif-bell" id="notif-bell-btn" onclick="toggleNotifDropdown()">
            <i class="fa-solid fa-bell"></i>
            <span class="notif-unread-dot" id="notif-badge" style="display:none;">0</span>
          </button>
          <div class="notif-dropdown" id="notif-dropdown" style="display:none;">
            <div class="notif-hdr">
              <span><i class="fa-solid fa-bell" style="color:#4a90d9;"></i> การแจ้งเตือน</span>
              <span id="notif-unread-label" style="font-size:11px;color:#718096;font-weight:400;"></span>
            </div>
            <div id="notif-list"><div class="notif-empty">กำลังโหลด...</div></div>
          </div>
        </div>
        <div id="topbar-actions"></div>
      </div>
    </div>
    <div class="content">

      <!-- หน้าหลัก -->
      <div class="page-section active" id="page-dashboard">
        <div style="display:flex;justify-content:flex-end;margin-bottom:8px;">
          <button class="btn btn-secondary btn-sm" onclick="loadDashboard()" id="btn-dashboard-refresh">
            <i class="fa-solid fa-rotate-right"></i> รีเฟรช
          </button>
        </div>
        <div class="stat-grid" id="stat-grid">
          <div class="loading">กำลังโหลด...</div>
        </div>
        <div class="card" id="dashboard-transit-card" style="display:none;">
          <div class="card-title"><i class="fa-solid fa-truck"></i> ออเดอร์ที่กำลังส่ง</div>
          <div id="transit-table"><div class="loading">กำลังโหลด...</div></div>
        </div>
        <div class="card" id="dashboard-recent-card">
          <div class="card-title"><i class="fa-solid fa-clipboard-list"></i> ออเดอร์ล่าสุด</div>
          <div id="recent-orders"><div class="loading">กำลังโหลด...</div></div>
        </div>
      </div>

      <!-- ใบสั่งส่งสินค้า -->
      <div class="page-section" id="page-orders">
        <div class="card">
          <div class="action-bar">
            <div class="search-box">
              <input type="text" id="order-search" placeholder="ค้นหา เลขที่ใบสั่ง..." oninput="filterOrders()">
              <select id="order-status-filter" onchange="filterOrders()">
                <option value="">ทุกสถานะ</option>
                <option value="pending">รอดำเนินการ</option>
                <option value="in_transit">กำลังส่ง</option>
                <option value="problem">มีปัญหา</option>
                <option value="completed">เสร็จสิ้น</option>
                <option value="cancelled">ยกเลิก</option>
              </select>
            </div>
            <button class="btn btn-success btn-sm" onclick="openCreateOrder()" id="btn-create-order">
              <i class="fa-solid fa-plus"></i> สร้างออเดอร์
            </button>
          </div>
          <div class="table-wrap">
            <table>
              <thead><tr>
                <th>เลขที่ใบสั่ง</th><th>สาขา</th><th>ยานพาหนะ</th><th>สถานะ</th>
                <th>วันที่นัดส่ง</th><th>สร้างโดย</th><th>จัดการ</th>
              </tr></thead>
              <tbody id="orders-tbody"><tr><td colspan="7" class="loading">กำลังโหลด...</td></tr></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- BRANCHES -->
      <div class="page-section" id="page-branches">
        <div class="card">
          <div class="action-bar">
            <span style="font-size:13px;color:#718096;" id="branch-count">-</span>
            <button class="btn btn-success btn-sm" onclick="openCreateBranch()">
              <i class="fa-solid fa-plus"></i> เพิ่มสาขา
            </button>
          </div>
          <div class="table-wrap">
            <table>
              <thead><tr><th>ID</th><th>ชื่อสาขา</th><th>ที่อยู่</th><th>พิกัด</th><th>สถานะ</th><th>จัดการ</th></tr></thead>
              <tbody id="branches-tbody"><tr><td colspan="6" class="loading">กำลังโหลด...</td></tr></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- VEHICLES -->
      <div class="page-section" id="page-vehicles">
        <div class="card">
          <div class="action-bar">
            <span style="font-size:13px;color:#718096;" id="vehicle-count">-</span>
            <button class="btn btn-success btn-sm" onclick="openCreateVehicle()">
              <i class="fa-solid fa-plus"></i> เพิ่มยานพาหนะ
            </button>
          </div>
          <div class="table-wrap">
            <table>
              <thead><tr><th>ID</th><th>ทะเบียน</th><th>คนขับ</th><th>สถานะ</th><th>จัดการ</th></tr></thead>
              <tbody id="vehicles-tbody"><tr><td colspan="5" class="loading">กำลังโหลด...</td></tr></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ติดตามรถ -->
      <div class="page-section" id="page-gps">
        <div class="card" id="gps-send-card">
          <div class="card-title"><i class="fa-solid fa-location-dot"></i> ส่งพิกัด GPS</div>
          <div class="form-row" style="margin-bottom:12px;">
            <div class="form-group" style="margin:0;" id="gps-vid-section">
              <label>รหัสยานพาหนะ</label>
              <input type="number" id="gps-manual-vid" placeholder="1">
            </div>
            <div class="form-group" style="margin:0;display:none;" id="gps-vehicle-info">
              <label>รถของคุณ</label>
              <div id="gps-vehicle-display" style="padding:10px 14px;border:1.5px solid #bee3f8;border-radius:8px;background:#ebf8ff;font-weight:700;font-size:15px;color:#2a4a7f;">
                กำลังโหลด...
              </div>
            </div>
            <div class="form-group" style="margin:0;" id="gps-lat-group"><label>ละติจูด</label><input type="number" id="gps-lat" placeholder="13.7563" step="any"></div>
            <div class="form-group" style="margin:0;" id="gps-lng-group"><label>ลองจิจูด</label><input type="number" id="gps-lng" placeholder="100.5018" step="any"></div>
          </div>
          <div style="display:flex;gap:10px;" id="gps-manual-btns">
            <button class="btn btn-secondary btn-sm" onclick="useMyLocation()">
              <i class="fa-solid fa-crosshairs"></i> ใช้ตำแหน่งปัจจุบัน
            </button>
            <button class="btn btn-success btn-sm" onclick="sendGps()">
              <i class="fa-solid fa-paper-plane"></i> ส่ง GPS
            </button>
          </div>
          <div id="gps-tracking-section" style="display:none;">
            <div style="display:flex;gap:10px;align-items:center;">
              <button class="btn btn-success btn-sm" id="btn-start-tracking" onclick="startTracking()">
                <i class="fa-solid fa-play"></i> เริ่มติดตาม
              </button>
              <button class="btn btn-danger btn-sm" id="btn-stop-tracking" style="display:none;" onclick="stopTracking()">
                <i class="fa-solid fa-stop"></i> หยุดติดตาม
              </button>
              <span id="tracking-status" style="font-size:13px;color:#718096;">หยุดแล้ว</span>
            </div>
          </div>
          <div id="gps-result" style="margin-top:12px;"></div>
        </div>
        <div class="card" id="gps-map-card" style="display:none;">
          <div class="card-title"><i class="fa-solid fa-map"></i> แผนที่ติดตามรถ Real-time</div>
          <div style="display:flex;justify-content:flex-end;margin-bottom:8px;">
            <span style="font-size:12px;color:#a0aec0;">
              <span style="color:#e53e3e;font-weight:700;">&#9679;</span> ตำแหน่งล่าสุด &nbsp;
              <span style="color:#38a169;font-weight:700;">&#9679;</span> ออกจากคลัง &nbsp;|&nbsp;
              อัปเดตทุก 10 วินาที
            </span>
          </div>
          <div id="gps-map" style="min-height:400px;height:calc(100vh - 320px);border-radius:8px;border:1.5px solid #e2e8f0;"></div>
        </div>
        <div class="card" id="gps-history-card">
          <div class="card-title"><i class="fa-solid fa-route"></i> ประวัติ GPS</div>
          <div style="margin-bottom:12px;">
            <select id="gps-vid" style="border:1.5px solid #e2e8f0;border-radius:8px;padding:8px 12px;font-size:13px;font-family:inherit;min-width:220px;" onchange="onVehicleSelect(this.value)">
              <option value="">-- เลือกรถ --</option>
            </select>
          </div>
          <div class="table-wrap">
            <table>
              <thead><tr><th>เวลา</th><th>ตำแหน่ง</th><th>ออเดอร์</th></tr></thead>
              <tbody id="gps-tbody">
                <tr><td colspan="3" style="text-align:center;color:#a0aec0;padding:24px;">เลือก Vehicle เพื่อดูประวัติ</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- RECEIVE -->
      <div class="page-section" id="page-receive">
        <div class="card">
          <div class="card-title">
            <i class="fa-solid fa-box"></i> รายการรับสินค้าวันนี้
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>เลขที่ใบสั่ง</th>
                  <th id="recv-th-branch" style="display:none;">สาขา</th>
                  <th>ยานพาหนะ</th>
                  <th>จำนวนสินค้า</th>
                  <th>วันที่นัดส่ง</th>
                  <th>ระยะทาง</th>
                  <th>ETA</th>
                  <th>จัดการ</th>
                </tr>
              </thead>
              <tbody id="receive-tbody">
                <tr>
                  <td colspan="7" class="loading">กำลังโหลด...</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- REPORTS -->
      <div class="page-section" id="page-reports">

        <!-- Filter bar -->
        <div class="card" style="margin-bottom:16px;padding:16px 20px;">
          <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div class="form-group" style="margin:0;">
              <label>ประเภทช่วงเวลา</label>
              <select id="rp-mode" onchange="onReportModeChange()"
                      style="border:1.5px solid #e2e8f0;border-radius:8px;padding:8px 12px;font-size:13px;font-family:inherit;background:#fff;">
                <option value="month">เดือน</option>
                <option value="quarter">Quarter</option>
                <option value="year">ปี</option>
                <option value="daterange">ช่วงวันที่</option>
              </select>
            </div>
            <!-- Month -->
            <div id="rp-wrap-month" style="display:flex;gap:8px;">
              <div class="form-group" style="margin:0;"><label>ปี</label><input type="number" id="rp-year-m" value="2026" min="2000" max="2100" style="width:90px;"></div>
              <div class="form-group" style="margin:0;"><label>เดือน</label>
                <select id="rp-month-sel" style="border:1.5px solid #e2e8f0;border-radius:8px;padding:8px 12px;font-size:13px;font-family:inherit;background:#fff;">
                  <option value="1">มกราคม</option><option value="2">กุมภาพันธ์</option>
                  <option value="3">มีนาคม</option><option value="4">เมษายน</option>
                  <option value="5">พฤษภาคม</option><option value="6" selected>มิถุนายน</option>
                  <option value="7">กรกฎาคม</option><option value="8">สิงหาคม</option>
                  <option value="9">กันยายน</option><option value="10">ตุลาคม</option>
                  <option value="11">พฤศจิกายน</option><option value="12">ธันวาคม</option>
                </select>
              </div>
            </div>
            <!-- Quarter -->
            <div id="rp-wrap-quarter" style="display:none;gap:8px;">
              <div class="form-group" style="margin:0;"><label>ปี</label><input type="number" id="rp-year-q" value="2026" min="2000" max="2100" style="width:90px;"></div>
              <div class="form-group" style="margin:0;"><label>Quarter</label>
                <select id="rp-quarter-sel" style="border:1.5px solid #e2e8f0;border-radius:8px;padding:8px 12px;font-size:13px;font-family:inherit;background:#fff;">
                  <option value="1">Q1 (ม.ค.–มี.ค.)</option>
                  <option value="2">Q2 (เม.ย.–มิ.ย.)</option>
                  <option value="3">Q3 (ก.ค.–ก.ย.)</option>
                  <option value="4">Q4 (ต.ค.–ธ.ค.)</option>
                </select>
              </div>
            </div>
            <!-- Year only -->
            <div id="rp-wrap-year" style="display:none;">
              <div class="form-group" style="margin:0;"><label>ปี</label><input type="number" id="rp-year-y" value="2026" min="2000" max="2100" style="width:90px;"></div>
            </div>
            <!-- Date range -->
            <div id="rp-wrap-daterange" style="display:none;gap:8px;">
              <div class="form-group" style="margin:0;"><label>จากวันที่</label><input type="date" id="rp-from"></div>
              <div class="form-group" style="margin:0;"><label>ถึงวันที่</label><input type="date" id="rp-to"></div>
            </div>
            <button class="btn btn-primary" onclick="loadDeliveryStats()" style="white-space:nowrap;">
              <i class="fa-solid fa-chart-bar"></i> ดูรายงาน
            </button>
          </div>
        </div>

        <!-- Section 1: Cards -->
        <div id="rp-cards" style="display:none;margin-bottom:16px;">
          <div class="stat-grid">
            <!-- completed -->
            <div class="stat-card">
              <div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
              <div>
                <div class="stat-value" id="rp-c-completed">-</div>
                <div class="stat-label">ส่งสำเร็จ (ครั้ง)</div>
              </div>
            </div>
            <!-- problem -->
            <div class="stat-card" style="flex-direction:column;align-items:flex-start;gap:8px;">
              <div style="display:flex;align-items:center;gap:12px;width:100%;">
                <div class="stat-icon" style="background:#fff5eb;color:#c05621;flex-shrink:0;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div>
                  <div class="stat-value" id="rp-c-problem" style="color:#c05621;">-</div>
                  <div class="stat-label">มีปัญหา (ครั้ง)</div>
                </div>
              </div>
              <div style="width:100%;padding-left:4px;">
                <select id="rp-problem-filter" onchange="onProblemFilterChange()"
                        style="width:100%;border:1.5px solid #e2e8f0;border-radius:6px;padding:5px 8px;font-size:12px;font-family:inherit;background:#fff;">
                  <option value="all">ทั้งหมด</option>
                  <option value="incomplete">รับไม่ครบ</option>
                  <option value="rejected">ปฏิเสธรับ</option>
                </select>
                <div style="margin-top:4px;font-size:12px;color:#718096;" id="rp-problem-sub"></div>
              </div>
            </div>
            <!-- BO -->
            <div class="stat-card">
              <div class="stat-icon" style="background:#f0e6ff;color:#6b21a8;"><i class="fa-solid fa-rotate"></i></div>
              <div>
                <div class="stat-value" id="rp-c-bo" style="color:#6b21a8;">-</div>
                <div class="stat-label">BO ส่งซ้ำ (ครั้ง)</div>
              </div>
            </div>
            <!-- Rate -->
            <div class="stat-card" style="flex-direction:column;align-items:flex-start;gap:6px;">
              <div style="display:flex;align-items:center;gap:12px;">
                <div class="stat-icon" style="background:#e6fffa;color:#276749;"><i class="fa-solid fa-percent"></i></div>
                <div>
                  <div class="stat-value" id="rp-c-rate" style="color:#276749;">-</div>
                  <div class="stat-label">อัตราสำเร็จ</div>
                </div>
              </div>
              <div style="width:100%;background:#e2e8f0;border-radius:99px;height:6px;overflow:hidden;">
                <div id="rp-rate-bar" style="height:100%;background:#48bb78;border-radius:99px;width:0%;transition:width .5s;"></div>
              </div>
              <div style="font-size:11px;color:#718096;" id="rp-c-rate-sub"></div>
            </div>
          </div>
        </div>

        <!-- Section 2: Trend chart -->
        <div class="card" id="rp-trend-card" style="display:none;margin-bottom:16px;">
          <div class="card-title"><i class="fa-solid fa-chart-column"></i> แนวโน้มรายเดือน <span id="rp-trend-year" style="font-size:13px;color:#718096;font-weight:400;"></span></div>
          <canvas id="rp-trend-chart" height="90"></canvas>
          <div style="display:flex;gap:16px;margin-top:8px;font-size:12px;color:#718096;">
            <span style="display:flex;align-items:center;gap:4px;"><span style="display:inline-block;width:12px;height:12px;background:#48bb78;border-radius:3px;"></span>เสร็จสิ้น</span>
            <span style="display:flex;align-items:center;gap:4px;"><span style="display:inline-block;width:12px;height:12px;background:#ed8936;border-radius:3px;"></span>มีปัญหา</span>
            <span style="display:flex;align-items:center;gap:4px;"><span style="display:inline-block;width:12px;height:12px;background:#bee3f8;border-radius:3px;border:2px solid #4299e1;"></span>ช่วง filter</span>
          </div>
        </div>

        <!-- Section 3: Item tables -->
        <div id="rp-items-section" style="display:none;display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
          <div class="card">
            <div class="card-title" style="color:#c05621;"><i class="fa-solid fa-box-open"></i> สินค้าขาดบ่อยสุด</div>
            <table id="rp-shortage-table">
              <thead><tr><th>#</th><th>ชื่อสินค้า</th><th style="text-align:center;">ครั้ง</th><th>เหตุผลหลัก</th></tr></thead>
              <tbody id="rp-shortage-tbody"><tr><td colspan="4" style="color:#a0aec0;text-align:center;">ยังไม่มีข้อมูล</td></tr></tbody>
            </table>
          </div>
          <div class="card">
            <div class="card-title" style="color:#9b2c2c;"><i class="fa-solid fa-ban"></i> สินค้าถูกปฏิเสธบ่อยสุด</div>
            <table id="rp-rejected-table">
              <thead><tr><th>#</th><th>ชื่อสินค้า</th><th style="text-align:center;">ครั้ง</th><th>เหตุผลหลัก</th></tr></thead>
              <tbody id="rp-rejected-tbody"><tr><td colspan="4" style="color:#a0aec0;text-align:center;">ยังไม่มีข้อมูล</td></tr></tbody>
            </table>
          </div>
        </div>

        <!-- Section 4: Timing -->
        <div class="card" id="rp-timing-card" style="display:none;">
          <div class="card-title"><i class="fa-solid fa-stopwatch"></i> เวลาเฉลี่ย (เฉพาะ DO ที่เสร็จสิ้นครบ)</div>
          <div class="stat-grid">
            <div class="stat-card">
              <div class="stat-icon blue"><i class="fa-solid fa-warehouse"></i></div>
              <div>
                <div class="stat-value" id="rp-t-prep" style="font-size:1.3rem;">-</div>
                <div class="stat-label">HQ เตรียมของ</div>
                <div style="font-size:11px;color:#a0aec0;">สร้าง DO → เริ่มส่ง</div>
              </div>
            </div>
            <div class="stat-card">
              <div class="stat-icon yellow"><i class="fa-solid fa-truck"></i></div>
              <div>
                <div class="stat-value" id="rp-t-drive" style="font-size:1.3rem;">-</div>
                <div class="stat-label">เวลาขับ</div>
                <div style="font-size:11px;color:#a0aec0;">เริ่มส่ง → ถึงสาขา</div>
              </div>
            </div>
            <div class="stat-card">
              <div class="stat-icon green"><i class="fa-solid fa-box-open"></i></div>
              <div>
                <div class="stat-value" id="rp-t-receive" style="font-size:1.3rem;">-</div>
                <div class="stat-label">Branch รับของ</div>
                <div style="font-size:11px;color:#a0aec0;">ถึงสาขา → ยืนยันรับ</div>
              </div>
            </div>
            <div class="stat-card">
              <div class="stat-icon" style="background:#fef3c7;color:#92400e;"><i class="fa-solid fa-sun"></i></div>
              <div>
                <div class="stat-value" id="rp-t-hour" style="font-size:1.3rem;">-</div>
                <div class="stat-label">ช่วงเวลาส่งเร็วสุด</div>
                <div style="font-size:11px;color:#a0aec0;">นับจากเวลารับสินค้า</div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- USERS -->
      <div class="page-section" id="page-users">
        <div class="card">
          <div class="action-bar">
            <span style="font-size:13px;color:#718096;" id="user-count">-</span>
            <button class="btn btn-success btn-sm" onclick="openCreateUser()">
              <i class="fa-solid fa-plus"></i> เพิ่มผู้ใช้
            </button>
          </div>
          <div class="table-wrap">
            <table>
              <thead><tr><th>ID</th><th>ชื่อ</th><th>ตำแหน่ง</th><th>สาขา</th><th>สถานะ</th><th>จัดการ</th></tr></thead>
              <tbody id="users-tbody"><tr><td colspan="6" class="loading">กำลังโหลด...</td></tr></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ITEMS -->
      <div class="page-section" id="page-items">
        <div class="card">
          <div class="action-bar">
            <span style="font-size:13px;color:#718096;" id="item-count">-</span>
            <button class="btn btn-success btn-sm" onclick="openCreateItem()">
              <i class="fa-solid fa-plus"></i> เพิ่มสินค้า
            </button>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>ชื่อสินค้า</th>
                  <th>หน่วย</th>
                  <th>หมวดหมู่</th>
                  <th style="text-align:center;">สถานะ</th>
                  <th style="text-align:center;">จัดการ</th>
                </tr>
              </thead>
              <tbody id="items-tbody"><tr><td colspan="5" class="loading">กำลังโหลด...</td></tr></tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- MODAL: Create Order -->
<div class="modal-overlay" id="modal-order">
  <div class="modal">
    <div class="modal-title"><i class="fa-solid fa-box"></i> สร้าง Delivery Order ใหม่</div>
    <div class="form-row">
      <div class="form-group"><label>สาขา (Branch)</label><select id="mo-branch"></select></div>
      <div class="form-group"><label>ยานพาหนะ (Vehicle)</label><select id="mo-vehicle"><option value="">-- ไม่ระบุ --</option></select></div>
      <div class="form-group"><label>วันที่นัดส่ง</label><input type="date" id="mo-date"></div>
    </div>
    <div class="form-group" style="margin-top:12px;">
      <label>รายการสินค้า</label>
      <div id="mo-items-list"></div>
      <button class="btn btn-secondary btn-sm" onclick="addItem()" style="margin-top:8px;">
        <i class="fa-solid fa-plus"></i> เพิ่มรายการ
      </button>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-order')">ยกเลิก</button>
      <button class="btn btn-success" onclick="submitOrder()">
        <i class="fa-solid fa-floppy-disk"></i> สร้างออเดอร์
      </button>
    </div>
  </div>
</div>

<!-- MODAL: Order Detail -->
<div class="modal-overlay" id="modal-order-detail">
  <div class="modal" style="width:640px;">
    <div class="modal-title" id="detail-title"><i class="fa-solid fa-box"></i> รายละเอียดออเดอร์</div>
    <div id="detail-content"></div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-order-detail')">ปิด</button>
      <div id="detail-actions" style="display:flex;gap:8px;"></div>
    </div>
  </div>
</div>

<!-- MODAL: Create Branch -->
<div class="modal-overlay" id="modal-branch">
  <div class="modal" style="width:640px;">
    <div class="modal-title"><i class="fa-solid fa-building"></i> เพิ่มสาขาใหม่</div>
    <div class="form-group"><label>ชื่อสาขา</label><input type="text" id="mb-name" placeholder="ชื่อสาขา..."></div>
    <div class="form-group">
      <label>ที่อยู่</label>
      <div style="display:flex;gap:8px;align-items:flex-start;">
        <textarea id="mb-address" rows="2" placeholder="ที่อยู่..." style="flex:1;"></textarea>
        <button class="btn btn-secondary btn-sm" onclick="searchAddress('mb')" style="white-space:nowrap;flex-shrink:0;">
          <i class="fa-solid fa-magnifying-glass"></i> ค้นหาจากที่อยู่
        </button>
      </div>
    </div>
    <div class="form-group">
      <label>วางพิกัด</label>
      <div style="display:flex;gap:8px;">
        <input type="text" id="mb-coords" placeholder="13.7563, 100.5018" style="flex:1;"
               onkeydown="if(event.key==='Enter'){event.preventDefault();pinFromCoords('mb');}">
        <button class="btn btn-secondary btn-sm" onclick="pinFromCoords('mb')" style="white-space:nowrap;flex-shrink:0;">
          <i class="fa-solid fa-location-crosshairs"></i> ปักหมุด
        </button>
      </div>
    </div>
    <div class="form-group">
      <label>แผนที่ — คลิกเพื่อปักหมุด</label>
      <div id="mb-map" style="height:300px;border-radius:8px;border:1.5px solid #e2e8f0;margin-top:6px;"></div>
    </div>
    <div class="form-row" style="display:none;">
      <div class="form-group"><label>ละติจูด</label><input type="number" id="mb-lat" step="any" placeholder="13.7563" readonly style="background:#f7fafc;"></div>
      <div class="form-group"><label>ลองจิจูด</label><input type="number" id="mb-lng" step="any" placeholder="100.5018" readonly style="background:#f7fafc;"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-branch')">ยกเลิก</button>
      <button class="btn btn-success" onclick="submitBranch()">
        <i class="fa-solid fa-floppy-disk"></i> บันทึก
      </button>
    </div>
  </div>
</div>

<!-- MODAL: Edit Branch -->
<div class="modal-overlay" id="modal-edit-branch">
  <div class="modal" style="width:640px;">
    <div class="modal-title"><i class="fa-solid fa-building"></i> แก้ไขสาขา</div>
    <input type="hidden" id="eb-id">
    <div class="form-group"><label>ชื่อสาขา</label><input type="text" id="eb-name" placeholder="ชื่อสาขา..."></div>
    <div class="form-group">
      <label>ที่อยู่</label>
      <div style="display:flex;gap:8px;align-items:flex-start;">
        <textarea id="eb-address" rows="2" placeholder="ที่อยู่..." style="flex:1;"></textarea>
        <button class="btn btn-secondary btn-sm" onclick="searchAddress('eb')" style="white-space:nowrap;flex-shrink:0;">
          <i class="fa-solid fa-magnifying-glass"></i> ค้นหาจากที่อยู่
        </button>
      </div>
    </div>
    <div class="form-group">
      <label>วางพิกัด</label>
      <div style="display:flex;gap:8px;">
        <input type="text" id="eb-coords" placeholder="13.7563, 100.5018" style="flex:1;"
               onkeydown="if(event.key==='Enter'){event.preventDefault();pinFromCoords('eb');}">
        <button class="btn btn-secondary btn-sm" onclick="pinFromCoords('eb')" style="white-space:nowrap;flex-shrink:0;">
          <i class="fa-solid fa-location-crosshairs"></i> ปักหมุด
        </button>
      </div>
    </div>
    <div class="form-group">
      <label>แผนที่ — คลิกเพื่อปักหมุด</label>
      <div id="eb-map" style="height:300px;border-radius:8px;border:1.5px solid #e2e8f0;margin-top:6px;"></div>
    </div>
    <div class="form-row" style="display:none;">
      <div class="form-group"><label>ละติจูด</label><input type="number" id="eb-lat" step="any" placeholder="13.7563" readonly style="background:#f7fafc;"></div>
      <div class="form-group"><label>ลองจิจูด</label><input type="number" id="eb-lng" step="any" placeholder="100.5018" readonly style="background:#f7fafc;"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-edit-branch')">ยกเลิก</button>
      <button class="btn btn-primary" onclick="submitEditBranch()">
        <i class="fa-solid fa-floppy-disk"></i> บันทึก
      </button>
    </div>
  </div>
</div>

<!-- MODAL: Create Vehicle -->
<div class="modal-overlay" id="modal-vehicle">
  <div class="modal">
    <div class="modal-title"><i class="fa-solid fa-truck"></i> เพิ่มยานพาหนะ</div>
    <div class="form-group"><label>ทะเบียนรถ</label><input type="text" id="mv-plate" placeholder="กก 1234 กทม"></div>
    <div class="form-group"><label>คนขับ (Driver)</label><select id="mv-driver"><option value="">-- ไม่ระบุ --</option></select></div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-vehicle')">ยกเลิก</button>
      <button class="btn btn-success" onclick="submitVehicle()">
        <i class="fa-solid fa-floppy-disk"></i> บันทึก
      </button>
    </div>
  </div>
</div>

<!-- MODAL: Edit Vehicle -->
<div class="modal-overlay" id="modal-edit-vehicle">
  <div class="modal">
    <div class="modal-title"><i class="fa-solid fa-truck"></i> แก้ไขยานพาหนะ</div>
    <input type="hidden" id="ev-id">
    <div class="form-group"><label>ทะเบียนรถ</label><input type="text" id="ev-plate" placeholder="กก 1234 กทม"></div>
    <div class="form-group"><label>คนขับ (Driver)</label><select id="ev-driver"><option value="">-- ไม่ระบุ --</option></select></div>
    <div class="form-group"><label>สถานะ</label>
      <select id="ev-active">
        <option value="1">ใช้งาน</option>
        <option value="0">ปิด</option>
      </select>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-edit-vehicle')">ยกเลิก</button>
      <button class="btn btn-primary" onclick="submitEditVehicle()">
        <i class="fa-solid fa-floppy-disk"></i> บันทึก
      </button>
    </div>
  </div>
</div>

<!-- MODAL: Create User -->
<div class="modal-overlay" id="modal-user">
  <div class="modal">
    <div class="modal-title"><i class="fa-solid fa-user-plus"></i> เพิ่มผู้ใช้ใหม่</div>
    <div class="form-row">
      <div class="form-group"><label>ชื่อ-นามสกุล</label><input type="text" id="mu-name" placeholder="ชื่อ นามสกุล"></div>
      <div class="form-group"><label>รหัสพนักงาน</label><input type="text" id="mu-empcode" placeholder="เช่น EMP001"></div>
      <div class="form-group"><label>Password</label><input type="password" id="mu-pass" placeholder="อย่างน้อย 8 ตัวอักษร"></div>
      <div class="form-group"><label>Role</label>
        <select id="mu-role" onchange="toggleBranchField('mu')">
          <option value="admin">Admin</option>
          <option value="hq">HQ</option>
          <option value="driver">Driver</option>
          <option value="branch">Branch</option>
        </select>
      </div>
      <div class="form-group" id="mu-branch-group"><label>สาขา</label><select id="mu-branch"><option value="">-- ไม่ระบุ --</option></select></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-user')">ยกเลิก</button>
      <button class="btn btn-success" onclick="submitUser()">
        <i class="fa-solid fa-floppy-disk"></i> สร้างผู้ใช้
      </button>
    </div>
  </div>
</div>

<!-- MODAL: Edit User -->
<div class="modal-overlay" id="modal-edit-user">
  <div class="modal">
    <div class="modal-title"><i class="fa-solid fa-user-pen"></i> แก้ไขผู้ใช้</div>
    <input type="hidden" id="eu-id">
    <div class="form-row">
      <div class="form-group"><label>ชื่อ-นามสกุล</label><input type="text" id="eu-name" placeholder="ชื่อ นามสกุล"></div>
      <div class="form-group"><label>Role</label>
        <select id="eu-role" onchange="toggleBranchField('eu')">
          <option value="admin">Admin</option>
          <option value="hq">HQ</option>
          <option value="driver">Driver</option>
          <option value="branch">Branch</option>
        </select>
      </div>
      <div class="form-group" id="eu-branch-group"><label>สาขา</label><select id="eu-branch"><option value="">-- ไม่ระบุ --</option></select></div>
      <div class="form-group"><label>รหัสพนักงาน</label><input type="text" id="eu-empcode" placeholder="เช่น EMP001"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-edit-user')">ยกเลิก</button>
      <button class="btn btn-primary" onclick="submitEditUser()">
        <i class="fa-solid fa-floppy-disk"></i> บันทึก
      </button>
    </div>
  </div>
</div>

<!-- MODAL: Receive -->
<div class="modal-overlay" id="modal-receive">
  <div class="modal" style="width:720px;">
    <div class="modal-title">
      <i class="fa-solid fa-box-open"></i>
      <span id="recv-modal-title">รับสินค้า</span>
    </div>
    <div class="table-wrap" style="margin-bottom:16px;">
      <table>
        <thead>
          <tr>
            <th>สินค้า</th>
            <th style="text-align:center;width:110px;">จำนวนสั่ง</th>
            <th style="text-align:center;width:100px;">รับจริง</th>
            <th>เหตุที่ขาด/ปัญหา</th>
          </tr>
        </thead>
        <tbody id="recv-items-tbody"></tbody>
      </table>
    </div>
    <div style="margin-bottom:16px;">
      <div style="font-size:12px;font-weight:600;color:#4a5568;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">
        ลายเซ็นผู้รับสินค้า
      </div>
      <div style="border:1.5px solid #e2e8f0;border-radius:8px;overflow:hidden;background:#fafafa;position:relative;">
        <canvas id="recv-sig-canvas" width="660" height="130"
                style="width:100%;display:block;touch-action:none;cursor:crosshair;"></canvas>
        <div id="recv-sig-hint" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none;color:#cbd5e0;font-size:13px;">
          <i class="fa-solid fa-signature" style="margin-right:6px;"></i> ลากเพื่อเซ็นชื่อ
        </div>
      </div>
      <button class="btn btn-secondary btn-sm" onclick="clearReceiveSignature()" style="margin-top:6px;">
        <i class="fa-solid fa-eraser"></i> ล้างลายเซ็น
      </button>
    </div>
    <div style="margin-bottom:16px;">
      <div style="font-size:12px;font-weight:600;color:#4a5568;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">
        หลักฐานการรับสินค้า
      </div>
      <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <label class="btn btn-secondary btn-sm" style="cursor:pointer;margin:0;">
          <i class="fa-solid fa-camera"></i> ถ่ายรูปหลักฐาน
          <input type="file" id="recv-photo-input" accept="image/*" capture="environment" multiple style="display:none;" onchange="addReceivePhoto(this)">
        </label>
        <div id="recv-photo-preview" style="display:flex;flex-wrap:wrap;gap:8px;"></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-receive')">ยกเลิก</button>
      <button class="btn btn-success" onclick="submitReceive()">
        <i class="fa-solid fa-circle-check"></i> ยืนยันรับสินค้า
      </button>
    </div>
  </div>
</div>

<!-- MODAL: Cancel Order -->
<div class="modal-overlay" id="modal-cancel-order">
  <div class="modal">
    <div class="modal-title"><i class="fa-solid fa-circle-xmark" style="color:#e53e3e;"></i> ยืนยันการยกเลิก DO</div>
    <input type="hidden" id="cancel-order-id">
    <div class="form-group">
      <label>เหตุผลการยกเลิก <span style="color:#e53e3e;">*</span></label>
      <select id="cancel-reason-select" onchange="onCancelReasonChange()"
        style="width:100%;border:1.5px solid #e2e8f0;border-radius:8px;padding:10px 14px;font-size:14px;font-family:inherit;background:#fff;">
        <option value="">-- กรุณาเลือกเหตุผล --</option>
        <option value="สินค้าหมดสต็อก">สินค้าหมดสต็อก</option>
        <option value="ลูกค้ายกเลิก">ลูกค้ายกเลิก</option>
        <option value="รถมีปัญหา">รถมีปัญหา</option>
        <option value="อื่นๆ">อื่นๆ</option>
      </select>
    </div>
    <div class="form-group" id="cancel-other-wrap" style="display:none;">
      <label>หมายเหตุ <span style="color:#e53e3e;">*</span></label>
      <textarea id="cancel-other-note" rows="3" placeholder="กรุณาระบุเหตุผล..."
        style="width:100%;border:1.5px solid #e2e8f0;border-radius:8px;padding:10px 14px;font-size:14px;font-family:inherit;resize:vertical;"></textarea>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-cancel-order')">ปิด</button>
      <button class="btn btn-danger" onclick="submitCancelOrder()">
        <i class="fa-solid fa-xmark"></i> ยืนยันยกเลิก
      </button>
    </div>
  </div>
</div>

<!-- MODAL: Item (Add / Edit) -->
<div class="modal-overlay" id="modal-item">
  <div class="modal">
    <div class="modal-title"><i class="fa-solid fa-boxes-stacked"></i> <span id="item-modal-title">เพิ่มสินค้า</span></div>
    <input type="hidden" id="mi-id">
    <div class="form-row">
      <div class="form-group" style="flex:2;">
        <label>ชื่อสินค้า <span style="color:#e53e3e;">*</span></label>
        <input type="text" id="mi-name" placeholder="ชื่อสินค้า..." style="width:100%;">
      </div>
      <div class="form-group" style="flex:1;">
        <label>หน่วย <span style="color:#e53e3e;">*</span></label>
        <input type="text" id="mi-unit" placeholder="เช่น กล่อง, ลัง, ถุง" style="width:100%;">
      </div>
    </div>
    <div class="form-group">
      <label>หมวดหมู่</label>
      <input type="text" id="mi-category" placeholder="เช่น อาหาร, เครื่องดื่ม, ของใช้" style="width:100%;">
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-item')">ยกเลิก</button>
      <button class="btn btn-success" onclick="submitItem()">
        <i class="fa-solid fa-floppy-disk"></i> บันทึก
      </button>
    </div>
  </div>
</div>

<!-- MODAL: Reject Backorder -->
<div class="modal-overlay" id="modal-reject-bo">
  <div class="modal">
    <div class="modal-title"><i class="fa-solid fa-circle-xmark" style="color:#e53e3e;"></i> ปฏิเสธ Backorder</div>
    <input type="hidden" id="rbo-id">
    <div class="form-group">
      <label>เหตุผลการปฏิเสธ</label>
      <textarea id="rbo-reason" rows="3" placeholder="กรุณาระบุเหตุผล..."
        style="width:100%;border:1.5px solid #e2e8f0;border-radius:8px;padding:10px 14px;font-size:14px;font-family:inherit;resize:vertical;"></textarea>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-reject-bo')">ยกเลิก</button>
      <button class="btn btn-danger" onclick="submitRejectBO()">
        <i class="fa-solid fa-xmark"></i> ยืนยันปฏิเสธ
      </button>
    </div>
  </div>
</div>

<script>
const BASE = '';
let token = localStorage.getItem('bh_token') || '';
let currentUser = null;
let allOrders = [];
let allBranches = [];
let allVehicles = [];
let notifications = [];
let notifOpen = false;
let notifPollId = null;
let pendingScrollDoId = null;

async function api(method, path, body) {
  const res = await fetch(BASE + path, {
    method,
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      ...(token ? {'Authorization': 'Bearer ' + token} : {}),
    },
    body: body ? JSON.stringify(body) : undefined,
  });
  const data = await res.json().catch(() => ({}));
  if (res.status === 401 && path !== '/api/auth/quick-login') {
    token = '';
    localStorage.removeItem('bh_token');
    document.getElementById('app').style.display = 'none';
    document.getElementById('login-screen').style.display = '';
    showToast('เซสชันหมดอายุ กรุณาล็อกอินใหม่', 'error');
  }
  return {ok: res.ok, status: res.status, data};
}

async function doQuickLogin() {
  const code = document.getElementById('employee-code').value.trim();
  const errEl = document.getElementById('quick-error');
  errEl.innerHTML = '';
  if (!code) {
    errEl.innerHTML = '<div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> กรุณาใส่รหัสพนักงาน</div>';
    return;
  }
  const r = await api('POST', '/api/auth/quick-login', {employee_code: code});
  if (r.ok) {
    token = r.data.token;
    localStorage.setItem('bh_token', token);
    initApp(r.data.user);
  } else {
    errEl.innerHTML = '<div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> ' + (r.data.message || 'ไม่พบรหัสพนักงานนี้') + '</div>';
  }
}

function selectRole(name, icon) {
  document.getElementById('qa-step-1').style.display = 'none';
  document.getElementById('qa-step-2').style.display = 'block';
  document.getElementById('qa-role-icon').textContent = icon;
  document.getElementById('qa-role-name').textContent = name;
  document.getElementById('quick-error').innerHTML = '';
  document.getElementById('employee-code').value = '';
  setTimeout(() => document.getElementById('employee-code').focus(), 80);
}

function backToRoleSelect() {
  document.getElementById('qa-step-2').style.display = 'none';
  document.getElementById('qa-step-1').style.display = 'block';
  document.getElementById('employee-code').value = '';
  document.getElementById('quick-error').innerHTML = '';
}


async function doLogout() {
  await api('POST', '/api/auth/logout');
  token = '';
  localStorage.removeItem('bh_token');
  currentUser = null;
  document.getElementById('app').style.display = 'none';
  document.getElementById('login-screen').style.display = 'flex';
  backToRoleSelect();
}

function initApp(user) {
  currentUser = user;
  document.getElementById('login-screen').style.display = 'none';
  document.getElementById('app').style.display = 'block';
  document.getElementById('sidebar-name').textContent = user.name;
  document.getElementById('sidebar-role').textContent = roleTH(user.role);
  document.getElementById('avatar-initial').textContent = user.name[0].toUpperCase();
  buildNav(user.role);
  if (['admin', 'hq', 'driver'].includes(user.role)) {
    document.getElementById('notif-wrap').style.display = 'block';
    loadNotifications();
    if (notifPollId) clearInterval(notifPollId);
    notifPollId = setInterval(loadNotifications, user.role === 'driver' ? 30000 : 60000);
  }
  navigate('dashboard');
}

const NAV_ITEMS = {
  dashboard: {icon:'fa-house',        label:'หน้าหลัก',       roles:['admin','hq','driver','branch']},
  orders:    {icon:'fa-box',           label:'ใบสั่งส่งสินค้า', roles:['admin','hq']},
  receive:   {icon:'fa-circle-check',  label:'รับสินค้า',       roles:['admin','branch']},
  gps:       {icon:'fa-location-dot',  label:'ติดตามรถ',    roles:['admin','hq']},
  branches:  {icon:'fa-building',      label:'สาขา',            roles:['admin','hq']},
  vehicles:  {icon:'fa-truck',         label:'ยานพาหนะ',        roles:['admin','hq']},
  reports:   {icon:'fa-chart-bar',     label:'รายงาน',          roles:['admin','hq']},
  users:     {icon:'fa-users',         label:'จัดการผู้ใช้',    roles:['admin']},
  items:     {icon:'fa-boxes-stacked', label:'สินค้า',           roles:['admin']},
};

function buildNav(role) {
  const nav = document.getElementById('sidebar-nav');
  nav.innerHTML = '';
  Object.entries(NAV_ITEMS).forEach(([key, item]) => {
    if (!item.roles.includes(role)) return;
    const el = document.createElement('div');
    el.className = 'nav-item';
    el.id = 'nav-' + key;
    el.innerHTML = `<i class="fa-solid ${item.icon}"></i><span>${item.label}</span>`;
    el.onclick = () => navigate(key);
    nav.appendChild(el);
  });
}

function navigate(page) {
  document.querySelectorAll('.page-section').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  const sec = document.getElementById('page-' + page);
  const nav = document.getElementById('nav-' + page);
  if (sec) sec.classList.add('active');
  if (nav) nav.classList.add('active');
  const titles = {
    dashboard:'หน้าหลัก', orders:'ใบสั่งส่งสินค้า', receive:'รับสินค้า',
    gps:'ติดตามรถ', branches:'สาขา', vehicles:'ยานพาหนะ',
    reports:'รายงาน', users:'จัดการผู้ใช้', items:'จัดการสินค้า'
  };
  document.getElementById('page-title').textContent = titles[page] || page;
  const loaders = {dashboard:loadDashboard, orders:loadOrders, branches:loadBranches, vehicles:loadVehicles, users:loadUsers, reports:()=>{}, gps:initGpsPage, receive:loadReceive, items:loadItems};
  if (loaders[page]) loaders[page]();
}

/* ---- DASHBOARD ---- */
let dashboardLoading = false;

async function loadDashboard() {
  if (dashboardLoading) return;
  dashboardLoading = true;
  const btn = document.getElementById('btn-dashboard-refresh');
  if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-rotate-right fa-spin"></i> กำลังโหลด...'; }

  const resetBtn = () => {
    dashboardLoading = false;
    if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-rotate-right"></i> รีเฟรช'; }
  };

  if (currentUser?.role === 'driver') {
    loadDriverDashboard();
    resetBtn();
    return;
  }

  const r = await api('GET', '/api/reports/delivery-summary');
  if (!r.ok) { resetBtn(); return; }
  const d = r.data;

  document.getElementById('stat-grid').innerHTML = `
    <div class="stat-card" style="cursor:pointer;" onclick="navigateToOrders('')"><div class="stat-icon blue"><i class="fa-solid fa-box"></i></div><div><div class="stat-value">${d.total}</div><div class="stat-label">ออเดอร์ทั้งหมด</div></div></div>
    <div class="stat-card" style="cursor:pointer;" onclick="navigateToOrders('pending')"><div class="stat-icon yellow"><i class="fa-solid fa-hourglass-half"></i></div><div><div class="stat-value">${d.pending}</div><div class="stat-label">รอดำเนินการ</div></div></div>
    <div class="stat-card" style="cursor:pointer;" onclick="navigateToOrders('in_transit')"><div class="stat-icon blue"><i class="fa-solid fa-truck"></i></div><div><div class="stat-value">${d.in_transit}</div><div class="stat-label">กำลังส่ง</div></div></div>
    <div class="stat-card" style="cursor:pointer;" onclick="navigateToOrders('delivered')"><div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div><div><div class="stat-value">${d.delivered}</div><div class="stat-label">ส่งแล้ว</div></div></div>
    <div class="stat-card" style="cursor:pointer;" onclick="navigateToOrders('cancelled')"><div class="stat-icon red"><i class="fa-solid fa-circle-xmark"></i></div><div><div class="stat-value">${d.cancelled}</div><div class="stat-label">ยกเลิก</div></div></div>`;

  const isAdminHq = ['admin', 'hq'].includes(currentUser?.role);
  document.getElementById('dashboard-transit-card').style.display = isAdminHq ? 'block' : 'none';
  document.getElementById('dashboard-recent-card').style.display = isAdminHq ? 'none' : 'block';

  if (isAdminHq) {
    const transitOrders = (d.orders || []).filter(o => o.status === 'in_transit');
    renderTransitTable(transitOrders);
  } else {
    const recent = (d.orders || []).slice(0, 5);
    document.getElementById('recent-orders').innerHTML = recent.length === 0
      ? `<div class="empty-state"><i class="fa-solid fa-inbox"></i><p>ยังไม่มีออเดอร์</p></div>`
      : `<div class="table-wrap"><table>
          <thead><tr><th>เลขที่ใบสั่ง</th><th>สาขา</th><th>สถานะ</th><th>วันที่นัดส่ง</th></tr></thead>
          <tbody>${recent.map(o => `<tr>
            <td><b style="color:#2a5298">${o.do_number}</b></td>
            <td>${o.branch?.name || '-'}</td>
            <td><span class="badge badge-${o.status}">${statusTH(o.status)}</span></td>
            <td>${formatDate(o.scheduled_date)}</td>
          </tr>`).join('')}</tbody>
        </table></div>`;
  }
  resetBtn();
}

async function loadDriverDashboard() {
  document.getElementById('dashboard-transit-card').style.display = 'none';
  document.getElementById('dashboard-recent-card').style.display = 'block';
  const r = await api('GET', '/api/driver/orders');
  const statGrid = document.getElementById('stat-grid');
  const recentEl = document.getElementById('recent-orders');
  if (!r.ok) {
    statGrid.innerHTML = '';
    recentEl.innerHTML = `<div class="empty-state"><i class="fa-solid fa-circle-exclamation"></i><p>โหลดข้อมูลไม่สำเร็จ</p></div>`;
    return;
  }
  const orders  = r.data;
  const pending  = orders.filter(o => o.status === 'pending');
  const active   = orders.filter(o => o.status !== 'pending');
  const nTransit = orders.filter(o => o.status === 'in_transit').length;
  const nDone    = orders.filter(o => o.status === 'delivered' || o.status === 'completed').length;

  statGrid.innerHTML = `
    <div class="stat-card"><div class="stat-icon blue"><i class="fa-solid fa-box"></i></div><div><div class="stat-value">${orders.length}</div><div class="stat-label">ออเดอร์ทั้งหมด</div></div></div>
    <div class="stat-card"><div class="stat-icon yellow"><i class="fa-solid fa-hourglass-half"></i></div><div><div class="stat-value">${pending.length}</div><div class="stat-label">รอส่ง</div></div></div>
    <div class="stat-card"><div class="stat-icon blue"><i class="fa-solid fa-truck"></i></div><div><div class="stat-value">${nTransit}</div><div class="stat-label">กำลังส่ง</div></div></div>
    <div class="stat-card"><div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div><div><div class="stat-value">${nDone}</div><div class="stat-label">ส่งแล้ว</div></div></div>`;

  let html = '';

  if (pending.length > 0) {
    html += `<div style="margin-bottom:20px;">
      <div style="font-weight:700;font-size:15px;margin-bottom:10px;color:#b7791f;"><i class="fa-solid fa-bell" style="color:#d69e2e;"></i> ออเดอร์รอส่ง (${pending.length})</div>
      ${pending.map(o => `
      <div id="do-card-${o.id}" style="background:#fffbeb;border:1.5px solid #f6c543;border-radius:12px;padding:16px;margin-bottom:12px;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
          <div>
            <div style="font-weight:700;font-size:17px;color:#2a5298;">${o.do_number}${o.is_backorder ? ` <span style="font-size:11px;background:#e2e8f0;padding:1px 5px;border-radius:4px;">BO</span>` : ''}</div>
            <div style="color:#4a5568;margin-top:3px;font-size:14px;"><i class="fa-solid fa-store fa-sm"></i> ${o.branch?.name || '-'}</div>
            <div style="color:#718096;font-size:13px;margin-top:2px;"><i class="fa-regular fa-calendar fa-sm"></i> ${formatDate(o.scheduled_date)}</div>
            <div style="color:#718096;font-size:13px;"><i class="fa-solid fa-boxes-stacked fa-sm"></i> ${o.items?.length || 0} รายการ</div>
          </div>
          <span class="badge badge-pending">รอส่ง</span>
        </div>
        <button class="btn btn-warning" style="width:100%;font-size:15px;padding:12px 0;border-radius:8px;font-weight:700;" onclick="startDelivery(${o.id})">
          <i class="fa-solid fa-truck-fast"></i>&nbsp; เริ่มส่ง
        </button>
      </div>`).join('')}
    </div>`;
  }

  if (active.length > 0) {
    html += `<div style="font-weight:700;font-size:14px;margin-bottom:8px;color:#4a5568;"><i class="fa-solid fa-list"></i> ออเดอร์อื่นๆ</div>
    <div class="table-wrap"><table>
      <thead><tr><th>เลขที่ใบสั่ง</th><th>สาขา</th><th>สถานะ</th><th>วันที่นัดส่ง</th></tr></thead>
      <tbody>${active.map(o => `<tr>
        <td><b style="color:#2a5298">${o.do_number}</b>${o.is_backorder ? `<span style="font-size:10px;color:#718096;margin-left:4px;font-weight:700;">[BO]</span>` : ''}</td>
        <td>${o.branch?.name || '-'}</td>
        <td><span class="badge badge-${o.status}">${statusTH(o.status)}</span></td>
        <td>${formatDate(o.scheduled_date)}</td>
      </tr>`).join('')}</tbody>
    </table></div>`;
  } else if (pending.length === 0) {
    html = `<div class="empty-state"><i class="fa-solid fa-inbox"></i><p>ไม่มีออเดอร์ที่ผูกกับรถของคุณ</p></div>`;
  }

  recentEl.innerHTML = html;

  if (pendingScrollDoId) {
    const scrollId = pendingScrollDoId;
    pendingScrollDoId = null;
    setTimeout(() => {
      const el = document.getElementById('do-card-' + scrollId);
      if (el) el.scrollIntoView({behavior:'smooth', block:'center'});
    }, 50);
  }
}

async function renderTransitTable(orders) {
  const el = document.getElementById('transit-table');
  if (!orders.length) {
    el.innerHTML = `<div class="empty-state"><i class="fa-solid fa-truck"></i><p>ไม่มีออเดอร์ที่กำลังส่ง</p></div>`;
    return;
  }
  el.innerHTML = `<div class="table-wrap"><table>
    <thead><tr><th>DO Number</th><th>สาขาปลายทาง</th><th>ทะเบียนรถ</th><th>ระยะทาง (กม.)</th><th>ETA (นาที)</th><th>จัดการ</th></tr></thead>
    <tbody>${orders.map(o => `<tr>
      <td><b style="color:#2a5298">${o.do_number}</b></td>
      <td>${o.branch?.name || '-'}</td>
      <td>${o.vehicle?.license_plate || '<span style="color:#a0aec0">-</span>'}</td>
      <td id="db-dist-${o.id}" style="font-size:12px;">${o.vehicle?.id ? '<span style="color:#a0aec0">...</span>' : '-'}</td>
      <td id="db-eta-${o.id}"  style="font-size:12px;">${o.vehicle?.id ? '<span style="color:#a0aec0">...</span>' : '-'}</td>
      <td>${o.vehicle?.id && currentUser?.role !== 'branch' ? `<button class="btn btn-primary btn-sm" onclick="navigateToGps(${o.vehicle.id})"><i class="fa-solid fa-location-dot"></i> ติดตาม</button>` : '-'}</td>
    </tr>`).join('')}</tbody>
  </table></div>`;

  for (const o of orders) {
    if (!o.vehicle?.id) continue;
    const dr = await api('GET', '/api/gps/distance/' + o.vehicle.id);
    const distEl = document.getElementById('db-dist-' + o.id);
    const etaEl  = document.getElementById('db-eta-'  + o.id);
    if (!distEl) continue;
    if (dr.ok) {
      distEl.innerHTML = `<span style="font-weight:600;color:#2a5298;">${dr.data.distance_km} กม.</span>`;
      etaEl.innerHTML  = `<span style="color:#276749;">~${dr.data.eta_minutes} นาที</span>`;
    } else {
      distEl.textContent = '-'; etaEl.textContent = '-';
    }
  }
}

/* ---- NOTIFICATIONS ---- */
async function loadNotifications() {
  const r = await api('GET', '/api/notifications');
  if (!r.ok) return;
  notifications = r.data;
  const unread = notifications.filter(n => !n.is_read).length;
  const badge = document.getElementById('notif-badge');
  const label = document.getElementById('notif-unread-label');
  badge.textContent = unread > 9 ? '9+' : String(unread);
  badge.style.display = unread > 0 ? 'flex' : 'none';
  if (label) label.textContent = unread > 0 ? `${unread} ยังไม่อ่าน` : 'อ่านครบแล้ว';
  renderNotifList();
}

function renderNotifList() {
  const el = document.getElementById('notif-list');
  if (!el) return;
  const list = notifications.slice(0, 10);
  if (!list.length) {
    el.innerHTML = '<div class="notif-empty"><i class="fa-solid fa-bell-slash" style="font-size:24px;display:block;margin-bottom:8px;"></i>ไม่มีการแจ้งเตือน</div>';
    return;
  }
  const isDriver = currentUser?.role === 'driver';
  el.innerHTML = list.map(n => {
    const icon = isDriver ? 'fa-truck' : 'fa-triangle-exclamation';
    const iconColor = isDriver ? '#3182ce' : '#d69e2e';
    const hint = isDriver && !n.is_read && n.do_id
      ? `<div style="font-size:11px;color:#3182ce;margin-top:3px;"><i class="fa-solid fa-arrow-right"></i> แตะเพื่อดูที่ Dashboard</div>`
      : '';
    return `
    <div class="notif-item ${n.is_read ? '' : 'unread'}" onclick="clickNotif(${n.id},${n.do_id || 'null'})">
      <div class="notif-msg"><i class="fa-solid ${icon}" style="color:${iconColor};margin-right:4px;"></i>${n.message}</div>
      <div class="notif-time">${formatDateTime(n.created_at)}</div>
      ${hint}
    </div>`;
  }).join('');
}

function toggleNotifDropdown() {
  notifOpen = !notifOpen;
  document.getElementById('notif-dropdown').style.display = notifOpen ? 'block' : 'none';
}

async function clickNotif(nId, doId) {
  document.getElementById('notif-dropdown').style.display = 'none';
  notifOpen = false;
  await api('POST', '/api/notifications/' + nId + '/read');
  const n = notifications.find(x => x.id === nId);
  if (n) n.is_read = true;
  const unread = notifications.filter(x => !x.is_read).length;
  const badge = document.getElementById('notif-badge');
  badge.textContent = unread > 9 ? '9+' : String(unread);
  badge.style.display = unread > 0 ? 'flex' : 'none';
  renderNotifList();
  if (doId) {
    if (currentUser?.role === 'driver') {
      pendingScrollDoId = doId;
      navigate('dashboard');
    } else {
      navigate('orders');
      setTimeout(() => viewOrder(doId), 350);
    }
  }
}

async function fetchDriverVehicleId() {
  if (driverVehicleId) return;
  const r = await api('GET', '/api/vehicles');
  if (!r.ok) return;
  const v = r.data.find(v => v.driver_id === currentUser?.id);
  if (v) driverVehicleId = v.id;
}

async function startDelivery(doId) {
  const r = await api('PATCH', '/api/delivery-orders/' + doId + '/dispatch');
  if (!r.ok) { showToast(r.data.message || 'เกิดข้อผิดพลาด', 'error'); return; }

  const doNotifs = notifications.filter(x => x.do_id == doId && !x.is_read);
  for (const n of doNotifs) {
    api('POST', '/api/notifications/' + n.id + '/read');
    n.is_read = true;
  }
  const unread = notifications.filter(x => !x.is_read).length;
  const badge = document.getElementById('notif-badge');
  badge.textContent = unread > 9 ? '9+' : String(unread);
  badge.style.display = unread > 0 ? 'flex' : 'none';
  renderNotifList();

  if (!isTracking) {
    await fetchDriverVehicleId();
    if (driverVehicleId) startTracking();
  }
  showToast('เริ่มส่งแล้ว — GPS กำลังติดตาม', 'success');
  loadDriverDashboard();
}

document.addEventListener('click', e => {
  const wrap = document.getElementById('notif-wrap');
  if (wrap && !wrap.contains(e.target)) {
    if (notifOpen) {
      document.getElementById('notif-dropdown').style.display = 'none';
      notifOpen = false;
    }
  }
});

/* ---- ORDERS ---- */
let pendingOrderStatus = null;

function navigateToOrders(status) {
  pendingOrderStatus = status;
  navigate('orders');
}

async function loadOrders() {
  let orders;
  if (currentUser?.role === 'driver') {
    const r = await api('GET', '/api/driver/orders');
    if (!r.ok) return;
    orders = r.data || [];
  } else {
    const r = await api('GET', '/api/delivery-orders?per_page=100');
    if (!r.ok) return;
    orders = r.data.data || [];
  }
  allOrders = orders;
  renderOrders(allOrders);
  if (!['admin','hq'].includes(currentUser?.role)) document.getElementById('btn-create-order').style.display = 'none';
  if (pendingOrderStatus !== null) {
    document.getElementById('order-status-filter').value = pendingOrderStatus;
    pendingOrderStatus = null;
    filterOrders();
  }
}

function buildChildMap(orders) {
  const map = {};
  orders.filter(o => o.is_backorder && o.parent_do_id).forEach(o => {
    (map[o.parent_do_id] = map[o.parent_do_id] || []).push(o);
  });
  return map;
}

function getAllBOs(rootId, childMap) {
  return (childMap[rootId] || []).reduce((acc, child) => {
    acc.push(child, ...getAllBOs(child.id, childMap));
    return acc;
  }, []);
}

function toggleBORows(parentId, e) {
  if (e) e.stopPropagation();
  const rows = document.querySelectorAll('.bo-sub-' + parentId);
  const icon = document.getElementById('bo-chev-' + parentId);
  const show = rows.length && rows[0].style.display === 'none';
  rows.forEach(r => r.style.display = show ? 'table-row' : 'none');
  if (icon) icon.style.transform = show ? 'rotate(180deg)' : 'rotate(0deg)';
}

function renderOrders(orders) {
  const tbody = document.getElementById('orders-tbody');
  const isAdminHq = ['admin', 'hq'].includes(currentUser?.role);

  // Driver: flat list (BOs appear as their own assigned orders)
  if (currentUser?.role === 'driver') {
    if (!orders.length) {
      tbody.innerHTML = `<tr><td colspan="7"><div class="empty-state"><i class="fa-solid fa-inbox"></i><p>ไม่มีออเดอร์</p></div></td></tr>`;
      return;
    }
    tbody.innerHTML = orders.map(o => {
      const boTag = o.is_backorder ? `<span style="font-size:10px;color:#718096;margin-left:4px;font-weight:700;">[BO]</span>` : '';
      return `<tr>
        <td><b style="color:#2a5298">${o.do_number}</b>${boTag}</td>
        <td>${o.branch?.name || '-'}</td>
        <td>${o.vehicle?.license_plate || '<span style="color:#a0aec0">-</span>'}</td>
        <td><span class="badge badge-${o.status}">${statusTH(o.status)}</span></td>
        <td>${formatDate(o.scheduled_date)}</td>
        <td>${o.creator?.name || '-'}</td>
        <td><button class="btn btn-primary btn-sm" onclick="viewOrder(${o.id})"><i class="fa-solid fa-eye"></i></button></td>
      </tr>`;
    }).join('');
    return;
  }

  // Admin/HQ/Branch: group BOs as sub-rows under parent DO
  const childMap = buildChildMap(allOrders);
  const roots = orders.filter(o => !o.is_backorder);

  if (!roots.length) {
    tbody.innerHTML = `<tr><td colspan="7"><div class="empty-state"><i class="fa-solid fa-inbox"></i><p>ไม่มีออเดอร์</p></div></td></tr>`;
    return;
  }

  tbody.innerHTML = roots.map(o => {
    const bos = getAllBOs(o.id, childMap);
    const subBadge = o.sub_status
      ? `<span class="badge badge-${o.sub_status}" style="margin-left:4px;">${subStatusTH(o.sub_status)}</span>`
      : '';
    const expandBtn = bos.length
      ? `<button onclick="toggleBORows(${o.id},event)"
           style="background:none;border:1.5px solid #d69e2e;border-radius:6px;padding:1px 7px;cursor:pointer;font-size:11px;color:#744210;margin-left:6px;vertical-align:middle;">
           <i class="fa-solid fa-chevron-down" id="bo-chev-${o.id}" style="font-size:9px;transition:transform .2s;"></i>
           ${bos.length} BO
         </button>`
      : '';

    const mainRow = `<tr>
      <td><b style="color:#2a5298">${o.do_number}</b>${expandBtn}</td>
      <td>${o.branch?.name || '-'}</td>
      <td>${o.vehicle?.license_plate || '<span style="color:#a0aec0">-</span>'}</td>
      <td><span class="badge badge-${o.status}">${statusTH(o.status)}</span>${subBadge}</td>
      <td>${formatDate(o.scheduled_date)}</td>
      <td>${o.creator?.name || '-'}</td>
      <td style="display:flex;gap:4px;flex-wrap:wrap;">
        <button class="btn btn-primary btn-sm" onclick="viewOrder(${o.id})"><i class="fa-solid fa-eye"></i></button>
        ${o.status === 'pending' && isAdminHq ? `<button class="btn btn-warning btn-sm" onclick="quickStatus(${o.id},'in_transit')"><i class="fa-solid fa-truck"></i> ส่ง</button>` : ''}
      </td>
    </tr>`;

    const boRows = bos.map(bo => {
      const isPending = bo.sub_status === 'bo_pending';
      const approveBtns = (isAdminHq && isPending)
        ? `<button class="btn btn-success btn-sm" onclick="approveBO(${bo.id})"><i class="fa-solid fa-check"></i> อนุมัติ</button>
           <button class="btn btn-danger btn-sm" onclick="openRejectBO(${bo.id})"><i class="fa-solid fa-xmark"></i> ปฏิเสธ</button>`
        : '';
      return `<tr class="bo-sub-${o.id}" style="display:none">
        <td colspan="7" style="padding:0;">
          <div style="display:flex;align-items:center;gap:10px;padding:7px 12px 7px 36px;background:#fffff0;border-left:3px solid #d69e2e;">
            <span style="font-size:10px;font-weight:700;color:#975a16;white-space:nowrap;">รอบ ${bo.bo_round}</span>
            <b style="color:#744210;font-size:13px;">${bo.do_number}</b>
            <span class="badge badge-${bo.sub_status}">${subStatusTH(bo.sub_status)}</span>
            <span style="font-size:12px;color:#718096;">${formatDate(bo.scheduled_date)}</span>
            <span style="font-size:12px;color:#a0aec0;">${bo.vehicle?.license_plate || '—'}</span>
            <div style="margin-left:auto;display:flex;gap:4px;">
              <button class="btn btn-primary btn-sm" onclick="viewOrder(${bo.id})"><i class="fa-solid fa-eye"></i></button>
              ${approveBtns}
            </div>
          </div>
        </td>
      </tr>`;
    }).join('');

    return mainRow + boRows;
  }).join('');
}

function filterOrders() {
  const search = document.getElementById('order-search').value.toLowerCase();
  const status = document.getElementById('order-status-filter').value;
  const childMap = buildChildMap(allOrders);

  // Start with root DOs only
  let filtered = allOrders.filter(o => !o.is_backorder);

  if (search) {
    filtered = filtered.filter(o =>
      o.do_number.toLowerCase().includes(search) ||
      (o.branch?.name || '').toLowerCase().includes(search)
    );
  }

  if (status === 'problem') {
    filtered = filtered.filter(o =>
      o.status === 'problem' ||
      getAllBOs(o.id, childMap).some(bo => bo.sub_status === 'bo_pending')
    );
  } else if (status) {
    filtered = filtered.filter(o => o.status === status);
  }

  renderOrders(filtered);
}

async function viewOrder(id) {
  const r = await api('GET', '/api/delivery-orders/' + id);
  if (!r.ok) return;
  const o = r.data;

  const isAdminHq = ['admin','hq'].includes(currentUser?.role);
  let driverOpts = '<option value="">-- เลือกคนขับ --</option>';
  if (isAdminHq) {
    const drRes = await api('GET', '/api/users');
    if (drRes.ok) {
      driverOpts += (drRes.data || [])
        .filter(u => u.role === 'driver')
        .map(d => `<option value="${d.id}">${d.name}</option>`)
        .join('');
    }
  }
  const pendingBOs  = (o.backorders || []).filter(bo => bo.sub_status === 'bo_pending');
  const boList      = (o.sub_status === 'bo_pending' && o.is_backorder) ? [o] : pendingBOs;
  const boCardsHtml = (isAdminHq && boList.length) ? boList.map(bo => `
    <div style="margin-top:16px;padding:16px;background:#fffff0;border:1.5px solid #f6e05e;border-radius:10px;">
      <div style="font-weight:700;color:#744210;margin-bottom:12px;">
        <i class="fa-solid fa-clock" style="color:#d69e2e;"></i>
        BO รอยืนยัน — ${bo.do_number}
        <span style="font-weight:400;font-size:12px;color:#975a16;margin-left:6px;">(รอบที่ ${bo.bo_round})</span>
      </div>
      <div style="overflow-x:auto;margin-bottom:12px;">
        <table style="width:100%;border-collapse:collapse;">
          <thead><tr style="background:#fefcbf;">
            <th style="padding:8px 12px;text-align:left;font-size:11px;color:#744210;font-weight:700;text-transform:uppercase;">สินค้า</th>
            <th style="padding:8px 12px;text-align:center;font-size:11px;color:#744210;font-weight:700;text-transform:uppercase;width:130px;">จำนวน (แก้ได้)</th>
            <th style="padding:8px 12px;text-align:left;font-size:11px;color:#744210;font-weight:700;text-transform:uppercase;width:70px;">หน่วย</th>
          </tr></thead>
          <tbody>${(bo.items||[]).map(i => `<tr>
            <td style="padding:7px 12px;border-bottom:1px solid #fefcbf;">${i.product_name}</td>
            <td style="padding:7px 12px;border-bottom:1px solid #fefcbf;text-align:center;">
              <input type="number" id="bo-qty-${bo.id}-${i.id}" value="${i.qty_ordered}"
                min="0.01" step="any"
                style="width:80px;border:1.5px solid #e2e8f0;border-radius:6px;padding:5px 8px;font-size:13px;text-align:center;font-family:inherit;">
            </td>
            <td style="padding:7px 12px;border-bottom:1px solid #fefcbf;font-size:13px;color:#718096;">${i.unit}</td>
          </tr>`).join('')}</tbody>
        </table>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;flex-wrap:wrap;">
        <div>
          <label style="display:block;font-size:12px;font-weight:600;color:#4a5568;text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px;">คนขับ <span style="color:#e53e3e;">*</span></label>
          <select id="bo-driver-${bo.id}" style="width:100%;border:1.5px solid #e2e8f0;border-radius:8px;padding:8px 12px;font-size:13px;font-family:inherit;">${driverOpts}</select>
        </div>
        <div>
          <label style="display:block;font-size:12px;font-weight:600;color:#4a5568;text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px;">วันที่ส่งซ้ำ <span style="color:#e53e3e;">*</span></label>
          <input type="date" id="bo-date-${bo.id}"
            value="${bo.scheduled_date ? bo.scheduled_date.split('T')[0] : ''}"
            style="width:100%;border:1.5px solid #e2e8f0;border-radius:8px;padding:8px 12px;font-size:13px;font-family:inherit;box-sizing:border-box;">
        </div>
      </div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <button class="btn btn-success btn-sm" onclick="approveBODetail(${bo.id})">
          <i class="fa-solid fa-circle-check"></i> ยืนยันส่งซ้ำ
        </button>
        <button class="btn btn-danger btn-sm" onclick="openRejectBO(${bo.id})">
          <i class="fa-solid fa-xmark"></i> ยกเลิก BO
        </button>
      </div>
    </div>`).join('') : '';

  document.getElementById('detail-title').innerHTML = `<i class="fa-solid fa-box"></i> ${o.do_number}`;
  document.getElementById('detail-content').innerHTML = `
    <div class="info-grid">
      <div class="info-item"><span class="label">สาขา</span><span class="val">${o.branch?.name || '-'}</span></div>
      <div class="info-item"><span class="label">สถานะ</span><span class="val">
        <span class="badge badge-${o.status}">${statusTH(o.status)}</span>
        ${o.sub_status ? `<span class="badge badge-${o.sub_status}" style="margin-left:4px;">${subStatusTH(o.sub_status)}</span>` : ''}
      </span></div>
      <div class="info-item"><span class="label">ยานพาหนะ</span><span class="val">${o.vehicle?.license_plate || '-'}</span></div>
      <div class="info-item"><span class="label">วันที่นัดส่ง</span><span class="val">${formatDate(o.scheduled_date)}</span></div>
      <div class="info-item"><span class="label">สร้างโดย</span><span class="val">${o.creator?.name || '-'}</span></div>
      <div class="info-item"><span class="label">ส่งเมื่อ</span><span class="val">${formatDateTime(o.delivered_at)}</span></div>
    </div>
    ${(() => {
      const items = o.items || [];
      const totalShortage = items.reduce((s, i) => s + (i.shortage_qty || 0), 0);
      const hasShortage = totalShortage > 0;
      const hasNote = items.some(i => i.item_note || i.remark);
      const rows = items.map(i => {
        const shortage = i.shortage_qty || 0;
        const shortageCell = shortage > 0
          ? `<span style="color:#c05621;font-weight:700;">-${shortage} ${i.unit}</span>`
          : `<span style="color:#38a169;">ครบ</span>`;
        const note = i.item_note || i.remark || '';
        return `<tr>
          <td>${i.product_name}</td>
          <td style="text-align:center;">${i.qty_ordered}</td>
          <td style="text-align:center;">${i.qty_received ?? '-'}</td>
          <td style="text-align:center;">${shortageCell}</td>
          <td style="text-align:center;">${i.unit}</td>
          ${hasNote ? `<td style="font-size:12px;color:#718096;">${note || '-'}</td>` : ''}
        </tr>`;
      }).join('');
      const summaryRow = hasShortage
        ? `<tr style="background:#fff5f5;"><td colspan="${hasNote ? 6 : 5}" style="padding:8px 14px;">
            <span style="color:#c05621;font-weight:700;font-size:12px;">
              <i class="fa-solid fa-triangle-exclamation"></i>
              ยอดรวมสินค้าขาด: ${totalShortage} รายการ/หน่วย
            </span>
          </td></tr>`
        : '';
      return `<div style="margin-top:16px;">
        <b style="font-size:13px;">รายการสินค้า</b>
        <table style="margin-top:8px;">
          <thead><tr>
            <th>สินค้า</th><th style="text-align:center;">สั่ง</th>
            <th style="text-align:center;">รับแล้ว</th><th style="text-align:center;">ขาด</th>
            <th style="text-align:center;">หน่วย</th>
            ${hasNote ? '<th>หมายเหตุ</th>' : ''}
          </tr></thead>
          <tbody>${rows}${summaryRow}</tbody>
        </table>
      </div>`;
    })()}
    ${o.receive_record ? (() => {
      const rr = o.receive_record;
      const rrStatusTH = {received_all:'รับครบ', partial:'รับไม่ครบ', rejected:'ปฏิเสธรับ'}[rr.status] || rr.status;
      const photos = rr.photos || [];
      const photosHtml = photos.length > 0
        ? `<div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:8px;">
            ${photos.map(p => {
              const fname = p.photo_path.split('/').pop();
              return `<img src="/api/evidence/${fname}" alt="หลักฐาน"
                style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:2px solid #c6f6d5;cursor:pointer;"
                onclick="window.open(this.src,'_blank')">`;
            }).join('')}
          </div>`
        : `<div style="font-size:12px;color:#a0aec0;margin-top:6px;"><i class="fa-solid fa-image"></i> ไม่มีรูปหลักฐาน</div>`;
      const sigHtml = rr.signature
        ? `<div style="margin-top:10px;">
            <div style="font-size:12px;font-weight:600;color:#4a5568;margin-bottom:4px;">ลายเซ็นผู้รับสินค้า</div>
            <img src="${rr.signature}" alt="ลายเซ็น"
              style="max-width:280px;border:1.5px solid #e2e8f0;border-radius:6px;background:#fff;padding:4px;display:block;">
          </div>`
        : `<div style="margin-top:10px;font-size:12px;color:#a0aec0;"><i class="fa-solid fa-signature"></i> ไม่มีลายเซ็น</div>`;
      return `<div style="margin-top:16px;padding:12px;background:#f0fff4;border-radius:8px;border:1px solid #c6f6d5;">
        <b style="color:#276749;"><i class="fa-solid fa-circle-check"></i> บันทึกการรับสินค้า</b>
        <div style="margin-top:4px;font-size:12px;color:#2f855a;">สถานะ: <b>${rrStatusTH}</b>${rr.remark ? ` | หมายเหตุ: ${rr.remark}` : ''}</div>
        ${sigHtml}
        <div style="margin-top:8px;font-size:12px;font-weight:600;color:#4a5568;">รูปหลักฐาน</div>
        ${photosHtml}
      </div>`;
    })() : ''}
    ${boCardsHtml}`;
  const actions = document.getElementById('detail-actions');
  actions.innerHTML = '';
  if (o.status === 'pending') {
    const isDriver = currentUser?.role === 'driver';
    if (!isDriver) {
      actions.innerHTML = `
        <button class="btn btn-warning" onclick="quickStatus(${o.id},'in_transit',null);closeModal('modal-order-detail')"><i class="fa-solid fa-truck"></i> เริ่มส่ง</button>
        <button class="btn btn-danger" onclick="openCancelModal(${o.id})"><i class="fa-solid fa-xmark"></i> ยกเลิก</button>`;
    }
  } else if (o.status === 'in_transit') {
    actions.innerHTML = `<button class="btn btn-success" onclick="quickStatus(${o.id},'delivered');closeModal('modal-order-detail')"><i class="fa-solid fa-circle-check"></i> ส่งสำเร็จ</button>`;
  }
  document.getElementById('modal-order-detail').classList.add('open');
}

async function quickStatus(id, status, vehicleId) {
  const r = await api('PUT', '/api/delivery-orders/' + id, {status});
  if (r.ok) {
    showToast('อัปเดตสถานะเรียบร้อย', 'success');
    loadOrders();
    if (status === 'in_transit' && vehicleId) navigateToGps(vehicleId);
  } else {
    showToast(r.data.message || 'เกิดข้อผิดพลาด', 'error');
  }
}

function openCancelModal(id) {
  document.getElementById('cancel-order-id').value = id;
  document.getElementById('cancel-reason-select').value = '';
  document.getElementById('cancel-other-note').value = '';
  document.getElementById('cancel-other-wrap').style.display = 'none';
  closeModal('modal-order-detail');
  document.getElementById('modal-cancel-order').classList.add('open');
}

function onCancelReasonChange() {
  const val = document.getElementById('cancel-reason-select').value;
  document.getElementById('cancel-other-wrap').style.display = val === 'อื่นๆ' ? 'block' : 'none';
}

async function submitCancelOrder() {
  const id = document.getElementById('cancel-order-id').value;
  const reason = document.getElementById('cancel-reason-select').value;
  const note = document.getElementById('cancel-other-note').value.trim();

  if (!reason) { showToast('กรุณาเลือกเหตุผลการยกเลิก', 'error'); return; }
  if (reason === 'อื่นๆ' && !note) { showToast('กรุณาระบุหมายเหตุ', 'error'); return; }

  const cancel_reason = reason === 'อื่นๆ' ? note : reason;
  const r = await api('PATCH', `/api/delivery-orders/${id}/cancel`, {cancel_reason});
  if (r.ok) {
    closeModal('modal-cancel-order');
    showToast('ยกเลิก DO เรียบร้อย', 'success');
    loadOrders();
  } else {
    showToast(r.data.message || 'เกิดข้อผิดพลาด', 'error');
  }
}

function approveBO(id) {
  viewOrder(id);
}

async function approveBODetail(boId) {
  const driverId = document.getElementById('bo-driver-' + boId)?.value;
  if (!driverId) { showToast('กรุณาเลือกคนขับก่อน', 'error'); return; }

  const scheduled_date = document.getElementById('bo-date-' + boId)?.value;
  if (!scheduled_date) { showToast('กรุณาเลือกวันที่ส่งซ้ำ', 'error'); return; }

  const qtyInputs = [...document.querySelectorAll(`[id^="bo-qty-${boId}-"]`)];
  const items = qtyInputs.map(inp => ({
    id: parseInt(inp.id.split('-').pop()),
    qty_ordered: parseFloat(inp.value) || 0,
  }));

  const r = await api('POST', `/api/delivery-orders/${boId}/approve-backorder`, {
    driver_id: parseInt(driverId),
    scheduled_date,
    items,
  });
  if (r.ok) {
    closeModal('modal-order-detail');
    showToast('อนุมัติ BO เรียบร้อย — แจ้ง Driver และ Branch แล้ว', 'success');
    loadOrders();
    loadNotifications();
  } else {
    showToast(r.data.message || 'เกิดข้อผิดพลาด', 'error');
  }
}

function openRejectBO(id) {
  document.getElementById('rbo-id').value = id;
  document.getElementById('rbo-reason').value = '';
  document.getElementById('modal-reject-bo').classList.add('open');
}

async function submitRejectBO() {
  const id     = document.getElementById('rbo-id').value;
  const reason = document.getElementById('rbo-reason').value.trim();
  if (!reason) { showToast('กรุณาระบุเหตุผล', 'error'); return; }
  const r = await api('POST', '/api/delivery-orders/' + id + '/reject-backorder', { rejection_reason: reason });
  if (r.ok) {
    closeModal('modal-reject-bo');
    showToast('ปฏิเสธ Backorder แล้ว', 'success');
    loadOrders();
  } else {
    showToast(r.data.message || 'เกิดข้อผิดพลาด', 'error');
  }
}

async function openCreateOrder() {
  await Promise.all([
    loadBranchOptions('mo-branch'),
    loadVehicleOptions('mo-vehicle'),
    (async () => {
      if (allItemsCache.length === 0) {
        const r = await api('GET', '/api/items?active=1');
        if (r.ok) allItemsCache = r.data;
      }
    })(),
  ]);
  document.getElementById('mo-date').value = new Date().toISOString().split('T')[0];
  document.getElementById('mo-items-list').innerHTML = '';
  addItem();
  document.getElementById('modal-order').classList.add('open');
}

function buildItemOptions() {
  const byCategory = {};
  allItemsCache.filter(i => i.is_active).forEach(i => {
    const cat = i.category || 'ทั่วไป';
    (byCategory[cat] = byCategory[cat] || []).push(i);
  });
  let opts = '<option value="">-- เลือกสินค้า --</option>';
  Object.entries(byCategory).forEach(([cat, items]) => {
    opts += `<optgroup label="${cat}">`;
    items.forEach(i => { opts += `<option value="${i.id}" data-unit="${i.unit}">${i.name} (${i.unit})</option>`; });
    opts += '</optgroup>';
  });
  return opts;
}

function onItemSelectChange(sel) {
  const opt = sel.options[sel.selectedIndex];
  const unitSpan = sel.closest('div').querySelector('.mo-unit-label');
  if (unitSpan) unitSpan.textContent = opt.dataset.unit || '';
}

function addItem() {
  if (allItemsCache.length === 0) { showToast('ยังไม่มีสินค้าในระบบ กรุณาเพิ่มในหน้าจัดการสินค้าก่อน', 'error'); return; }
  const list = document.getElementById('mo-items-list');
  const div = document.createElement('div');
  div.style.cssText = 'display:grid;grid-template-columns:2fr 100px 60px auto;gap:8px;margin-bottom:8px;align-items:center;';
  div.innerHTML = `
    <select class="item-select" onchange="onItemSelectChange(this)"
            style="border:1.5px solid #e2e8f0;border-radius:6px;padding:7px 10px;font-size:13px;font-family:inherit;background:#fff;">
      ${buildItemOptions()}
    </select>
    <input type="number" placeholder="จำนวน" class="item-qty" min="0.01" step="any"
           style="border:1.5px solid #e2e8f0;border-radius:6px;padding:7px 10px;font-size:13px;font-family:inherit;">
    <span class="mo-unit-label" style="font-size:12px;color:#718096;text-align:center;"></span>
    <button onclick="this.parentElement.remove()" class="btn btn-danger btn-sm"><i class="fa-solid fa-xmark"></i></button>`;
  list.appendChild(div);
}

async function submitOrder() {
  const rows = [...document.querySelectorAll('#mo-items-list > div')];
  if (rows.length === 0) { showToast('กรุณาเพิ่มสินค้าอย่างน้อย 1 รายการ', 'error'); return; }

  const items = rows.map(div => ({
    item_id: parseInt(div.querySelector('.item-select').value),
    qty_ordered: parseFloat(div.querySelector('.item-qty').value),
  }));

  const invalid = items.find(i => !i.item_id || !i.qty_ordered || i.qty_ordered <= 0);
  if (invalid) { showToast('กรุณาเลือกสินค้าและระบุจำนวนให้ครบ', 'error'); return; }

  const body = {
    branch_id: parseInt(document.getElementById('mo-branch').value),
    vehicle_id: document.getElementById('mo-vehicle').value ? parseInt(document.getElementById('mo-vehicle').value) : null,
    scheduled_date: document.getElementById('mo-date').value,
    items,
  };
  const r = await api('POST', '/api/delivery-orders', body);
  if (r.ok) { closeModal('modal-order'); showToast('สร้างออเดอร์เรียบร้อย', 'success'); loadOrders(); }
  else showToast(JSON.stringify(r.data.errors || r.data.message), 'error');
}

/* ---- BRANCHES ---- */
async function loadBranches() {
  const r = await api('GET', '/api/branches');
  if (!r.ok) return;
  allBranches = r.data;
  document.getElementById('branch-count').textContent = `${r.data.length} สาขา`;
  document.getElementById('branches-tbody').innerHTML = r.data.length === 0
    ? `<tr><td colspan="6"><div class="empty-state"><i class="fa-solid fa-building"></i><p>ยังไม่มีสาขา</p></div></td></tr>`
    : r.data.map(b => `<tr>
        <td>${b.id}</td>
        <td><b>${b.name}</b></td>
        <td style="color:#718096;font-size:12px;">${b.address || '-'}</td>
        <td style="font-size:12px;color:#718096;">${b.lat ? `${b.lat}, ${b.lng}` : '-'}</td>
        <td><span class="badge ${b.is_active ? 'badge-delivered':'badge-cancelled'}">${b.is_active ? 'ใช้งาน':'ปิด'}</span></td>
        <td style="display:flex;gap:4px;">
          <button class="btn btn-primary btn-sm" onclick="openEditBranch(${b.id})"><i class="fa-solid fa-pen"></i> แก้ไข</button>
          <button class="btn btn-danger btn-sm" onclick="deleteBranch(${b.id})"><i class="fa-solid fa-trash"></i></button>
        </td>
      </tr>`).join('');
}

let mbMap = null;
let mbMarker = null;

function openCreateBranch() {
  ['mb-name','mb-address','mb-lat','mb-lng','mb-coords'].forEach(id => document.getElementById(id).value = '');
  document.getElementById('modal-branch').classList.add('open');
  setTimeout(() => {
    if (mbMap) { mbMap.remove(); mbMap = null; mbMarker = null; }
    mbMap = L.map('mb-map').setView([13.7563, 100.5018], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(mbMap);
    mbMap.on('click', async function(e) {
      const lat = e.latlng.lat.toFixed(6);
      const lng = e.latlng.lng.toFixed(6);
      document.getElementById('mb-lat').value = lat;
      document.getElementById('mb-lng').value = lng;
      document.getElementById('mb-coords').value = lat + ', ' + lng;
      if (mbMarker) mbMap.removeLayer(mbMarker);
      mbMarker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(mbMap);
      await reverseGeocodeToAddress('mb', parseFloat(lat), parseFloat(lng));
    });
  }, 100);
}

async function submitBranch() {
  const body = {
    name: document.getElementById('mb-name').value,
    address: document.getElementById('mb-address').value || null,
    lat: document.getElementById('mb-lat').value || null,
    lng: document.getElementById('mb-lng').value || null,
  };
  const r = await api('POST', '/api/branches', body);
  if (r.ok) { closeModal('modal-branch'); showToast('เพิ่มสาขาเรียบร้อย', 'success'); allBranches = []; loadBranches(); }
  else showToast(JSON.stringify(r.data.errors || r.data.message), 'error');
}

async function searchAddress(prefix) {
  const address = document.getElementById(prefix + '-address').value.trim();
  if (!address) { showToast('กรุณากรอกที่อยู่ก่อน', 'error'); return; }
  showToast('กำลังค้นหาตำแหน่ง...', 'success');
  try {
    const res = await fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(address));
    const data = await res.json();
    if (!data.length) { showToast('ไม่พบตำแหน่ง ลองคำค้นหาอื่น', 'error'); return; }
    const lat = parseFloat(data[0].lat);
    const lng = parseFloat(data[0].lon);
    document.getElementById(prefix + '-lat').value = lat.toFixed(6);
    document.getElementById(prefix + '-lng').value = lng.toFixed(6);
    document.getElementById(prefix + '-coords').value = lat.toFixed(6) + ', ' + lng.toFixed(6);
    const map = prefix === 'mb' ? mbMap : ebMap;
    if (map) {
      map.setView([lat, lng], 15);
      if (prefix === 'mb') {
        if (mbMarker) mbMap.removeLayer(mbMarker);
        mbMarker = L.marker([lat, lng]).addTo(mbMap);
      } else {
        if (ebMarker) ebMap.removeLayer(ebMarker);
        ebMarker = L.marker([lat, lng]).addTo(ebMap);
      }
    }
    showToast('พบตำแหน่งแล้ว', 'success');
  } catch (e) {
    showToast('เกิดข้อผิดพลาดในการค้นหา', 'error');
  }
}

async function reverseGeocodeToAddress(prefix, lat, lng) {
  try {
    const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
    const data = await res.json();
    if (data && data.display_name) {
      document.getElementById(prefix + '-address').value = data.display_name;
    }
  } catch (e) {}
}

async function pinFromCoords(prefix) {
  const val = document.getElementById(prefix + '-coords').value.trim();
  const parts = val.split(',');
  if (parts.length < 2) { showToast('รูปแบบพิกัดไม่ถูกต้อง เช่น 13.7563, 100.5018', 'error'); return; }
  const lat = parseFloat(parts[0].trim());
  const lng = parseFloat(parts[1].trim());
  if (isNaN(lat) || isNaN(lng)) { showToast('พิกัดไม่ถูกต้อง', 'error'); return; }
  document.getElementById(prefix + '-lat').value = lat.toFixed(6);
  document.getElementById(prefix + '-lng').value = lng.toFixed(6);
  const map = prefix === 'mb' ? mbMap : ebMap;
  if (map) {
    map.setView([lat, lng], 15);
    if (prefix === 'mb') {
      if (mbMarker) mbMap.removeLayer(mbMarker);
      mbMarker = L.marker([lat, lng]).addTo(mbMap);
    } else {
      if (ebMarker) ebMap.removeLayer(ebMarker);
      ebMarker = L.marker([lat, lng]).addTo(ebMap);
    }
  }
  showToast('ปักหมุดเรียบร้อย', 'success');
  await reverseGeocodeToAddress(prefix, lat, lng);
}

let ebMap = null;
let ebMarker = null;

function openEditBranch(id) {
  const branch = allBranches.find(b => b.id === id);
  if (!branch) return;
  document.getElementById('eb-id').value = branch.id;
  document.getElementById('eb-name').value = branch.name;
  document.getElementById('eb-address').value = branch.address || '';
  document.getElementById('eb-lat').value = branch.lat || '';
  document.getElementById('eb-lng').value = branch.lng || '';
  document.getElementById('eb-coords').value = (branch.lat && branch.lng)
    ? parseFloat(branch.lat).toFixed(6) + ', ' + parseFloat(branch.lng).toFixed(6)
    : '';
  document.getElementById('modal-edit-branch').classList.add('open');

  setTimeout(() => {
    if (ebMap) { ebMap.remove(); ebMap = null; ebMarker = null; }

    const centerLat = branch.lat ? parseFloat(branch.lat) : 13.7563;
    const centerLng = branch.lng ? parseFloat(branch.lng) : 100.5018;
    const zoom = branch.lat ? 14 : 10;

    ebMap = L.map('eb-map').setView([centerLat, centerLng], zoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(ebMap);

    if (branch.lat && branch.lng) {
      ebMarker = L.marker([parseFloat(branch.lat), parseFloat(branch.lng)]).addTo(ebMap);
    }

    ebMap.on('click', async function(e) {
      const lat = e.latlng.lat.toFixed(6);
      const lng = e.latlng.lng.toFixed(6);
      document.getElementById('eb-lat').value = lat;
      document.getElementById('eb-lng').value = lng;
      document.getElementById('eb-coords').value = lat + ', ' + lng;
      if (ebMarker) ebMap.removeLayer(ebMarker);
      ebMarker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(ebMap);
      await reverseGeocodeToAddress('eb', parseFloat(lat), parseFloat(lng));
    });
  }, 100);
}

async function submitEditBranch() {
  const id = document.getElementById('eb-id').value;
  const body = {
    name: document.getElementById('eb-name').value,
    address: document.getElementById('eb-address').value || null,
    lat: document.getElementById('eb-lat').value || null,
    lng: document.getElementById('eb-lng').value || null,
  };
  const r = await api('PUT', '/api/branches/' + id, body);
  if (r.ok) { closeModal('modal-edit-branch'); showToast('แก้ไขสาขาเรียบร้อย', 'success'); allBranches = []; loadBranches(); }
  else showToast(JSON.stringify(r.data.errors || r.data.message), 'error');
}

async function deleteBranch(id) {
  if (!confirm('ยืนยันลบสาขานี้? ข้อมูลจะถูกซ่อน (Soft Delete)')) return;
  const r = await api('DELETE', '/api/branches/' + id);
  if (r.ok) { showToast('ลบสาขาเรียบร้อย', 'success'); allBranches = []; loadBranches(); }
  else showToast(r.data.message || 'เกิดข้อผิดพลาด', 'error');
}

/* ---- VEHICLES ---- */
async function loadVehicles() {
  const r = await api('GET', '/api/vehicles');
  if (!r.ok) return;
  allVehicles = r.data;
  document.getElementById('vehicle-count').textContent = `${r.data.length} คัน`;
  document.getElementById('vehicles-tbody').innerHTML = r.data.length === 0
    ? `<tr><td colspan="5"><div class="empty-state"><i class="fa-solid fa-truck"></i><p>ยังไม่มียานพาหนะ</p></div></td></tr>`
    : r.data.map(v => `<tr>
        <td>${v.id}</td>
        <td><b>${v.license_plate}</b></td>
        <td>${v.driver?.name || '<span style="color:#a0aec0">-</span>'}</td>
        <td><span class="badge ${v.is_active ? 'badge-delivered':'badge-cancelled'}">${v.is_active ? 'ใช้งาน':'ปิด'}</span></td>
        <td style="display:flex;gap:4px;">
          <button class="btn btn-primary btn-sm" onclick="openEditVehicle(${v.id})"><i class="fa-solid fa-pen"></i> แก้ไข</button>
          <button class="btn btn-danger btn-sm" onclick="deleteVehicle(${v.id})"><i class="fa-solid fa-trash"></i></button>
        </td>
      </tr>`).join('');
}

async function openCreateVehicle() {
  await loadDriverOptions('mv-driver');
  document.getElementById('mv-plate').value = '';
  document.getElementById('modal-vehicle').classList.add('open');
}

async function submitVehicle() {
  const body = {
    license_plate: document.getElementById('mv-plate').value,
    driver_id: document.getElementById('mv-driver').value || null,
  };
  const r = await api('POST', '/api/vehicles', body);
  if (r.ok) { closeModal('modal-vehicle'); showToast('เพิ่มยานพาหนะเรียบร้อย', 'success'); allVehicles = []; loadVehicles(); }
  else showToast(JSON.stringify(r.data.errors || r.data.message), 'error');
}

async function openEditVehicle(id) {
  const vehicle = allVehicles.find(v => v.id === id);
  if (!vehicle) return;
  await loadDriverOptions('ev-driver');
  document.getElementById('ev-id').value = vehicle.id;
  document.getElementById('ev-plate').value = vehicle.license_plate;
  document.getElementById('ev-driver').value = vehicle.driver_id || '';
  document.getElementById('ev-active').value = vehicle.is_active ? '1' : '0';
  document.getElementById('modal-edit-vehicle').classList.add('open');
}

async function submitEditVehicle() {
  const id = document.getElementById('ev-id').value;
  const body = {
    license_plate: document.getElementById('ev-plate').value,
    driver_id: document.getElementById('ev-driver').value || null,
    is_active: document.getElementById('ev-active').value === '1',
  };
  const r = await api('PUT', '/api/vehicles/' + id, body);
  if (r.ok) { closeModal('modal-edit-vehicle'); showToast('แก้ไขยานพาหนะเรียบร้อย', 'success'); allVehicles = []; loadVehicles(); }
  else showToast(JSON.stringify(r.data.errors || r.data.message), 'error');
}

async function deleteVehicle(id) {
  if (!confirm('ยืนยันลบยานพาหนะนี้? ข้อมูลจะถูกซ่อน (Soft Delete)')) return;
  const r = await api('DELETE', '/api/vehicles/' + id);
  if (r.ok) { showToast('ลบยานพาหนะเรียบร้อย', 'success'); allVehicles = []; loadVehicles(); }
  else showToast(r.data.message || 'เกิดข้อผิดพลาด', 'error');
}

/* ---- GPS ---- */
function useMyLocation() {
  if (!navigator.geolocation) return showToast('Browser ไม่รองรับ GPS', 'error');
  navigator.geolocation.getCurrentPosition(p => {
    document.getElementById('gps-lat').value = p.coords.latitude.toFixed(6);
    document.getElementById('gps-lng').value = p.coords.longitude.toFixed(6);
    showToast('ได้รับพิกัดแล้ว', 'success');
  });
}

async function sendGps() {
  const vid = currentUser?.role === 'driver'
    ? driverVehicleId
    : parseInt(document.getElementById('gps-manual-vid').value);
  if (!vid) { showToast('ไม่พบรหัสยานพาหนะ', 'error'); return; }
  const body = {
    vehicle_id: vid,
    lat: parseFloat(document.getElementById('gps-lat').value),
    lng: parseFloat(document.getElementById('gps-lng').value),
  };
  const r = await api('POST', '/api/gps/update', body);
  const el = document.getElementById('gps-result');
  el.innerHTML = r.ok
    ? `<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> บันทึก GPS เรียบร้อย — ${body.lat}, ${body.lng}</div>`
    : `<div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> ${JSON.stringify(r.data.errors || r.data.message)}</div>`;
}

let pendingGpsVehicleId = null;

function navigateToGps(vehicleId) {
  pendingGpsVehicleId = vehicleId;
  navigate('gps');
}

let driverVehicleId = null;
let gpsMap = null;
let gpsMarkers = {};
let gpsMapInterval = null;
let watchId = null;
let isTracking = false;
let gpsTimerId = null;
let autoStartGps = false;
let routePolyline = null;
let routeMarkers = [];

async function initGpsPage() {
  if (gpsMapInterval) { clearInterval(gpsMapInterval); gpsMapInterval = null; }
  const isDriver = currentUser?.role === 'driver';
  document.getElementById('gps-send-card').style.display = isDriver ? 'block' : 'none';
  document.getElementById('gps-history-card').style.display = isDriver ? 'none' : 'block';
  document.getElementById('gps-map-card').style.display = isDriver ? 'none' : 'block';
  if (isDriver) {
    document.getElementById('gps-vid-section').style.display = 'none';
    document.getElementById('gps-vehicle-info').style.display = 'block';
    document.getElementById('gps-lat-group').style.display = 'none';
    document.getElementById('gps-lng-group').style.display = 'none';
    document.getElementById('gps-manual-btns').style.display = 'none';
    document.getElementById('gps-tracking-section').style.display = 'block';
    loadDriverVehicle();
  } else {
    const r = await api('GET', '/api/vehicles');
    if (r.ok && Array.isArray(r.data)) {
      const opts = r.data.map(v => `<option value="${v.id}">${v.license_plate}</option>`).join('');
      document.getElementById('gps-vid').innerHTML = '<option value="">-- เลือกรถ --</option>' + opts;
    }
    if (pendingGpsVehicleId) {
      document.getElementById('gps-vid').value = pendingGpsVehicleId;
      pendingGpsVehicleId = null;
      onVehicleSelect(document.getElementById('gps-vid').value);
    }
    setTimeout(loadGpsMap, 200);
    gpsMapInterval = setInterval(loadGpsMap, 10000);
  }
}

async function loadDriverVehicle() {
  const r = await api('GET', '/api/vehicles');
  const display = document.getElementById('gps-vehicle-display');
  if (!r.ok) { display.textContent = 'โหลดข้อมูลไม่สำเร็จ'; return; }
  const vehicle = r.data.find(v => v.driver_id === currentUser?.id);
  if (vehicle) {
    driverVehicleId = vehicle.id;
    display.textContent = vehicle.license_plate;
    display.style.color = '#22543d';
    display.style.background = '#f0fff4';
    display.style.borderColor = '#c6f6d5';
    if (autoStartGps && !isTracking) {
      autoStartGps = false;
      startTracking();
    }
  } else {
    driverVehicleId = null;
    display.textContent = 'ไม่พบรถที่ผูกกับบัญชีนี้';
    display.style.color = '#c53030';
    display.style.background = '#fff5f5';
    display.style.borderColor = '#fed7d7';
    autoStartGps = false;
  }
}

function haversineDistance(lat1, lng1, lat2, lng2) {
  const R = 6371000;
  const dLat = (lat2 - lat1) * Math.PI / 180;
  const dLng = (lng2 - lng1) * Math.PI / 180;
  const a = Math.sin(dLat/2) ** 2
    + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLng/2) ** 2;
  return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}

async function startTracking() {
  if (!driverVehicleId) { showToast('ไม่พบรถที่ผูกกับบัญชีนี้ — กรุณาล็อกอินใหม่', 'error'); return; }
  if (!navigator.geolocation) { showToast('Browser ไม่รองรับ GPS', 'error'); return; }
  if (location.protocol !== 'https:' && location.hostname !== 'localhost') {
    showToast('GPS ต้องการ HTTPS — กรุณาเข้าใช้งานผ่านลิงก์ HTTPS', 'error');
    document.getElementById('gps-result').innerHTML =
      '<div class="alert alert-error"><i class="fa-solid fa-lock"></i> GPS ต้องการการเชื่อมต่อแบบ HTTPS เท่านั้น<br>' +
      '<small>กรุณาเข้าใช้งานผ่านลิงก์ https:// แทน http://</small></div>';
    return;
  }
  isTracking = true;
  let currentLat = null, currentLng = null;
  let lastSentLat = null, lastSentLng = null;
  document.getElementById('btn-start-tracking').style.display = 'none';
  document.getElementById('btn-stop-tracking').style.display = 'inline-flex';
  document.getElementById('tracking-status').innerHTML = '<span style="color:#d69e2e;font-weight:600;">&#9679; กำลังรอสัญญาณ GPS...</span>';

  const doSendGps = async (lat, lng) => {
    const r = await api('POST', '/api/gps/update', { lat, lng });
    if (r.ok) {
      document.getElementById('tracking-status').innerHTML =
        '<span style="color:#276749;font-weight:600;">&#9679; กำลังติดตาม</span>' +
        `<span style="font-size:11px;color:#718096;margin-left:8px;">${lat.toFixed(5)}, ${lng.toFixed(5)} — ${new Date().toLocaleTimeString('th-TH')}</span>`;
    }
  };

  watchId = navigator.geolocation.watchPosition(
    async (pos) => {
      if (!isTracking) return;
      currentLat = pos.coords.latitude;
      currentLng = pos.coords.longitude;
      if (lastSentLat !== null && haversineDistance(lastSentLat, lastSentLng, currentLat, currentLng) < 50) return;
      lastSentLat = currentLat; lastSentLng = currentLng;
      await doSendGps(currentLat, currentLng);
    },
    (err) => {
      const msgs = {
        1: 'GPS ถูกบล็อก — กรุณากด "อนุญาต" ให้ Browser เข้าถึงตำแหน่ง',
        2: 'ไม่สามารถระบุตำแหน่งได้ — กรุณาเปิด GPS บนอุปกรณ์',
        3: 'หมดเวลาดึงสัญญาณ GPS — กรุณาลองใหม่ในที่โล่ง',
      };
      const msg = msgs[err.code] || 'ดึง GPS ไม่ได้ (รหัสข้อผิดพลาด ' + err.code + ')';
      showToast(msg, 'error');
      document.getElementById('gps-result').innerHTML =
        `<div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> ${msg}</div>`;
      stopTracking();
    },
    { enableHighAccuracy: true, maximumAge: 0, timeout: 15000 }
  );

  // Timer-based fallback: send every 30s regardless of movement distance
  gpsTimerId = setInterval(async () => {
    if (!isTracking || currentLat === null) return;
    await doSendGps(currentLat, currentLng);
  }, 30000);

  showToast('เริ่มติดตามตำแหน่งแล้ว', 'success');
}

function stopTracking() {
  isTracking = false;
  if (watchId !== null) { navigator.geolocation.clearWatch(watchId); watchId = null; }
  if (gpsTimerId !== null) { clearInterval(gpsTimerId); gpsTimerId = null; }
  document.getElementById('btn-start-tracking').style.display = 'inline-flex';
  document.getElementById('btn-stop-tracking').style.display = 'none';
  document.getElementById('tracking-status').textContent = 'หยุดติดตามแล้ว';
  showToast('หยุดติดตามตำแหน่งแล้ว', 'success');
}

async function loadGpsMap() {
  const mapEl = document.getElementById('gps-map');
  if (!mapEl || mapEl.offsetParent === null) return;
  const r = await api('GET', '/api/vehicles');
  if (!r.ok || !Array.isArray(r.data)) return;

  if (!gpsMap) {
    gpsMap = L.map('gps-map').setView([13.7563, 100.5018], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(gpsMap);
  }

  for (const v of r.data) {
    const gr = await api('GET', '/api/gps/' + v.id);
    if (!gr.ok || !gr.data.length) continue;
    const latest = gr.data[0];
    const lat = parseFloat(latest.lat);
    const lng = parseFloat(latest.lng);
    const time = formatDateTime(latest.recorded_at);
    const popup = `<b>${v.license_plate}</b><br><small>อัปเดต: ${time}</small>`;
    if (gpsMarkers[v.id]) {
      gpsMarkers[v.id].setLatLng([lat, lng]).setPopupContent(popup);
    } else {
      gpsMarkers[v.id] = L.marker([lat, lng]).addTo(gpsMap).bindPopup(popup);
    }
  }
}

async function loadVehicleRoute(vehicleId) {
  if (!gpsMap) return;
  if (routePolyline) { gpsMap.removeLayer(routePolyline); routePolyline = null; }
  routeMarkers.forEach(m => gpsMap.removeLayer(m));
  routeMarkers = [];
  if (!vehicleId) return;

  const r = await api('GET', '/api/gps/' + vehicleId);
  if (!r.ok || r.data.length < 2) return;

  // API returns newest-first → reverse for chronological order
  const points = r.data.slice().reverse().map(g => [parseFloat(g.lat), parseFloat(g.lng)]);

  routePolyline = L.polyline(points, { color: '#4299e1', weight: 4, opacity: 0.8 }).addTo(gpsMap);
  gpsMap.fitBounds(routePolyline.getBounds(), { padding: [30, 30] });

  const dot = (color) => L.divIcon({
    html: `<div style="width:14px;height:14px;border-radius:50%;background:${color};border:2px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,.4);"></div>`,
    iconSize: [14, 14], iconAnchor: [7, 7], className: ''
  });

  routeMarkers.push(
    L.marker(points[0], { icon: dot('#38a169') }).addTo(gpsMap).bindPopup('จุดออกจากคลัง')
  );
  const lastLog = r.data[0];
  const time = formatDateTime(lastLog.recorded_at);
  routeMarkers.push(
    L.marker(points[points.length - 1], { icon: dot('#e53e3e') })
      .addTo(gpsMap).bindPopup(`<b>ตำแหน่งล่าสุด</b><br><small>${time}</small>`).openPopup()
  );

  const dr = await api('GET', '/api/gps/distance/' + vehicleId);
  if (dr.ok && dr.data.branch_lat && dr.data.branch_lng) {
    const branchIcon = L.divIcon({
      html: `<div style="background:#2b6cb0;color:#fff;padding:3px 7px;border-radius:6px;font-size:11px;font-weight:700;white-space:nowrap;box-shadow:0 1px 4px rgba(0,0,0,.3);"><i class="fa-solid fa-building"></i> ${dr.data.branch_name}</div>`,
      iconAnchor: [30, 12], className: ''
    });
    routeMarkers.push(
      L.marker([dr.data.branch_lat, dr.data.branch_lng], { icon: branchIcon })
        .addTo(gpsMap)
        .bindPopup(`<b>${dr.data.branch_name}</b><br>สาขาปลายทาง<br><span style="color:#2b6cb0;font-weight:600;">อีก ${dr.data.distance_km} กม.</span> &nbsp; <span style="color:#276749;">~${dr.data.eta_minutes} นาที</span>`)
    );
  }
}

async function reverseGeocode(lat, lng) {
  try {
    const res = await fetch(
      `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`,
      { headers: { 'Accept-Language': 'th' } }
    );
    const d = await res.json();
    return d.display_name || `${lat}, ${lng}`;
  } catch {
    return `${lat}, ${lng}`;
  }
}

function onVehicleSelect(vid) {
  loadVehicleRoute(vid);
  loadGpsHistory(vid);
}

async function loadGpsHistory(vid) {
  if (!vid) return;
  const r = await api('GET', '/api/gps/' + vid);
  const tbody = document.getElementById('gps-tbody');
  if (!r.ok || !r.data.length) {
    tbody.innerHTML = `<tr><td colspan="3" style="text-align:center;color:#a0aec0;padding:24px;">ไม่มีข้อมูล GPS</td></tr>`;
    return;
  }
  tbody.innerHTML = `<tr><td colspan="3" style="text-align:center;color:#a0aec0;padding:12px;"><i class="fa-solid fa-spinner fa-spin"></i> กำลังโหลดตำแหน่ง...</td></tr>`;
  const records = r.data.slice(0, 10);
  const rows = [];
  for (const g of records) {
    const place = await reverseGeocode(g.lat, g.lng);
    rows.push(`<tr>
      <td style="font-size:12px;">${formatDateTime(g.recorded_at)}</td>
      <td style="font-size:12px;">${place}</td>
      <td style="font-size:12px;color:#718096;">${g.delivery_order?.do_number || '-'}</td>
    </tr>`);
  }
  tbody.innerHTML = rows.join('');
}

/* ---- SIGNATURE PAD ---- */
let recvSigCanvas = null;
let recvSigCtx = null;
let recvSigDrawing = false;
let recvSigHasContent = false;

function initReceiveSignaturePad() {
  recvSigCanvas = document.getElementById('recv-sig-canvas');
  if (!recvSigCanvas) return;
  recvSigCtx = recvSigCanvas.getContext('2d');
  recvSigCtx.clearRect(0, 0, recvSigCanvas.width, recvSigCanvas.height);
  recvSigCtx.strokeStyle = '#1e3a5f';
  recvSigCtx.lineWidth = 2;
  recvSigCtx.lineCap = 'round';
  recvSigCtx.lineJoin = 'round';
  recvSigHasContent = false;
  document.getElementById('recv-sig-hint').style.display = 'flex';

  const pos = (e) => {
    const rect = recvSigCanvas.getBoundingClientRect();
    const sx = recvSigCanvas.width / rect.width;
    const sy = recvSigCanvas.height / rect.height;
    const src = e.touches ? e.touches[0] : e;
    return { x: (src.clientX - rect.left) * sx, y: (src.clientY - rect.top) * sy };
  };

  recvSigCanvas.onmousedown = (e) => {
    recvSigDrawing = true;
    const p = pos(e); recvSigCtx.beginPath(); recvSigCtx.moveTo(p.x, p.y);
  };
  recvSigCanvas.onmousemove = (e) => {
    if (!recvSigDrawing) return;
    const p = pos(e); recvSigCtx.lineTo(p.x, p.y); recvSigCtx.stroke();
    recvSigHasContent = true;
    document.getElementById('recv-sig-hint').style.display = 'none';
  };
  recvSigCanvas.onmouseup = recvSigCanvas.onmouseleave = () => { recvSigDrawing = false; };

  recvSigCanvas.ontouchstart = (e) => {
    e.preventDefault(); recvSigDrawing = true;
    const p = pos(e); recvSigCtx.beginPath(); recvSigCtx.moveTo(p.x, p.y);
  };
  recvSigCanvas.ontouchmove = (e) => {
    e.preventDefault(); if (!recvSigDrawing) return;
    const p = pos(e); recvSigCtx.lineTo(p.x, p.y); recvSigCtx.stroke();
    recvSigHasContent = true;
    document.getElementById('recv-sig-hint').style.display = 'none';
  };
  recvSigCanvas.ontouchend = () => { recvSigDrawing = false; };
}

function clearReceiveSignature() {
  if (!recvSigCtx) return;
  recvSigCtx.clearRect(0, 0, recvSigCanvas.width, recvSigCanvas.height);
  recvSigHasContent = false;
  document.getElementById('recv-sig-hint').style.display = 'flex';
}

function getReceiveSignature() {
  if (!recvSigCanvas || !recvSigHasContent) return null;
  return recvSigCanvas.toDataURL('image/png');
}

/* ---- RECEIVE ---- */
let receiveInterval = null;

async function loadReceive() {
  if (receiveInterval) { clearInterval(receiveInterval); receiveInterval = null; }
  const tbody = document.getElementById('receive-tbody');
  const isAdmin = currentUser?.role === 'admin';
  const colspan = isAdmin ? 8 : 7;
  document.getElementById('recv-th-branch').style.display = isAdmin ? '' : 'none';

  const r = await api('GET', '/api/branch/delivery-orders');
  if (!r.ok) {
    tbody.innerHTML = `<tr><td colspan="${colspan}" style="text-align:center;color:#e53e3e;padding:24px;">โหลดข้อมูลไม่สำเร็จ</td></tr>`;
    return;
  }
  const orders = r.data.data || r.data || [];
  receiveOrdersCache = orders;
  if (!orders.length) {
    tbody.innerHTML = `<tr><td colspan="${colspan}"><div class="empty-state"><i class="fa-solid fa-inbox"></i><p>ไม่มีรายการรับสินค้าวันนี้</p></div></td></tr>`;
    return;
  }
  tbody.innerHTML = orders.map(o => `<tr>
    <td><b style="color:#2a5298">${o.do_number}</b></td>
    ${isAdmin ? `<td>${o.branch?.name || '<span style="color:#a0aec0">-</span>'}</td>` : ''}
    <td>${o.vehicle?.license_plate || '<span style="color:#a0aec0">-</span>'}</td>
    <td>${(o.items||[]).length} รายการ</td>
    <td>${formatDate(o.scheduled_date)}</td>
    <td id="recv-dist-${o.id}" style="font-size:12px;">${o.vehicle?.id ? '<span style="color:#a0aec0;">...</span>' : '-'}</td>
    <td id="recv-eta-${o.id}" style="font-size:12px;">${o.vehicle?.id ? '<span style="color:#a0aec0;">...</span>' : '-'}</td>
    <td>
      <button class="btn btn-success btn-sm" onclick="openReceiveModal(${o.id})"><i class="fa-solid fa-circle-check"></i> รับสินค้า</button>
    </td>
  </tr>`).join('');

  for (const o of orders) {
    if (o.status !== 'in_transit' || !o.vehicle?.id) continue;
    const dr = await api('GET', '/api/gps/distance/' + o.vehicle.id);
    const distEl = document.getElementById('recv-dist-' + o.id);
    const etaEl  = document.getElementById('recv-eta-'  + o.id);
    if (!distEl) continue;
    if (dr.ok) {
      distEl.innerHTML = `<span style="font-weight:600;color:#2a5298;">อีก ${dr.data.distance_km} กม.</span>`;
      etaEl.innerHTML  = `<span style="color:#276749;">~${dr.data.eta_minutes} นาที</span>`;
    } else {
      distEl.textContent = '-'; etaEl.textContent = '-';
    }
  }

  receiveInterval = setInterval(loadReceive, 30000);
}

let receiveOrderId = null;
let receivePhotos = [];
let receiveOrdersCache = [];

async function openReceiveModal(id) {
  receiveOrderId = id;
  receivePhotos = [];
  document.getElementById('recv-photo-preview').innerHTML = '';
  document.getElementById('recv-photo-input').value = '';

  const o = receiveOrdersCache.find(x => x.id === id);
  if (!o) { showToast('ไม่พบข้อมูลออเดอร์ กรุณาโหลดหน้าใหม่', 'error'); return; }

  document.getElementById('recv-modal-title').textContent = 'รับสินค้า — ' + o.do_number;
  const reasonOptions = [
    '', 'เสียหาย/แตก', 'ของไม่ครบตั้งแต่ต้น', 'ของผิดรายการ', 'หมดอายุ', 'อื่นๆ',
  ].map(v => `<option value="${v}">${v === '' ? '— ไม่มี —' : v}</option>`).join('');

  document.getElementById('recv-items-tbody').innerHTML = (o.items || []).map(i => `
    <tr>
      <td>${i.product_name}</td>
      <td style="text-align:center;white-space:nowrap;">${i.qty_ordered} ${i.unit}</td>
      <td style="text-align:center;">
        <input type="number" class="recv-qty" data-id="${i.id}" data-ordered="${i.qty_ordered}"
               value="${i.qty_ordered}" min="0" step="any"
               style="width:80px;border:1.5px solid #e2e8f0;border-radius:6px;padding:6px 8px;font-size:13px;text-align:center;font-family:inherit;">
      </td>
      <td>
        <select class="recv-reason" onchange="onReasonChange(this)"
                style="width:100%;border:1.5px solid #e2e8f0;border-radius:6px;padding:6px 8px;font-size:13px;font-family:inherit;background:#fff;">
          ${reasonOptions}
        </select>
        <input type="text" class="recv-reason-other" placeholder="ระบุเหตุผล *"
               style="display:none;width:100%;margin-top:6px;border:1.5px solid #f6ad55;border-radius:6px;padding:6px 8px;font-size:13px;font-family:inherit;">
      </td>
    </tr>
  `).join('');

  document.getElementById('modal-receive').classList.add('open');
  setTimeout(initReceiveSignaturePad, 50);
}

function onReasonChange(sel) {
  const other = sel.closest('td').querySelector('.recv-reason-other');
  other.style.display = sel.value === 'อื่นๆ' ? 'block' : 'none';
  if (sel.value !== 'อื่นๆ') other.value = '';
}

function addReceivePhoto(input) {
  const preview = document.getElementById('recv-photo-preview');
  for (const file of input.files) {
    const idx = receivePhotos.length;
    receivePhotos.push(file);
    const reader = new FileReader();
    reader.onload = e => {
      const div = document.createElement('div');
      div.style.cssText = 'position:relative;display:inline-block;';
      div.dataset.idx = idx;
      div.innerHTML = `
        <img src="${e.target.result}" style="width:72px;height:72px;object-fit:cover;border-radius:8px;border:2px solid #e2e8f0;display:block;">
        <button onclick="removeReceivePhoto(this,${idx})" style="position:absolute;top:-6px;right:-6px;background:#e53e3e;color:#fff;border:none;border-radius:50%;width:18px;height:18px;font-size:10px;cursor:pointer;line-height:1;">✕</button>`;
      preview.appendChild(div);
    };
    reader.readAsDataURL(file);
  }
  input.value = '';
}

function removeReceivePhoto(btn, idx) {
  receivePhotos[idx] = null;
  btn.parentElement.remove();
}

async function submitReceive() {
  if (!receiveOrderId) return;

  const rows = [...document.querySelectorAll('#recv-items-tbody tr')];
  const items = rows.map(row => {
    const qtyInput  = row.querySelector('.recv-qty');
    const reasonSel = row.querySelector('.recv-reason');
    const reasonOther = row.querySelector('.recv-reason-other');
    const shortage_reason = reasonSel.value === 'อื่นๆ'
      ? (reasonOther.value.trim() || null)
      : (reasonSel.value || null);
    return {
      id: parseInt(qtyInput.dataset.id),
      qty_received: parseFloat(qtyInput.value) || 0,
      qty_ordered: parseFloat(qtyInput.dataset.ordered),
      shortage_reason,
    };
  });

  const hasInvalidOther = items.some((item, idx) => {
    const row = rows[idx];
    const sel = row.querySelector('.recv-reason');
    return sel.value === 'อื่นๆ' && !item.shortage_reason;
  });
  if (hasInvalidOther) { showToast('กรุณาระบุเหตุผลในช่อง "อื่นๆ"', 'error'); return; }

  const allFull = items.every(i => i.qty_received >= i.qty_ordered);
  const payload = items.map(({ id, qty_received, shortage_reason }) => ({ id, qty_received, shortage_reason }));
  const signature = getReceiveSignature();

  const r = await api('POST', '/api/receive/' + receiveOrderId, { items: payload, signature });
  if (!r.ok) { showToast(r.data.message || 'เกิดข้อผิดพลาด', 'error'); return; }

  const validPhotos = receivePhotos.filter(p => p !== null);
  if (validPhotos.length > 0) {
    const fd = new FormData();
    validPhotos.forEach(p => fd.append('photos[]', p));
    try {
      const photoRes = await fetch(BASE + '/api/receive/' + receiveOrderId + '/photos', {
        method: 'POST',
        headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' },
        body: fd,
      });
      if (!photoRes.ok) {
        const errData = await photoRes.json().catch(() => ({}));
        showToast('บันทึกสำเร็จ แต่อัปโหลดรูปไม่ได้: ' + (errData.message || 'ขนาดไฟล์ใหญ่เกินไป'), 'error');
        closeModal('modal-receive');
        loadReceive();
        return;
      }
    } catch (e) {
      showToast('บันทึกสำเร็จ แต่อัปโหลดรูปไม่ได้: ตรวจสอบการเชื่อมต่อ', 'error');
      closeModal('modal-receive');
      loadReceive();
      return;
    }
  }

  closeModal('modal-receive');
  showToast(allFull ? 'รับสินค้าครบแล้ว' : 'บันทึกรับสินค้าบางส่วนแล้ว', 'success');
  loadReceive();
}

/* ---- REPORTS ---- */
let rpTrendChart = null;
let rpStatsCache = null;

function onReportModeChange() {
  const mode = document.getElementById('rp-mode').value;
  const wraps = ['month', 'quarter', 'year', 'daterange'];
  wraps.forEach(w => {
    const el = document.getElementById('rp-wrap-' + w);
    if (el) el.style.display = (w === mode) ? 'flex' : 'none';
  });
}

function buildReportParams() {
  const mode = document.getElementById('rp-mode').value;
  const p = new URLSearchParams();
  if (mode === 'month') {
    p.set('year',  document.getElementById('rp-year-m').value);
    p.set('month', document.getElementById('rp-month-sel').value);
  } else if (mode === 'quarter') {
    p.set('year',    document.getElementById('rp-year-q').value);
    p.set('quarter', document.getElementById('rp-quarter-sel').value);
  } else if (mode === 'year') {
    p.set('year', document.getElementById('rp-year-y').value);
  } else {
    const from = document.getElementById('rp-from').value;
    const to   = document.getElementById('rp-to').value;
    if (!from || !to) { showToast('กรุณาระบุวันเริ่มต้นและสิ้นสุด', 'error'); return null; }
    p.set('from', from); p.set('to', to);
  }
  return p.toString();
}

function fmtMin(min) {
  if (min == null) return 'ไม่มีข้อมูล';
  if (min < 60) return `${min} นาที`;
  const h = Math.floor(min / 60), m = min % 60;
  return m > 0 ? `${h} ชม. ${m} น.` : `${h} ชม.`;
}

function fmtHour(h) {
  if (h == null) return 'ไม่มีข้อมูล';
  const suffix = h < 12 ? 'น.' : (h < 18 ? 'บ่าย' : 'เย็น/ค่ำ');
  const display = h === 0 ? 'เที่ยงคืน' : (h === 12 ? 'เที่ยง' : `${h}:00 ${suffix}`);
  return display;
}

function onProblemFilterChange() {
  if (!rpStatsCache) return;
  const c = rpStatsCache.cards;
  const filter = document.getElementById('rp-problem-filter').value;
  let count = c.problem;
  let sub = `รับไม่ครบ ${c.problem_incomplete} | ปฏิเสธ ${c.problem_rejected}`;
  if (filter === 'incomplete') { count = c.problem_incomplete; sub = `จาก DO มีปัญหาทั้งหมด ${c.problem} ครั้ง`; }
  if (filter === 'rejected')   { count = c.problem_rejected;   sub = `จาก DO มีปัญหาทั้งหมด ${c.problem} ครั้ง`; }
  document.getElementById('rp-c-problem').textContent = count;
  document.getElementById('rp-problem-sub').textContent = sub;
}

async function loadDeliveryStats() {
  const params = buildReportParams();
  if (params === null) return;

  const r = await api('GET', '/api/reports/delivery-stats?' + params);
  if (!r.ok) { showToast('เกิดข้อผิดพลาดในการโหลดรายงาน', 'error'); return; }
  const d = r.data;
  rpStatsCache = d;

  // Show all sections
  ['rp-cards','rp-trend-card','rp-timing-card'].forEach(id => {
    document.getElementById(id).style.display = '';
  });
  document.getElementById('rp-items-section').style.display = 'grid';

  // ─── Section 1: Cards ─────────────────────────────────────────
  const c = d.cards;
  document.getElementById('rp-c-completed').textContent = c.completed;
  document.getElementById('rp-c-bo').textContent = c.bo_count;
  document.getElementById('rp-c-rate').textContent = c.success_rate + '%';
  document.getElementById('rp-rate-bar').style.width = c.success_rate + '%';
  document.getElementById('rp-c-rate-sub').textContent =
    `มีปัญหา ${c.problem_rate}% | รวม ${c.total} ครั้ง`;
  document.getElementById('rp-problem-filter').value = 'all';
  onProblemFilterChange();

  // ─── Section 2: Trend chart ───────────────────────────────────
  const trend = d.trend;
  document.getElementById('rp-trend-year').textContent = `(ปี ${d.filter.year})`;
  const completedBg = trend.map(t => t.highlight ? '#276749' : '#48bb78');
  const problemBg   = trend.map(t => t.highlight ? '#c05621' : '#ed8936');
  const borderWidth = trend.map(t => t.highlight ? 3 : 0);
  const borderColor = trend.map(t => t.highlight ? '#1a202c' : 'transparent');

  if (rpTrendChart) rpTrendChart.destroy();
  rpTrendChart = new Chart(document.getElementById('rp-trend-chart'), {
    type: 'bar',
    data: {
      labels: trend.map(t => t.label),
      datasets: [
        {
          label: 'เสร็จสิ้น',
          data: trend.map(t => t.completed),
          backgroundColor: completedBg,
          borderColor,
          borderWidth,
          borderRadius: 4,
        },
        {
          label: 'มีปัญหา',
          data: trend.map(t => t.problem),
          backgroundColor: problemBg,
          borderColor,
          borderWidth,
          borderRadius: 4,
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            title: ctx => `${ctx[0].label} ${d.filter.year}`,
          },
        },
      },
      scales: {
        x: { stacked: false, grid: { display: false } },
        y: { beginAtZero: true, ticks: { stepSize: 1 } },
      },
    },
  });

  // ─── Section 3: Item tables ───────────────────────────────────
  const rankColors = ['#d69e2e', '#718096', '#c05621'];
  const itemRow = (item, i) => `<tr>
    <td style="text-align:center;font-weight:700;color:${rankColors[i]||'#718096'}">${i+1}</td>
    <td><b>${item.product_name}</b></td>
    <td style="text-align:center;font-weight:700;">${item.count}</td>
    <td style="font-size:12px;color:#718096;">${item.top_reason || '-'}</td>
  </tr>`;

  const shortageRows = d.top_shortage_items.length
    ? d.top_shortage_items.map(itemRow).join('')
    : '<tr><td colspan="4" style="text-align:center;color:#a0aec0;">ไม่มีข้อมูล</td></tr>';

  const rejectedRows = d.top_rejected_items.length
    ? d.top_rejected_items.map(itemRow).join('')
    : '<tr><td colspan="4" style="text-align:center;color:#a0aec0;">ไม่มีข้อมูล</td></tr>';

  document.getElementById('rp-shortage-tbody').innerHTML = shortageRows;
  document.getElementById('rp-rejected-tbody').innerHTML = rejectedRows;

  // ─── Section 4: Timing ────────────────────────────────────────
  const t = d.timing;
  document.getElementById('rp-t-prep').textContent    = fmtMin(t.avg_prep_min);
  document.getElementById('rp-t-drive').textContent   = fmtMin(t.avg_drive_min);
  document.getElementById('rp-t-receive').textContent = fmtMin(t.avg_receive_min);
  document.getElementById('rp-t-hour').textContent    = fmtHour(t.fastest_hour);
}

/* ---- USERS ---- */
async function loadUsers() {
  const r = await api('GET', '/api/users');
  if (!r.ok) return;
  document.getElementById('user-count').textContent = `${r.data.length} ผู้ใช้`;
  document.getElementById('users-tbody').innerHTML = r.data.map(u => `<tr>
    <td>${u.id}</td>
    <td><b>${u.name}</b></td>
    <td><span class="badge badge-${u.role}">${roleTH(u.role)}</span></td>
    <td>${u.branch?.name || '-'}</td>
    <td><span class="badge ${u.is_active ? 'badge-delivered':'badge-cancelled'}">${u.is_active ? 'ใช้งาน':'ปิด'}</span></td>
    <td style="display:flex;gap:4px;">
      <button class="btn btn-warning btn-sm" onclick="toggleUser(${u.id},${!u.is_active})">${u.is_active ? 'ปิด':'เปิด'}</button>
      <button class="btn btn-primary btn-sm" onclick="openEditUser(${u.id})"><i class="fa-solid fa-pen"></i> แก้ไข</button>
      ${u.id !== currentUser?.id ? `<button class="btn btn-danger btn-sm" onclick="deleteUser(${u.id})"><i class="fa-solid fa-trash"></i></button>` : ''}
    </td>
  </tr>`).join('');
}

async function toggleUser(id, active) {
  const r = await api('PUT', '/api/users/' + id, {is_active: active});
  if (r.ok) { showToast('อัปเดตผู้ใช้แล้ว', 'success'); loadUsers(); }
}

async function deleteUser(id) {
  if (!confirm('ยืนยันลบผู้ใช้นี้?')) return;
  const r = await api('DELETE', '/api/users/' + id);
  if (r.ok) { showToast('ลบผู้ใช้แล้ว', 'success'); loadUsers(); }
  else showToast(r.data.message, 'error');
}

function toggleBranchField(prefix) {
  const role = document.getElementById(prefix + '-role').value;
  document.getElementById(prefix + '-branch-group').style.display = role === 'branch' ? 'block' : 'none';
}

async function openCreateUser() {
  await loadBranchOptions('mu-branch');
  ['mu-name','mu-empcode','mu-pass'].forEach(id => document.getElementById(id).value = '');
  document.getElementById('mu-role').value = 'branch';
  toggleBranchField('mu');
  document.getElementById('modal-user').classList.add('open');
}

function buildAutoEmail(empCode, name) {
  if (empCode) return empCode.toLowerCase() + '@bh-logistic.com';
  return name.toLowerCase().replace(/\s+/g, '') + '_' + Date.now() + '@bh-logistic.com';
}

async function submitUser() {
  const empCode = document.getElementById('mu-empcode').value.trim();
  const name = document.getElementById('mu-name').value.trim();
  const body = {
    name,
    email: buildAutoEmail(empCode, name),
    password: document.getElementById('mu-pass').value,
    role: document.getElementById('mu-role').value,
    branch_id: document.getElementById('mu-branch').value || null,
    employee_code: empCode || null,
  };
  const r = await api('POST', '/api/users', body);
  if (r.ok) { closeModal('modal-user'); showToast('สร้างผู้ใช้เรียบร้อย', 'success'); loadUsers(); }
  else showToast(JSON.stringify(r.data.errors || r.data.message), 'error');
}

async function openEditUser(id) {
  const r = await api('GET', '/api/users');
  if (!r.ok) return;
  const user = r.data.find(u => u.id === id);
  if (!user) return;
  await loadBranchOptions('eu-branch');
  document.getElementById('eu-id').value = user.id;
  document.getElementById('eu-name').value = user.name;
  document.getElementById('eu-role').value = user.role;
  document.getElementById('eu-branch').value = user.branch_id || '';
  document.getElementById('eu-empcode').value = user.employee_code || '';
  toggleBranchField('eu');
  document.getElementById('modal-edit-user').classList.add('open');
}

async function submitEditUser() {
  const id = document.getElementById('eu-id').value;
  const empCode = document.getElementById('eu-empcode').value.trim();
  const name = document.getElementById('eu-name').value.trim();
  const body = {
    name,
    email: buildAutoEmail(empCode, name),
    role: document.getElementById('eu-role').value,
    branch_id: document.getElementById('eu-branch').value || null,
    employee_code: empCode || null,
  };
  const r = await api('PUT', '/api/users/' + id, body);
  if (r.ok) { closeModal('modal-edit-user'); showToast('แก้ไขผู้ใช้เรียบร้อย', 'success'); loadUsers(); }
  else showToast(JSON.stringify(r.data.errors || r.data.message), 'error');
}

/* ---- HELPERS ---- */
async function loadBranchOptions(selectId) {
  const r = await api('GET', '/api/branches');
  const sel = document.getElementById(selectId);
  if (!sel) return;
  if (r.ok && Array.isArray(r.data)) {
    allBranches = r.data;
    const prefix = selectId === 'mu-branch' ? '<option value="">-- ไม่ระบุ --</option>' : '';
    sel.innerHTML = prefix + r.data.map(b => `<option value="${b.id}">${b.name}</option>`).join('');
  }
}

async function loadVehicleOptions(selectId) {
  const r = await api('GET', '/api/vehicles');
  const sel = document.getElementById(selectId);
  if (!sel) return;
  let html = '<option value="">-- ไม่ระบุ --</option>';
  if (r.ok && Array.isArray(r.data)) { allVehicles = r.data; html += r.data.map(v => `<option value="${v.id}">${v.license_plate}</option>`).join(''); }
  sel.innerHTML = html;
}

async function loadDriverOptions(selectId) {
  const r = await api('GET', '/api/users');
  const sel = document.getElementById(selectId);
  if (!sel) return;
  let html = '<option value="">-- ไม่ระบุ --</option>';
  if (r.ok) html += r.data.filter(u => u.role === 'driver').map(u => `<option value="${u.id}">${u.name}</option>`).join('');
  sel.innerHTML = html;
}

function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-overlay').forEach(m => m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); }));

function statusTH(s) {
  return {
    pending:    'รอดำเนินการ',
    in_transit: 'กำลังส่ง',
    delivered:  'ส่งแล้ว',
    problem:    'มีปัญหา',
    completed:  'เสร็จสิ้น',
    cancelled:  'ยกเลิก',
  }[s] || s;
}

function subStatusTH(s) {
  return {
    incomplete:  'รับไม่ครบ',
    rejected:    'ปฏิเสธรับ',
    bo_pending:  'รอ BO อนุมัติ',
    bo_approved: 'BO อนุมัติแล้ว',
    bo_rejected: 'BO ปฏิเสธ',
  }[s] || s;
}

function roleTH(r) {
  return {admin:'ผู้ดูแลระบบ', hq:'สำนักงานใหญ่', driver:'คนขับ', branch:'สาขา'}[r] || r;
}

function formatDate(dateStr) {
  if (!dateStr) return '-';
  return new Date(dateStr).toLocaleDateString('th-TH', {year:'numeric', month:'long', day:'numeric'});
}

function formatDateTime(dateStr) {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  const date = d.toLocaleDateString('th-TH', {year:'numeric', month:'long', day:'numeric'});
  const time = d.toLocaleTimeString('th-TH', {hour:'2-digit', minute:'2-digit'});
  return `${date} ${time}`;
}

let toastTimer;
function showToast(msg, type = 'success') {
  let t = document.getElementById('toast');
  if (!t) {
    t = document.createElement('div');
    t.id = 'toast';
    t.style.cssText = 'position:fixed;bottom:24px;right:24px;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:600;z-index:999;transition:opacity .3s;display:flex;align-items:center;gap:8px;';
    document.body.appendChild(t);
  }
  const icon = type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation';
  t.innerHTML = `<i class="fa-solid ${icon}"></i> ${msg}`;
  t.style.background = type === 'success' ? '#38a169' : '#e53e3e';
  t.style.color = '#fff';
  t.style.opacity = '1';
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => t.style.opacity = '0', 3000);
}

/* ---- ITEMS ---- */
let allItemsCache = [];

async function loadItems() {
  const r = await api('GET', '/api/items');
  if (!r.ok) return;
  allItemsCache = r.data;
  document.getElementById('item-count').textContent = `${r.data.length} รายการ`;
  document.getElementById('items-tbody').innerHTML = r.data.length === 0
    ? `<tr><td colspan="5"><div class="empty-state"><i class="fa-solid fa-boxes-stacked"></i><p>ยังไม่มีสินค้า</p></div></td></tr>`
    : r.data.map(item => `<tr>
        <td><b>${item.name}</b></td>
        <td>${item.unit}</td>
        <td style="color:#718096;font-size:12px;">${item.category || '-'}</td>
        <td style="text-align:center;">
          <span class="badge ${item.is_active ? 'badge-delivered' : 'badge-cancelled'}">
            ${item.is_active ? 'ใช้งาน' : 'ปิดใช้'}
          </span>
        </td>
        <td style="text-align:center;display:flex;gap:4px;justify-content:center;">
          <button class="btn btn-primary btn-sm" onclick="openEditItem(${item.id})"><i class="fa-solid fa-pen"></i> แก้ไข</button>
          <button class="btn ${item.is_active ? 'btn-danger' : 'btn-success'} btn-sm" onclick="toggleItem(${item.id})">
            <i class="fa-solid ${item.is_active ? 'fa-ban' : 'fa-check'}"></i> ${item.is_active ? 'ปิด' : 'เปิด'}
          </button>
        </td>
      </tr>`).join('');
}

function openCreateItem() {
  document.getElementById('mi-id').value = '';
  document.getElementById('mi-name').value = '';
  document.getElementById('mi-unit').value = '';
  document.getElementById('mi-category').value = '';
  document.getElementById('item-modal-title').textContent = 'เพิ่มสินค้า';
  document.getElementById('modal-item').classList.add('open');
}

function openEditItem(id) {
  const item = allItemsCache.find(i => i.id === id);
  if (!item) return;
  document.getElementById('mi-id').value = item.id;
  document.getElementById('mi-name').value = item.name;
  document.getElementById('mi-unit').value = item.unit;
  document.getElementById('mi-category').value = item.category || '';
  document.getElementById('item-modal-title').textContent = 'แก้ไขสินค้า';
  document.getElementById('modal-item').classList.add('open');
}

async function submitItem() {
  const id = document.getElementById('mi-id').value;
  const name = document.getElementById('mi-name').value.trim();
  const unit = document.getElementById('mi-unit').value.trim();
  const category = document.getElementById('mi-category').value.trim();
  if (!name || !unit) { showToast('กรุณาระบุชื่อสินค้าและหน่วย', 'error'); return; }
  const body = { name, unit, category: category || null };
  const r = id
    ? await api('PUT', `/api/items/${id}`, body)
    : await api('POST', '/api/items', body);
  if (r.ok) {
    closeModal('modal-item');
    showToast(id ? 'แก้ไขสินค้าเรียบร้อย' : 'เพิ่มสินค้าเรียบร้อย', 'success');
    loadItems();
  } else {
    showToast(r.data.message || 'เกิดข้อผิดพลาด', 'error');
  }
}

async function toggleItem(id) {
  const r = await api('PATCH', `/api/items/${id}/toggle`);
  if (r.ok) { showToast('อัปเดตสถานะเรียบร้อย', 'success'); loadItems(); }
  else showToast(r.data.message || 'เกิดข้อผิดพลาด', 'error');
}

window.onload = async () => {
  if (token) {
    const r = await api('GET', '/api/auth/me');
    if (r.ok) initApp(r.data);
    else { token = ''; localStorage.removeItem('bh_token'); }
  }
};

document.addEventListener('keydown', e => {
  if (e.key === 'Enter' && document.getElementById('login-screen').style.display !== 'none') doQuickLogin();
});
</script>
</body>
</html>
