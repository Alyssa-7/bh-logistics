<!DOCTYPE html>
<html lang="en-GB">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BH-Logistic — ระบบบริหารจัดการขนส่ง</title>
  <link rel="icon" id="link-favicon" type="image/png" href="/favicon.ico">
  <link rel="apple-touch-icon" id="link-apple-touch" href="/favicon.ico">
  <link rel="manifest" id="link-manifest">
  <meta name="theme-color" id="meta-theme-color" content="#1e3a5f">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="default">
  <meta name="apple-mobile-web-app-title" id="meta-app-title" content="BH-Logistic">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
  <style>
    *{box-sizing:border-box;margin:0;padding:0;}
    *,*::before,*::after{border-radius:0!important;}
    button,input,select,textarea{font-family:inherit;}
    body{font-family:'Prompt',sans-serif;background:#f0f4f8;color:#2d3748;font-size:14px;}

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
    .form-group input,.form-group select,.form-group textarea{width:100%;border:1.5px solid #e2e8f0;border-radius:8px;padding:9px 12px;font-size:14px;transition:border .2s;font-family:inherit;}
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
    .sidebar{position:fixed;top:0;left:0;bottom:0;width:240px;background:#1e3a5f;display:flex;flex-direction:column;z-index:999;}
    .leaflet-container{z-index:1 !important;}
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
    .badge-pending{background:#f6e05e;color:#744210;}
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
    /* ---- CREATE ORDER PAGE ---- */
    .co-layout{display:grid;grid-template-columns:1fr 380px;gap:16px;align-items:start;}
    .co-form-panel{max-height:calc(100vh - 140px);overflow-y:auto;}
    .co-map-panel{position:sticky;top:16px;}
    .co-stop-card{border:1.5px solid #bee3f8;border-radius:10px;padding:14px;margin-bottom:12px;background:#f7fbff;position:relative;}
    .co-stop-card:first-child::before{content:'';position:absolute;left:20px;bottom:-12px;width:2px;height:12px;background:#bee3f8;}
    .co-suggest-box{position:relative;}
    .co-suggestions{display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1.5px solid #bee3f8;border-top:none;border-radius:0 0 8px 8px;box-shadow:0 6px 16px rgba(0,0,0,.1);z-index:300;max-height:180px;overflow-y:auto;}
    .co-suggest-item{padding:8px 12px;cursor:pointer;font-size:13px;border-bottom:1px solid #f0f4f8;display:flex;align-items:center;justify-content:space-between;}
    .co-suggest-item:hover{background:#ebf8ff;}
    .co-suggest-item:last-child{border-bottom:none;}
    .co-hq-pin{display:flex;align-items:center;gap:10px;padding:10px 14px;background:#f0fff4;border:1.5px solid #9ae6b4;border-radius:8px;margin-bottom:12px;}
    @media(max-width:860px){.co-layout{grid-template-columns:1fr;}.co-map-panel{position:static;}.co-form-panel{max-height:none;}}
    /* ---- ORDER TABS ---- */
    .order-tabs{display:flex;gap:8px;margin:-20px -20px 16px;padding:14px 16px 12px;overflow-x:auto;flex-wrap:nowrap;scrollbar-width:none;background:#f8fafc;border-bottom:1px solid #e2e8f0;}
    .order-tabs::-webkit-scrollbar{display:none;}
    .order-tab{background:#f1f5f9;border:none;border-radius:20px;padding:7px 14px;font-size:13px;font-weight:600;color:#64748b;cursor:pointer;white-space:nowrap;display:flex;align-items:center;gap:6px;transition:background .15s,color .15s;}
    .order-tab:hover{background:#e2e8f0;color:#334155;}
    .order-tab.active{background:#1e3a5f;color:#fff;}
    .order-tab-badge{font-size:11px;background:rgba(0,0,0,.10);color:inherit;border-radius:20px;padding:1px 7px;font-weight:700;min-width:18px;text-align:center;}
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

    /* ── GPS Multi-drop Tracking ── */
    .gps-filter-btn{background:#f0f4f8;border:1.5px solid #e2e8f0;border-radius:8px;padding:6px 12px;font-size:12px;font-weight:600;color:#4a5568;cursor:pointer;transition:all .15s;font-family:inherit;}
    .gps-filter-btn:hover{border-color:#2a5298;color:#2a5298;}
    .gps-filter-active{background:#1e3a5f;border-color:#1e3a5f;color:#fff;}
    .gps-driver-card{background:#fff;border:1.5px solid #e2e8f0;border-radius:12px;padding:12px 16px;cursor:default;transition:border-color .2s,box-shadow .2s;}
    .gps-driver-card:hover{border-color:#4a90d9;box-shadow:0 3px 12px rgba(74,144,217,.15);}
    .gps-driver-card.gps-card-focused{border-color:#2a5298;box-shadow:0 0 0 3px rgba(42,82,152,.12);}
    @keyframes pulse-border{0%{box-shadow:0 0 0 0 rgba(46,117,182,.5);}70%{box-shadow:0 0 0 8px rgba(46,117,182,0);}100%{box-shadow:0 0 0 0 rgba(46,117,182,0);}}
    .highlight-pulse{animation:pulse-border .6s ease 3;border:2px solid #2E75B6 !important;}
    .gps-card-header{display:flex;align-items:center;gap:8px;cursor:pointer;user-select:none;}
    .gps-card-header:hover .gps-chevron{color:#2a5298;}
    .gps-chevron{transition:transform .2s;color:#a0aec0;font-size:10px;flex-shrink:0;}
    .gps-chevron.open{transform:rotate(90deg);}
    .gps-card-body{margin-top:12px;}
    .gps-timeline{border-left:2px solid #e2e8f0;padding-left:16px;margin-left:8px;margin-top:10px;}
    .gps-stop{position:relative;margin-bottom:14px;}
    .gps-stop-dot{position:absolute;left:-21px;top:3px;width:12px;height:12px;border-radius:50%;border:2px solid #fff;box-shadow:0 0 0 2px currentColor;}
    .gps-stop-num{display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:50%;font-size:11px;font-weight:700;flex-shrink:0;margin-right:6px;}
    .gps-stop-row{display:flex;align-items:flex-start;gap:4px;cursor:pointer;}
    .gps-stop-row:hover .gps-stop-name{text-decoration:underline;color:#2a5298;}
    .gps-stop-name{font-weight:600;font-size:13px;color:#1e3a5f;}
    .gps-badge{display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600;margin-left:6px;white-space:nowrap;}
    .gps-badge-done{background:#c6f6d5;color:#276749;}
    .gps-badge-transit{background:#bee3f8;color:#2b6cb0;}
    .gps-badge-queue{background:#fef3c7;color:#92400e;}

    /* ─── RESPONSIVE ────────────────────────────────────────────── */
    .hamburger{display:none;align-items:center;justify-content:center;background:none;border:none;cursor:pointer;padding:7px 8px;color:#1e3a5f;font-size:18px;border-radius:8px;transition:background .15s;flex-shrink:0;}
    .hamburger:hover{background:#ebf4ff;}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:98;}
    .sidebar-overlay.open{display:block;}
    /* ─── RESPONSIVE ≤900px: sidebar กลายเป็น drawer ───────── */
    @media(max-width:900px){
      .hamburger{display:flex;}
      .sidebar{transform:translateX(-240px);transition:transform .25s ease;}
      .sidebar.open{transform:translateX(0);}
      .main{margin-left:0 !important;}
      .topbar{padding:0 12px;}
      .content{padding:16px;}
    }
    /* ─── ≤780px: GPS split → stack (แผนที่บน, รายชื่อคนขับล่าง) ── */
    @media(max-width:780px){
      .gps-split{flex-direction:column;}
      .gps-map-wrap{position:static !important;width:100%;}
      .gps-left-panel{width:100%;}
      #gps-map{height:260px !important;min-height:200px !important;}
    }
    /* ─── ≤640px: mobile ─────────────────────────────────────── */
    @media(max-width:640px){
      .content{padding:10px;}
      .topbar h1{font-size:14px;}
      .card{padding:14px;}
      .form-row{grid-template-columns:1fr !important;}
      .info-grid{grid-template-columns:1fr !important;}
      .stat-card{flex-direction:row;flex-wrap:wrap;padding:12px;}
      .modal{width:calc(100vw - 20px) !important;max-width:none;padding:16px;border-radius:12px;margin:0 10px;}
      .modal-footer{flex-wrap:wrap;}
      .modal-footer .btn{flex:1;min-width:120px;justify-content:center;}
      .notif-dropdown{width:calc(100vw - 20px);right:-6px;}
      .order-tabs{padding:10px 12px 8px;}
      .stat-grid{grid-template-columns:repeat(2,1fr) !important;}
      .login-card{width:calc(100vw - 24px);padding:24px;}
      /* ป้องกัน iOS auto-zoom เมื่อ focus input */
      input,select,textarea{font-size:16px !important;}
      .search-box input{width:160px;}
      /* GPS filter row แบบ scroll ไม่ wrap */
      #gps-admin-panel .gps-filter-row{flex-wrap:nowrap;overflow-x:auto;padding-bottom:4px;scrollbar-width:none;}
    }
    /* ─── Safe area สำหรับ iPhone notch/home-bar ───────────── */
    @supports(padding:max(0px)){
      .topbar{padding-top:env(safe-area-inset-top);height:calc(60px + env(safe-area-inset-top));}
      .sidebar{padding-top:env(safe-area-inset-top);padding-bottom:max(16px,env(safe-area-inset-bottom));}
      .main{padding-bottom:env(safe-area-inset-bottom);}
    }
    /* ─── Touch UX ───────────────────────────────────────────── */
    *{-webkit-tap-highlight-color:transparent;}
    .nav-item,.btn,.gps-card-header,.order-tab{min-height:44px;}
    /* Report grids */
    .rp-row2{display:grid;grid-template-columns:300px 1fr;gap:16px;margin-bottom:16px;}
    .rp-row3{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;}
    @media(max-width:768px){
      .rp-row2,.rp-row3{grid-template-columns:1fr;}
    }
    .gps-stat-pill{background:#f7fafc;border:1px solid #e2e8f0;border-radius:8px;padding:4px 10px;font-size:12px;color:#4a5568;display:inline-flex;align-items:center;gap:5px;}
    /* ── Calendar ── */
    .cal-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:3px;}
    .cal-head{text-align:center;font-size:11px;font-weight:700;color:#718096;padding:8px 2px;text-transform:uppercase;}
    .cal-cell{min-height:72px;border-radius:8px;padding:5px 4px;background:#f8fafc;cursor:default;transition:background .15s,border-color .15s;border:1.5px solid transparent;position:relative;}
    .cal-cell.cal-has-work{background:#fff;cursor:pointer;}
    .cal-cell.cal-has-work:hover{background:#ebf4ff;border-color:#bee3f8;}
    .cal-cell.cal-other-month{opacity:.3;pointer-events:none;}
    .cal-cell.cal-today{background:#ebf8ff;border-color:#4a90d9;}
    .cal-cell.cal-selected{border-color:#2a5298 !important;box-shadow:0 0 0 2px rgba(42,82,152,.18);}
    .cal-date-num{font-size:12px;font-weight:700;color:#4a5568;margin-bottom:3px;line-height:1;}
    .cal-cell.cal-today .cal-date-num{background:#2a5298;color:#fff;border-radius:50%;width:22px;height:22px;display:inline-flex;align-items:center;justify-content:center;}
    .cal-bar-wrap{position:absolute;inset:0;border-radius:7px;overflow:hidden;display:flex;}
    .cal-count{font-size:9px;color:#718096;font-weight:700;line-height:1;padding-top:1px;}
    /* Day detail slide-up */
    .cal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:210;align-items:flex-end;justify-content:center;}
    .cal-overlay.open{display:flex;}
    .cal-sheet{background:#fff;border-radius:16px 16px 0 0;width:100%;max-width:640px;max-height:82vh;overflow-y:auto;padding:20px 20px 32px;}
    .cal-sheet-handle{width:40px;height:4px;background:#e2e8f0;border-radius:4px;margin:0 auto 16px;}
    @media(min-width:640px){.cal-overlay{align-items:center;}.cal-sheet{border-radius:16px;margin:16px;max-height:85vh;}}
    @media(max-width:480px){.cal-cell{min-height:54px;padding:3px 2px;}.cal-count{font-size:8px;}}
    /* GPS responsive layout */
    .gps-split{display:flex;gap:14px;align-items:flex-start;}
    .gps-left-panel{flex:0 0 60%;min-width:0;display:flex;flex-direction:column;gap:10px;}
    .gps-map-wrap{flex:0 0 40%;min-width:0;position:sticky;top:72px;display:flex;flex-direction:column;gap:10px;}
    #gps-map{height:520px;min-height:380px;}
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px;}
    .info-item span.label{color:#718096;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:2px;}
    .info-item span.val{font-size:14px;}
    .scan-found{border:1.5px solid #bee3f8;border-radius:10px;padding:16px;background:#ebf8ff;}
    .scan-found .scan-title{font-weight:700;color:#2a4a7f;font-size:15px;margin-bottom:12px;}

    /* ── System Settings ── */
    .sysconf-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;align-items:start;}
    .sysconf-col{display:flex;flex-direction:column;gap:12px;}
    .sysconf-full{grid-column:1/-1;}
    .sysconf-card{height:fit-content;}
    .sysconf-hint{font-size:11px;color:#a0aec0;margin:4px 0 0;}
    .sc-logo-wrap{display:flex;gap:14px;align-items:flex-start;}
    .sc-logo-preview{width:72px;height:72px;border-radius:10px;background:#f0f4f8;border:1.5px solid #e2e8f0;display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;}
    .sc-check-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;}
    .sc-check-item{display:flex;align-items:center;gap:8px;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;cursor:pointer;font-size:13px;color:#2d3748;user-select:none;transition:border-color .15s,background .15s;}
    .sc-check-item:hover{background:#f7fafc;border-color:#bee3f8;}
    .sc-check-item input[type=checkbox]{width:16px;height:16px;accent-color:#2a5298;flex-shrink:0;cursor:pointer;}
    .sc-check-item.checked{background:#ebf4ff;border-color:#4a90d9;}
    .sc-broadcast-row{display:flex;flex-direction:column;gap:4px;padding:10px 0;border-bottom:1px solid #f0f4f8;}
    .sc-broadcast-row:last-child{border-bottom:none;}
    .sc-broadcast-meta{display:flex;align-items:center;gap:6px;flex-wrap:wrap;}
    .badge-bc-normal{background:#bee3f8;color:#2a4a7f;}
    .badge-bc-important{background:#fefcbf;color:#744210;}
    .badge-bc-urgent{background:#fed7d7;color:#742a2a;}
    @media(max-width:768px){
      .sysconf-grid{grid-template-columns:1fr;}
      .sc-check-grid{grid-template-columns:1fr 1fr;}
    }
    @media(max-width:400px){
      .sc-check-grid{grid-template-columns:1fr;}
    }
    @media (max-width: 768px) {
      input[type="date"] {
        color: #333;
        background: #fff;
        font-size: 16px;
      }
    }
    .date-input-wrap { position: relative; display: inline-block; }
    .date-input-wrap.full { display: block; width: 100%; }
    .date-ph {
      display: none;
    }
    @media (max-width: 768px) {
      .date-ph {
        display: block;
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        color: #a0aec0;
        font-size: 13px;
        pointer-events: none;
        user-select: none;
        white-space: nowrap;
      }
    }
    /* ── Modal X button ───────────────────────────────────── */
    .modal-x-btn{position:absolute;top:14px;right:14px;background:#fff5f5;border:none;border-radius:50%;width:32px;height:32px;font-size:15px;cursor:pointer;color:#e53e3e;display:flex;align-items:center;justify-content:center;z-index:10;padding:0;flex-shrink:0;-webkit-tap-highlight-color:transparent;}
    .modal-x-btn:hover{background:#fed7d7;color:#c53030;}
    .modal{position:relative;}
    /* ── Date picker — คลิกที่ไหนในช่องก็เปิดปฏิทิน ─────── */
    input[type="date"]{cursor:pointer;}
    input[type="date"]::-webkit-calendar-picker-indicator{cursor:pointer;width:16px;height:16px;opacity:0.6;}
    input[type="date"]::-webkit-calendar-picker-indicator:hover{opacity:1;}
    /* ── ขอบตัดคม: sharp corners (ยกเว้น modal, filter tab, circle) ── */
    .card,.stat-card,.qa-card,
    .co-stop-card,.co-hq-pin,.co-suggestions,
    .notif-dropdown,.gps-driver-card,.gps-stat-pill,
    .sc-check-item,.sc-logo-preview,.scan-found,
    .alert,.cal-cell,.notif-bell,.hamburger,.btn,
    .date-input-wrap{border-radius:0!important;}
    input:not([type="radio"]):not([type="checkbox"]):not([type="range"]),
    select,textarea{border-radius:0!important;}
    button:not(.order-tab):not(.pickup-filter-btn):not(.gps-filter-btn):not(.modal-x-btn){border-radius:0!important;}
    /* inline-style div containers ที่เป็น card/box */
    [style*="border-radius:12px"],[style*="border-radius: 12px"],
    [style*="border-radius:10px"],[style*="border-radius: 10px"],
    [style*="border-radius:8px"],[style*="border-radius: 8px"]{border-radius:0!important;}
    /* ยกเว้นสิ่งที่ต้องคงเป็นวงกลม/โค้ง */
    .user-avatar,.modal-x-btn,.notif-unread-dot,
    .gps-stop-dot,.gps-stop-num{border-radius:50%!important;}
    .toggle-slider{border-radius:34px!important;}
    .toggle-slider::before{border-radius:50%!important;}
    /* Toggle switch */
    .toggle-switch{position:relative;display:inline-block;width:46px;height:26px;flex-shrink:0;}
    .toggle-switch input{opacity:0;width:0;height:0;}
    .toggle-slider{position:absolute;cursor:pointer;inset:0;background:#cbd5e0;transition:.2s;}
    .toggle-slider::before{content:'';position:absolute;width:20px;height:20px;left:3px;bottom:3px;background:#fff;transition:.2s;}
    .toggle-switch input:checked+.toggle-slider{background:#38a169;}
    .toggle-switch input:checked+.toggle-slider::before{transform:translateX(20px);}
    .badge,.order-tab-badge{border-radius:20px!important;}
    .cal-sheet-handle{border-radius:4px!important;}
    [style*="border-radius:50%"],[style*="border-radius: 50%"]{border-radius:50%!important;}
    [style*="border-radius:99px"],[style*="border-radius: 99px"]{border-radius:99px!important;}
    [style*="border-radius:20px"],[style*="border-radius: 20px"]{border-radius:20px!important;}
  </style>
</head>
<body>

<!-- LOGIN -->
<div id="login-screen">
  <div class="login-card">
    <div class="login-logo">
      <div class="logo-icon" id="login-logo-icon"><i class="fa-solid fa-truck"></i></div>
      <h1 id="login-system-name">BH-Logistic</h1>
      <p>ระบบบริหารจัดการขนส่งสินค้า</p>
    </div>

    <!-- Main Login: Employee ID + Password -->
    <div id="login-error"></div>
    <div class="form-group">
      <label>รหัสพนักงาน (Employee ID)</label>
      <input type="text" id="login-employee-id" placeholder="เช่น EMP001" autocomplete="off"
        onkeydown="if(event.key==='Enter')document.getElementById('login-password').focus()">
    </div>
    <div class="form-group">
      <label>รหัสผ่าน</label>
      <input type="password" id="login-password" placeholder="รหัสผ่าน"
        onkeydown="if(event.key==='Enter')doEmployeeLogin()">
    </div>
    <button class="btn btn-primary btn-block" onclick="doEmployeeLogin()" style="margin-bottom:4px;">
      <i class="fa-solid fa-right-to-bracket"></i> เข้าระบบ
    </button>

    <!-- Quick Access: IT Testing -->
    <div style="display:flex;align-items:center;gap:10px;margin:22px 0 14px;">
      <div style="flex:1;height:1px;background:#e2e8f0;"></div>
      <span style="font-size:10px;color:#a0aec0;font-weight:700;letter-spacing:.6px;white-space:nowrap;">QUICK ACCESS · IT TESTING</span>
      <div style="flex:1;height:1px;background:#e2e8f0;"></div>
    </div>
    <div class="qa-grid">
      <div class="qa-card" onclick="doQuickAccess('hq')">
        <i class="qa-icon fa-solid fa-building" style="color:#2a5298;"></i>
        <span class="qa-label">ครัวกลาง</span>
      </div>
      <div class="qa-card" onclick="doQuickAccess('driver')">
        <i class="qa-icon fa-solid fa-truck" style="color:#d69e2e;"></i>
        <span class="qa-label">ไรเดอร์</span>
      </div>
      <div class="qa-card" onclick="doQuickAccess('branch')">
        <i class="qa-icon fa-solid fa-store" style="color:#38a169;"></i>
        <span class="qa-label">หน้าสาขา</span>
      </div>
      <div class="qa-card" onclick="doQuickAccess('admin')">
        <i class="qa-icon fa-solid fa-gear" style="color:#718096;"></i>
        <span class="qa-label">แอดมิน</span>
      </div>
    </div>
  </div>
</div>

<!-- APP -->
<div id="app">
  <div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>
  <div class="sidebar" id="sidebar">
    <div class="sidebar-logo">
      <div class="logo-wrap">
        <div class="logo-box" id="sidebar-logo-box"><i class="fa-solid fa-truck"></i></div>
        <div>
          <h2 id="sidebar-system-name">BH-Logistic</h2>
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
      <div style="display:flex;align-items:center;gap:4px;">
        <button class="hamburger" onclick="toggleSidebar()" aria-label="เมนู">
          <i class="fa-solid fa-bars"></i>
        </button>
        <h1 id="page-title">หน้าหลัก</h1>
      </div>
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
            <div style="padding:8px 12px;display:flex;gap:6px;border-bottom:1px solid #f0f4f8;">
              <button id="btn-enable-push" onclick="enablePushFromButton()" style="font-size:11px;padding:4px 10px;background:#27ae60;border-radius:6px;border:none;color:white;cursor:pointer;">🔔 เปิดรับแจ้งเตือน</button>
              <button id="btn-test-push" onclick="sendTestPush()" style="font-size:11px;padding:4px 10px;background:#8e44ad;border-radius:6px;border:none;color:white;cursor:pointer;display:none;">🧪 ทดสอบ Push</button>
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
        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:8px;margin-bottom:8px;">
          <div style="position:relative;flex:1;min-width:200px;">
            <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#a0aec0;font-size:13px;pointer-events:none;"></i>
            <input type="text" id="driver-dashboard-search" placeholder="ค้นหา รหัสออเดอร์, สินค้า หรือสาขา..." oninput="filterDriverOrders(currentDriverFilter)"
              style="border:1.5px solid #e2e8f0;padding:8px 10px 8px 32px;font-size:13px;width:100%;box-sizing:border-box;">
          </div>
          <div style="position:relative;">
            <input type="text" id="driver-dashboard-date-text" placeholder="วว/ดด/ปปปป" readonly
              style="border:1.5px solid #e2e8f0;padding:8px 10px;font-size:13px;cursor:pointer;min-width:130px;"
              onclick="try{document.getElementById('driver-dashboard-date').showPicker()}catch(e){}">
            <input type="date" id="driver-dashboard-date" tabindex="-1"
              style="position:absolute;inset:0;opacity:0;pointer-events:none;"
              onchange="syncDateToText(this,'driver-dashboard-date-text');filterDriverOrders(currentDriverFilter)">
          </div>
          <button class="btn btn-secondary btn-sm" onclick="loadDashboard()" id="btn-dashboard-refresh" title="รีเฟรช">
            <i class="fa-solid fa-rotate-right"></i>
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

      <!-- ออเดอร์ทั้งหมด -->
      <div class="page-section" id="page-orders">
        <div class="card">
          <!-- Status tabs -->
          <div class="order-tabs">
            <button class="order-tab active" data-status="" onclick="userSetOrderTab('')">
              ทุกสถานะ <span class="order-tab-badge" id="tab-count-all">0</span>
            </button>
            <button class="order-tab" data-status="pending" onclick="userSetOrderTab('pending')">
              รอดำเนินการ <span class="order-tab-badge" id="tab-count-pending">0</span>
            </button>
            <button class="order-tab" data-status="in_transit" onclick="userSetOrderTab('in_transit')">
              กำลังส่ง <span class="order-tab-badge" id="tab-count-in_transit">0</span>
            </button>
            <button class="order-tab" data-status="completed" onclick="userSetOrderTab('completed')">
              เสร็จสิ้น <span class="order-tab-badge" id="tab-count-completed">0</span>
            </button>
            <button class="order-tab" data-status="cancelled" onclick="userSetOrderTab('cancelled')">
              ยกเลิก <span class="order-tab-badge" id="tab-count-cancelled">0</span>
            </button>
          </div>
          <!-- Filters row -->
          <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:14px;align-items:center;">
            <div style="position:relative;width:45%;min-width:220px;">
              <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#a0aec0;font-size:13px;pointer-events:none;"></i>
              <input type="text" id="order-search" placeholder="ค้นหา รหัสออเดอร์, ชื่อสินค้า หรือสาขา..." oninput="filterOrders()"
                style="border:1.5px solid #e2e8f0;border-radius:8px;padding:8px 10px 8px 32px;font-size:13px;font-family:inherit;width:100%;box-sizing:border-box;">
            </div>
            <div style="position:relative;">
              <input type="text" id="order-date-filter-text" placeholder="วว/ดด/ปปปป" readonly
                style="border:1.5px solid #e2e8f0;padding:8px 10px;font-size:13px;font-family:inherit;cursor:pointer;min-width:130px;"
                title="กรองตามวันที่นัดส่ง"
                onclick="try{document.getElementById('order-date-filter').showPicker()}catch(e){}">
              <input type="date" id="order-date-filter" tabindex="-1"
                style="position:absolute;inset:0;opacity:0;pointer-events:none;"
                onchange="syncDateToText(this,'order-date-filter-text');filterOrders()">
            </div>
            <button class="btn btn-secondary btn-sm" onclick="loadOrders()" title="รีเฟรช"><i class="fa-solid fa-rotate-right"></i></button>
          </div>
          <div class="table-wrap">
            <table>
              <thead><tr>
                <th>สินค้า</th>
                <th>สาขา</th>
                <th>วันนัดส่ง</th>
                <th>สถานะ</th>
                <th>จัดการ</th>
              </tr></thead>
              <tbody id="orders-tbody"><tr><td colspan="5" class="loading">กำลังโหลด...</td></tr></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- CREATE ORDER -->
      <div class="page-section" id="page-create-order">
        <div class="co-layout">
          <!-- LEFT: Form -->
          <div class="card co-form-panel">
            <!-- Header fields -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;">
              <div>
                <label style="font-size:12px;font-weight:600;color:#4a5568;display:block;margin-bottom:5px;"><i class="fa-solid fa-calendar-days"></i> วันที่นัดส่ง</label>
                <div style="position:relative;">
                  <input type="text" id="co-date-text" placeholder="วว/ดด/ปปปป" readonly
                    style="border:1.5px solid #e2e8f0;border-radius:8px;padding:9px 12px;font-size:14px;font-family:inherit;width:100%;box-sizing:border-box;cursor:pointer;color:#2d3748;"
                    onclick="try{document.getElementById('co-date-native').showPicker()}catch(e){}">
                  <input type="date" id="co-date-native" tabindex="-1"
                    style="position:absolute;inset:0;opacity:0;pointer-events:none;"
                    onchange="syncDateToText(this,'co-date-text')">
                </div>
              </div>
              <div>
                <label style="font-size:12px;font-weight:600;color:#4a5568;display:block;margin-bottom:5px;"><i class="fa-solid fa-truck"></i> คนขับรถ</label>
                <select id="co-driver" onchange="updateCoMap()"
                  style="border:1.5px solid #e2e8f0;border-radius:8px;padding:9px 12px;font-size:14px;font-family:inherit;background:#fff;width:100%;box-sizing:border-box;">
                  <option value="">-- ไม่กำหนด (รอรับงาน) --</option>
                </select>
              </div>
            </div>

            <!-- Route label -->
            <div style="font-size:13px;font-weight:700;color:#2d3748;margin-bottom:10px;"><i class="fa-solid fa-route"></i> เส้นทางจัดส่ง</div>

            <!-- HQ start pin -->
            <div class="co-hq-pin">
              <i class="fa-solid fa-circle" style="color:#38a169;font-size:18px;"></i>
              <div>
                <div style="font-size:13px;font-weight:700;color:#276749;">จุดเริ่มต้น: <span id="co-hq-label">คลังสินค้ากลาง (HQ)</span></div>
                <div style="font-size:11px;color:#68d391;">จุดออกเดินทางของรถขนส่ง</div>
              </div>
            </div>

            <!-- Stops list -->
            <div id="co-stops-list"></div>

            <!-- Add stop button -->
            <button onclick="addCoStop()"
              style="width:100%;padding:10px;border:2px dashed #bee3f8;border-radius:8px;background:none;color:#4299e1;font-size:13px;font-weight:600;cursor:pointer;margin-top:4px;">
              <i class="fa-solid fa-plus"></i> เพิ่มจุดส่งถัดไป (ถ้ามีหลายที่)
            </button>

            <!-- Actions -->
            <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:20px;padding-top:16px;border-top:1.5px solid #e2e8f0;">
              <button class="btn btn-secondary" onclick="navigate('orders')">ยกเลิก</button>
              <button class="btn btn-success" onclick="submitCreateOrder()">
                <i class="fa-solid fa-truck"></i> บันทึกและเปิดงาน
              </button>
            </div>
          </div>

          <!-- RIGHT: Map -->
          <div class="card co-map-panel" style="padding:14px;display:flex;flex-direction:column;">
            <div style="font-size:13px;font-weight:700;color:#2d3748;margin-bottom:10px;">
              <i class="fa-solid fa-map" style="color:#4299e1;"></i> แผนที่จำลองเส้นทาง
            </div>
            <div id="co-map" style="flex:1;border-radius:8px;min-height:380px;height:calc(100vh - 340px);border:1.5px solid #e2e8f0;"></div>
            <div id="co-map-info" style="margin-top:10px;font-size:12px;color:#718096;line-height:1.7;min-height:36px;">
              <span style="color:#a0aec0;">แผนที่จะอัปเดตเส้นทางอัตโนมัติเมื่อมีการเพิ่มจุดส่ง</span>
            </div>
          </div>
        </div>
      </div>

      <!-- SETTINGS -->
      <div class="page-section" id="page-settings">

        <!-- Page header -->
        <div style="margin-bottom:20px;">
          <h2 style="font-size:20px;font-weight:800;color:#1e3a5f;margin:0 0 4px;"><i class="fa-solid fa-gear" style="color:#4a90d9;margin-right:8px;"></i>ตั้งค่าระบบ</h2>
          <p style="font-size:13px;color:#718096;margin:0;">กำหนดค่าการทำงานพื้นฐานของระบบ</p>
        </div>

        <div class="sysconf-grid">

          <!-- ── คอลัมน์ซ้าย: ข้อมูลระบบ + ความปลอดภัย ── -->
          <div class="sysconf-col">

            <!-- Section 1: ข้อมูลระบบ -->
            <div class="card sysconf-card">
              <div class="card-title"><i class="fa-solid fa-circle-info"></i> ข้อมูลระบบ</div>
              <div class="form-group">
                <label>ชื่อระบบ</label>
                <input type="text" id="sc-system-name" placeholder="BH-Logistic">
              </div>
              <div class="form-group">
                <label>โลโก้หน้าเว็บ</label>
                <div class="sc-logo-wrap">
                  <div class="sc-logo-preview" id="sc-logo-preview">
                    <i class="fa-solid fa-truck" id="sc-logo-icon" style="font-size:28px;color:#4a90d9;"></i>
                    <img id="sc-logo-img" src="" alt="" style="display:none;width:56px;height:56px;object-fit:contain;border-radius:8px;">
                  </div>
                  <div style="flex:1;min-width:0;">
                    <button class="btn btn-secondary btn-sm" onclick="document.getElementById('sc-logo-file').click()">
                      <i class="fa-solid fa-upload"></i> เลือกรูป
                    </button>
                    <input type="file" id="sc-logo-file" accept="image/*" style="display:none;" onchange="previewLogo(event)">
                    <p style="font-size:11px;color:#a0aec0;margin:6px 0 0;">PNG, JPG · แนะนำ 128×128 px<br>แสดงใน Sidebar และ Favicon</p>
                    <button class="btn btn-sm" id="sc-logo-clear" onclick="clearLogo()" style="display:none;border:1.5px solid #e2e8f0;color:#718096;background:#fff;margin-top:6px;">
                      <i class="fa-solid fa-trash"></i> ลบโลโก้
                    </button>
                  </div>
                </div>
              </div>
              <div style="display:flex;gap:8px;justify-content:flex-end;flex-wrap:wrap;">
                <button class="btn" onclick="resetSystemInfo()" style="border:1.5px solid #4a90d9;color:#4a90d9;background:#fff;"><i class="fa-solid fa-rotate-left"></i> ค่าเริ่มต้น</button>
                <button class="btn btn-primary" onclick="saveSystemInfo()"><i class="fa-solid fa-floppy-disk"></i> บันทึก</button>
              </div>
            </div>

            <!-- Section 2: ความปลอดภัย -->
            <div class="card sysconf-card">
              <div class="card-title"><i class="fa-solid fa-shield-halved"></i> ความปลอดภัย</div>
              <div class="form-group">
                <label>จำนวนครั้ง Login ผิดก่อนบล็อก</label>
                <input type="number" id="sc-login-attempts" min="1" max="20" placeholder="5">
                <p class="sysconf-hint">ค่าเริ่มต้น 5 ครั้ง</p>
              </div>
              <div class="form-group">
                <label>Session หมดอายุ (นาที)</label>
                <input type="number" id="sc-session-timeout" min="5" max="1440" placeholder="480">
                <p class="sysconf-hint">ค่าเริ่มต้น 480 นาที (8 ชั่วโมง)</p>
              </div>
              <div class="form-group">
                <label>บังคับเปลี่ยนรหัสทุกกี่วัน</label>
                <input type="number" id="sc-password-days" min="0" max="365" placeholder="0">
                <p class="sysconf-hint">0 = ไม่บังคับ</p>
              </div>
              <div style="display:flex;gap:8px;justify-content:flex-end;flex-wrap:wrap;">
                <button class="btn" onclick="resetSecurity()" style="border:1.5px solid #4a90d9;color:#4a90d9;background:#fff;"><i class="fa-solid fa-rotate-left"></i> ค่าเริ่มต้น</button>
                <button class="btn btn-primary" onclick="saveSecurity()"><i class="fa-solid fa-floppy-disk"></i> บันทึก</button>
              </div>
            </div>

          </div><!-- /คอลัมน์ซ้าย -->

          <!-- ── คอลัมน์ขวา: สำรองข้อมูล + แจ้งเตือน ── -->
          <div class="sysconf-col">

            <!-- Section 3: สำรองข้อมูล -->
            <div class="card sysconf-card">
              <div class="card-title"><i class="fa-solid fa-database"></i> สำรองข้อมูล</div>
              <p style="font-size:13px;color:#718096;margin:0 0 16px;">Export ออเดอร์เป็นไฟล์ CSV หรือ Excel สามารถกรองข้อมูลก่อน export ได้</p>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px;">
                <div class="form-group" style="margin:0;">
                  <label style="font-size:12px;">วันที่เริ่มต้น</label>
                  <input type="date" id="ex-date-from" class="form-control" data-no-ph="1" lang="en-GB" style="font-size:14px;padding:9px 12px;">
                </div>
                <div class="form-group" style="margin:0;">
                  <label style="font-size:12px;">วันที่สิ้นสุด</label>
                  <input type="date" id="ex-date-to" class="form-control" data-no-ph="1" lang="en-GB" style="font-size:14px;padding:9px 12px;">
                </div>
                <div class="form-group" style="margin:0;">
                  <label style="font-size:12px;">สาขา</label>
                  <select id="ex-branch" class="form-control" style="font-size:14px;padding:9px 12px;">
                    <option value="">— ทุกสาขา —</option>
                  </select>
                </div>
                <div class="form-group" style="margin:0;">
                  <label style="font-size:12px;">คนขับ</label>
                  <select id="ex-driver" class="form-control" style="font-size:14px;padding:9px 12px;">
                    <option value="">— ทุกคนขับ —</option>
                  </select>
                </div>
                <div class="form-group" style="margin:0;grid-column:1/-1;">
                  <label style="font-size:12px;">สถานะ</label>
                  <select id="ex-status" class="form-control" style="font-size:14px;padding:9px 12px;">
                    <option value="">— ทุกสถานะ —</option>
                    <option value="pending">รอดำเนินการ</option>
                    <option value="in_transit">กำลังส่ง</option>
                    <option value="completed">สำเร็จ</option>
                    <option value="cancelled">ยกเลิก</option>
                  </select>
                </div>
              </div>
              <div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin-bottom:4px;">
                <button class="btn" onclick="exportOrders('csv')" id="sc-btn-csv"
                        style="border:1.5px solid #4a90d9;color:#4a90d9;background:#fff;gap:6px;">
                  <i class="fa-solid fa-file-csv"></i> Export CSV
                </button>
                <button class="btn btn-primary" onclick="exportOrders('excel')" id="sc-btn-excel">
                  <i class="fa-solid fa-file-excel"></i> Export Excel
                </button>
                <button class="btn" onclick="clearExportFilters()"
                        style="border:1.5px solid #e2e8f0;color:#718096;background:#fff;font-size:12px;">
                  <i class="fa-solid fa-xmark"></i> ล้างตัวกรอง
                </button>
              </div>
              <div id="sc-export-info" style="font-size:12px;color:#a0aec0;border-top:1px solid #f0f4f8;padding-top:12px;display:none;margin-top:12px;">
                <div id="sc-last-csv"></div>
                <div id="sc-last-excel"></div>
              </div>
            </div>

            <!-- Section 4: แจ้งเตือนพิเศษ (Broadcast) -->
            <div class="card sysconf-card">
              <div class="card-title"><i class="fa-solid fa-bullhorn"></i> แจ้งเตือนพิเศษ (Broadcast)</div>
              <div class="form-group">
                <label>เลือกกลุ่มผู้รับ</label>
                <div class="sc-check-grid">
                  <label class="sc-check-item"><input type="checkbox" id="sc-role-all" onchange="toggleAllRoles(this)"> <span>ทุกคน</span></label>
                  <label class="sc-check-item"><input type="checkbox" id="sc-role-driver" value="driver"> <span>Driver เท่านั้น</span></label>
                  <label class="sc-check-item"><input type="checkbox" id="sc-role-branch" value="branch"> <span>Branch เท่านั้น</span></label>
                  <label class="sc-check-item"><input type="checkbox" id="sc-role-hq" value="hq"> <span>HQ เท่านั้น</span></label>
                </div>
              </div>
              <div class="form-group">
                <label>ข้อความแจ้งเตือน</label>
                <textarea id="sc-broadcast-msg" rows="3" placeholder="พิมพ์ข้อความที่ต้องการแจ้งเตือน..." style="resize:vertical;min-height:80px;"></textarea>
              </div>
              <div class="form-group">
                <label>ระดับความสำคัญ</label>
                <select id="sc-broadcast-priority">
                  <option value="normal">ปกติ</option>
                  <option value="important">สำคัญ</option>
                  <option value="urgent">ด่วนมาก</option>
                </select>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-bottom:16px;">
                <button class="btn btn-primary" onclick="sendBroadcast()">
                  <i class="fa-solid fa-paper-plane"></i> ส่งแจ้งเตือน
                </button>
              </div>
              <div style="border-top:1px solid #f0f4f8;padding-top:14px;">
                <div style="font-size:12px;font-weight:700;color:#4a5568;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">ประวัติการส่ง 10 รายการล่าสุด</div>
                <div id="sc-broadcast-history"><div class="loading" style="padding:12px 0;">กำลังโหลด...</div></div>
              </div>
            </div>

          </div><!-- /คอลัมน์ขวา -->

          <!-- Section 5: ตำแหน่งคลังสินค้า HQ (full-width) -->
          <div class="card sysconf-card sysconf-full">
            <div class="card-title"><i class="fa-solid fa-warehouse"></i> ตำแหน่งคลังสินค้ากลาง (HQ)</div>
            <p style="font-size:13px;color:#718096;margin:0 0 16px;">ใช้เป็นจุดเริ่มต้นในการคำนวณเส้นทางและระยะเวลาวิ่งรถ</p>
            <div style="max-width:480px;">
              <div class="form-group">
                <label>ชื่อคลังสินค้า</label>
                <input type="text" id="st-hq-name" placeholder="คลังสินค้ากลาง (HQ)">
              </div>
              <div class="form-group">
                <label>วางพิกัด</label>
                <div style="display:flex;gap:8px;">
                  <input type="text" id="st-coords" placeholder="13.7563, 100.5018" style="flex:1;"
                         onkeydown="if(event.key==='Enter'){event.preventDefault();pinFromCoordsSettings();}">
                  <button class="btn btn-secondary btn-sm" onclick="pinFromCoordsSettings()" style="white-space:nowrap;flex-shrink:0;">
                    <i class="fa-solid fa-location-crosshairs"></i> ปักหมุด
                  </button>
                </div>
              </div>
              <div class="form-group">
                <label>แผนที่ — คลิกเพื่อปักหมุด</label>
                <div id="st-map" style="height:300px;border-radius:8px;border:1.5px solid #e2e8f0;margin-top:6px;"></div>
              </div>
              <div style="display:none;">
                <input type="number" id="st-hq-lat" step="any">
                <input type="number" id="st-hq-lng" step="any">
              </div>
              <div style="display:flex;justify-content:flex-end;">
                <button class="btn btn-primary" onclick="saveHqSettings()">
                  <i class="fa-solid fa-floppy-disk"></i> บันทึกการตั้งค่า
                </button>
              </div>
            </div>
          </div>

        </div><!-- /sysconf-grid -->
      </div>

      <!-- BRANCHES -->
      <div class="page-section" id="page-branches">
        <div class="card">
          <div style="display:flex;flex-wrap:wrap;align-items:center;gap:8px;margin-bottom:12px;">
            <div style="position:relative;flex:1;min-width:200px;">
              <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#a0aec0;font-size:13px;pointer-events:none;"></i>
              <input type="text" id="branch-search" placeholder="ค้นหาชื่อสาขา หรือที่อยู่..." oninput="filterBranches()"
                style="border:1.5px solid #e2e8f0;padding:8px 10px 8px 32px;font-size:13px;width:100%;box-sizing:border-box;">
            </div>
            <span style="font-size:13px;color:#718096;white-space:nowrap;" id="branch-count">-</span>
            <button class="btn btn-success btn-sm" id="btn-add-branch" onclick="openCreateBranch()">
              <i class="fa-solid fa-plus"></i> เพิ่มสาขา
            </button>
          </div>
          <div class="table-wrap">
            <table>
              <thead><tr><th>ID</th><th>ชื่อสาขา</th><th>ที่อยู่</th><th>พิกัด</th><th>สถานะ</th><th id="branch-th-manage">จัดการ</th></tr></thead>
              <tbody id="branches-tbody"><tr><td colspan="6" class="loading">กำลังโหลด...</td></tr></tbody>
            </table>
          </div>
        </div>
      </div>


      <!-- ติดตามคนขับ -->
      <div class="page-section" id="page-gps">

        <!-- ── Driver: ส่งพิกัด GPS ── -->
        <div class="card" id="gps-send-card" style="display:none;">
          <div class="card-title"><i class="fa-solid fa-satellite-dish"></i> ส่งพิกัด GPS</div>
          <div class="form-row" style="margin-bottom:12px;">
            <div class="form-group" style="margin:0;" id="gps-lat-group"><label>ละติจูด</label><input type="number" id="gps-lat" placeholder="13.7563" step="any"></div>
            <div class="form-group" style="margin:0;" id="gps-lng-group"><label>ลองจิจูด</label><input type="number" id="gps-lng" placeholder="100.5018" step="any"></div>
          </div>
          <div style="display:flex;gap:10px;" id="gps-manual-btns">
            <button class="btn btn-secondary btn-sm" onclick="useMyLocation()"><i class="fa-solid fa-crosshairs"></i> ใช้ตำแหน่งปัจจุบัน</button>
            <button class="btn btn-success btn-sm" onclick="sendGps()"><i class="fa-solid fa-paper-plane"></i> ส่ง GPS</button>
          </div>
          <div id="gps-tracking-section" style="display:none;">
            <div style="display:flex;gap:10px;align-items:center;">
              <button class="btn btn-success btn-sm" id="btn-start-tracking" onclick="startTracking()"><i class="fa-solid fa-play"></i> เริ่มติดตาม</button>
              <button class="btn btn-danger btn-sm" id="btn-stop-tracking" style="display:none;" onclick="stopTracking()"><i class="fa-solid fa-stop"></i> หยุดติดตาม</button>
              <span id="tracking-status" style="font-size:13px;color:#718096;">หยุดแล้ว</span>
            </div>
          </div>
          <div id="gps-result" style="margin-top:12px;"></div>
        </div>

        <!-- ── Admin / HQ: Multi-drop Live Tracking ── -->
        <div id="gps-admin-panel" style="display:none;">

          <!-- Split-screen row -->
          <div class="gps-split">

            <!-- LEFT 60%: รายชื่อคนขับ & ลำดับคิวงาน -->
            <div class="gps-left-panel">

              <!-- Search + Filters -->
              <div class="card" style="padding:14px;margin:0;">
                <div style="margin-bottom:10px;">
                  <input type="text" id="gps-search" placeholder="ค้นหาคนขับ / สาขา / สินค้า..."
                    oninput="filterDriverCards()"
                    style="width:100%;border:1.5px solid #e2e8f0;border-radius:8px;padding:9px 12px;font-size:14px;font-family:inherit;">
                </div>
                <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
                  <button class="gps-filter-btn gps-filter-active" data-filter="all" onclick="setGpsFilter('all')">ทั้งหมด</button>
                  <button class="gps-filter-btn" data-filter="active" onclick="setGpsFilter('active')">
                    <span style="color:#38a169;">&#9679;</span> กำลังวิ่ง (<span id="cnt-active">0</span>)
                  </button>
                  <button class="gps-filter-btn" data-filter="waiting" onclick="setGpsFilter('waiting')">
                    <span style="color:#d69e2e;">&#9679;</span> รองาน (<span id="cnt-waiting">0</span>)
                  </button>
                  <button class="gps-filter-btn" data-filter="idle" onclick="setGpsFilter('idle')">
                    <span style="color:#a0aec0;">&#9679;</span> ว่างงาน (<span id="cnt-idle">0</span>)
                  </button>
                  <button class="btn btn-secondary btn-sm" style="margin-left:auto;" onclick="loadAllDriversPanel()">
                    <i class="fa-solid fa-rotate-right"></i>
                  </button>
                </div>
              </div>

              <!-- Driver cards list -->
              <div id="gps-drivers-list">
                <div class="loading" style="padding:32px;text-align:center;">กำลังโหลดข้อมูลคนขับ...</div>
              </div>

            </div><!-- /LEFT -->

            <!-- RIGHT 40%: แผนที่เรียลไทม์ -->
            <div class="gps-map-wrap">

              <div class="card" style="padding:0;overflow:hidden;margin:0;position:relative;">
                <div id="gps-map"></div>
                <div id="gps-map-status"
                  style="position:absolute;bottom:10px;left:10px;background:rgba(255,255,255,0.92);border-radius:8px;padding:5px 10px;font-size:11px;color:#718096;box-shadow:0 1px 6px rgba(0,0,0,.15);pointer-events:none;">
                  <i class="fa-solid fa-satellite-dish"></i> กำลังโหลด...
                </div>
              </div>

              <div class="card" style="padding:10px 14px;margin:0;">
                <div style="display:flex;gap:14px;font-size:12px;color:#718096;flex-wrap:wrap;align-items:center;justify-content:center;">
                  <span><span style="color:#e53e3e;font-weight:700;"><i class="fa-solid fa-truck"></i></span> คนขับ</span>
                  <span><span style="color:#2b6cb0;font-weight:700;">■</span> สาขา</span>
                  <span><span style="color:#4299e1;font-weight:700;">──</span> เส้นทาง</span>
                  <span style="font-style:italic;">อัปเดตทุก 10 วินาที</span>
                </div>
              </div>

            </div><!-- /RIGHT -->
          </div><!-- /split-screen row -->


        </div><!-- /gps-admin-panel -->
      </div>

      <!-- สินค้าตีกลับ (HQ/Admin) -->
      <div class="page-section" id="page-rejected">
        <div class="card">
          <div id="hq-returns-tabs" style="display:flex;gap:8px;margin:-20px -20px 14px;padding:14px 16px 12px;background:#f8fafc;border-bottom:1px solid #e2e8f0;overflow-x:auto;flex-wrap:nowrap;scrollbar-width:none;align-items:center;">
            <span style="font-size:12px;color:#a0aec0;margin-right:4px;">กำลังโหลด...</span>
          </div>
          <div style="display:flex;flex-wrap:wrap;align-items:center;gap:8px;margin-bottom:10px;">
            <div style="position:relative;flex:1;min-width:200px;">
              <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#a0aec0;font-size:13px;pointer-events:none;"></i>
              <input type="text" id="hq-return-search" placeholder="ค้นหา ชื่อสินค้า หรือสาขา..." oninput="renderHqReturns()"
                style="border:1.5px solid #e2e8f0;padding:8px 10px 8px 32px;font-size:13px;width:100%;box-sizing:border-box;">
            </div>
            <div style="position:relative;">
              <input type="text" id="hq-return-date-text" placeholder="วว/ดด/ปปปป" readonly
                style="border:1.5px solid #e2e8f0;padding:8px 10px;font-size:13px;cursor:pointer;min-width:130px;"
                onclick="try{document.getElementById('hq-return-date').showPicker()}catch(e){}">
              <input type="date" id="hq-return-date" tabindex="-1"
                style="position:absolute;inset:0;opacity:0;pointer-events:none;"
                onchange="syncDateToText(this,'hq-return-date-text');renderHqReturns()">
            </div>
            <button class="btn btn-secondary btn-sm" onclick="clearHqReturnFilters()" title="ล้างตัวกรอง"><i class="fa-solid fa-xmark"></i></button>
            <button class="btn btn-secondary btn-sm" onclick="loadHqReturns()" title="รีเฟรช"><i class="fa-solid fa-rotate-right"></i></button>
          </div>
          <div id="hq-returns-list"><div class="loading">กำลังโหลด...</div></div>
        </div>
      </div>

      <!-- RECEIVE -->

      <!-- ติดตามสินค้า (Branch) -->
      <div class="page-section" id="page-track">
        <div class="card">
          <div class="action-bar">
            <span style="font-size:13px;color:#718096;" id="track-count">กำลังโหลด...</span>
            <button class="btn btn-secondary btn-sm" onclick="loadTrackOrders()" title="รีเฟรช">
              <i class="fa-solid fa-rotate-right"></i>
            </button>
          </div>
          <div id="track-list"><div class="loading">กำลังโหลด...</div></div>
        </div>
      </div>

      <!-- RETURNS (Branch) -->
      <div class="page-section" id="page-returns">
        <div class="card" style="margin-bottom:18px;">
          <div class="card-title"><i class="fa-solid fa-rotate-left"></i> แจ้งตีกลับสินค้า</div>
          <div style="display:flex;flex-direction:column;gap:14px;">
            <div class="form-group">
              <label>สินค้าที่ตีกลับ</label>
              <div class="co-suggest-box" style="position:relative;">
                <input type="text" id="ret-product" placeholder="พิมพ์ชื่อสินค้า..." autocomplete="off"
                  oninput="showRetSuggestions(this)" onblur="delayHideRetSuggestions(this)"
                  style="width:100%;border:1.5px solid #e2e8f0;border-radius:8px;padding:9px 12px;font-size:14px;font-family:inherit;box-sizing:border-box;">
                <div id="ret-product-suggestions" class="co-suggestions" style="display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1.5px solid #e2e8f0;border-top:none;border-radius:0 0 8px 8px;z-index:50;max-height:200px;overflow-y:auto;box-shadow:0 4px 12px rgba(0,0,0,.08);"></div>
              </div>
            </div>
            <div class="form-row" style="grid-template-columns:1fr 1fr;">
              <div class="form-group">
                <label>จำนวน</label>
                <input type="number" id="ret-qty" min="1" step="1" placeholder="ระบุจำนวน" style="width:100%;border:1.5px solid #e2e8f0;border-radius:8px;padding:9px 12px;font-size:14px;font-family:inherit;">
              </div>
              <div class="form-group">
                <label>หมายเหตุ</label>
                <select id="ret-reason" style="width:100%;border:1.5px solid #e2e8f0;border-radius:8px;padding:9px 12px;font-size:14px;font-family:inherit;background:#fff;" onchange="toggleReturnRemark()">
                  <option value="">-- เลือกสาเหตุ --</option>
                  <option value="damaged">สินค้าชำรุด</option>
                  <option value="expired">สินค้าหมดอายุ</option>
                  <option value="wrong_item">ส่งผิดรายการ</option>
                  <option value="other">อื่นๆ</option>
                </select>
              </div>
            </div>
            <div class="form-group" id="ret-remark-group" style="display:none;">
              <label>ระบุรายละเอียด (อื่นๆ)</label>
              <input type="text" id="ret-remark" placeholder="ระบุสาเหตุ..." style="width:100%;border:1.5px solid #e2e8f0;border-radius:8px;padding:9px 12px;font-size:14px;font-family:inherit;">
            </div>
            <div class="form-group">
              <label>
                <i class="fa-solid fa-camera" style="color:#e53e3e;"></i> หลักฐานรูปภาพประกอบ
                <span style="color:#e53e3e;font-size:12px;margin-left:4px;">*บังคับ</span>
              </label>
              <input type="file" id="ret-photo" accept="image/*" style="display:none;" onchange="previewReturnPhoto(this)">
              <div onclick="document.getElementById('ret-photo').click()"
                style="border:2px dashed #cbd5e0;border-radius:10px;padding:20px;text-align:center;cursor:pointer;background:#f7fafc;transition:all .15s;"
                onmouseover="this.style.borderColor='#63b3ed';this.style.background='#ebf8ff';"
                onmouseout="this.style.borderColor='#cbd5e0';this.style.background='#f7fafc';">
                <i class="fa-solid fa-camera" style="font-size:28px;color:#a0aec0;display:block;margin-bottom:8px;"></i>
                <span style="font-size:13px;color:#718096;">แตะเพื่อถ่าย / เลือกรูป</span>
              </div>
              <div id="ret-photo-preview" style="margin-top:8px;display:none;">
                <img id="ret-photo-img" style="max-width:100%;border-radius:8px;max-height:180px;object-fit:cover;">
                <button type="button" onclick="clearReturnPhoto()" style="display:block;margin-top:6px;font-size:12px;color:#e53e3e;background:none;border:none;cursor:pointer;">
                  <i class="fa-solid fa-xmark"></i> ลบรูป
                </button>
              </div>
            </div>
            <div>
              <button class="btn btn-danger" onclick="submitReturn()" style="width:100%;">
                <i class="fa-solid fa-rotate-left"></i> ส่งคำร้องตีกลับสินค้า
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- DRIVER HISTORY -->
      <div class="page-section" id="page-driver-history">
        <div class="card">
          <div style="display:flex;flex-wrap:wrap;align-items:center;gap:8px;margin-bottom:12px;">
            <div style="position:relative;flex:1;min-width:200px;">
              <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#a0aec0;font-size:13px;pointer-events:none;"></i>
              <input type="text" id="driver-history-search" placeholder="ค้นหา รหัสออเดอร์, สินค้า หรือสาขา..." oninput="filterDriverHistory()"
                style="border:1.5px solid #e2e8f0;padding:8px 10px 8px 32px;font-size:13px;width:100%;box-sizing:border-box;">
            </div>
            <div style="position:relative;">
              <input type="text" id="driver-history-date-text" placeholder="วว/ดด/ปปปป" readonly
                style="border:1.5px solid #e2e8f0;padding:8px 10px;font-size:13px;cursor:pointer;min-width:130px;"
                onclick="try{document.getElementById('driver-history-date').showPicker()}catch(e){}">
              <input type="date" id="driver-history-date" tabindex="-1"
                style="position:absolute;inset:0;opacity:0;pointer-events:none;"
                onchange="syncDateToText(this,'driver-history-date-text');filterDriverHistory()">
            </div>
            <span style="font-size:13px;color:#718096;white-space:nowrap;" id="driver-history-count">ออเดอร์ที่ผ่านมา</span>
          </div>
          <div id="driver-history-list"><div class="loading">กำลังโหลด...</div></div>
        </div>
      </div>

      <!-- RETURN HISTORY (Branch) -->
      <div class="page-section" id="page-return-history">
        <div class="card">
          <div id="return-history-tabs" style="display:flex;gap:8px;margin:-20px -20px 14px;padding:14px 16px 12px;background:#f8fafc;border-bottom:1px solid #e2e8f0;overflow-x:auto;flex-wrap:nowrap;scrollbar-width:none;align-items:center;">
            <span style="font-size:12px;color:#a0aec0;margin-right:4px;">กำลังโหลด...</span>
          </div>
          <div style="display:flex;flex-wrap:wrap;align-items:center;gap:8px;margin-bottom:10px;">
            <div style="position:relative;flex:1;min-width:200px;">
              <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#a0aec0;font-size:13px;pointer-events:none;"></i>
              <input type="text" id="ret-history-search" placeholder="ค้นหา ชื่อสินค้า..." oninput="renderReturnHistory()"
                style="border:1.5px solid #e2e8f0;padding:8px 10px 8px 32px;font-size:13px;width:100%;box-sizing:border-box;">
            </div>
            <div style="position:relative;">
              <input type="text" id="ret-history-date-text" placeholder="วว/ดด/ปปปป" readonly
                style="border:1.5px solid #e2e8f0;padding:8px 10px;font-size:13px;cursor:pointer;min-width:130px;"
                onclick="try{document.getElementById('ret-history-date').showPicker()}catch(e){}">
              <input type="date" id="ret-history-date" tabindex="-1"
                style="position:absolute;inset:0;opacity:0;pointer-events:none;"
                onchange="syncDateToText(this,'ret-history-date-text');renderReturnHistory()">
            </div>
            <button class="btn btn-secondary btn-sm" onclick="clearRetHistoryFilters()" title="ล้างตัวกรอง"><i class="fa-solid fa-xmark"></i></button>
            <button class="btn btn-secondary btn-sm" onclick="loadReturnHistory()" title="รีเฟรช"><i class="fa-solid fa-rotate-right"></i></button>
          </div>
          <div id="return-history-list"><div class="loading">กำลังโหลด...</div></div>
        </div>
      </div>

      <!-- REPORTS -->
      <div class="page-section" id="page-reports">

        <!-- ── Filter Bar ─────────────────────────────────────────── -->
        <div class="card" style="margin-bottom:16px;padding:16px 20px;">
          <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div class="form-group" style="margin:0;">
              <label>ช่วงเวลา</label>
              <select id="rp-mode" onchange="onReportModeChange()"
                      style="border:1.5px solid #e2e8f0;border-radius:8px;padding:9px 12px;font-size:14px;font-family:inherit;background:#fff;">
                <option value="month">เดือน</option>
                <option value="quarter">Quarter</option>
                <option value="year">ปี</option>
                <option value="daterange">ช่วงวันที่</option>
              </select>
            </div>
            <div id="rp-wrap-month" style="display:flex;gap:8px;">
              <div class="form-group" style="margin:0;"><label>ปี</label><input type="number" id="rp-year-m" value="2026" min="2000" max="2100" style="width:90px;"></div>
              <div class="form-group" style="margin:0;"><label>เดือน</label>
                <select id="rp-month-sel" style="border:1.5px solid #e2e8f0;border-radius:8px;padding:9px 12px;font-size:14px;font-family:inherit;background:#fff;">
                  <option value="1">มกราคม</option><option value="2">กุมภาพันธ์</option>
                  <option value="3">มีนาคม</option><option value="4">เมษายน</option>
                  <option value="5">พฤษภาคม</option><option value="6" selected>มิถุนายน</option>
                  <option value="7">กรกฎาคม</option><option value="8">สิงหาคม</option>
                  <option value="9">กันยายน</option><option value="10">ตุลาคม</option>
                  <option value="11">พฤศจิกายน</option><option value="12">ธันวาคม</option>
                </select>
              </div>
            </div>
            <div id="rp-wrap-quarter" style="display:none;gap:8px;">
              <div class="form-group" style="margin:0;"><label>ปี</label><input type="number" id="rp-year-q" value="2026" min="2000" max="2100" style="width:90px;"></div>
              <div class="form-group" style="margin:0;"><label>Quarter</label>
                <select id="rp-quarter-sel" style="border:1.5px solid #e2e8f0;border-radius:8px;padding:9px 12px;font-size:14px;font-family:inherit;background:#fff;">
                  <option value="1">Q1 (ม.ค.–มี.ค.)</option><option value="2">Q2 (เม.ย.–มิ.ย.)</option>
                  <option value="3">Q3 (ก.ค.–ก.ย.)</option><option value="4">Q4 (ต.ค.–ธ.ค.)</option>
                </select>
              </div>
            </div>
            <div id="rp-wrap-year" style="display:none;">
              <div class="form-group" style="margin:0;"><label>ปี</label><input type="number" id="rp-year-y" value="2026" min="2000" max="2100" style="width:90px;"></div>
            </div>
            <div id="rp-wrap-daterange" style="display:none;gap:8px;">
              <div class="form-group" style="margin:0;"><label>จากวันที่</label><input type="date" id="rp-from" data-no-ph="1" lang="en-GB"></div>
              <div class="form-group" style="margin:0;"><label>ถึงวันที่</label><input type="date" id="rp-to" data-no-ph="1" lang="en-GB"></div>
            </div>
            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;color:#4a5568;white-space:nowrap;padding-bottom:4px;">
              <input type="checkbox" id="rp-compare" style="width:14px;height:14px;accent-color:#3182ce;"> เทียบช่วงก่อนหน้า
            </label>
            <button class="btn btn-primary" onclick="loadDeliveryStats()" style="white-space:nowrap;">
              <i class="fa-solid fa-chart-bar"></i> ดูรายงาน
            </button>
          </div>
        </div>

        <!-- ── Empty state ────────────────────────────────────────── -->
        <div id="rp-empty" style="text-align:center;padding:70px 20px;color:#a0aec0;">
          <i class="fa-solid fa-chart-pie" style="font-size:54px;display:block;margin-bottom:16px;opacity:.35;"></i>
          <p style="font-size:15px;font-weight:600;margin-bottom:4px;color:#718096;">เลือกช่วงเวลาแล้วกด "ดูรายงาน"</p>
          <p style="font-size:13px;">วิเคราะห์ประสิทธิภาพการส่ง · เปรียบเทียบช่วงเวลา · ติดตาม KPI</p>
        </div>

        <!-- ── Main content ───────────────────────────────────────── -->
        <div id="rp-content" style="display:none;">

          <!-- Period header -->
          <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;flex-wrap:wrap;gap:8px;">
            <div>
              <div id="rp-period-label" style="font-size:20px;font-weight:800;color:#1a202c;line-height:1.2;"></div>
              <div id="rp-period-range"  style="font-size:12px;color:#718096;margin-top:3px;"></div>
            </div>
            <div id="rp-compare-badge" style="display:none;font-size:12px;background:#ebf8ff;color:#2b6cb0;padding:6px 14px;border-radius:99px;border:1px solid #bee3f8;font-weight:600;">
              <i class="fa-solid fa-code-compare" style="margin-right:4px;"></i><span id="rp-compare-label"></span>
            </div>
          </div>

          <!-- ── KPI Row (5 cards) ─────────────────────────────────── -->
          <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:16px;">
            <!-- Total -->
            <div class="stat-card" style="flex-direction:column;align-items:flex-start;gap:8px;padding:16px 14px;">
              <div style="display:flex;align-items:center;gap:10px;width:100%;">
                <div class="stat-icon blue" style="flex-shrink:0;width:36px;height:36px;font-size:14px;"><i class="fa-solid fa-box"></i></div>
                <div style="flex:1;min-width:0;">
                  <div style="font-size:26px;font-weight:800;line-height:1;" id="rp-kpi-total">-</div>
                  <div class="stat-label">ออเดอร์ทั้งหมด</div>
                </div>
                <div id="rp-kpi-total-delta" style="min-width:36px;text-align:right;"></div>
              </div>
            </div>
            <!-- Success Rate -->
            <div class="stat-card" style="flex-direction:column;align-items:flex-start;gap:8px;padding:16px 14px;">
              <div style="display:flex;align-items:center;gap:10px;width:100%;">
                <div class="stat-icon green" style="flex-shrink:0;width:36px;height:36px;font-size:14px;"><i class="fa-solid fa-circle-check"></i></div>
                <div style="flex:1;min-width:0;">
                  <div style="font-size:26px;font-weight:800;line-height:1;color:#276749;" id="rp-kpi-rate">-</div>
                  <div class="stat-label">อัตราสำเร็จ</div>
                </div>
                <div id="rp-kpi-rate-delta" style="min-width:36px;text-align:right;"></div>
              </div>
              <div style="width:100%;background:#e2e8f0;border-radius:99px;height:6px;overflow:hidden;">
                <div id="rp-kpi-rate-bar" style="height:100%;border-radius:99px;width:0%;transition:width .6s;"></div>
              </div>
              <div style="font-size:11px;color:#718096;" id="rp-kpi-rate-sub"></div>
            </div>
            <!-- Return requests -->
            <div class="stat-card" style="flex-direction:column;align-items:flex-start;gap:8px;padding:16px 14px;">
              <div style="display:flex;align-items:center;gap:10px;width:100%;">
                <div class="stat-icon" style="background:#fff5f5;color:#c53030;flex-shrink:0;width:36px;height:36px;font-size:14px;"><i class="fa-solid fa-rotate-left"></i></div>
                <div style="flex:1;min-width:0;">
                  <div style="font-size:26px;font-weight:800;line-height:1;color:#c53030;" id="rp-kpi-bo">-</div>
                  <div class="stat-label">ตีกลับ</div>
                </div>
                <div id="rp-kpi-bo-delta" style="min-width:36px;text-align:right;"></div>
              </div>
              <div style="font-size:11px;color:#718096;" id="rp-kpi-bo-sub"></div>
            </div>
          </div>

          <!-- ── Row 2: Donut + Trend ──────────────────────────────── -->
          <div class="rp-row2">
            <!-- Donut -->
            <div class="card" style="padding:20px;">
              <div class="card-title" style="margin-bottom:14px;"><i class="fa-solid fa-chart-pie"></i> สัดส่วนผลลัพธ์</div>
              <div style="position:relative;width:170px;height:170px;margin:0 auto;">
                <canvas id="rp-donut-chart" width="170" height="170"></canvas>
                <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;pointer-events:none;">
                  <div style="font-size:24px;font-weight:800;color:#1a202c;" id="rp-donut-center">-</div>
                  <div style="font-size:10px;color:#718096;">ออเดอร์</div>
                </div>
              </div>
              <div id="rp-donut-legend" style="margin-top:14px;display:flex;flex-direction:column;gap:4px;"></div>
            </div>
            <!-- Trend bar + line -->
            <div class="card" style="padding:20px;">
              <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;flex-wrap:wrap;gap:8px;">
                <div class="card-title" style="margin:0;"><i class="fa-solid fa-chart-column"></i> แนวโน้มรายเดือน <span id="rp-trend-year" style="font-size:12px;color:#718096;font-weight:400;"></span></div>
                <div style="display:flex;gap:12px;font-size:11px;color:#718096;">
                  <span style="display:flex;align-items:center;gap:4px;"><span style="display:inline-block;width:10px;height:10px;background:#48bb78;border-radius:2px;"></span>สำเร็จ</span>
                  <span style="display:flex;align-items:center;gap:4px;"><span style="display:inline-block;width:10px;height:10px;background:#ed8936;border-radius:2px;"></span>ปัญหา</span>
                  <span style="display:flex;align-items:center;gap:4px;"><span style="display:inline-block;width:16px;height:2px;background:#3182ce;border-radius:1px;vertical-align:middle;"></span>อัตราสำเร็จ %</span>
                </div>
              </div>
              <canvas id="rp-trend-chart" height="120"></canvas>
            </div>
          </div>

          <!-- ── Row 3: Branch returns (full-width card) ──────────── -->
          <div class="card" style="padding:20px;margin-bottom:16px;">
            <!-- Header: title + branch dropdown -->
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:16px;">
              <div class="card-title" style="margin:0;"><i class="fa-solid fa-rotate-left"></i> สาขาที่ตีกลับสินค้าบ่อย</div>
              <div id="rp-branch-selector-wrap" style="display:none;align-items:center;gap:8px;">
                <label style="font-size:12px;color:#718096;font-weight:600;">ดูรายละเอียดสาขา:</label>
                <select id="rp-branch-dropdown"
                  style="border:1.5px solid #e2e8f0;border-radius:8px;padding:9px 12px;font-size:14px;font-family:inherit;color:#2d3748;cursor:pointer;background:#fff;"
                  onchange="onBranchDropdownChange()">
                  <option value="">-- เลือกสาขา --</option>
                </select>
              </div>
            </div>
            <!-- Body: chart left + detail right -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start;">
              <!-- Chart -->
              <div>
                <div id="rp-branch-wrap" style="position:relative;width:100%;height:220px;"></div>
              </div>
              <!-- Detail panel -->
              <div id="rp-branch-detail-panel" style="display:none;">
                <div style="font-size:13px;font-weight:700;color:#1e3a5f;margin-bottom:12px;" id="rp-bd-title"></div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                  <div>
                    <div style="font-size:11px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;"><i class="fa-solid fa-box fa-sm"></i> สินค้าที่ตีกลับ</div>
                    <div id="rp-bd-products"></div>
                  </div>
                  <div>
                    <div style="font-size:11px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;"><i class="fa-solid fa-circle-exclamation fa-sm"></i> สาเหตุ</div>
                    <div id="rp-bd-reasons"></div>
                  </div>
                </div>
              </div>
              <div id="rp-branch-detail-empty" style="display:none;text-align:center;padding:40px 20px;color:#a0aec0;font-size:13px;">
                <i class="fa-solid fa-circle-check" style="display:block;font-size:24px;margin-bottom:8px;color:#68d391;"></i>
                สาขานี้ไม่มีการตีกลับในช่วงเวลานี้
              </div>
              <div id="rp-branch-detail-hint" style="display:flex;align-items:center;justify-content:center;padding:40px 20px;color:#a0aec0;font-size:13px;">
                <span><i class="fa-solid fa-hand-pointer" style="margin-right:6px;"></i>เลือกสาขาจาก Dropdown เพื่อดูรายละเอียด</span>
              </div>
            </div>
          </div>

          <!-- ── Row 3b: Return breakdown (full-width card) ─────────── -->
          <div class="card" style="padding:20px;margin-bottom:16px;">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;margin-bottom:14px;">
              <div style="display:flex;align-items:center;gap:10px;">
                <div class="card-title" style="margin:0;"><i class="fa-solid fa-chart-bar"></i> รายการตีกลับ</div>
                <span id="rp-item-chart-branch-label" style="display:none;font-size:11px;font-weight:700;background:#ebf4ff;color:#2a5298;border-radius:20px;padding:2px 10px;">ทั้งหมด</span>
              </div>
              <div style="display:flex;gap:4px;">
                <button id="rp-tab-shortage" onclick="switchItemTab('shortage')"
                        style="padding:5px 14px;border-radius:6px;border:1.5px solid #3182ce;font-size:12px;cursor:pointer;background:#3182ce;color:#fff;font-family:inherit;">สินค้าที่ตีกลับ</button>
                <button id="rp-tab-rejected" onclick="switchItemTab('rejected')"
                        style="padding:5px 14px;border-radius:6px;border:1.5px solid #e2e8f0;font-size:12px;cursor:pointer;background:#f7fafc;color:#718096;font-family:inherit;">เหตุผลตีกลับ</button>
              </div>
            </div>
            <div id="rp-item-shortage" style="position:relative;width:100%;height:240px;">
              <div id="rp-shortage-empty" style="display:none;text-align:center;padding:40px;color:#a0aec0;font-size:13px;"><i class="fa-solid fa-inbox" style="display:block;font-size:24px;margin-bottom:8px;opacity:.4;"></i>ไม่มีข้อมูล</div>
              <canvas id="rp-shortage-chart"></canvas>
            </div>
            <div id="rp-item-rejected" style="display:none;position:relative;width:100%;height:240px;">
              <div id="rp-rejected-empty" style="display:none;text-align:center;padding:40px;color:#a0aec0;font-size:13px;"><i class="fa-solid fa-inbox" style="display:block;font-size:24px;margin-bottom:8px;opacity:.4;"></i>ไม่มีข้อมูล</div>
              <canvas id="rp-rejected-chart"></canvas>
            </div>
          </div>

          <!-- ── Row 4: Timing Pipeline + Funnel + Driver Timing ─── -->
          <div class="card" style="padding:20px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:6px;">
              <div class="card-title" style="margin:0;"><i class="fa-solid fa-stopwatch"></i> ประสิทธิภาพกระบวนการ <span style="font-size:12px;color:#718096;font-weight:400;" id="rp-t-count"></span></div>
            </div>

            <!-- Pipeline -->
            <div style="display:flex;gap:0;overflow:hidden;border-radius:12px;border:1.5px solid #e2e8f0;">
              <div style="flex:1;padding:20px 16px;text-align:center;background:#ebf8ff;border-right:1px solid #e2e8f0;">
                <i class="fa-solid fa-warehouse" style="font-size:22px;color:#2b6cb0;margin-bottom:10px;display:block;"></i>
                <div style="font-size:11px;color:#4a5568;margin-bottom:6px;font-weight:600;">เตรียมสินค้า</div>
                <div style="font-size:20px;font-weight:800;color:#2b6cb0;" id="rp-t-prep">-</div>
                <div style="font-size:10px;color:#a0aec0;margin-top:4px;">สร้าง DO → ออกรถ</div>
              </div>
              <div style="display:flex;align-items:center;padding:0 6px;background:#f7fafc;">
                <i class="fa-solid fa-chevron-right" style="color:#cbd5e0;font-size:11px;"></i>
              </div>
              <div style="flex:1;padding:20px 16px;text-align:center;background:#fffff0;border-right:1px solid #e2e8f0;">
                <i class="fa-solid fa-truck" style="font-size:22px;color:#975a16;margin-bottom:10px;display:block;"></i>
                <div style="font-size:11px;color:#4a5568;margin-bottom:6px;font-weight:600;">ขับรถส่ง</div>
                <div style="font-size:20px;font-weight:800;color:#975a16;" id="rp-t-drive">-</div>
                <div style="font-size:10px;color:#a0aec0;margin-top:4px;">ออกรถ → ถึงสาขา</div>
              </div>
              <div style="display:flex;align-items:center;padding:0 6px;background:#f7fafc;">
                <i class="fa-solid fa-chevron-right" style="color:#cbd5e0;font-size:11px;"></i>
              </div>
              <div style="flex:1;padding:20px 16px;text-align:center;background:#f0fff4;border-right:1px solid #e2e8f0;">
                <i class="fa-solid fa-box-open" style="font-size:22px;color:#276749;margin-bottom:10px;display:block;"></i>
                <div style="font-size:11px;color:#4a5568;margin-bottom:6px;font-weight:600;">รับสินค้า</div>
                <div style="font-size:20px;font-weight:800;color:#276749;" id="rp-t-receive">-</div>
                <div style="font-size:10px;color:#a0aec0;margin-top:4px;">ถึงสาขา → ยืนยันรับ</div>
              </div>
              <div style="display:flex;align-items:center;padding:0 6px;background:#f7fafc;">
                <i class="fa-solid fa-chevron-right" style="color:#cbd5e0;font-size:11px;"></i>
              </div>
              <div style="flex:1;padding:20px 16px;text-align:center;background:#fef9e7;">
                <i class="fa-solid fa-clock" style="font-size:22px;color:#92400e;margin-bottom:10px;display:block;"></i>
                <div style="font-size:11px;color:#4a5568;margin-bottom:6px;font-weight:600;">รวมทั้งกระบวนการ</div>
                <div style="font-size:20px;font-weight:800;color:#92400e;" id="rp-t-total">-</div>
                <div style="font-size:10px;color:#a0aec0;margin-top:4px;">Peak Hour: <span id="rp-t-hour" style="font-weight:600;color:#718096;">-</span></div>
              </div>
            </div>

            <!-- Funnel (Conversion) -->
            <div id="rp-funnel-wrap" style="display:none;margin-top:24px;border-top:1px solid #e2e8f0;padding-top:18px;">
              <div style="font-size:13px;font-weight:700;color:#1e3a5f;margin-bottom:14px;display:flex;align-items:center;gap:7px;">
                <i class="fa-solid fa-filter" style="color:#4a90d9;font-size:13px;"></i> ออเดอร์ค้างแต่ละขั้น
                <span style="font-size:11px;color:#a0aec0;font-weight:400;">— DO แต่ละขั้นตอน (% จากทั้งหมด)</span>
              </div>
              <div id="rp-funnel-steps" style="display:flex;align-items:flex-start;gap:0;overflow-x:auto;"></div>
            </div>

            <!-- Per-Driver Timing -->
            <div id="rp-driver-timing-wrap" style="display:none;margin-top:24px;border-top:1px solid #e2e8f0;padding-top:18px;">
              <div style="font-size:13px;font-weight:700;color:#1e3a5f;margin-bottom:10px;display:flex;align-items:center;gap:7px;">
                <i class="fa-solid fa-ranking-star" style="color:#4a90d9;font-size:13px;"></i> ประสิทธิภาพรายคนขับ
              </div>
              <div id="rp-driver-timing-table"></div>
            </div>
          </div>


        </div><!-- /rp-content -->

      </div>

      <!-- USERS -->
      <div class="page-section" id="page-users">

        <!-- Tab Navigation -->
        <div style="display:flex;gap:8px;margin-bottom:16px;border-bottom:2px solid #e2e8f0;padding-bottom:0;">
          <button id="utab-list" onclick="switchUserTab('list')"
            style="padding:10px 18px;border:none;background:none;font-size:13px;font-weight:700;color:#2a5298;cursor:pointer;border-bottom:3px solid #2a5298;margin-bottom:-2px;font-family:inherit;">
            <i class="fa-solid fa-users"></i> รายชื่อผู้ใช้
          </button>
          <button id="utab-history" onclick="switchUserTab('history')"
            style="padding:10px 18px;border:none;background:none;font-size:13px;font-weight:600;color:#718096;cursor:pointer;border-bottom:3px solid transparent;margin-bottom:-2px;font-family:inherit;">
            <i class="fa-solid fa-clock-rotate-left"></i> ประวัติการเข้าใช้งาน
          </button>
        </div>

        <!-- Panel: รายชื่อผู้ใช้ -->
        <div id="upanel-list">
          <div class="card">
            <div style="display:flex;flex-wrap:wrap;align-items:center;gap:8px;margin-bottom:10px;">
              <div style="position:relative;flex:1;min-width:200px;">
                <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#a0aec0;font-size:13px;pointer-events:none;"></i>
                <input type="text" id="user-search" placeholder="ค้นหาชื่อ, รหัสพนักงาน หรือสาขา..." oninput="filterUsers()"
                  style="border:1.5px solid #e2e8f0;padding:8px 10px 8px 32px;font-size:13px;width:100%;box-sizing:border-box;">
              </div>
              <span style="font-size:13px;color:#718096;white-space:nowrap;" id="user-count">-</span>
              <button class="btn btn-success btn-sm" onclick="openCreateUser()">
                <i class="fa-solid fa-plus"></i> เพิ่มผู้ใช้
              </button>
            </div>
            <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px;">
              <button class="order-tab active" id="urole-btn-all"   onclick="setUserRoleFilter('')">      ทั้งหมด     <span class="order-tab-badge" id="urole-cnt-all">0</span></button>
              <button class="order-tab"        id="urole-btn-admin" onclick="setUserRoleFilter('admin')"> ผู้ดูแลระบบ <span class="order-tab-badge" id="urole-cnt-admin">0</span></button>
              <button class="order-tab"        id="urole-btn-hq"    onclick="setUserRoleFilter('hq')">    ครัวกลาง    <span class="order-tab-badge" id="urole-cnt-hq">0</span></button>
              <button class="order-tab"        id="urole-btn-driver" onclick="setUserRoleFilter('driver')">ไรเดอร์    <span class="order-tab-badge" id="urole-cnt-driver">0</span></button>
              <button class="order-tab"        id="urole-btn-branch" onclick="setUserRoleFilter('branch')">สาขา       <span class="order-tab-badge" id="urole-cnt-branch">0</span></button>
            </div>
            <div class="table-wrap">
              <table>
                <thead><tr><th>ID</th><th>ชื่อ</th><th>รหัสพนักงาน</th><th>เบอร์โทร</th><th>ทะเบียน</th><th>สถานะ</th><th>จัดการ</th></tr></thead>
                <tbody id="users-tbody"><tr><td colspan="7" class="loading">กำลังโหลด...</td></tr></tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Panel: ประวัติการเข้าใช้งาน -->
        <div id="upanel-history" style="display:none;">
          <div class="card">
            <div class="action-bar">
              <span style="font-size:13px;color:#718096;" id="lh-count">-</span>
            </div>
            <div id="lh-list"><div class="loading">กำลังโหลด...</div></div>
          </div>
        </div>

      </div>

      <!-- ══════════════════ CALENDAR ══════════════════ -->
      <div class="page-section" id="page-calendar">

        <!-- Summary cards -->
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px;" id="cal-summary-row">
          <div class="stat-card">
            <div class="stat-icon" style="background:#f1f5f9;color:#1e3a5f;width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">
              <i class="fa-solid fa-box"></i>
            </div>
            <div><div class="stat-value" id="cal-sum-total" style="color:#1e3a5f;">-</div><div class="stat-label">งานทั้งเดือน</div></div>
          </div>
          <div class="stat-card">
            <div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
            <div><div class="stat-value" id="cal-sum-done" style="color:#276749;">-</div><div class="stat-label">ส่งสำเร็จ</div></div>
          </div>
          <div class="stat-card">
            <div class="stat-icon blue"><i class="fa-solid fa-calendar-days"></i></div>
            <div><div class="stat-value" id="cal-sum-days" style="color:#2b6cb0;">-</div><div class="stat-label">วันที่มีงาน</div></div>
          </div>
        </div>

        <!-- Calendar card -->
        <div class="card" style="padding:16px;">

          <!-- Header: nav + HQ branch filter -->
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;flex-wrap:wrap;">
            <button onclick="calPrev()" style="border:1.5px solid #e2e8f0;background:#f8fafc;border-radius:8px;padding:7px 12px;cursor:pointer;font-size:14px;color:#4a5568;font-family:inherit;">&#8249;</button>
            <span id="cal-title" style="font-size:16px;font-weight:700;color:#1e3a5f;flex:1;text-align:center;min-width:160px;"></span>
            <button onclick="calNext()" style="border:1.5px solid #e2e8f0;background:#f8fafc;border-radius:8px;padding:7px 12px;cursor:pointer;font-size:14px;color:#4a5568;font-family:inherit;">&#8250;</button>
            <div id="cal-branch-filter" style="display:none;">
              <select id="cal-branch-sel" onchange="loadCalendar()"
                style="border:1.5px solid #e2e8f0;border-radius:8px;padding:9px 12px;font-size:14px;font-family:inherit;background:#fff;">
                <option value="">ทุกสาขา</option>
              </select>
            </div>
            <button onclick="loadCalendar()" style="border:1.5px solid #e2e8f0;background:#f8fafc;border-radius:8px;padding:7px 10px;cursor:pointer;font-size:13px;color:#718096;font-family:inherit;" title="รีเฟรช">
              <i class="fa-solid fa-rotate-right" id="cal-spin"></i>
            </button>
          </div>

          <!-- Legend -->
          <div id="cal-legend" style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:14px;font-size:11px;color:#718096;"></div>

          <!-- Day headers -->
          <div class="cal-grid" style="margin-bottom:3px;">
            <div class="cal-head">จ</div><div class="cal-head">อ</div><div class="cal-head">พ</div>
            <div class="cal-head">พฤ</div><div class="cal-head">ศ</div>
            <div class="cal-head" style="color:#e53e3e;">ส</div>
            <div class="cal-head" style="color:#e53e3e;">อา</div>
          </div>

          <!-- Calendar cells -->
          <div class="cal-grid" id="cal-body">
            <div style="grid-column:1/-1;padding:32px;text-align:center;color:#a0aec0;">กำลังโหลด...</div>
          </div>

        </div><!-- /card -->
      </div><!-- /page-calendar -->

      <!-- Day detail slide-up overlay -->
      <div class="cal-overlay" id="cal-overlay" onclick="if(event.target===this)closeCalDetail()">
        <div class="cal-sheet">
          <div class="cal-sheet-handle"></div>
          <div id="cal-detail-content"></div>
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
      <div class="form-group"><label>วันที่นัดส่ง</label>
        <div style="position:relative;">
          <input type="text" id="mo-date-text" placeholder="วว/ดด/ปปปป" readonly
            style="border:1.5px solid #e2e8f0;border-radius:8px;padding:9px 12px;font-size:14px;font-family:inherit;width:100%;box-sizing:border-box;cursor:pointer;color:#2d3748;"
            onclick="try{document.getElementById('mo-date-native').showPicker()}catch(e){}">
          <input type="date" id="mo-date-native" tabindex="-1"
            style="position:absolute;inset:0;opacity:0;pointer-events:none;"
            onchange="syncDateToText(this,'mo-date-text')">
        </div>
      </div>
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

<!-- MODAL: Driver Evidence Viewer -->
<div class="modal-overlay" id="modal-driver-evidence">
  <div class="modal" style="max-width:480px;">
    <div class="modal-title"><i class="fa-solid fa-camera"></i> หลักฐานการส่งสินค้า</div>
    <div id="driver-evidence-content" style="display:flex;flex-direction:column;gap:14px;padding:4px 0;max-height:65vh;overflow-y:auto;"></div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-driver-evidence')">ปิด</button>
    </div>
  </div>
</div>

<!-- MODAL: Order Detail -->
<div class="modal-overlay" id="modal-order-detail">
  <div class="modal" style="width:780px;max-height:92vh;overflow-y:auto;">
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
    <div class="modal-footer" style="justify-content:space-between;">
      <button class="btn btn-danger" onclick="deleteBranchFromModal()">
        <i class="fa-solid fa-trash"></i> ลบ
      </button>
      <div style="display:flex;gap:8px;">
        <button class="btn btn-secondary" onclick="closeModal('modal-edit-branch')">ยกเลิก</button>
        <button class="btn btn-primary" onclick="submitEditBranch()">
          <i class="fa-solid fa-floppy-disk"></i> บันทึก
        </button>
      </div>
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
      <div class="form-group"><label>รหัสผ่าน</label><input type="password" id="mu-pass" placeholder="อย่างน้อย 8 ตัวอักษร"></div>
      <div class="form-group"><label>ตำแหน่ง</label>
        <select id="mu-role" onchange="toggleContactFields('mu')">
          <option value="admin">แอดมิน</option>
          <option value="hq">ครัวกลาง</option>
          <option value="driver">ไรเดอร์</option>
          <option value="branch">หน้าสาขา</option>
        </select>
      </div>
    </div>
    <div id="mu-contact-section" style="display:none;border-top:2px solid #ebf4ff;margin-top:4px;padding-top:14px;">
      <div style="font-size:12px;font-weight:700;color:#2a5298;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
        <i class="fa-solid fa-address-book"></i> ข้อมูลไรเดอร์
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>เบอร์โทรศัพท์</label>
          <input type="tel" id="mu-phone" placeholder="0812345678">
        </div>
        <div class="form-group">
          <label>ทะเบียนรถ</label>
          <input type="text" id="mu-plate" placeholder="เช่น กข 1234 กรุงเทพ" style="text-transform:uppercase;">
        </div>
      </div>
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
      <div class="form-group"><label>รหัสพนักงาน</label><input type="text" id="eu-empcode" placeholder="เช่น EMP001"></div>
      <div class="form-group"><label>ตำแหน่ง</label>
        <select id="eu-role" onchange="toggleContactFields('eu')">
          <option value="admin">แอดมิน</option>
          <option value="hq">ครัวกลาง</option>
          <option value="driver">ไรเดอร์</option>
          <option value="branch">หน้าสาขา</option>
        </select>
      </div>
      <div class="form-group">
        <label>รหัสผ่าน</label>
        <input type="password" id="eu-pass" placeholder="กำหนดรหัสผ่านสำหรับล็อกอิน">
      </div>
      <div class="form-group">
        <label>สถานะการใช้งาน</label>
        <div style="display:flex;align-items:center;gap:10px;margin-top:8px;">
          <label class="toggle-switch">
            <input type="checkbox" id="eu-is-active" onchange="document.getElementById('eu-active-label').textContent=this.checked?'ใช้งาน':'ปิดการใช้งาน'">
            <span class="toggle-slider"></span>
          </label>
          <span id="eu-active-label" style="font-size:13px;color:#4a5568;">ใช้งาน</span>
        </div>
      </div>
    </div>
    <div id="eu-contact-section" style="display:none;border-top:2px solid #ebf4ff;margin-top:4px;padding-top:14px;">
      <div style="font-size:12px;font-weight:700;color:#2a5298;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
        <i class="fa-solid fa-address-book"></i> ข้อมูลไรเดอร์
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>เบอร์โทรศัพท์</label>
          <input type="tel" id="eu-phone" placeholder="0812345678">
        </div>
        <div class="form-group">
          <label>ทะเบียนรถ</label>
          <input type="text" id="eu-plate" placeholder="เช่น กข 1234 กรุงเทพ" style="text-transform:uppercase;">
        </div>
      </div>
    </div>
    <div class="modal-footer" style="justify-content:space-between;">
      <button class="btn btn-danger" id="eu-delete-btn" onclick="deleteUserFromModal()">
        <i class="fa-solid fa-trash"></i> ลบผู้ใช้
      </button>
      <div style="display:flex;gap:8px;">
        <button class="btn btn-secondary" onclick="closeModal('modal-edit-user')">ยกเลิก</button>
        <button class="btn btn-primary" onclick="submitEditUser()">
          <i class="fa-solid fa-floppy-disk"></i> บันทึก
        </button>
      </div>
    </div>
  </div>
</div>


<!-- MODAL: Return Detail (Branch) -->
<div class="modal-overlay" id="modal-return-detail">
  <div class="modal" style="width:480px;">
    <div class="modal-title"><i class="fa-solid fa-rotate-left"></i> รายละเอียดคำร้องตีกลับ</div>
    <div id="ret-modal-content"></div>
    <div class="modal-footer" style="margin-top:16px;">
      <button class="btn btn-secondary" onclick="closeModal('modal-return-detail')">ปิด</button>
    </div>
  </div>
</div>

<!-- MODAL: HQ Return Detail -->
<div class="modal-overlay" id="modal-hq-return-detail">
  <div class="modal" style="width:500px;">
    <div class="modal-title"><i class="fa-solid fa-rotate-left"></i> คำร้องตีกลับสินค้า</div>
    <div id="hq-ret-modal-content"></div>
    <div class="modal-footer" style="margin-top:16px;">
      <button class="btn btn-secondary" onclick="closeModal('modal-hq-return-detail')">ปิด</button>
    </div>
  </div>
</div>

<!-- MODAL: Receive -->

<!-- MODAL: Branch — ดูรายละเอียด DO + Timeline -->
<div class="modal-overlay" id="modal-branch-track">
  <div class="modal" style="width:700px;max-height:90vh;overflow-y:auto;">
    <div class="modal-title">
      <i class="fa-solid fa-magnifying-glass" style="color:#2a5298;"></i>
      <span id="btrack-modal-title">รายละเอียดออเดอร์</span>
    </div>

    <!-- คนขับ -->
    <div id="btrack-driver" style="margin-bottom:4px;"></div>

    <!-- รายการสินค้า -->
    <div id="btrack-items" style="margin-bottom:20px;"></div>

    <!-- Checkpoint Timeline -->
    <div style="margin-bottom:20px;">
      <div style="font-size:11px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:.8px;margin-bottom:16px;">
        <i class="fa-solid fa-route"></i> สถานะการจัดส่ง
      </div>
      <div id="btrack-timeline"></div>
    </div>

    <!-- หลักฐาน -->
    <div id="btrack-evidence"></div>

    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-branch-track')">ปิด</button>
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
        style="width:100%;border:1.5px solid #e2e8f0;border-radius:8px;padding:9px 12px;font-size:14px;font-family:inherit;background:#fff;">
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
        style="width:100%;border:1.5px solid #e2e8f0;border-radius:8px;padding:9px 12px;font-size:14px;font-family:inherit;resize:vertical;"></textarea>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-cancel-order')">ปิด</button>
      <button class="btn btn-danger" onclick="submitCancelOrder()">
        <i class="fa-solid fa-xmark"></i> ยืนยันยกเลิก
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
        style="width:100%;border:1.5px solid #e2e8f0;border-radius:8px;padding:9px 12px;font-size:14px;font-family:inherit;resize:vertical;"></textarea>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-reject-bo')">ยกเลิก</button>
      <button class="btn btn-danger" onclick="submitRejectBO()">
        <i class="fa-solid fa-xmark"></i> ยืนยันปฏิเสธ
      </button>
    </div>
  </div>
</div>

<!-- Modal: Driver รับออเดอร์ + เช็คสินค้า + ถ่ายรูปก่อนออกเดินทาง -->
<div class="modal-overlay" id="modal-accept-order">
  <div class="modal" style="width:600px;">
    <div class="modal-title">
      <i class="fa-solid fa-truck-fast" style="color:#38a169;"></i> เช็คสินค้าก่อนออกเดินทาง
    </div>
    <input type="hidden" id="aod-id">

    <!-- รายการสินค้า -->
    <div id="aod-items" style="margin-bottom:16px;"></div>

    <!-- ถ่ายรูปก่อนออก (บังคับ) -->
    <div style="margin-bottom:4px;">
      <label style="font-weight:600;font-size:13px;color:#2d3748;display:block;margin-bottom:6px;">
        <i class="fa-solid fa-camera" style="color:#e53e3e;"></i> ถ่ายรูปสินค้าก่อนออก
        <span style="color:#e53e3e;font-size:12px;margin-left:4px;">*บังคับ</span>
      </label>
      <input type="file" id="aod-photo-input" accept="image/*" multiple
        style="display:none;" onchange="previewDepartPhoto(event)">
      <div id="aod-photo-grid" style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:8px;"></div>
      <div onclick="document.getElementById('aod-photo-input').click()"
        style="border:2px dashed #cbd5e0;border-radius:10px;min-height:80px;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;background:#f7fafc;color:#a0aec0;font-size:13px;gap:6px;transition:border-color .2s;">
        <i class="fa-solid fa-camera" style="font-size:24px;"></i>
        <span id="aod-photo-hint">แตะเพื่อถ่าย / เลือกรูป</span>
      </div>
    </div>

    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="cancelAcceptOrder()">ยกเลิก</button>
      <button class="btn btn-success" id="aod-confirm-btn" onclick="submitDepart()"
        disabled style="opacity:.45;cursor:not-allowed;">
        <i class="fa-solid fa-truck-fast"></i> ยืนยันออกเดินทาง
      </button>
    </div>
  </div>
</div>

<!-- Modal: Driver — รายละเอียดออเดอร์ -->
<div class="modal-overlay" id="modal-driver-order-detail">
  <div class="modal" style="width:600px;max-height:85vh;overflow-y:auto;">
    <div class="modal-title">
      <i class="fa-solid fa-clipboard-list" style="color:#2a5298;"></i>
      <span id="dod-title">รายละเอียดออเดอร์</span>
    </div>
    <div id="dod-body" style="min-height:80px;"></div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-driver-order-detail')">ปิด</button>
    </div>
  </div>
</div>

<!-- Modal: Driver ถึงแล้ว — ยืนยันการส่งมอบ / ปฏิเสธสินค้า -->
<div class="modal-overlay" id="modal-arrive">
  <div class="modal" style="width:640px;">
    <div class="modal-title">
      <i class="fa-solid fa-location-dot" style="color:#7c3aed;"></i> ยืนยันการส่งมอบ
    </div>
    <input type="hidden" id="arr-do-id">

    <!-- DO info summary -->
    <div id="arr-do-info" style="margin-bottom:14px;"></div>

    <!-- รูปหลักฐาน (บังคับ) -->
    <div style="margin-bottom:16px;">
      <label style="font-weight:600;font-size:13px;color:#2d3748;display:block;margin-bottom:6px;">
        <i class="fa-solid fa-camera" style="color:#e53e3e;"></i> รูปหลักฐานการส่งมอบ
        <span style="color:#e53e3e;font-size:12px;margin-left:4px;">*บังคับ</span>
      </label>
      <input type="file" id="arr-photo-input" accept="image/*" multiple
        style="display:none;" onchange="previewArrivePhoto(event)">
      <div id="arr-photo-grid" style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:8px;"></div>
      <div onclick="document.getElementById('arr-photo-input').click()"
        style="border:2px dashed #cbd5e0;border-radius:10px;min-height:80px;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;background:#f7fafc;color:#a0aec0;font-size:13px;gap:6px;transition:border-color .2s;">
        <i class="fa-solid fa-camera" style="font-size:24px;"></i>
        <span id="arr-photo-hint">แตะเพื่อถ่ายรูป / เลือกรูป</span>
      </div>
    </div>

    <!-- ลายเซ็นผู้รับ (บังคับ) -->
    <div style="margin-bottom:16px;">
      <div style="font-size:12px;font-weight:700;color:#4a5568;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">
        ลายเซ็นผู้รับ <span style="color:#e53e3e;font-size:11px;">* บังคับ</span>
      </div>
      <div style="border:1.5px solid #e2e8f0;border-radius:8px;overflow:hidden;background:#fafafa;position:relative;">
        <canvas id="arr-sig-canvas" width="580" height="220"
          style="width:100%;display:block;touch-action:none;cursor:crosshair;"></canvas>
        <div id="arr-sig-hint"
          style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none;color:#cbd5e0;font-size:13px;">
          <i class="fa-solid fa-signature" style="margin-right:6px;"></i> ลากเพื่อเซ็นชื่อ
        </div>
      </div>
      <button class="btn btn-secondary" onclick="clearArriveSignature()"
        style="margin-top:6px;font-size:12px;padding:4px 12px;">
        <i class="fa-solid fa-eraser"></i> ล้างลายเซ็น
      </button>
    </div>

    <!-- ปุ่มหลัก -->
    <div id="arr-main-footer" class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-arrive')">ยกเลิก</button>
      <button class="btn btn-success" id="arr-sign-btn" onclick="submitArriveSign()"
        disabled style="opacity:.45;cursor:not-allowed;">
        <i class="fa-solid fa-circle-check"></i> ส่งสำเร็จ
      </button>
    </div>
  </div>
</div>

<!-- MODAL: Confirm Dialog (แทน confirm()) -->
<div class="modal-overlay" id="modal-confirm">
  <div class="modal" style="max-width:360px;">
    <div class="modal-title" id="confirm-modal-title"><i class="fa-solid fa-circle-question"></i> ยืนยัน</div>
    <div id="confirm-modal-message" style="color:#2d3748;line-height:1.7;margin-bottom:4px;"></div>
    <div class="modal-footer">
      <button class="btn btn-danger" id="confirm-modal-ok"><i class="fa-solid fa-check"></i> ยืนยัน</button>
    </div>
  </div>
</div>

<script>
const BASE = '';
let token = localStorage.getItem('bh_token') || '';
let currentUser = null;
let allOrders = [];
let allHqReturns = [];
let hqReturnFilter = 'pending';
let allReturnHistory = [];
let returnHistoryFilter = 'pending';
let allBranches = [];
const locationPollers = {};
let notifications = [];
let notifOpen = false;
let notifPollId = null;
let availPollId = null;
let pendingScrollDoId = null;
let currentDriverFilter = 'available';

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
  if (res.status === 401 && path !== '/api/auth/pin-login') {
    token = '';
    localStorage.removeItem('bh_token');
    document.getElementById('app').style.display = 'none';
    document.getElementById('login-screen').style.display = '';
    showToast('เซสชันหมดอายุ กรุณาล็อกอินใหม่', 'error');
  }
  return {ok: res.ok, status: res.status, data};
}

async function doEmployeeLogin() {
  const empId    = document.getElementById('login-employee-id').value.trim();
  const password = document.getElementById('login-password').value;
  const errEl    = document.getElementById('login-error');
  errEl.innerHTML = '';
  if (!empId) {
    errEl.innerHTML = '<div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> กรุณาใส่รหัสพนักงาน</div>';
    document.getElementById('login-employee-id').focus();
    return;
  }
  if (!password) {
    errEl.innerHTML = '<div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> กรุณาใส่รหัสผ่าน</div>';
    document.getElementById('login-password').focus();
    return;
  }
  const r = await api('POST', '/api/auth/employee-login', {employee_code: empId, password});
  if (r.ok) {
    token = r.data.token;
    localStorage.setItem('bh_token', token);
    initApp(r.data.user);
  } else {
    errEl.innerHTML = '<div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> ' + (r.data.message || 'รหัสพนักงานหรือรหัสผ่านไม่ถูกต้อง') + '</div>';
    document.getElementById('login-password').value = '';
    document.getElementById('login-password').focus();
  }
}

async function doQuickAccess(role) {
  const errEl = document.getElementById('login-error');
  errEl.innerHTML = '';
  const r = await api('POST', '/api/auth/quick-access', {role});
  if (r.ok) {
    token = r.data.token;
    localStorage.setItem('bh_token', token);
    initApp(r.data.user);
  } else {
    errEl.innerHTML = '<div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> ' + (r.data.message || 'ไม่พบผู้ใช้สำหรับ role นี้') + '</div>';
  }
}

function resetLoginForm() {
  document.getElementById('login-employee-id').value = '';
  document.getElementById('login-password').value = '';
  document.getElementById('login-error').innerHTML = '';
}


async function doLogout() {
  if (availPollId)  { clearInterval(availPollId);  availPollId  = null; }
  if (notifPollId)  { clearInterval(notifPollId);  notifPollId  = null; }
  if (gpsMapInterval) { clearInterval(gpsMapInterval); gpsMapInterval = null; }
  await api('POST', '/api/auth/logout');
  token = '';
  localStorage.removeItem('bh_token');
  currentUser = null;
  document.getElementById('app').style.display = 'none';
  document.getElementById('login-screen').style.display = 'flex';
  resetLoginForm();
}

function initApp(user) {
  currentUser = user;
  document.getElementById('login-screen').style.display = 'none';
  document.getElementById('app').style.display = 'block';
  document.getElementById('sidebar-name').textContent = user.name;
  document.getElementById('sidebar-role').textContent = roleTH(user.role);
  document.getElementById('avatar-initial').textContent = user.name[0].toUpperCase();
  buildNav(user.role);
  if (['admin', 'hq', 'driver', 'branch'].includes(user.role)) {
    document.getElementById('notif-wrap').style.display = 'block';
    loadNotifications();
    if (notifPollId) clearInterval(notifPollId);
    notifPollId = setInterval(loadNotifications, user.role === 'driver' ? 30000 : 60000);
    if (user.role === 'driver') {
      if (availPollId) clearInterval(availPollId);
      // poll available orders ทุก 30s เพื่อให้ขึ้นทันทีเมื่อ HQ สร้างออเดอร์ใหม่
      availPollId = setInterval(loadDriverDashboard, 30000);
    }
  }
  // Load global branding (logo + system name)
  api('GET', '/api/settings').then(r => {
    if (!r.ok) return;
    if (r.data.system_name) {
      const nameEl = document.getElementById('sidebar-system-name');
      if (nameEl) nameEl.textContent = r.data.system_name;
      document.title = r.data.system_name + ' — ระบบบริหารจัดการขนส่ง';
    }
    if (r.data.logo_base64) applySiteLogo(r.data.logo_base64);
  });

  if (user.role === 'branch') navigate('track');
  else if (user.role === 'driver') navigate('calendar');
  else navigate('orders');

  setupPushNotifications();
}

const NAV_ITEMS = {
  calendar:         {icon:'fa-calendar-days',      label:'ปฏิทิน',            roles:['admin','hq','branch','driver']},
  dashboard:        {icon:'fa-house',              label:'หน้ารับงาน',         roles:['driver']},
  'driver-history': {icon:'fa-clock-rotate-left', label:'ออเดอร์ที่ผ่านมา', roles:['driver']},
  'create-order':   {icon:'fa-plus-circle',        label:'สร้างออเดอร์',      roles:['hq']},
  orders:           {icon:'fa-box',                label:'ออเดอร์ทั้งหมด',    roles:['admin','hq']},
  rejected:  {icon:'fa-rotate-left',   label:'สินค้าตีกลับ',    roles:['admin','hq']},
  track:     {icon:'fa-magnifying-glass', label:'ติดตามสินค้า', roles:['branch']},
  returns:          {icon:'fa-rotate-left',        label:'ตีกลับสินค้า',  roles:['branch']},
  'return-history': {icon:'fa-clock-rotate-left',  label:'ประวัติคำร้อง',  roles:['branch']},
  gps:       {icon:'fa-location-dot',  label:'ติดตามคนขับ', roles:['admin','hq']},
  branches:  {icon:'fa-building',      label:'สาขา',            roles:['admin','hq']},
  reports:   {icon:'fa-chart-bar',     label:'รายงาน',          roles:['admin','hq']},
  users:     {icon:'fa-users',         label:'จัดการผู้ใช้',    roles:['admin']},
  settings:  {icon:'fa-gear',          label:'ตั้งค่าระบบ',      roles:['admin']},
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

function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('sidebar-overlay').classList.toggle('open');
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sidebar-overlay').classList.remove('open');
}

function navigate(page) {
  closeSidebar();
  document.querySelectorAll('.page-section').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  const sec = document.getElementById('page-' + page);
  const nav = document.getElementById('nav-' + page);
  if (sec) sec.classList.add('active');
  if (nav) nav.classList.add('active');
  const titles = {
    dashboard:'หน้ารับงาน', orders:'ออเดอร์ทั้งหมด', 'create-order':'สร้างออเดอร์จัดส่งสินค้า', receive:'รับสินค้า', settings:'ตั้งค่าระบบ',
    rejected:'สินค้าตีกลับ', track:'ติดตามสินค้า', gps:'ติดตามคนขับ', branches:'สาขา',
    reports:'รายงาน', users:'จัดการผู้ใช้', returns:'ตีกลับสินค้า', 'return-history':'ประวัติคำร้อง', 'driver-history':'ออเดอร์ที่ผ่านมา',
    calendar:'ปฏิทิน'
  };
  document.getElementById('page-title').textContent = titles[page] || page;
  if (page === 'orders') { orderActiveStatus = ''; orderTabSetByUser = false; }
  const loaders = {dashboard:loadDashboard, orders:loadOrders, 'create-order':initCreateOrderPage, branches:loadBranches, users:loadUsers, reports:()=>{}, gps:initGpsPage, rejected:loadHqReturns, track:loadTrackOrders, returns:loadReturns, 'return-history':loadReturnHistory, 'driver-history':loadDriverHistory, settings:loadSettingsPage, calendar:initCalendarPage};
  if (loaders[page]) loaders[page]();
}

/* ---- DASHBOARD ---- */
let dashboardLoading = false;

async function loadDashboard() {
  if (dashboardLoading) return;
  dashboardLoading = true;
  const btn = document.getElementById('btn-dashboard-refresh');
  if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-rotate-right fa-spin"></i>'; }

  const resetBtn = () => {
    dashboardLoading = false;
    if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-rotate-right"></i>'; }
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
    <div class="stat-card" style="cursor:pointer;" onclick="navigateToOrders('completed')"><div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div><div><div class="stat-value">${(d.completed||0)+(d.delivered||0)}</div><div class="stat-label">ส่งสำเร็จ</div></div></div>
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
          <thead><tr><th>สินค้า</th><th>สาขา</th><th>สถานะ</th><th>วันที่นัดส่ง</th></tr></thead>
          <tbody>${recent.map(o => `<tr>
            <td><b>${itemsSummary(o.items)}</b><br><span style="font-size:11px;color:#a0aec0;">${o.do_number}</span></td>
            <td>${orderBranchDisplay(o)}</td>
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
  const statGrid = document.getElementById('stat-grid');
  const recentEl = document.getElementById('recent-orders');

  // โหลด available orders และ assigned orders พร้อมกัน
  const [availRes, assignedRes] = await Promise.all([
    api('GET', '/api/driver/available-orders'),
    api('GET', '/api/driver/orders'),
  ]);

  if (!assignedRes.ok) {
    statGrid.innerHTML = '';
    recentEl.innerHTML = `<div class="empty-state"><i class="fa-solid fa-circle-exclamation"></i><p>โหลดข้อมูลไม่สำเร็จ</p></div>`;
    return;
  }

  const available = availRes.ok ? (availRes.data || []) : [];
  const orders    = assignedRes.data || [];
  const pending   = orders.filter(o => o.status === 'pending');
  const active    = orders.filter(o => o.status !== 'pending');
  const inTransit = active.filter(o => o.status === 'in_transit');
  const done      = active.filter(o => o.status !== 'in_transit');
  const nTransit  = inTransit.length;
  const nDone     = orders.filter(o => ['delivered','completed'].includes(o.status)).length;

  statGrid.innerHTML = `
    <div class="stat-card"><div class="stat-icon blue"><i class="fa-solid fa-box"></i></div><div><div class="stat-value">${orders.length}</div><div class="stat-label">ออเดอร์ทั้งหมด</div></div></div>
    <div class="stat-card"><div class="stat-icon yellow"><i class="fa-solid fa-hourglass-half"></i></div><div><div class="stat-value">${pending.length}</div><div class="stat-label">รอส่ง</div></div></div>
    <div class="stat-card"><div class="stat-icon blue"><i class="fa-solid fa-truck"></i></div><div><div class="stat-value">${nTransit}</div><div class="stat-label">กำลังส่ง</div></div></div>
    <div class="stat-card"><div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div><div><div class="stat-value">${nDone}</div><div class="stat-label">ส่งแล้ว</div></div></div>`;

  // เรียง: กำลังส่ง → รอส่ง → ใหม่ → ส่งแล้ว
  const allActive = [...available, ...pending, ...inTransit];

  const filterBar = `<div id="driver-status-filters" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px;">
    ${[
      {f:'available', label:'ใหม่',     count: available.length},
      {f:'pending',   label:'รอส่ง',    count: pending.length},
      {f:'transit',   label:'กำลังส่ง', count: inTransit.length},
    ].map(b => {
      const isActive = b.f === currentDriverFilter;
      return `<button data-filter="${b.f}" onclick="filterDriverOrders('${b.f}')"
        class="driver-filter-btn order-tab${isActive ? ' active' : ''}"
        style="${isActive ? 'background:#1e3a5f;color:#fff;' : ''}">
        ${b.label} <span class="order-tab-badge">${b.count}</span>
      </button>`;
    }).join('')}
  </div>`;

  let html = '';
  if (allActive.length === 0) {
    html = `<div style="text-align:center;padding:24px 16px;color:#a0aec0;background:#f7fafc;border-radius:10px;border:1.5px dashed #e2e8f0;">
      <i class="fa-solid fa-satellite-dish" style="font-size:22px;margin-bottom:8px;display:block;color:#cbd5e0;"></i>
      กำลังรอออเดอร์ใหม่จาก HQ
      <span style="background:#38a169;color:#fff;font-size:10px;padding:2px 7px;border-radius:10px;font-weight:700;margin-left:6px;vertical-align:middle;">● LIVE</span>
    </div>`;
  } else {
    html = allActive.map(o => {
      let type;
      if (o.dispatched_at) type = 'transit';
      else if (o.driver_id) type = 'pending';
      else type = 'available';
      return renderDriverOrderCard(o, type);
    }).join('');
  }

  recentEl.innerHTML = filterBar + html;
  filterDriverOrders(currentDriverFilter);

  // Auto-restart GPS ถ้ามีออเดอร์ in_transit แต่ยังไม่ได้ track (เช่น reload หน้า)
  if (inTransit.length > 0 && !isTracking) {
    await fetchDriverVehicleId();
    if (driverVehicleId) startTracking();
  }

  // โหลด ETA สำหรับออเดอร์กำลังส่ง (async หลัง render)
  if (currentUser?.id && inTransit.length > 0) {
    for (const o of inTransit) {
      api('GET', '/api/gps/distance/' + currentUser.id).then(dr => {
        const etaEl = document.getElementById('eta-' + o.id);
        if (!etaEl) return;
        if (dr.ok && dr.data?.eta_minutes != null) {
          etaEl.innerHTML = `<i class="fa-solid fa-clock fa-sm" style="color:#276749;"></i> ETA: <b style="color:#276749;">~${dr.data.eta_minutes} นาที</b>`;
        } else {
          etaEl.innerHTML = `<i class="fa-solid fa-route fa-sm"></i> ETA: <span style="color:#a0aec0;">ไม่มีข้อมูล GPS</span>`;
        }
      });
    }
  }

  if (pendingScrollDoId) {
    const scrollId = pendingScrollDoId;
    pendingScrollDoId = null;
    setTimeout(() => {
      filterDriverOrders('available');
      const el = document.querySelector(`[data-do-id="${scrollId}"]`);
      if (el) {
        el.scrollIntoView({behavior:'smooth', block:'center'});
        el.classList.add('highlight-pulse');
        setTimeout(() => el.classList.remove('highlight-pulse'), 2000);
      }
    }, 80);
  }
}

async function refreshDriverCard(doId) {
  const r = await api('GET', '/api/delivery-orders/' + doId);
  if (!r.ok) { loadDriverDashboard(); return; }
  const o = r.data;
  let type;
  if (['completed','cancelled','rejected'].includes(o.status)) type = 'done';
  else if (o.status === 'in_transit') type = 'transit';
  else if (o.driver_id && o.dispatched_at === null) type = 'pending';
  else if (o.driver_id) type = 'pending';
  else type = 'available';

  const el = document.getElementById('order-card-' + doId);
  if (!el) { loadDriverDashboard(); return; }

  if (type === 'done') {
    el.style.transition = 'opacity .3s';
    el.style.opacity = '0';
    setTimeout(() => el.remove(), 300);
  } else {
    const temp = document.createElement('div');
    temp.innerHTML = renderDriverOrderCard(o, type).trim();
    el.replaceWith(temp.firstElementChild);
    filterDriverOrders(currentDriverFilter);
    if (type === 'transit' && currentUser?.id) {
      api('GET', '/api/gps/distance/' + currentUser.id).then(dr => {
        const etaEl = document.getElementById('eta-' + doId);
        if (!etaEl) return;
        if (dr.ok && dr.data?.eta_minutes != null) {
          etaEl.innerHTML = `<i class="fa-solid fa-clock fa-sm" style="color:#276749;"></i> ETA: <b style="color:#276749;">~${dr.data.eta_minutes} นาที</b>`;
        } else {
          etaEl.innerHTML = `<i class="fa-solid fa-route fa-sm"></i> ETA: <span style="color:#a0aec0;">ไม่มีข้อมูล GPS</span>`;
        }
      });
    }
  }
}

function driverDisplay(driver) {
  if (!driver) return '<span style="color:#a0aec0">-</span>';
  return `<span>${driver.name}</span>`;
}
function driverPlate(driver) {
  if (!driver?.vehicle_plate) return '<span style="color:#a0aec0">-</span>';
  return `<span style="font-weight:700;">${driver.vehicle_plate}</span>`;
}

function orderBranchDisplay(o) {
  if (o.stops && o.stops.length > 0) {
    return [...o.stops]
      .sort((a,b) => (a.sequence||0)-(b.sequence||0))
      .map(s => s.branch?.name || '-')
      .join(' → ');
  }
  return o.branch?.name || '-';
}

function itemsSummary(items) {
  if (!items || !items.length) return 'ไม่มีรายการ';
  const first = items[0];
  const firstText = `${first.product_name} × ${first.qty_ordered}${first.unit ? ' '+first.unit : ''}`;
  if (items.length === 1) return firstText;
  const moreTag = `<span style="display:inline-block;background:#e2e8f0;color:#4a5568;font-size:10px;border-radius:10px;padding:1px 8px;margin-left:5px;font-weight:700;vertical-align:middle;">+อีก ${items.length-1} รายการ</span>`;
  return firstText + moreTag;
}

function filterDriverOrders(type) {
  currentDriverFilter = type;
  const search  = (document.getElementById('driver-dashboard-search')?.value || '').toLowerCase().trim();
  const dateVal = document.getElementById('driver-dashboard-date')?.value || '';
  document.querySelectorAll('.driver-filter-btn').forEach(b => {
    const isActive = b.dataset.filter === type;
    b.classList.toggle('active', isActive);
    b.style.background = isActive ? '#1e3a5f' : '';
    b.style.color      = isActive ? '#fff'    : '';
  });
  document.querySelectorAll('[data-order-type]').forEach(el => {
    const typeMatch   = el.dataset.orderType === type;
    const searchMatch = !search  || (el.dataset.search || '').includes(search);
    const dateMatch   = !dateVal || (el.dataset.date   || '') === dateVal;
    el.style.display  = (typeMatch && searchMatch && dateMatch) ? '' : 'none';
  });
}

function shortTime(iso) {
  if (!iso) return '–';
  const d = new Date(iso);
  if (isNaN(d)) return '–';
  return d.toLocaleTimeString('th-TH', {hour:'2-digit', minute:'2-digit'});
}

function driverTimeline(o, type) {
  const step1Done = type !== 'available';
  const step2Done = type === 'transit' || type === 'done';
  const step2Active = type === 'transit';
  const step3Done = type === 'done';

  const steps = [
    {icon:'fa-box',          label:'รับออเดอร์', done:step1Done, active:false,       time: step1Done ? 'รับแล้ว' : '–'},
    {icon:'fa-truck-fast',   label:'เริ่มส่ง',   done:step2Done, active:step2Active,  time: shortTime(o.dispatched_at)},
    {icon:'fa-circle-check', label:'ส่งสำเร็จ',  done:step3Done, active:false,       time: shortTime(o.arrived_at)},
  ];

  return steps.map((s, i) => {
    const dotBg    = s.done ? '#38a169' : s.active ? '#4299e1' : '#e2e8f0';
    const iconCol  = (s.done || s.active) ? '#fff' : '#a0aec0';
    const labelCol = s.done ? '#276749' : s.active ? '#2a5298' : '#a0aec0';
    const shadow   = s.done ? '0 2px 6px rgba(56,161,105,.35)' : s.active ? '0 0 0 4px rgba(66,153,225,.25)' : 'none';
    const lineLColor = i === 0 ? 'transparent' : (steps[i-1].done ? '#38a169' : '#e2e8f0');
    const lineRColor = i === steps.length-1 ? 'transparent' : (s.done ? '#38a169' : '#e2e8f0');
    return `
      <div style="flex:1;display:flex;flex-direction:column;align-items:center;">
        <div style="display:flex;align-items:center;width:100%;">
          <div style="flex:1;height:3px;background:${lineLColor};border-radius:2px;"></div>
          <div style="width:40px;height:40px;border-radius:50%;background:${dotBg};display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:${shadow};">
            <i class="fa-solid ${s.icon}" style="color:${iconCol};font-size:14px;"></i>
          </div>
          <div style="flex:1;height:3px;background:${lineRColor};border-radius:2px;"></div>
        </div>
        <div style="font-size:11px;font-weight:700;color:${labelCol};margin-top:7px;text-align:center;">${s.label}</div>
        <div style="font-size:11px;color:#718096;margin-top:2px;">${s.time}</div>
      </div>`;
  }).join('');
}

function renderOrderDestinations(o) {
  if (o.stops && o.stops.length > 0) {
    const sorted = [...o.stops].sort((a,b) => (a.sequence||0)-(b.sequence||0));
    return `<div style="margin-top:6px;">` + sorted.map((s,i) => {
      const done = stopIsDone(s);
      return `<div style="font-size:12px;color:${done?'#a0aec0':'#4a5568'};margin-top:${i===0?0:3}px;display:flex;align-items:center;gap:5px;">
        <span style="background:${done?'#e2e8f0':'#ebf8ff'};color:${done?'#a0aec0':'#2a5298'};border-radius:50%;width:16px;height:16px;display:inline-flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0;">${s.sequence||i+1}</span>
        <span style="${done?'text-decoration:line-through;':''}">${s.branch?.name||'-'}</span>
        ${done?'<i class="fa-solid fa-check" style="color:#38a169;font-size:10px;"></i>':''}
      </div>`;
    }).join('') + `</div>`;
  }
  return `<div style="font-size:13px;color:#4a5568;margin-top:5px;"><i class="fa-solid fa-store fa-sm"></i> ${o.branch?.name||'-'}</div>`;
}

async function viewDriverOrderDetail(id) {
  document.getElementById('dod-title').textContent = 'กำลังโหลด...';
  document.getElementById('dod-body').innerHTML = '<div style="text-align:center;padding:24px;color:#a0aec0;"><i class="fa-solid fa-spinner fa-spin"></i></div>';
  document.getElementById('modal-driver-order-detail').classList.add('open');

  const r = await api('GET', '/api/delivery-orders/' + id);
  if (!r.ok) {
    document.getElementById('dod-body').innerHTML = '<div style="color:#e53e3e;padding:16px;">โหลดข้อมูลไม่สำเร็จ</div>';
    return;
  }
  const o = r.data;
  document.getElementById('dod-title').textContent = o.do_number;

  const isMultiStop = o.stops && o.stops.length > 0;
  const sortedStops = isMultiStop ? [...o.stops].sort((a,b) => (a.sequence||0)-(b.sequence||0)) : [];
  const isRejected  = o.sub_status === 'rejected';


  // ---- timeline ----
  let tlNodes = [];
  if (isMultiStop) {
    tlNodes.push({ type:'fixed', icon:'fa-file-plus', label:'สร้างออเดอร์', ts: o.created_at,    done: true,             photoKey: null });
    tlNodes.push({ type:'fixed', icon:'fa-truck',     label:'ออกเดินทาง',   ts: o.dispatched_at, done: !!o.dispatched_at, photoKey: 'depart' });
    sortedStops.forEach(s => {
      tlNodes.push({ type:'stop', stop: s, done: stopIsDone(s), hasProblem: s.sub_status === 'problem' || s.status === 'problem' });
    });
  } else {
    tlNodes = [
      { type:'fixed', icon:'fa-file-plus',    label:'สร้างออเดอร์', ts: o.created_at,    done: true,             photoKey: null },
      { type:'fixed', icon:'fa-truck',        label:'ออกเดินทาง',   ts: o.dispatched_at, done: !!o.dispatched_at, photoKey: 'depart' },
      { type:'fixed', icon:'fa-location-dot', label:'ถึงสาขา',      ts: o.arrived_at,    done: !!o.arrived_at,    photoKey: null },
      { type:'fixed', icon: isRejected ? 'fa-xmark' : 'fa-circle-check',
        label: isRejected ? 'ปฏิเสธรับ' : 'ส่งสำเร็จ',
        ts: o.received_at, done: !!o.received_at, danger: isRejected, photoKey: 'arrive' },
    ];
  }
  const tlReversed = [...tlNodes].reverse();
  const tlHtml = `<div style="margin:18px 0 6px;border-top:1.5px solid #e2e8f0;padding-top:16px;">
    <div style="font-size:11px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:.8px;margin-bottom:8px;"><i class="fa-solid fa-clock-rotate-left"></i> ความเคลื่อนไหว</div>
    ${tlReversed.map((node, i) => {
      const isLast = i === tlReversed.length - 1;
      if (node.type === 'stop') {
        const s = node.stop;
        const dotBorder = node.done ? '#38a169' : node.hasProblem ? '#fc8181' : '#cbd5e0';
        const dotBg     = node.done ? '#c6f6d5' : node.hasProblem ? '#fff5f5' : '#f7fafc';
        const iColor    = node.done ? '#276749' : node.hasProblem ? '#c53030' : '#a0aec0';
        const timeColor = node.done ? '#276749' : node.hasProblem ? '#e53e3e' : '#a0aec0';
        const lineColor = node.done && !isLast ? '#38a169' : '#e2e8f0';
        const badge     = node.done
          ? `<span style="font-size:10px;background:#c6f6d5;color:#276749;padding:1px 8px;font-weight:700;"><i class="fa-solid fa-circle-check"></i> ส่งแล้ว</span>`
          : node.hasProblem
          ? `<span style="font-size:10px;background:#fff5f5;color:#c53030;padding:1px 8px;font-weight:700;"><i class="fa-solid fa-triangle-exclamation"></i> มีปัญหา</span>`
          : `<span style="font-size:10px;background:#e2e8f0;color:#718096;padding:1px 8px;font-weight:700;"><i class="fa-solid fa-clock"></i> รอส่ง</span>`;
        const suid   = 'dod-' + o.id + '-stop-' + s.id;
        const sItems = s.items || [];
        const hasEv  = s.arrive_photo_path || s.signature;
        const itemRows = sItems.map(it => {
          const sh = it.shortage_qty || 0;
          return `<tr style="border-top:1px solid #f0f4f8;">
            <td style="padding:3px 8px;">${it.product_name}</td>
            <td style="padding:3px 8px;text-align:center;">${it.qty_ordered}${sh > 0 ? `<span style="color:#c05621;font-weight:700;"> (-${sh})</span>` : ''}</td>
            <td style="padding:3px 8px;">${it.unit}</td>
          </tr>`;
        }).join('');
        return `<div style="display:flex;align-items:stretch;">
          <div style="width:76px;flex-shrink:0;text-align:right;padding-right:10px;padding-top:10px;">
            ${s.delivered_at ? `<div style="font-size:10px;color:${timeColor};font-weight:700;line-height:1.5;">${formatDateTime(s.delivered_at).replace(' ','\n').split('\n').join('<br>')}</div>` : `<div style="font-size:11px;color:#cbd5e0;">—</div>`}
          </div>
          <div style="display:flex;flex-direction:column;align-items:center;width:40px;flex-shrink:0;">
            <div style="width:2px;background:${i === 0 ? 'transparent' : '#e2e8f0'};flex:${i === 0 ? '0 0 10px' : '1'};min-height:10px;"></div>
            <div style="width:34px;height:34px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:${dotBg};border:2.5px solid ${dotBorder};">
              <i class="fa-solid fa-store" style="font-size:12px;color:${iColor};"></i>
            </div>
            <div style="width:2px;background:${isLast ? 'transparent' : lineColor};flex:${isLast ? '0 0 10px' : '1'};min-height:10px;"></div>
          </div>
          <div style="padding:8px 0 14px 12px;flex:1;min-width:0;">
            <div style="display:flex;align-items:center;flex-wrap:wrap;gap:5px;margin-bottom:6px;">
              <span style="width:18px;height:18px;background:#2a5298;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:10px;font-weight:700;flex-shrink:0;">${s.sequence}</span>
              <span style="font-size:13px;font-weight:700;color:${node.done ? '#1e3a5f' : '#718096'};">${s.branch?.name || '-'}</span>
              ${badge}
            </div>
            ${sItems.length > 0 ? `<table style="width:100%;font-size:12px;border-collapse:collapse;margin-bottom:6px;background:#fafafa;border:1px solid #e2e8f0;">
              <thead><tr style="background:#f0f4f8;">
                <th style="padding:4px 8px;text-align:left;font-weight:600;color:#4a5568;">สินค้า</th>
                <th style="padding:4px 8px;text-align:center;font-weight:600;color:#4a5568;width:70px;">จำนวน</th>
                <th style="padding:4px 8px;text-align:left;font-weight:600;color:#4a5568;width:50px;">หน่วย</th>
              </tr></thead>
              <tbody>${itemRows}</tbody>
            </table>` : ''}
            ${hasEv ? `<button onclick="var el=document.getElementById('${suid}');el.style.display=el.style.display==='none'?'flex':'none';" style="background:#f0fff4;border:1px solid #c6f6d5;color:#276749;padding:4px 12px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:5px;"><i class="fa-solid fa-images"></i> ดูหลักฐาน</button>
            <div id="${suid}" style="display:none;gap:12px;flex-wrap:wrap;margin-top:8px;">
              ${s.arrive_photo_path ? `<div><div style="font-size:11px;font-weight:700;color:#4a5568;margin-bottom:5px;">รูปหลังส่ง</div><div style="display:flex;flex-wrap:wrap;gap:6px;">${renderPhotoGrid(parsePhotoPaths(s.arrive_photo_path),'arrive-photos')}</div><div style="font-size:10px;color:#a0aec0;margin-top:3px;">กดที่รูปเพื่อดูเต็มจอ</div></div>` : ''}
              ${s.signature ? `<div><div style="font-size:11px;font-weight:700;color:#4a5568;margin-bottom:5px;">ลายเซ็น</div><img src="${s.signature}" style="max-width:160px;max-height:100px;border:1.5px solid #e2e8f0;background:#fff;padding:3px;display:block;cursor:zoom-in;" onclick="openPhotoViewer(this.src,'ลายเซ็น — ${s.branch?.name || ''}')"><div style="font-size:10px;color:#a0aec0;margin-top:3px;">กดที่รูปเพื่อดูเต็มจอ</div></div>` : ''}
            </div>` : ''}
          </div>
        </div>`;
      }
      const p = node;
      const dotBorder = p.done ? (p.danger ? '#fc8181' : '#38a169') : '#cbd5e0';
      const dotBg     = p.done ? (p.danger ? '#fff5f5' : '#c6f6d5') : '#f7fafc';
      const txtColor  = p.done ? (p.danger ? '#c53030' : '#1e3a5f') : '#a0aec0';
      const timeColor = p.done ? (p.danger ? '#e53e3e' : '#276749') : '#a0aec0';
      const lineColor = p.done && !isLast ? '#38a169' : '#e2e8f0';
      const uid = 'dod-' + o.id + '-' + p.photoKey;
      let photoBlock = '';
      if (p.photoKey === 'depart' && o.depart_photo_path) {
        photoBlock = `<div style="margin-top:6px;">
          <button onclick="var el=document.getElementById('${uid}');el.style.display=el.style.display==='none'?'block':'none';" style="background:#ebf8ff;border:1px solid #bee3f8;color:#2a5298;padding:4px 12px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:5px;"><i class="fa-solid fa-camera"></i> ดูหลักฐาน</button>
          <div id="${uid}" style="display:none;margin-top:8px;">
            <div style="display:flex;flex-wrap:wrap;gap:6px;">${renderPhotoGrid(parsePhotoPaths(o.depart_photo_path),'depart-photos')}</div>
            <div style="font-size:10px;color:#a0aec0;margin-top:3px;">กดที่รูปเพื่อดูเต็มจอ</div>
          </div>
        </div>`;
      } else if (p.photoKey === 'arrive') {
        const rr = o.receive_record;
        const hasArrive = !!o.arrive_photo_path;
        const hasSig    = !!(rr && rr.signature);
        if (hasArrive || hasSig) {
          photoBlock = `<div style="margin-top:6px;">
            <button onclick="var el=document.getElementById('${uid}');el.style.display=el.style.display==='none'?'flex':'none';" style="background:#f0fff4;border:1px solid #c6f6d5;color:#276749;padding:4px 12px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:5px;"><i class="fa-solid fa-images"></i> ดูหลักฐาน</button>
            <div id="${uid}" style="display:none;gap:12px;flex-wrap:wrap;margin-top:8px;">
              ${hasArrive ? `<div><div style="font-size:11px;font-weight:700;color:#4a5568;margin-bottom:5px;">รูปหลังส่ง</div><div style="display:flex;flex-wrap:wrap;gap:6px;">${renderPhotoGrid(parsePhotoPaths(o.arrive_photo_path),'arrive-photos')}</div><div style="font-size:10px;color:#a0aec0;margin-top:3px;">กดที่รูปเพื่อดูเต็มจอ</div></div>` : ''}
              ${hasSig ? `<div><div style="font-size:11px;font-weight:700;color:#4a5568;margin-bottom:5px;">ลายเซ็น</div><img src="${rr.signature}" style="max-width:180px;max-height:120px;border:1.5px solid #e2e8f0;background:#fff;padding:3px;display:block;cursor:zoom-in;" onclick="openPhotoViewer(this.src,'ลายเซ็น')"><div style="font-size:10px;color:#a0aec0;margin-top:3px;">กดที่รูปเพื่อดูเต็มจอ</div></div>` : ''}
            </div>
          </div>`;
        } else if (p.done) {
          photoBlock = `<div style="margin-top:5px;display:inline-flex;align-items:center;gap:5px;background:#fff5f5;border:1px solid #fed7d7;color:#c53030;padding:3px 10px;font-size:11px;font-weight:700;border-radius:6px;"><i class="fa-solid fa-triangle-exclamation"></i> ไม่พบหลักฐานการส่ง</div>`;
        }
      }
      return `<div style="display:flex;align-items:stretch;">
        <div style="width:76px;flex-shrink:0;text-align:right;padding-right:10px;padding-top:10px;">
          ${p.ts ? `<div style="font-size:10px;color:${timeColor};font-weight:700;line-height:1.5;">${formatDateTime(p.ts).replace(' ','\n').split('\n').join('<br>')}</div>` : `<div style="font-size:11px;color:#cbd5e0;">—</div>`}
        </div>
        <div style="display:flex;flex-direction:column;align-items:center;width:40px;flex-shrink:0;">
          <div style="width:2px;background:${i === 0 ? 'transparent' : '#e2e8f0'};flex:${i === 0 ? '0 0 10px' : '1'};min-height:10px;"></div>
          <div style="width:34px;height:34px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:${dotBg};border:2.5px solid ${dotBorder};">
            <i class="fa-solid ${p.icon}" style="font-size:13px;color:${p.done ? (p.danger ? '#c53030' : '#276749') : '#a0aec0'};"></i>
          </div>
          <div style="width:2px;background:${isLast ? 'transparent' : lineColor};flex:${isLast ? '0 0 10px' : '1'};min-height:10px;"></div>
        </div>
        <div style="padding:8px 0 12px 12px;flex:1;min-width:0;">
          <div style="font-size:13px;font-weight:700;color:${txtColor};padding-top:2px;">${p.label}</div>
          ${photoBlock}
        </div>
      </div>`;
    }).join('')}
  </div>`;

  document.getElementById('dod-body').innerHTML = `
    <div class="info-grid">
      <div class="info-item"><span class="label">ต้นทาง</span><span class="val"><i class="fa-solid fa-warehouse" style="color:#38a169;font-size:11px;margin-right:4px;"></i>${coSettings.hq_name}</span></div>
      <div class="info-item"><span class="label">ปลายทาง</span><span class="val">${orderBranchDisplay(o)}</span></div>
      <div class="info-item"><span class="label">สถานะ</span><span class="val">
        <span class="badge badge-${o.status}">${statusTH(o.status)}</span>
        ${o.sub_status ? `<span class="badge badge-${o.sub_status}" style="margin-left:4px;">${subStatusTH(o.sub_status)}</span>` : ''}
      </span></div>
      <div class="info-item"><span class="label">คนขับ</span><span class="val">${driverDisplay(o.driver)}</span></div>
      <div class="info-item"><span class="label">ทะเบียน</span><span class="val">${driverPlate(o.driver)}</span></div>
      <div class="info-item"><span class="label">วันที่นัดส่ง</span><span class="val">${formatDate(o.scheduled_date)}</span></div>
      <div class="info-item"><span class="label">ส่งเมื่อ</span><span class="val">${formatDateTime(o.received_at)}</span></div>
    </div>
    ${tlHtml}`;
  setTimeout(initLocationNodes, 0);
}

function stopIsDone(s) {
  return ['delivered','completed'].includes(s.status);
}

async function fetchLocationNode(uid, driverId) {
  const el = document.getElementById(uid);
  if (!el) return;
  try {
    const r = await api('GET', '/api/gps/driver/' + driverId);
    if (!r.ok || !r.data?.length) { el.textContent = 'ไม่พบข้อมูลตำแหน่ง'; return; }
    const latest = [...r.data].sort((a,b) => new Date(b.created_at) - new Date(a.created_at))[0];
    const lat = latest.lat ?? latest.latitude;
    const lng = latest.lng ?? latest.longitude ?? latest.long;
    if (lat == null || lng == null) { el.textContent = 'ไม่พบพิกัด'; return; }
    let label = `${Number(lat).toFixed(4)}, ${Number(lng).toFixed(4)}`;
    try {
      const geo = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=th&zoom=14`);
      if (geo.ok) {
        const gd = await geo.json();
        const a  = gd.address || {};
        const parts = [
          a.suburb || a.neighbourhood || a.quarter || a.village || a.hamlet,
          a.county || a.city_district || a.district,
          a.city   || a.town || a.state_district || a.state
        ].filter(Boolean);
        label = parts.slice(0,2).join(', ') || gd.display_name?.split(',').slice(0,2).join(',') || label;
      }
    } catch(e) {}
    el.innerHTML = `<i class="fa-solid fa-location-dot" style="color:#3182ce;font-size:10px;"></i> ${label}`;
  } catch(e) {
    el.textContent = 'ระบุตำแหน่งไม่ได้';
  }
}

function startLocationPolling(uid, driverId) {
  if (locationPollers[uid]) return;
  locationPollers[uid] = setInterval(() => {
    const el = document.getElementById(uid);
    if (!el) { stopLocationPolling(uid); return; }
    fetchLocationNode(uid, driverId);
  }, 15000);
}

function stopLocationPolling(uid) {
  if (locationPollers[uid]) { clearInterval(locationPollers[uid]); delete locationPollers[uid]; }
}

function initLocationNodes() {
  document.querySelectorAll('[data-location-driver]').forEach(el => {
    if (el.dataset.fetched) return;
    el.dataset.fetched = '1';
    const uid = el.dataset.locationNode;
    const driverId = el.dataset.locationDriver;
    fetchLocationNode(uid, driverId);
    startLocationPolling(uid, driverId);
  });
}

function getNavTarget(o) {
  if (o.stops && o.stops.length > 0) {
    const sorted = [...o.stops].sort((a,b) => (a.sequence||0)-(b.sequence||0));
    const next = sorted.find(s => !stopIsDone(s)) || sorted[0];
    return {lat: next.branch?.lat, lng: next.branch?.lng, name: next.branch?.name};
  }
  return {lat: o.branch?.lat, lng: o.branch?.lng, name: o.branch?.name};
}

function openNavigation(lat, lng) {
  if (!lat || !lng) { showToast('ไม่มีข้อมูลพิกัดสาขา กรุณาตั้งค่าพิกัดในระบบก่อน', 'error'); return; }
  const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
  if (isIOS) {
    // window.location.href เปิด Maps app โดยไม่ navigate ออกจาก PWA
    window.location.href = `maps://maps.apple.com/?daddr=${lat},${lng}&dirflg=d`;
  } else {
    window.open(`https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`, '_blank', 'noopener,noreferrer');
  }
}

function renderDriverOrderCard(o, type) {
  const boTag = o.is_backorder
    ? `<span style="font-size:10px;background:#e2e8f0;padding:1px 6px;border-radius:4px;color:#718096;font-weight:600;margin-left:4px;">BO</span>`
    : '';

  const styleMap = {
    available: 'background:#f0fff4;border:2px solid #68d391;',
    pending:   'background:#fffbeb;border:2px solid #f6e05e;',
    transit:   'background:#ebf8ff;border:2px solid #63b3ed;',
    done:      'background:#f7fafc;border:1.5px solid #e2e8f0;',
  };
  const badgeClassMap = {available:'badge-pending', pending:'badge-pending', transit:'badge-in_transit', done:'badge-completed'};
  const badgeLabelMap = {available:'รอรับงาน', pending:'รอส่ง', transit:'กำลังส่ง', done:'ส่งสำเร็จ'};
  const cardIdPrefix  = 'order';

  let actionBtn = '';
  if (type === 'available') {
    actionBtn = `<button class="btn btn-success" id="accept-btn-${o.id}" onclick="event.stopPropagation();acceptOrder(${o.id}, this)"
      style="width:100%;font-size:15px;padding:12px;border-radius:8px;font-weight:700;margin-top:14px;">
      <i class="fa-solid fa-hand-pointer"></i>&nbsp; รับออเดอร์
    </button>`;
  } else if (type === 'pending') {
    // คนขับยกเลิกได้เฉพาะออเดอร์ที่ตัวเองรับ (driver_id ตรง)
    const _driverCanCancel = o.driver_id && o.driver_id === currentUser?.id;
    actionBtn = `<div style="margin-top:14px;display:flex;flex-direction:column;gap:8px;">
      <button class="btn btn-warning" onclick="event.stopPropagation();openDepartModal(${o.id})"
        style="width:100%;font-size:15px;padding:12px;border-radius:8px;font-weight:700;">
        <i class="fa-solid fa-truck-fast"></i>&nbsp; เริ่มส่ง
      </button>
      ${_driverCanCancel ? `<button class="btn btn-secondary" onclick="event.stopPropagation();openCancelModal(${o.id})"
        style="width:100%;font-size:13px;padding:8px;border-radius:8px;font-weight:600;color:#e53e3e;border-color:#feb2b2;background:#fff5f5;">
        <i class="fa-solid fa-xmark"></i> ยกเลิก (ส่งไม่ได้)
      </button>` : ''}
    </div>`;
  } else if (type === 'transit') {
    const nav = getNavTarget(o);
    const nextStop = (o.stops && o.stops.length > 0)
      ? [...o.stops].sort((a,b) => (a.sequence||0)-(b.sequence||0)).find(s => !stopIsDone(s))
      : null;
    const nextStopId = nextStop ? nextStop.id : 'null';
    const hasCoords = !!(nav.lat && nav.lng);
    const navLat = nav.lat || 'null';
    const navLng = nav.lng || 'null';
    const navBtn = `<button onclick="event.stopPropagation();openNavigation(${navLat}, ${navLng})"
         style="width:100%;background:${hasCoords?'#276749':'#a0aec0'};color:#fff;border:none;border-radius:8px;padding:11px;font-size:14px;font-weight:700;cursor:pointer;margin-bottom:8px;display:flex;align-items:center;justify-content:center;gap:8px;opacity:${hasCoords?1:0.7};">
         <i class="fa-solid fa-map-location-dot"></i> นำทางไป ${nav.name||'สาขา'}
       </button>`;
    actionBtn = `<div style="margin-top:14px;" onclick="event.stopPropagation()">
      <div id="eta-${o.id}" style="text-align:center;font-size:12px;color:#718096;margin-bottom:8px;">
        <i class="fa-solid fa-clock fa-sm"></i> ETA: <span style="color:#a0aec0;">กำลังคำนวณ...</span>
      </div>
      ${navBtn}
      <button onclick="event.stopPropagation();openArriveModal(${o.id}, ${nextStopId})"
        style="width:100%;background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;border:none;border-radius:8px;padding:12px;font-size:15px;font-weight:700;cursor:pointer;">
        <i class="fa-solid fa-location-dot"></i>&nbsp; ถึงแล้ว
      </button>
    </div>`;
  }

  const searchText = [o.do_number, o.branch?.name || '', (o.items||[]).map(i => i.product_name).join(' ')].join(' ').toLowerCase();
  const dateVal = (o.scheduled_date || '').slice(0, 10);
  return `
    <div id="${cardIdPrefix}-card-${o.id}" data-do-id="${o.id}" data-order-type="${type}" data-search="${searchText}" data-date="${dateVal}" style="${styleMap[type]||''}border-radius:14px;padding:16px;margin-bottom:14px;cursor:pointer;" onclick="viewDriverOrderDetail(${o.id})">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px;">
        <div style="min-width:0;flex:1;">
          <div style="font-weight:700;font-size:15px;color:#2d3748;line-height:1.3;">${itemsSummary(o.items)}${boTag}</div>
          <div style="font-size:11px;color:#a0aec0;margin-top:1px;">${o.do_number}</div>
          ${renderOrderDestinations(o)}
          <div style="font-size:12px;color:#718096;margin-top:3px;">
            <i class="fa-regular fa-calendar fa-sm"></i> นัดส่ง ${formatDate(o.scheduled_date)}
          </div>
        </div>
        <span class="badge ${badgeClassMap[type]}" style="flex-shrink:0;margin-left:8px;">${badgeLabelMap[type]}</span>
      </div>
      <div style="background:rgba(255,255,255,0.65);border-radius:10px;padding:14px 6px 10px;">
        <div style="display:flex;align-items:flex-start;">
          ${driverTimeline(o, type)}
        </div>
      </div>
      ${actionBtn}
    </div>`;
}

let _driverHistoryOrders = [];

async function loadDriverHistory() {
  const el = document.getElementById('driver-history-list');
  const countEl = document.getElementById('driver-history-count');
  if (!el) return;
  el.innerHTML = '<div class="loading">กำลังโหลด...</div>';
  const r = await api('GET', '/api/driver/orders');
  if (!r.ok) {
    el.innerHTML = '<div class="empty-state"><i class="fa-solid fa-circle-exclamation"></i><p>โหลดข้อมูลไม่สำเร็จ</p></div>';
    return;
  }
  // รวมออเดอร์เสร็จแล้ว + in_transit ที่มี stop ส่งแล้ว (เพื่อแสดงทันทีต่อ stop)
  const allOrders = r.data || [];
  const done = allOrders.filter(o => {
    if (!['pending','in_transit'].includes(o.status)) return true;
    if (o.status === 'in_transit' && o.stops?.some(s => stopIsDone(s))) return true;
    return false;
  });
  done.sort((a, b) => new Date(b.received_at || b.delivered_at || b.created_at) - new Date(a.received_at || a.delivered_at || a.created_at));
  _driverHistoryOrders = done;
  if (countEl) countEl.textContent = `ประวัติ ${done.length} ออเดอร์`;
  if (!done.length) {
    el.innerHTML = '<div class="empty-state"><i class="fa-solid fa-inbox"></i><p>ยังไม่มีประวัติการส่ง</p></div>';
    return;
  }
  el.innerHTML = renderDriverHistoryCards(done);
}

function renderDriverHistoryCards(orders) {
  if (!orders.length) return '<div class="empty-state"><i class="fa-solid fa-magnifying-glass"></i><p>ไม่พบรายการ</p></div>';

  function buildTimelineHtml(o) {
    const isMultiStop = o.stops && o.stops.length > 0;
    const sortedStops = isMultiStop ? [...o.stops].sort((a,b) => (a.sequence||0)-(b.sequence||0)) : [];
    const isRejected  = o.sub_status === 'rejected';
    let tlNodes = [];
    if (isMultiStop) {
      tlNodes.push({ type:'fixed', icon:'fa-file-plus', label:'สร้างออเดอร์', ts: o.created_at,    done: true,             photoKey: null });
      tlNodes.push({ type:'fixed', icon:'fa-truck',     label:'ออกเดินทาง',   ts: o.dispatched_at, done: !!o.dispatched_at, photoKey: 'depart' });
      sortedStops.forEach(s => tlNodes.push({ type:'stop', stop: s, done: stopIsDone(s), hasProblem: s.sub_status==='problem'||s.status==='problem' }));
    } else {
      tlNodes = [
        { type:'fixed', icon:'fa-file-plus',    label:'สร้างออเดอร์', ts: o.created_at,    done: true,             photoKey: null },
        { type:'fixed', icon:'fa-truck',        label:'ออกเดินทาง',   ts: o.dispatched_at, done: !!o.dispatched_at, photoKey: 'depart' },
        { type:'fixed', icon:'fa-location-dot', label:'ถึงสาขา',      ts: o.arrived_at,    done: !!o.arrived_at,    photoKey: null },
        { type:'fixed', icon: isRejected?'fa-xmark':'fa-circle-check',
          label: isRejected?'ปฏิเสธรับ':'ส่งสำเร็จ',
          ts: o.received_at, done: !!o.received_at, danger: isRejected, photoKey: 'arrive' },
      ];
    }
    return [...tlNodes].reverse().map((node, i, arr) => {
      const isLast = i === arr.length - 1;
      if (node.type === 'stop') {
        const s = node.stop;
        const dotBorder = node.done ? '#38a169' : node.hasProblem ? '#fc8181' : '#cbd5e0';
        const dotBg     = node.done ? '#c6f6d5' : node.hasProblem ? '#fff5f5' : '#f7fafc';
        const iColor    = node.done ? '#276749' : node.hasProblem ? '#c53030' : '#a0aec0';
        const timeColor = node.done ? '#276749' : node.hasProblem ? '#e53e3e' : '#a0aec0';
        const lineColor = node.done && !isLast ? '#38a169' : '#e2e8f0';
        const badge     = node.done
          ? `<span style="font-size:10px;background:#c6f6d5;color:#276749;padding:1px 8px;font-weight:700;"><i class="fa-solid fa-circle-check"></i> ส่งแล้ว</span>`
          : node.hasProblem
          ? `<span style="font-size:10px;background:#fff5f5;color:#c53030;padding:1px 8px;font-weight:700;"><i class="fa-solid fa-triangle-exclamation"></i> มีปัญหา</span>`
          : `<span style="font-size:10px;background:#e2e8f0;color:#718096;padding:1px 8px;font-weight:700;"><i class="fa-solid fa-clock"></i> รอส่ง</span>`;
        const uid    = 'dh-' + o.id + '-stop-' + s.id;
        const sItems = s.items || [];
        const hasEv  = s.arrive_photo_path || s.signature;
        const itemRows = sItems.map(it => {
          const sh = it.shortage_qty || 0;
          return `<tr style="border-top:1px solid #f0f4f8;"><td style="padding:3px 8px;">${it.product_name}</td><td style="padding:3px 8px;text-align:center;">${it.qty_ordered}${sh>0?`<span style="color:#c05621;font-weight:700;"> (-${sh})</span>`:''}</td><td style="padding:3px 8px;">${it.unit}</td></tr>`;
        }).join('');
        return `<div style="display:flex;align-items:stretch;">
          <div style="width:72px;flex-shrink:0;text-align:right;padding-right:10px;padding-top:10px;">
            ${s.delivered_at?`<div style="font-size:10px;color:${timeColor};font-weight:700;line-height:1.5;">${formatDateTime(s.delivered_at).replace(' ','<br>')}</div>`:`<div style="font-size:11px;color:#cbd5e0;">—</div>`}
          </div>
          <div style="display:flex;flex-direction:column;align-items:center;width:36px;flex-shrink:0;">
            <div style="width:2px;background:${i===0?'transparent':'#e2e8f0'};flex:${i===0?'0 0 10px':'1'};min-height:10px;"></div>
            <div style="width:32px;height:32px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:${dotBg};border:2.5px solid ${dotBorder};"><i class="fa-solid fa-store" style="font-size:12px;color:${iColor};"></i></div>
            <div style="width:2px;background:${isLast?'transparent':lineColor};flex:${isLast?'0 0 10px':'1'};min-height:10px;"></div>
          </div>
          <div style="padding:6px 0 14px 10px;flex:1;min-width:0;">
            <div style="display:flex;align-items:center;flex-wrap:wrap;gap:5px;margin-bottom:5px;">
              <span style="width:18px;height:18px;background:#2a5298;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:10px;font-weight:700;flex-shrink:0;">${s.sequence}</span>
              <span style="font-size:13px;font-weight:700;color:${node.done?'#1e3a5f':'#718096'};">${s.branch?.name||'-'}</span>
              ${badge}
            </div>
            ${sItems.length>0?`<table style="width:100%;font-size:12px;border-collapse:collapse;margin-bottom:6px;background:#fafafa;border:1px solid #e2e8f0;"><thead><tr style="background:#f0f4f8;"><th style="padding:4px 8px;text-align:left;font-weight:600;color:#4a5568;">สินค้า</th><th style="padding:4px 8px;text-align:center;font-weight:600;color:#4a5568;width:65px;">จำนวน</th><th style="padding:4px 8px;text-align:left;font-weight:600;color:#4a5568;width:45px;">หน่วย</th></tr></thead><tbody>${itemRows}</tbody></table>`:''}
            ${hasEv?`<button onclick="event.stopPropagation();var el=document.getElementById('${uid}');el.style.display=el.style.display==='none'?'flex':'none';" style="background:#f0fff4;border:1px solid #c6f6d5;color:#276749;padding:4px 12px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:5px;"><i class="fa-solid fa-images"></i> ดูหลักฐาน</button>
            <div id="${uid}" style="display:none;gap:12px;flex-wrap:wrap;margin-top:8px;">
              ${s.arrive_photo_path?`<div><div style="font-size:11px;font-weight:700;color:#4a5568;margin-bottom:5px;">รูปหลังส่ง</div><div style="display:flex;flex-wrap:wrap;gap:6px;">${renderPhotoGrid(parsePhotoPaths(s.arrive_photo_path),'arrive-photos')}</div><div style="font-size:10px;color:#a0aec0;margin-top:3px;">กดที่รูปเพื่อดูเต็มจอ</div></div>`:''}
              ${s.signature?`<div><div style="font-size:11px;font-weight:700;color:#4a5568;margin-bottom:5px;">ลายเซ็น</div><img src="${s.signature}" style="max-width:150px;max-height:100px;border:1.5px solid #e2e8f0;background:#fff;padding:3px;display:block;cursor:zoom-in;" onclick="event.stopPropagation();openPhotoViewer(this.src,'ลายเซ็น — ${s.branch?.name||''}')"><div style="font-size:10px;color:#a0aec0;margin-top:3px;">กดที่รูปเพื่อดูเต็มจอ</div></div>`:''}
            </div>`:node.done?`<div style="margin-top:5px;display:inline-flex;align-items:center;gap:5px;background:#fff5f5;border:1px solid #fed7d7;color:#c53030;padding:3px 10px;font-size:11px;font-weight:700;border-radius:6px;"><i class="fa-solid fa-triangle-exclamation"></i> ไม่พบหลักฐาน</div>`:''}
          </div>
        </div>`;
      }
      const p = node;
      const dotBorder = p.done ? (p.danger ? '#fc8181' : '#38a169') : '#cbd5e0';
      const dotBg     = p.done ? (p.danger ? '#fff5f5' : '#c6f6d5') : '#f7fafc';
      const txtColor  = p.done ? (p.danger ? '#c53030' : '#1e3a5f') : '#a0aec0';
      const timeColor = p.done ? (p.danger ? '#e53e3e' : '#276749') : '#a0aec0';
      const lineColor = p.done && !isLast ? '#38a169' : '#e2e8f0';
      const uid = 'dh-' + o.id + '-' + p.photoKey;
      let photoBlock = '';
      if (p.photoKey === 'depart' && o.depart_photo_path) {
        photoBlock = `<div style="margin-top:6px;"><button onclick="event.stopPropagation();var el=document.getElementById('${uid}');el.style.display=el.style.display==='none'?'block':'none';" style="background:#ebf8ff;border:1px solid #bee3f8;color:#2a5298;padding:4px 12px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:5px;"><i class="fa-solid fa-camera"></i> ดูหลักฐาน</button>
          <div id="${uid}" style="display:none;margin-top:8px;"><div style="display:flex;flex-wrap:wrap;gap:6px;">${renderPhotoGrid(parsePhotoPaths(o.depart_photo_path),'depart-photos')}</div><div style="font-size:10px;color:#a0aec0;margin-top:3px;">กดที่รูปเพื่อดูเต็มจอ</div></div></div>`;
      } else if (p.photoKey === 'arrive') {
        const rr = o.receive_record;
        const hasArrive = !!o.arrive_photo_path;
        const hasSig    = !!(rr && rr.signature);
        if (hasArrive || hasSig) {
          photoBlock = `<div style="margin-top:6px;"><button onclick="event.stopPropagation();var el=document.getElementById('${uid}');el.style.display=el.style.display==='none'?'flex':'none';" style="background:#f0fff4;border:1px solid #c6f6d5;color:#276749;padding:4px 12px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:5px;"><i class="fa-solid fa-images"></i> ดูหลักฐาน</button>
            <div id="${uid}" style="display:none;gap:12px;flex-wrap:wrap;margin-top:8px;">
              ${hasArrive?`<div><div style="font-size:11px;font-weight:700;color:#4a5568;margin-bottom:5px;">รูปหลังส่ง</div><div style="display:flex;flex-wrap:wrap;gap:6px;">${renderPhotoGrid(parsePhotoPaths(o.arrive_photo_path),'arrive-photos')}</div><div style="font-size:10px;color:#a0aec0;margin-top:3px;">กดที่รูปเพื่อดูเต็มจอ</div></div>`:''}
              ${hasSig?`<div><div style="font-size:11px;font-weight:700;color:#4a5568;margin-bottom:5px;">ลายเซ็น</div><img src="${rr.signature}" style="max-width:150px;max-height:100px;border:1.5px solid #e2e8f0;background:#fff;padding:3px;display:block;cursor:zoom-in;" onclick="event.stopPropagation();openPhotoViewer(this.src,'ลายเซ็น')"><div style="font-size:10px;color:#a0aec0;margin-top:3px;">กดที่รูปเพื่อดูเต็มจอ</div></div>`:''}
            </div></div>`;
        } else if (p.done) {
          photoBlock = `<div style="margin-top:5px;display:inline-flex;align-items:center;gap:5px;background:#fff5f5;border:1px solid #fed7d7;color:#c53030;padding:3px 10px;font-size:11px;font-weight:700;border-radius:6px;"><i class="fa-solid fa-triangle-exclamation"></i> ไม่พบหลักฐานการส่ง</div>`;
        }
      }
      return `<div style="display:flex;align-items:stretch;">
        <div style="width:72px;flex-shrink:0;text-align:right;padding-right:10px;padding-top:10px;">
          ${p.ts?`<div style="font-size:10px;color:${timeColor};font-weight:700;line-height:1.5;">${formatDateTime(p.ts).replace(' ','<br>')}</div>`:`<div style="font-size:11px;color:#cbd5e0;">—</div>`}
        </div>
        <div style="display:flex;flex-direction:column;align-items:center;width:36px;flex-shrink:0;">
          <div style="width:2px;background:${i===0?'transparent':'#e2e8f0'};flex:${i===0?'0 0 10px':'1'};min-height:10px;"></div>
          <div style="width:32px;height:32px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:${dotBg};border:2.5px solid ${dotBorder};"><i class="fa-solid ${p.icon}" style="font-size:13px;color:${p.done?(p.danger?'#c53030':'#276749'):'#a0aec0'};"></i></div>
          <div style="width:2px;background:${isLast?'transparent':lineColor};flex:${isLast?'0 0 10px':'1'};min-height:10px;"></div>
        </div>
        <div style="padding:6px 0 12px 10px;flex:1;min-width:0;">
          <div style="font-size:13px;font-weight:700;color:${txtColor};padding-top:2px;">${p.label}</div>
          ${photoBlock}
        </div>
      </div>`;
    }).join('');
  }

  const rows = orders.map(o => {
    const isMultiStop  = o.stops && o.stops.length > 0;
    const sortedStops  = isMultiStop ? [...o.stops].sort((a,b) => (a.sequence||0)-(b.sequence||0)) : [];
    const branchLabel  = isMultiStop
      ? sortedStops.map(s => s.branch?.name || '-').join(', ')
      : (o.branch?.name || '-');
    const doLabel      = `<div style="font-size:10px;color:#a0aec0;">${o.do_number}${o.is_backorder?' <span style="color:#718096;">[BO]</span>':''}</div><div style="font-weight:700;">${branchLabel}</div>`;
    const statusBadge  = `<span class="badge badge-${o.status}">${statusTH(o.status)}</span>`;
    const detailId     = 'dh-detail-' + o.id;
    const chevronId    = 'dh-ch-' + o.id;
    const toggle       = `var r=document.getElementById('${detailId}');var ch=document.getElementById('${chevronId}');var open=r.style.display==='none';r.style.display=open?'table-row':'none';ch.style.transform=open?'rotate(180deg)':'rotate(0deg)';`;
    return `
      <tr onclick="${toggle}" style="cursor:pointer;">
        <td>${doLabel}</td>
        <td>${statusBadge}</td>
        <td>${formatDate(o.scheduled_date)}</td>
        <td style="text-align:center;width:32px;"><i id="${chevronId}" class="fa-solid fa-chevron-down" style="font-size:12px;color:#a0aec0;transition:transform .2s;"></i></td>
      </tr>
      <tr id="${detailId}" style="display:none;">
        <td colspan="4" style="padding:0;background:#f9fafb;border-top:none;">
          <div style="padding:14px 16px;border-top:2px solid #4a90d9;">
            <div style="font-size:11px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:.7px;margin-bottom:10px;"><i class="fa-solid fa-clock-rotate-left" style="color:#4a90d9;"></i> ความเคลื่อนไหว</div>
            ${buildTimelineHtml(o)}
          </div>
        </td>
      </tr>`;
  }).join('');

  return `<div class="table-wrap"><table>
    <thead><tr>
      <th>สาขา / ออเดอร์</th>
      <th>สถานะ</th>
      <th>วันนัดส่ง</th>
      <th></th>
    </tr></thead>
    <tbody>${rows}</tbody>
  </table></div>`;
}

function filterDriverHistory() {
  const search  = (document.getElementById('driver-history-search')?.value || '').toLowerCase().trim();
  const dateVal = document.getElementById('driver-history-date')?.value || '';
  const el = document.getElementById('driver-history-list');
  if (!el) return;
  const filtered = _driverHistoryOrders.filter(o => {
    const branches = o.stops?.length ? o.stops.map(s => s.branch?.name || '') : [o.branch?.name || ''];
    const items    = o.stops?.length ? o.stops.flatMap(s => s.items || []) : (o.items || []);
    const textMatch = !search ||
      (o.do_number || '').toLowerCase().includes(search) ||
      branches.some(b => b.toLowerCase().includes(search)) ||
      items.some(i => (i.product_name || '').toLowerCase().includes(search));
    const dateMatch = !dateVal || (o.scheduled_date || '').slice(0, 10) === dateVal;
    return textMatch && dateMatch;
  });
  el.innerHTML = renderDriverHistoryCards(filtered);
}

function viewDriverEvidence(id, stopId = null) {
  const o = _driverHistoryOrders.find(x => x.id === id);
  if (!o) return;
  const content = document.getElementById('driver-evidence-content');

  // ─── ระบุว่าเปิดดูหลักฐานของสาขาไหน ───
  const stop = stopId ? (o.stops || []).find(s => s.id == stopId) : null;
  const branchName = stop ? (stop.branch?.name || '-') : (o.branch?.name || '-');
  const items = stop ? (stop.items || []) : (o.items || []);

  const itemRows = items.map(it => `
    <tr>
      <td style="padding:4px 8px;">${it.product_name || '-'}</td>
      <td style="padding:4px 8px;text-align:right;">${it.qty_ordered} ${it.unit || ''}</td>
    </tr>`).join('');

  const infoBlock = `
    <div style="background:#f7fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px 14px;margin-bottom:12px;">
      <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
        <span style="font-size:12px;color:#718096;">เลขที่ออเดอร์</span>
        <span style="font-size:12px;font-weight:600;color:#2d3748;">${o.do_number}</span>
      </div>
      <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
        <span style="font-size:12px;color:#718096;">สาขา</span>
        <span style="font-size:12px;color:#2d3748;">${branchName}${stop ? ` <span style="font-size:10px;background:#ebf8ff;color:#2a5298;border-radius:8px;padding:1px 5px;">จุดที่ ${stop.sequence}</span>` : ''}</span>
      </div>
      <div style="display:flex;justify-content:space-between;margin-bottom:${items.length ? '10px' : '0'};">
        <span style="font-size:12px;color:#718096;">วันที่นัดส่ง</span>
        <span style="font-size:12px;color:#2d3748;">${formatDate(o.scheduled_date)}</span>
      </div>
      ${items.length ? `
      <div style="font-size:12px;font-weight:600;color:#4a5568;margin-bottom:6px;">รายการสินค้า</div>
      <table style="width:100%;border-collapse:collapse;font-size:12px;">
        <thead><tr style="border-bottom:1px solid #e2e8f0;">
          <th style="padding:4px 8px;text-align:left;color:#718096;font-weight:500;">สินค้า</th>
          <th style="padding:4px 8px;text-align:right;color:#718096;font-weight:500;">จำนวน</th>
        </tr></thead>
        <tbody>${itemRows}</tbody>
      </table>` : ''}
    </div>`;

  // รูปถึงสาขา (stop-specific หรือ order-level สำหรับ single-stop)
  const arrivePhotoPath = stop ? stop.arrive_photo_path : o.arrive_photo_path;
  // ลายเซ็น
  const signatureSrc = stop ? stop.signature : o.receive_record?.signature;

  let photoBlocks = '';

  // ─── รูปเมื่อถึงสาขา ───
  if (arrivePhotoPath) {
    photoBlocks += `<div style="margin-bottom:12px;">
      <div style="font-size:12px;font-weight:600;color:#4a5568;margin-bottom:6px;">รูปเมื่อถึงสาขา</div>
      <img src="/storage/arrive-photos/${arrivePhotoPath}" style="width:100%;border-radius:8px;border:1px solid #e2e8f0;display:block;" onerror="this.parentElement.style.display='none'">
    </div>`;
  }

  // receive_record photos (single-stop เท่านั้น)
  if (!stop && o.receive_record?.photos?.length) {
    o.receive_record.photos.forEach((p, i) => {
      photoBlocks += `<div style="margin-bottom:12px;">
        <div style="font-size:12px;font-weight:600;color:#4a5568;margin-bottom:6px;">หลักฐานรับสินค้า ${i+1}</div>
        <img src="/storage/${p.photo_path}" style="width:100%;border-radius:8px;border:1px solid #e2e8f0;display:block;" onerror="this.parentElement.style.display='none'">
      </div>`;
    });
  }

  // ─── สรุปการส่ง: รูปก่อนออก + ลายเซ็น (แสดงเสมอ) ───
  const _dPaths = parsePhotoPaths(o.depart_photo_path);
  const departImg = _dPaths.length
    ? `<div style="display:flex;flex-wrap:wrap;gap:6px;">${renderPhotoGrid(_dPaths,'depart-photos')}</div>`
    : `<div style="background:#f7fafc;border:1px dashed #cbd5e0;border-radius:8px;padding:16px;text-align:center;font-size:12px;color:#a0aec0;">ไม่มีรูปก่อนออก</div>`;
  const signImg = signatureSrc
    ? `<img src="${signatureSrc}" style="width:100%;max-width:260px;border-radius:6px;border:1px solid #e2e8f0;background:#fff;padding:4px;" onerror="this.parentElement.style.display='none'">`
    : `<div style="background:#f7fafc;border:1px dashed #cbd5e0;border-radius:8px;padding:16px;text-align:center;font-size:12px;color:#a0aec0;">ไม่มีลายเซ็น</div>`;

  photoBlocks += `
    <div style="border-top:1.5px solid #e2e8f0;margin-top:4px;padding-top:12px;">
      <div style="font-size:12px;font-weight:700;color:#2d3748;margin-bottom:10px;">สรุปการส่ง</div>
      <div style="margin-bottom:10px;">
        <div style="font-size:11px;color:#718096;margin-bottom:5px;">รูปสินค้าก่อนออก</div>
        ${departImg}
      </div>
      <div>
        <div style="font-size:11px;color:#718096;margin-bottom:5px;">ลายเซ็นผู้รับ</div>
        ${signImg}
      </div>
    </div>`;

  content.innerHTML = infoBlock + photoBlocks;
  document.getElementById('modal-driver-evidence').classList.add('open');
}

async function renderTransitTable(orders) {
  const el = document.getElementById('transit-table');
  if (!orders.length) {
    el.innerHTML = `<div class="empty-state"><i class="fa-solid fa-truck"></i><p>ไม่มีออเดอร์ที่กำลังส่ง</p></div>`;
    return;
  }
  el.innerHTML = `<div class="table-wrap"><table>
    <thead><tr><th>สินค้า</th><th>สาขาปลายทาง</th><th>คนขับ</th><th>ระยะทาง (กม.)</th><th>ETA (นาที)</th><th>จัดการ</th></tr></thead>
    <tbody>${orders.map(o => `<tr>
      <td><b>${itemsSummary(o.items)}</b><br><span style="font-size:11px;color:#a0aec0;">${o.do_number}</span></td>
      <td>${orderBranchDisplay(o)}</td>
      <td>${driverDisplay(o.driver)}</td>
      <td id="db-dist-${o.id}" style="font-size:12px;">${o.driver_id ? '<span style="color:#a0aec0">...</span>' : '-'}</td>
      <td id="db-eta-${o.id}"  style="font-size:12px;">${o.driver_id ? '<span style="color:#a0aec0">...</span>' : '-'}</td>
      <td>${o.driver_id && currentUser?.role !== 'branch' ? `<button class="btn btn-primary btn-sm" onclick="navigateToGps(${o.driver_id})"><i class="fa-solid fa-location-dot"></i> ติดตาม</button>` : '-'}</td>
    </tr>`).join('')}</tbody>
  </table></div>`;

  for (const o of orders) {
    if (!o.driver_id) continue;
    const dr = await api('GET', '/api/gps/distance/' + o.driver_id);
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
  el.innerHTML = list.map(n => {
    const iconMap = {
      do_assigned:    'fa-truck',
      do_new:         'fa-box-open',
      do_dispatched:  'fa-truck-fast',
      bo_assigned:    'fa-truck',
      bo_approved:    'fa-truck',
      driver_arrival: 'fa-location-dot',
      rejection_alert:'fa-triangle-exclamation',
      bo_created:     'fa-box',
      broadcast:      'fa-bullhorn',
      return_request: 'fa-rotate-left',
      return_approved:'fa-circle-check',
      return_rejected:'fa-circle-xmark',
    };
    const colorMap = {
      do_assigned:    '#3182ce',
      do_new:         '#3182ce',
      do_dispatched:  '#7c3aed',
      bo_assigned:    '#3182ce',
      bo_approved:    '#38a169',
      driver_arrival: '#7c3aed',
      rejection_alert:'#d69e2e',
      bo_created:     '#e53e3e',
      broadcast:      '#d97706',
      return_request: '#e53e3e',
      return_approved:'#38a169',
      return_rejected:'#e53e3e',
    };
    const icon  = iconMap[n.type]  || 'fa-bell';
    const color = colorMap[n.type] || '#718096';
    const isDriver = currentUser?.role === 'driver';
    const isBranch = currentUser?.role === 'branch';
    const hint = (!n.is_read && n.do_id && (isDriver || isBranch))
      ? `<div style="font-size:11px;color:${color};margin-top:3px;"><i class="fa-solid fa-arrow-right"></i> แตะเพื่อดูรายละเอียด</div>`
      : '';
    return `
    <div class="notif-item ${n.is_read ? '' : 'unread'}" onclick="clickNotif(${n.id},${n.do_id || 'null'})">
      <div class="notif-msg"><i class="fa-solid ${icon}" style="color:${color};margin-right:4px;"></i>${n.message}</div>
      <div class="notif-time">${formatDateTime(n.created_at)}</div>
      ${hint}
    </div>`;
  }).join('');
}

function toggleNotifDropdown() {
  notifOpen = !notifOpen;
  document.getElementById('notif-dropdown').style.display = notifOpen ? 'block' : 'none';
  if (notifOpen) updatePushButton();
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
  driverVehicleId = currentUser?.id || null;
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
  refreshDriverCard(doId);
}

// ── Driver: รับออเดอร์ใหม่ ──────────────────────────────────────────
let _departPhotos = [];

async function acceptOrder(doId, btnEl) {
  if (btnEl) { btnEl.disabled = true; btnEl.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> กำลังรับ...'; }

  const r = await api('POST', '/api/delivery-orders/' + doId + '/accept-order');
  if (!r.ok) {
    showToast(r.data?.message || 'รับออเดอร์ไม่สำเร็จ', 'error');
    if (btnEl) { btnEl.disabled = false; btnEl.innerHTML = '<i class="fa-solid fa-hand-pointer"></i>&nbsp; รับออเดอร์'; }
    return;
  }
  // เปลี่ยน card เป็น pending in-place ก่อนเปิด modal
  const el = document.getElementById('order-card-' + doId);
  if (el) {
    const temp = document.createElement('div');
    temp.innerHTML = renderDriverOrderCard(r.data, 'pending').trim();
    el.replaceWith(temp.firstElementChild);
  }
  _openAcceptModal(r.data);
}

async function openDepartModal(doId) {
  const r = await api('GET', '/api/delivery-orders/' + doId);
  if (r.ok) _openAcceptModal(r.data);
  else showToast('โหลดออเดอร์ไม่สำเร็จ', 'error');
}

function _openAcceptModal(order) {
  _departPhotos = [];
  document.getElementById('aod-id').value = order.id;

  // ปลายทาง
  const destHtml = (() => {
    if (order.stops && order.stops.length > 0) {
      const sorted = [...order.stops].sort((a,b) => (a.sequence||0)-(b.sequence||0));
      return `<div style="margin-bottom:12px;background:#ebf8ff;border:1px solid #bee3f8;border-radius:8px;padding:10px 14px;">
        <div style="font-weight:600;font-size:13px;color:#2a5298;margin-bottom:6px;"><i class="fa-solid fa-route"></i> เส้นทางจัดส่ง (${sorted.length} จุด)</div>
        ${sorted.map((s,i) => `<div style="font-size:13px;color:#2d3748;padding:3px 0;display:flex;align-items:center;gap:6px;">
          <span style="background:#2a5298;color:#fff;border-radius:50%;width:18px;height:18px;display:inline-flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0;">${s.sequence||i+1}</span>
          <span>${s.branch?.name||'-'}</span>
        </div>`).join('')}
      </div>`;
    }
    return `<div style="margin-bottom:12px;background:#ebf8ff;border:1px solid #bee3f8;border-radius:8px;padding:10px 14px;">
      <div style="font-size:13px;color:#2d3748;"><i class="fa-solid fa-store" style="color:#2a5298;"></i> <b>ปลายทาง:</b> ${order.branch?.name||'-'}</div>
    </div>`;
  })();

  // รายการสินค้า
  const items = order.items || [];
  document.getElementById('aod-items').innerHTML = destHtml + `
    <div style="font-weight:600;font-size:13px;color:#2d3748;margin-bottom:8px;">
      <i class="fa-solid fa-boxes-stacked" style="color:#38a169;"></i> รายการสินค้า (${items.length} รายการ)
    </div>
    <div style="background:#f0fff4;border:1px solid #9ae6b4;border-radius:8px;padding:10px 14px;">
      ${items.length ? items.map(i => `
        <div style="display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid #c6f6d5;font-size:13px;">
          <span style="color:#2d3748;">${i.product_name}</span>
          <span style="font-weight:700;color:#276749;">${i.qty_ordered} ${i.unit||''}</span>
        </div>`).join('') : '<div style="color:#a0aec0;font-size:13px;">ไม่มีรายการสินค้า</div>'}
    </div>`;

  // reset รูป
  _departPhotos = [];
  document.getElementById('aod-photo-input').value = '';
  document.getElementById('aod-photo-grid').innerHTML = '';
  const btn = document.getElementById('aod-confirm-btn');
  btn.disabled = true; btn.style.opacity = '.45'; btn.style.cursor = 'not-allowed';
  btn.innerHTML = '<i class="fa-solid fa-truck-fast"></i> ยืนยันออกเดินทาง';

  document.getElementById('modal-accept-order').classList.add('open');
}

function _renderDepartPhotoGrid() {
  const grid = document.getElementById('aod-photo-grid');
  if (!grid) return;
  grid.innerHTML = _departPhotos.map((b64, i) => `
    <div style="position:relative;width:80px;height:80px;flex-shrink:0;">
      <img src="${b64}" style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:2px solid #e2e8f0;">
      <button onclick="removeDepartPhoto(${i})" style="position:absolute;top:-6px;right:-6px;width:20px;height:20px;background:#e53e3e;color:#fff;border:none;border-radius:50%;cursor:pointer;font-size:11px;display:flex;align-items:center;justify-content:center;padding:0;">✕</button>
    </div>`).join('');
  const hint = document.getElementById('aod-photo-hint');
  if (hint) hint.textContent = _departPhotos.length >= 5 ? 'ครบ 5 รูปแล้ว (สูงสุด)' : `${_departPhotos.length}/5 รูป (ต้องการอย่างน้อย 2)`;
  const btn = document.getElementById('aod-confirm-btn');
  if (!btn) return;
  const ok = _departPhotos.length >= 2;
  btn.disabled = !ok; btn.style.opacity = ok ? '1' : '.45'; btn.style.cursor = ok ? 'pointer' : 'not-allowed';
}

function removeDepartPhoto(i) {
  _departPhotos.splice(i, 1);
  _renderDepartPhotoGrid();
}

async function previewDepartPhoto(e) {
  const files = Array.from(e.target.files);
  if (!files.length) return;
  for (const file of files) {
    if (_departPhotos.length >= 5) { showToast('อัปโหลดได้สูงสุด 5 รูป', 'error'); break; }
    const compressed = await compressImage(file, 1024, 0.75);
    await new Promise(resolve => {
      const reader = new FileReader();
      reader.onload = ev => { _departPhotos.push(ev.target.result); resolve(); };
      reader.readAsDataURL(compressed);
    });
  }
  e.target.value = '';
  _renderDepartPhotoGrid();
}

function cancelAcceptOrder() {
  const doId = parseInt(document.getElementById('aod-id').value);
  closeModal('modal-accept-order');
  _departPhotos = [];
  if (doId) refreshDriverCard(doId); else loadDriverDashboard();
}

async function submitDepart() {
  if (_departPhotos.length < 2) { showToast('กรุณาถ่ายรูปอย่างน้อย 2 รูปก่อนออกเดินทาง', 'error'); return; }
  const doId = parseInt(document.getElementById('aod-id').value);
  const btn  = document.getElementById('aod-confirm-btn');
  btn.disabled = true; btn.style.opacity = '.65';
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> กำลังดำเนินการ...';

  // อัปโหลดรูป
  const photoRes = await api('POST', '/api/delivery-orders/' + doId + '/depart-photo', { photos: _departPhotos });
  if (!photoRes.ok) {
    showToast('อัปโหลดรูปไม่สำเร็จ', 'error');
    btn.disabled = false; btn.style.opacity = '1';
    btn.innerHTML = '<i class="fa-solid fa-truck-fast"></i> ยืนยันออกเดินทาง';
    return;
  }

  // dispatch → dispatched_at = now()
  const dispRes = await api('PATCH', '/api/delivery-orders/' + doId + '/dispatch');
  if (!dispRes.ok) {
    showToast(dispRes.data?.message || 'เกิดข้อผิดพลาด', 'error');
    btn.disabled = false; btn.style.opacity = '1';
    btn.innerHTML = '<i class="fa-solid fa-truck-fast"></i> ยืนยันออกเดินทาง';
    return;
  }

  // เปิด GPS อัตโนมัติ
  if (!isTracking) {
    await fetchDriverVehicleId();
    if (driverVehicleId) startTracking();
  }

  closeModal('modal-accept-order');
  _departPhotos = [];
  markRecentAction(doId);
  showToast('ออกเดินทางแล้ว — GPS กำลังติดตาม', 'success');
  refreshDriverCard(doId);
}

// ── Driver: ถึงแล้ว modal ─────────────────────────────────────────
let _arrPhotos     = [];
let _arrSigHasData = false;
let _arrSigCtx     = null;
let _arrSigDrawing = false;
let _arrCurrentItems = [];
let _arrCurrentStopId = null;

async function openArriveModal(doId, stopId = null) {
  _arrPhotos = [];
  _arrSigHasData = false;
  _arrCurrentItems = [];
  _arrCurrentStopId = (stopId && stopId !== 'null') ? stopId : null;
  document.getElementById('arr-do-id').value = doId;
  document.getElementById('arr-photo-input').value = '';
  document.getElementById('arr-photo-grid').innerHTML = '';
  const signBtn = document.getElementById('arr-sign-btn');
  if (signBtn) {
    signBtn.disabled = true; signBtn.style.opacity = '.45'; signBtn.style.cursor = 'not-allowed';
    signBtn.innerHTML = '<i class="fa-solid fa-circle-check"></i> ส่งสำเร็จ';
  }
  initArriveSignature();

  // แจ้งสาขาว่าคนขับถึงแล้ว (fire-and-forget)
  api('POST', '/api/delivery-orders/' + doId + '/notify-arrival');

  const r = await api('GET', '/api/delivery-orders/' + doId);
  if (r.ok) {
    const o = r.data;
    let currentBranch = o.branch;
    let currentItems  = o.items || [];
    let stopLabel     = '';

    if (_arrCurrentStopId && o.stops && o.stops.length > 0) {
      const s = o.stops.find(s => s.id == _arrCurrentStopId);
      if (s) {
        currentBranch = s.branch;
        currentItems  = s.items || [];
        stopLabel     = `<span style="background:#ebf8ff;color:#2a5298;border-radius:10px;padding:1px 8px;font-size:10px;font-weight:700;margin-left:6px;">จุดที่ ${s.sequence}/${o.stops.length}</span>`;
      }
    }

    _arrCurrentItems = currentItems;
    const itemsHtml = (currentItems.length > 0)
      ? `<div style="margin-top:10px;display:flex;flex-direction:column;gap:6px;">${currentItems.map(i =>
          `<div style="display:flex;justify-content:space-between;align-items:center;background:#edf2f7;border-radius:8px;padding:8px 14px;">
            <span style="font-size:16px;color:#2d3748;font-weight:600;">${i.product_name}</span>
            <span style="font-size:17px;font-weight:800;color:#2a5298;white-space:nowrap;margin-left:12px;">${i.qty_ordered} ${i.unit||''}</span>
          </div>`).join('')}</div>`
      : '';
    document.getElementById('arr-do-info').innerHTML = `
      <div style="background:#f7fafc;border-radius:8px;padding:10px 14px;font-size:13px;">
        <b style="color:#2a5298;">${o.do_number}</b>${stopLabel}
        <span style="margin:0 6px;color:#cbd5e0;">—</span>
        <i class="fa-solid fa-store fa-sm"></i> ${currentBranch?.name || '-'}
        ${currentBranch?.address ? `<div style="color:#718096;font-size:12px;margin-top:3px;">${currentBranch.address}</div>` : ''}
        ${itemsHtml}
      </div>`;
  }

  document.getElementById('modal-arrive').classList.add('open');
}

function initArriveSignature() {
  const canvas = document.getElementById('arr-sig-canvas');
  if (!canvas) return;
  _arrSigCtx = canvas.getContext('2d');
  _arrSigCtx.clearRect(0, 0, canvas.width, canvas.height);
  _arrSigHasData = false;
  _arrSigDrawing = false;
  const sigHint = document.getElementById('arr-sig-hint');
  if (sigHint) sigHint.style.display = 'flex';
  _arrSigCtx.strokeStyle = '#1a202c';
  _arrSigCtx.lineWidth = 2;
  _arrSigCtx.lineCap = 'round';
  _arrSigCtx.lineJoin = 'round';
  const pos = e => {
    const r = canvas.getBoundingClientRect();
    const src = e.touches ? e.touches[0] : e;
    return {
      x: (src.clientX - r.left) * (canvas.width / r.width),
      y: (src.clientY - r.top)  * (canvas.height / r.height),
    };
  };
  canvas.onmousedown = canvas.ontouchstart = e => {
    e.preventDefault(); _arrSigDrawing = true;
    const p = pos(e); _arrSigCtx.beginPath(); _arrSigCtx.moveTo(p.x, p.y);
  };
  canvas.onmousemove = canvas.ontouchmove = e => {
    if (!_arrSigDrawing) return; e.preventDefault();
    const p = pos(e); _arrSigCtx.lineTo(p.x, p.y); _arrSigCtx.stroke();
    if (!_arrSigHasData) {
      _arrSigHasData = true;
      if (sigHint) sigHint.style.display = 'none';
      _updateArriveBtn();
    }
  };
  canvas.onmouseup = canvas.onmouseleave = canvas.ontouchend = () => { _arrSigDrawing = false; };
}

function clearArriveSignature() {
  if (!_arrSigCtx) return;
  const canvas = document.getElementById('arr-sig-canvas');
  _arrSigCtx.clearRect(0, 0, canvas.width, canvas.height);
  _arrSigHasData = false;
  const sh = document.getElementById('arr-sig-hint');
  if (sh) sh.style.display = 'flex';
}

function _renderArrivePhotoGrid() {
  const grid = document.getElementById('arr-photo-grid');
  if (!grid) return;
  grid.innerHTML = _arrPhotos.map((b64, i) => `
    <div style="position:relative;width:80px;height:80px;flex-shrink:0;">
      <img src="${b64}" style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:2px solid #e2e8f0;">
      <button onclick="removeArrivePhoto(${i})" style="position:absolute;top:-6px;right:-6px;width:20px;height:20px;background:#e53e3e;color:#fff;border:none;border-radius:50%;cursor:pointer;font-size:11px;display:flex;align-items:center;justify-content:center;padding:0;">✕</button>
    </div>`).join('');
  const hint = document.getElementById('arr-photo-hint');
  if (hint) hint.textContent = _arrPhotos.length >= 5 ? 'ครบ 5 รูปแล้ว (สูงสุด)' : `${_arrPhotos.length}/5 รูป (ต้องการอย่างน้อย 2)`;
  _updateArriveBtn();
}

function removeArrivePhoto(i) {
  _arrPhotos.splice(i, 1);
  _renderArrivePhotoGrid();
}

function _updateArriveBtn() {
  const btn = document.getElementById('arr-sign-btn');
  if (!btn) return;
  const ok = _arrPhotos.length >= 2 && _arrSigHasData;
  btn.disabled = !ok; btn.style.opacity = ok ? '1' : '.45'; btn.style.cursor = ok ? 'pointer' : 'not-allowed';
}

async function previewArrivePhoto(e) {
  const files = Array.from(e.target.files);
  if (!files.length) return;
  for (const file of files) {
    if (_arrPhotos.length >= 5) { showToast('อัปโหลดได้สูงสุด 5 รูป', 'error'); break; }
    const compressed = await compressImage(file, 1024, 0.75);
    await new Promise(resolve => {
      const reader = new FileReader();
      reader.onload = ev => { _arrPhotos.push(ev.target.result); resolve(); };
      reader.readAsDataURL(compressed);
    });
  }
  e.target.value = '';
  _renderArrivePhotoGrid();
}


async function submitArriveSign() {
  if (_arrPhotos.length < 2) { showToast('กรุณาถ่ายรูปหลักฐานอย่างน้อย 2 รูป', 'error'); return; }
  if (!_arrSigHasData) { showToast('กรุณาลงลายเซ็นก่อนยืนยันการส่ง', 'error'); return; }
  const doId = parseInt(document.getElementById('arr-do-id').value);
  const btn  = document.getElementById('arr-sign-btn');
  btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> กำลังส่ง...';
  const sig = _arrSigHasData ? document.getElementById('arr-sig-canvas').toDataURL('image/png') : null;
  const r = await api('POST', '/api/delivery-orders/' + doId + '/confirm-delivery', {
    action: 'sign',
    arrive_photos: _arrPhotos,
    signature: sig,
    stop_id: _arrCurrentStopId || null,
  });
  if (!r.ok) {
    showToast(r.data?.message || 'เกิดข้อผิดพลาด', 'error');
    btn.disabled = false; btn.style.opacity = '1';
    btn.innerHTML = '<i class="fa-solid fa-circle-check"></i> ส่งสำเร็จ';
    return;
  }
  closeModal('modal-arrive');
  _arrPhotoB64 = null;
  markRecentAction(doId);
  showToast('ส่งสำเร็จแล้ว', 'success');
  refreshDriverCard(doId);
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
let pendingOrderStatus   = null;
let orderActiveStatus    = '';
let orderTabSetByUser    = false;
const recentlyActed = {};
function markRecentAction(id) {
  recentlyActed[id] = true;
  setTimeout(() => delete recentlyActed[id], 5 * 60 * 1000);
}
let _ordersLoadId        = 0;

function navigateToOrders(status) {
  pendingOrderStatus = status ?? '';
  navigate('orders');
}

function userSetOrderTab(status) {
  orderTabSetByUser = true;
  setOrderTab(status);
}

function setOrderTab(status) {
  orderActiveStatus = status;
  document.querySelectorAll('#page-orders .order-tabs .order-tab').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.status === status);
  });
  filterOrders();
}

function updateOrderTabs(orders) {
  const roots = orders.filter(o => !o.is_backorder);
  const counts = { '': roots.length };
  ['pending','in_transit','completed','cancelled'].forEach(s => {
    counts[s] = roots.filter(o => o.status === s).length;
  });
  document.getElementById('tab-count-all').textContent       = counts[''];
  document.getElementById('tab-count-pending').textContent   = counts['pending'];
  document.getElementById('tab-count-in_transit').textContent= counts['in_transit'];
  document.getElementById('tab-count-completed').textContent = counts['completed'];
  document.getElementById('tab-count-cancelled').textContent = counts['cancelled'];
}

async function loadOrders() {
  if (!currentUser) return;
  const myId = ++_ordersLoadId;
  let orders;
  if (currentUser?.role === 'driver') {
    const r = await api('GET', '/api/driver/orders');
    if (!r.ok || myId !== _ordersLoadId) return;
    orders = r.data || [];
  } else {
    const r = await api('GET', '/api/delivery-orders?per_page=100');
    if (!r.ok || myId !== _ordersLoadId) return;
    orders = r.data.data || [];
  }
  allOrders = orders;
  updateOrderTabs(orders);

  if (pendingOrderStatus !== null) {
    setOrderTab(pendingOrderStatus);
    pendingOrderStatus = null;
  } else if (!orderTabSetByUser) {
    setOrderTab('pending');
  } else {
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

  // Driver: flat list
  if (currentUser?.role === 'driver') {
    if (!orders.length) {
      tbody.innerHTML = `<tr><td colspan="5"><div class="empty-state"><i class="fa-solid fa-inbox"></i><p>ไม่มีออเดอร์</p></div></td></tr>`;
      return;
    }
    tbody.innerHTML = orders.map(o => {
      const boTag = o.is_backorder ? `<span style="font-size:10px;color:#718096;margin-left:4px;font-weight:600;">[BO]</span>` : '';
      return `<tr onclick="toggleOrderTimeline(${o.id})" style="cursor:pointer;">
        <td><b>${itemsSummary(o.items)}</b>${boTag}</td>
        <td>${orderBranchDisplay(o)}</td>
        <td>${formatDate(o.scheduled_date)}</td>
        <td><span class="badge badge-${o.status}">${statusTH(o.status)}</span>${recentlyActed[o.id] ? '<div style="font-size:10px;color:#a0aec0;margin-top:2px;line-height:1;">เมื่อสักครู่</div>' : ''}</td>
        <td style="text-align:center;width:32px;"><i id="ord-ch-${o.id}" class="fa-solid fa-chevron-down" style="font-size:12px;color:#a0aec0;transition:transform .2s;"></i></td>
      </tr>
      <tr id="ord-detail-${o.id}" style="display:none;">
        <td colspan="5" style="padding:0;background:#f9fafb;border-top:none;"></td>
      </tr>`;
    }).join('');
    return;
  }

  // Admin/HQ/Branch: group BOs as sub-rows under parent DO
  const childMap = buildChildMap(allOrders);
  const roots = orders.filter(o => !o.is_backorder);

  if (!roots.length) {
    tbody.innerHTML = `<tr><td colspan="5"><div class="empty-state"><i class="fa-solid fa-inbox"></i><p>ไม่มีออเดอร์</p></div></td></tr>`;
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

    const mainRow = `<tr onclick="toggleOrderTimeline(${o.id})" style="cursor:pointer;">
      <td><b>${itemsSummary(o.items)}</b>${expandBtn}</td>
      <td>${orderBranchDisplay(o)}</td>
      <td>${formatDate(o.scheduled_date)}</td>
      <td><span class="badge badge-${o.status}">${statusTH(o.status)}</span>${subBadge}${recentlyActed[o.id] ? '<div style="font-size:10px;color:#a0aec0;margin-top:2px;line-height:1;">เมื่อสักครู่</div>' : ''}</td>
      <td style="text-align:center;width:32px;"><i id="ord-ch-${o.id}" class="fa-solid fa-chevron-down" style="font-size:12px;color:#a0aec0;transition:transform .2s;"></i></td>
    </tr>`;

    const boRows = bos.map(bo => {
      const isPending = bo.sub_status === 'bo_pending';
      const approveBtns = (isAdminHq && isPending)
        ? `<button class="btn btn-success btn-sm" onclick="event.stopPropagation();approveBO(${bo.id})"><i class="fa-solid fa-check"></i> อนุมัติ</button>
           <button class="btn btn-danger btn-sm" onclick="event.stopPropagation();openRejectBO(${bo.id})"><i class="fa-solid fa-xmark"></i> ปฏิเสธ</button>`
        : '';
      return `<tr class="bo-sub-${o.id}" style="display:none" onclick="toggleOrderTimeline(${bo.id})" >
        <td colspan="5" style="padding:0;">
          <div style="display:flex;align-items:center;gap:10px;padding:7px 12px 7px 36px;background:#fffff0;border-left:3px solid #d69e2e;cursor:pointer;">
            <span style="font-size:10px;font-weight:700;color:#975a16;white-space:nowrap;">รอบ ${bo.bo_round}</span>
            <span><b style="color:#744210;font-size:13px;">${itemsSummary(bo.items)}</b><br><span style="font-size:10px;color:#a0aec0;">${bo.do_number}</span></span>
            <span class="badge badge-${bo.sub_status}">${subStatusTH(bo.sub_status)}</span>
            <span style="font-size:12px;color:#718096;">${formatDate(bo.scheduled_date)}</span>
            <span style="font-size:12px;">${driverDisplay(bo.driver)}</span>
            <div style="margin-left:auto;display:flex;gap:4px;">
              <i id="ord-ch-${bo.id}" class="fa-solid fa-chevron-down" style="font-size:12px;color:#a0aec0;transition:transform .2s;margin-right:4px;"></i>
              ${approveBtns}
            </div>
          </div>
        </td>
      </tr>
      <tr id="ord-detail-${bo.id}" style="display:none;">
        <td colspan="5" style="padding:0;background:#fffff8;border-top:none;border-left:3px solid #d69e2e;"></td>
      </tr>`;
    }).join('');

    const detailRow = `<tr id="ord-detail-${o.id}" style="display:none;">
      <td colspan="5" style="padding:0;background:#f9fafb;border-top:none;"></td>
    </tr>`;

    return mainRow + boRows + detailRow;
  }).join('');
}

async function toggleOrderTimeline(id) {
  const row = document.getElementById('ord-detail-' + id);
  const ch  = document.getElementById('ord-ch-' + id);
  if (!row) return;
  const open = row.style.display !== 'none';
  row.style.display = open ? 'none' : 'table-row';
  if (ch) ch.style.transform = open ? 'rotate(0deg)' : 'rotate(180deg)';

  if (open) {
    row.querySelectorAll('[data-location-node]').forEach(el => stopLocationPolling(el.dataset.locationNode));
    return;
  }

  if (row.dataset.loaded) {
    row.querySelectorAll('[data-location-node]').forEach(el => {
      fetchLocationNode(el.dataset.locationNode, el.dataset.locationDriver);
      startLocationPolling(el.dataset.locationNode, el.dataset.locationDriver);
    });
    return;
  }

  const cell = row.querySelector('td');
  cell.innerHTML = '<div style="padding:14px;color:#a0aec0;text-align:center;"><i class="fa-solid fa-spinner fa-spin"></i> กำลังโหลด...</div>';
  const r = await api('GET', '/api/delivery-orders/' + id);
  if (!r.ok) { cell.innerHTML = '<div style="padding:14px;color:#e53e3e;">โหลดข้อมูลไม่สำเร็จ</div>'; return; }
  cell.innerHTML = buildOrderDetailHtml(r.data);
  row.dataset.loaded = '1';
  setTimeout(initLocationNodes, 0);
}

function buildOrderDetailHtml(o) {
  const isMultiStop = o.stops && o.stops.length > 0;
  const sortedStops = isMultiStop ? [...o.stops].sort((a,b) => (a.sequence||0)-(b.sequence||0)) : [];
  const isRejected  = o.sub_status === 'rejected';

  // timeline nodes
  let tlNodes = [];
  if (isMultiStop) {
    tlNodes.push({ type:'fixed', icon:'fa-file-plus', label:'สร้างออเดอร์', ts: o.created_at,    done: true,             photoKey: null });
    tlNodes.push({ type:'fixed', icon:'fa-truck',     label:'ออกเดินทาง',   ts: o.dispatched_at, done: !!o.dispatched_at, photoKey: 'depart' });
    sortedStops.forEach(s => tlNodes.push({ type:'stop', stop: s, done: stopIsDone(s), hasProblem: s.sub_status==='problem'||s.status==='problem' }));
  } else {
    tlNodes = [
      { type:'fixed', icon:'fa-file-plus',    label:'สร้างออเดอร์', ts: o.created_at,    done: true,             photoKey: null },
      { type:'fixed', icon:'fa-truck',        label:'ออกเดินทาง',   ts: o.dispatched_at, done: !!o.dispatched_at, photoKey: 'depart' },
      { type:'fixed', icon:'fa-location-dot', label:'ถึงสาขา',      ts: o.arrived_at,    done: !!o.arrived_at,    photoKey: null },
      { type:'fixed', icon: isRejected?'fa-xmark':'fa-circle-check',
        label: isRejected?'ปฏิเสธรับ':'ส่งสำเร็จ',
        ts: o.received_at, done: !!o.received_at, danger: isRejected, photoKey: 'arrive' },
    ];
  }
  const tlRows = [...tlNodes].reverse().map((node, i, arr) => {
    const isLast = i === arr.length - 1;
    if (node.type === 'stop') {
      const s = node.stop;
      const dotBorder = node.done ? '#38a169' : node.hasProblem ? '#fc8181' : '#cbd5e0';
      const dotBg     = node.done ? '#c6f6d5' : node.hasProblem ? '#fff5f5' : '#f7fafc';
      const iColor    = node.done ? '#276749' : node.hasProblem ? '#c53030' : '#a0aec0';
      const timeColor = node.done ? '#276749' : node.hasProblem ? '#e53e3e' : '#a0aec0';
      const lineColor = node.done && !isLast ? '#38a169' : '#e2e8f0';
      const badge     = node.done
        ? `<span style="font-size:10px;background:#c6f6d5;color:#276749;padding:1px 8px;font-weight:700;"><i class="fa-solid fa-circle-check"></i> ส่งแล้ว</span>`
        : node.hasProblem
        ? `<span style="font-size:10px;background:#fff5f5;color:#c53030;padding:1px 8px;font-weight:700;"><i class="fa-solid fa-triangle-exclamation"></i> มีปัญหา</span>`
        : `<span style="font-size:10px;background:#e2e8f0;color:#718096;padding:1px 8px;font-weight:700;"><i class="fa-solid fa-clock"></i> รอส่ง</span>`;
      const uid    = 'ord-' + o.id + '-stop-' + s.id;
      const sItems = s.items || [];
      const hasEv  = s.arrive_photo_path || s.signature;
      const itemRows = sItems.map(it => {
        const sh = it.shortage_qty || 0;
        return `<tr style="border-top:1px solid #f0f4f8;"><td style="padding:3px 8px;">${it.product_name}</td><td style="padding:3px 8px;text-align:center;">${it.qty_ordered}${sh>0?`<span style="color:#c05621;font-weight:700;"> (-${sh})</span>`:''}</td><td style="padding:3px 8px;">${it.unit}</td></tr>`;
      }).join('');
      return `<div style="display:flex;align-items:stretch;">
        <div style="width:76px;flex-shrink:0;text-align:right;padding-right:10px;padding-top:10px;">
          ${s.delivered_at?`<div style="font-size:10px;color:${timeColor};font-weight:700;line-height:1.5;">${formatDateTime(s.delivered_at).replace(' ','<br>')}</div>`:`<div style="font-size:11px;color:#cbd5e0;">—</div>`}
        </div>
        <div style="display:flex;flex-direction:column;align-items:center;width:40px;flex-shrink:0;">
          <div style="width:2px;background:${i===0?'transparent':(arr[i-1]?.done?'#38a169':'#e2e8f0')};flex:${i===0?'0 0 10px':'1'};min-height:10px;"></div>
          <div style="width:34px;height:34px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:${dotBg};border:2.5px solid ${dotBorder};"><i class="fa-solid fa-store" style="font-size:12px;color:${iColor};"></i></div>
          <div style="width:2px;background:${isLast?'transparent':lineColor};flex:${isLast?'0 0 10px':'1'};min-height:10px;"></div>
        </div>
        <div style="padding:8px 0 14px 12px;flex:1;min-width:0;">
          <div style="display:flex;align-items:center;flex-wrap:wrap;gap:5px;margin-bottom:6px;">
            <span style="width:18px;height:18px;background:#2a5298;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:10px;font-weight:700;flex-shrink:0;">${s.sequence}</span>
            <span style="font-size:13px;font-weight:700;color:${node.done?'#1e3a5f':'#718096'};">${s.branch?.name||'-'}</span>
            ${badge}
          </div>
          ${sItems.length>0?`<table style="width:100%;font-size:12px;border-collapse:collapse;margin-bottom:6px;background:#fafafa;border:1px solid #e2e8f0;"><thead><tr style="background:#f0f4f8;"><th style="padding:4px 8px;text-align:left;font-weight:600;color:#4a5568;">สินค้า</th><th style="padding:4px 8px;text-align:center;font-weight:600;color:#4a5568;width:70px;">จำนวน</th><th style="padding:4px 8px;text-align:left;font-weight:600;color:#4a5568;width:50px;">หน่วย</th></tr></thead><tbody>${itemRows}</tbody></table>`:''}
          ${hasEv?`<button onclick="event.stopPropagation();var el=document.getElementById('${uid}');el.style.display=el.style.display==='none'?'flex':'none';" style="background:#f0fff4;border:1px solid #c6f6d5;color:#276749;padding:4px 12px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:5px;"><i class="fa-solid fa-images"></i> ดูหลักฐาน</button>
          <div id="${uid}" style="display:none;gap:12px;flex-wrap:wrap;margin-top:8px;">
            ${s.arrive_photo_path?`<div><div style="font-size:11px;font-weight:700;color:#4a5568;margin-bottom:5px;">รูปหลังส่ง</div><div style="display:flex;flex-wrap:wrap;gap:6px;">${renderPhotoGrid(parsePhotoPaths(s.arrive_photo_path),'arrive-photos')}</div><div style="font-size:10px;color:#a0aec0;margin-top:3px;">กดที่รูปเพื่อดูเต็มจอ</div></div>`:''}
            ${s.signature?`<div><div style="font-size:11px;font-weight:700;color:#4a5568;margin-bottom:5px;">ลายเซ็น</div><img src="${s.signature}" style="max-width:180px;max-height:120px;border:1.5px solid #e2e8f0;background:#fff;padding:3px;display:block;cursor:zoom-in;" onclick="event.stopPropagation();openPhotoViewer(this.src,'ลายเซ็น — ${s.branch?.name||''}')"><div style="font-size:10px;color:#a0aec0;margin-top:3px;">กดที่รูปเพื่อดูเต็มจอ</div></div>`:''}
          </div>`:node.done?`<div style="margin-top:5px;display:inline-flex;align-items:center;gap:5px;background:#fff5f5;border:1px solid #fed7d7;color:#c53030;padding:3px 10px;font-size:11px;font-weight:700;border-radius:6px;"><i class="fa-solid fa-triangle-exclamation"></i> ไม่พบหลักฐาน</div>`:''}
        </div>
      </div>`;
    }
    const p = node;
    const dotBorder = p.done ? (p.danger ? '#fc8181' : '#38a169') : '#cbd5e0';
    const dotBg     = p.done ? (p.danger ? '#fff5f5' : '#c6f6d5') : '#f7fafc';
    const txtColor  = p.done ? (p.danger ? '#c53030' : '#1e3a5f') : '#a0aec0';
    const timeColor = p.done ? (p.danger ? '#e53e3e' : '#276749') : '#a0aec0';
    const lineColor = p.done && !isLast ? '#38a169' : '#e2e8f0';
    const uid = 'ord-' + o.id + '-' + p.photoKey;
    let photoBlock = '';
    if (p.photoKey === 'depart' && o.depart_photo_path) {
      photoBlock = `<div style="margin-top:6px;"><button onclick="event.stopPropagation();var el=document.getElementById('${uid}');el.style.display=el.style.display==='none'?'block':'none';" style="background:#ebf8ff;border:1px solid #bee3f8;color:#2a5298;padding:4px 12px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:5px;"><i class="fa-solid fa-camera"></i> ดูหลักฐาน</button>
        <div id="${uid}" style="display:none;margin-top:8px;"><div style="display:flex;flex-wrap:wrap;gap:6px;">${renderPhotoGrid(parsePhotoPaths(o.depart_photo_path),'depart-photos')}</div><div style="font-size:10px;color:#a0aec0;margin-top:3px;">กดที่รูปเพื่อดูเต็มจอ</div></div></div>`;
    } else if (p.photoKey === 'arrive') {
      const rr = o.receive_record;
      const hasArrive = !!o.arrive_photo_path;
      const hasSig    = !!(rr && rr.signature);
      if (hasArrive || hasSig) {
        photoBlock = `<div style="margin-top:6px;"><button onclick="event.stopPropagation();var el=document.getElementById('${uid}');el.style.display=el.style.display==='none'?'flex':'none';" style="background:#f0fff4;border:1px solid #c6f6d5;color:#276749;padding:4px 12px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:5px;"><i class="fa-solid fa-images"></i> ดูหลักฐาน</button>
          <div id="${uid}" style="display:none;gap:12px;flex-wrap:wrap;margin-top:8px;">
            ${hasArrive?`<div><div style="font-size:11px;font-weight:700;color:#4a5568;margin-bottom:5px;">รูปหลังส่ง</div><div style="display:flex;flex-wrap:wrap;gap:6px;">${renderPhotoGrid(parsePhotoPaths(o.arrive_photo_path),'arrive-photos')}</div><div style="font-size:10px;color:#a0aec0;margin-top:3px;">กดที่รูปเพื่อดูเต็มจอ</div></div>`:''}
            ${hasSig?`<div><div style="font-size:11px;font-weight:700;color:#4a5568;margin-bottom:5px;">ลายเซ็น</div><img src="${rr.signature}" style="max-width:180px;max-height:120px;border:1.5px solid #e2e8f0;background:#fff;padding:3px;display:block;cursor:zoom-in;" onclick="event.stopPropagation();openPhotoViewer(this.src,'ลายเซ็น')"><div style="font-size:10px;color:#a0aec0;margin-top:3px;">กดที่รูปเพื่อดูเต็มจอ</div></div>`:''}
          </div></div>`;
      } else if (p.done) {
        photoBlock = `<div style="margin-top:5px;display:inline-flex;align-items:center;gap:5px;background:#fff5f5;border:1px solid #fed7d7;color:#c53030;padding:3px 10px;font-size:11px;font-weight:700;border-radius:6px;"><i class="fa-solid fa-triangle-exclamation"></i> ไม่พบหลักฐานการส่ง</div>`;
      }
    }
    return `<div style="display:flex;align-items:stretch;">
      <div style="width:76px;flex-shrink:0;text-align:right;padding-right:10px;padding-top:10px;">
        ${p.ts?`<div style="font-size:10px;color:${timeColor};font-weight:700;line-height:1.5;">${formatDateTime(p.ts).replace(' ','<br>')}</div>`:`<div style="font-size:11px;color:#cbd5e0;">—</div>`}
      </div>
      <div style="display:flex;flex-direction:column;align-items:center;width:40px;flex-shrink:0;">
        <div style="width:2px;background:${i===0?'transparent':(arr[i-1]?.done?'#38a169':'#e2e8f0')};flex:${i===0?'0 0 10px':'1'};min-height:10px;"></div>
        <div style="width:34px;height:34px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:${dotBg};border:2.5px solid ${dotBorder};"><i class="fa-solid ${p.icon}" style="font-size:13px;color:${p.done?(p.danger?'#c53030':'#276749'):'#a0aec0'};"></i></div>
        <div style="width:2px;background:${isLast?'transparent':lineColor};flex:${isLast?'0 0 10px':'1'};min-height:10px;"></div>
      </div>
      <div style="padding:8px 0 12px 12px;flex:1;min-width:0;">
        <div style="font-size:13px;font-weight:700;color:${txtColor};padding-top:2px;">${p.label}</div>
        ${photoBlock}
      </div>
    </div>`;
  }).join('');

  const _role = currentUser?.role;
  const cancelBtn = (o.status === 'pending' && (_role === 'admin' || _role === 'hq'))
    ? `<div style="margin-top:12px;padding-top:12px;border-top:1px solid #e2e8f0;">
         <button class="btn btn-danger" onclick="openCancelModal(${o.id})" style="width:100%;">
           <i class="fa-solid fa-xmark"></i> ยกเลิกออเดอร์
         </button>
       </div>`
    : '';

  return `<div style="padding:14px 16px;">
    <div style="display:flex;align-items:center;flex-wrap:wrap;gap:16px;padding:8px 12px;background:#f0f4f8;border-radius:8px;margin-bottom:12px;font-size:12px;">
      <span style="color:#718096;font-weight:600;">รหัส</span>
      <span style="font-family:monospace;font-weight:700;color:#2a5298;">${o.do_number}</span>
      <span style="color:#e2e8f0;">|</span>
      <span style="color:#718096;font-weight:600;">คนขับ</span>
      <span style="font-weight:700;color:#1e3a5f;">${o.driver?.name || '-'}</span>
      <span style="color:#e2e8f0;">|</span>
      <span style="color:#718096;font-weight:600;">ทะเบียน</span>
      <span style="font-weight:700;color:#1e3a5f;">${o.driver?.vehicle_plate || '-'}</span>
    </div>
    <div style="font-size:11px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:.7px;margin-bottom:10px;"><i class="fa-solid fa-clock-rotate-left" style="color:#4a90d9;"></i> ความเคลื่อนไหว</div>
    ${tlRows}
    ${cancelBtn}
  </div>`;
}

function filterOrders() {
  const search  = (document.getElementById('order-search')?.value || '').toLowerCase();
  const dateVal = document.getElementById('order-date-filter')?.value || '';

  let filtered = allOrders.filter(o => !o.is_backorder);

  if (orderActiveStatus) {
    filtered = filtered.filter(o => o.status === orderActiveStatus || recentlyActed[o.id]);
  }

  if (search) {
    filtered = filtered.filter(o =>
      o.do_number.toLowerCase().includes(search) ||
      (o.branch?.name || '').toLowerCase().includes(search) ||
      (o.items || []).some(i => (i.product_name || '').toLowerCase().includes(search))
    );
  }

  if (dateVal) {
    filtered = filtered.filter(o => (o.scheduled_date || '').slice(0, 10) === dateVal);
  }

  filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

  renderOrders(filtered);
}

function openPhotoViewer(src, title) {
  let ov = document.getElementById('photo-viewer-overlay');
  if (!ov) {
    ov = document.createElement('div');
    ov.id = 'photo-viewer-overlay';
    ov.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.88);z-index:9999;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:zoom-out;padding:20px;';
    document.body.appendChild(ov);
  }
  ov.onclick = () => { ov.style.display = 'none'; };
  const isSig = src && src.startsWith('data:');
  const imgStyle = `max-width:90vw;max-height:82vh;object-fit:contain;box-shadow:0 8px 40px rgba(0,0,0,.6);${isSig ? 'background:#fff;padding:16px;border-radius:10px;' : ''}`;
  ov.innerHTML = `
    <div style="color:#fff;font-size:13px;font-weight:600;margin-bottom:12px;opacity:.8;">${title || ''}</div>
    <img src="${src}" style="${imgStyle}">
    <div style="color:rgba(255,255,255,.5);font-size:11px;margin-top:12px;">แตะที่ใดก็ได้เพื่อปิด</div>`;
  ov.style.display = 'flex';
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
        .filter(u => u.role === 'driver' && u.is_active)
        .map(d => `<option value="${d.id}" ${o.driver_id == d.id ? 'selected' : ''}>${d.name}</option>`)
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
          <select id="bo-driver-${bo.id}" style="width:100%;border:1.5px solid #e2e8f0;border-radius:8px;padding:9px 12px;font-size:14px;font-family:inherit;">${driverOpts}</select>
        </div>
        <div>
          <label style="display:block;font-size:12px;font-weight:600;color:#4a5568;text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px;">วันที่ส่งซ้ำ <span style="color:#e53e3e;">*</span></label>
          <input type="date" id="bo-date-${bo.id}" data-no-ph="1" lang="en-GB"
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

  document.getElementById('detail-title').innerHTML =
    `<i class="fa-solid fa-box"></i> ${o.do_number}` +
    (o.is_backorder ? ` <span style="font-size:12px;background:#e2e8f0;padding:2px 8px;border-radius:6px;margin-left:6px;color:#718096;font-weight:600;">BO รอบ ${o.bo_round}</span>` : '');

  // ---- checkpoint timeline + embedded photos ----
  const isRejected  = o.sub_status === 'rejected';
  const isMultiStop = o.stops && o.stops.length > 0;
  const sortedStops = isMultiStop ? [...o.stops].sort((a,b) => (a.sequence||0)-(b.sequence||0)) : [];

  // สร้าง nodes: multi-stop = node ต่อสาขา, single-stop = 4 nodes คงที่
  let tlNodes = [];
  if (isMultiStop) {
    tlNodes.push({ type:'fixed', icon:'fa-file-plus', label:'สร้างออเดอร์', ts: o.created_at,    done: true,             photoKey: null });
    tlNodes.push({ type:'fixed', icon:'fa-truck',     label:'ออกเดินทาง',   ts: o.dispatched_at, done: !!o.dispatched_at, photoKey: 'depart' });
    sortedStops.forEach(s => {
      tlNodes.push({ type:'stop', stop: s, done: stopIsDone(s), hasProblem: s.sub_status === 'problem' || s.status === 'problem' });
    });
  } else {
    tlNodes = [
      { type:'fixed', icon:'fa-file-plus',    label:'สร้างออเดอร์', ts: o.created_at,    done: true,             photoKey: null },
      { type:'fixed', icon:'fa-truck',        label:'ออกเดินทาง',   ts: o.dispatched_at, done: !!o.dispatched_at, photoKey: 'depart' },
      { type:'fixed', icon:'fa-location-dot', label:'ถึงสาขา',      ts: o.arrived_at,    done: !!o.arrived_at,    photoKey: null },
      { type:'fixed', icon: isRejected ? 'fa-xmark' : 'fa-circle-check',
        label: isRejected ? 'ปฏิเสธรับ' : 'ส่งสำเร็จ',
        ts: o.received_at, done: !!o.received_at, danger: isRejected, photoKey: 'arrive' },
    ];
  }
  const tlReversed = [...tlNodes].reverse();
  const tlHtml = `<div style="margin:18px 0 6px;border-top:1.5px solid #e2e8f0;padding-top:16px;">
    <div style="font-size:11px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:.8px;margin-bottom:8px;"><i class="fa-solid fa-clock-rotate-left"></i> ความเคลื่อนไหว</div>
    ${tlReversed.map((node, i) => {
      const isLast = i === tlReversed.length - 1;
      if (false) { // location node removed
      }
      if (node.type === 'stop') {
        const s = node.stop;
        const dotBorder = node.done ? '#38a169' : node.hasProblem ? '#fc8181' : '#cbd5e0';
        const dotBg     = node.done ? '#c6f6d5' : node.hasProblem ? '#fff5f5' : '#f7fafc';
        const iColor    = node.done ? '#276749' : node.hasProblem ? '#c53030' : '#a0aec0';
        const timeColor = node.done ? '#276749' : node.hasProblem ? '#e53e3e' : '#a0aec0';
        const lineColor = node.done && !isLast ? '#38a169' : '#e2e8f0';
        const stTs      = s.delivered_at || null;
        const badge     = node.done
          ? `<span style="font-size:10px;background:#c6f6d5;color:#276749;padding:1px 8px;font-weight:700;"><i class="fa-solid fa-circle-check"></i> ส่งแล้ว</span>`
          : node.hasProblem
          ? `<span style="font-size:10px;background:#fff5f5;color:#c53030;padding:1px 8px;font-weight:700;"><i class="fa-solid fa-triangle-exclamation"></i> มีปัญหา</span>`
          : `<span style="font-size:10px;background:#e2e8f0;color:#718096;padding:1px 8px;font-weight:700;"><i class="fa-solid fa-clock"></i> รอส่ง</span>`;
        const suid   = 'tl-' + o.id + '-stop-' + s.id;
        const sItems = s.items || [];
        const hasEv  = s.arrive_photo_path || s.signature;
        const itemRows = sItems.map(it => {
          const sh = it.shortage_qty || 0;
          return `<tr style="border-top:1px solid #f0f4f8;">
            <td style="padding:3px 8px;">${it.product_name}</td>
            <td style="padding:3px 8px;text-align:center;">${it.qty_ordered}${sh > 0 ? `<span style="color:#c05621;font-weight:700;"> (-${sh})</span>` : ''}</td>
            <td style="padding:3px 8px;">${it.unit}</td>
          </tr>`;
        }).join('');
        return `<div style="display:flex;align-items:stretch;">
          <div style="width:76px;flex-shrink:0;text-align:right;padding-right:10px;padding-top:10px;">
            ${stTs ? `<div style="font-size:10px;color:${timeColor};font-weight:700;line-height:1.5;">${formatDateTime(stTs).replace(' ','\n').split('\n').join('<br>')}</div>` : `<div style="font-size:11px;color:#cbd5e0;">—</div>`}
          </div>
          <div style="display:flex;flex-direction:column;align-items:center;width:40px;flex-shrink:0;">
            <div style="width:2px;background:${i === 0 ? 'transparent' : '#e2e8f0'};flex:${i === 0 ? '0 0 10px' : '1'};min-height:10px;"></div>
            <div style="width:34px;height:34px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:${dotBg};border:2.5px solid ${dotBorder};">
              <i class="fa-solid fa-store" style="font-size:12px;color:${iColor};"></i>
            </div>
            <div style="width:2px;background:${isLast ? 'transparent' : lineColor};flex:${isLast ? '0 0 10px' : '1'};min-height:10px;"></div>
          </div>
          <div style="padding:8px 0 14px 12px;flex:1;min-width:0;">
            <div style="display:flex;align-items:center;flex-wrap:wrap;gap:5px;margin-bottom:6px;">
              <span style="width:18px;height:18px;background:#2a5298;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:10px;font-weight:700;flex-shrink:0;">${s.sequence}</span>
              <span style="font-size:13px;font-weight:700;color:${node.done ? '#1e3a5f' : '#718096'};">${s.branch?.name || '-'}</span>
              ${badge}
            </div>
            ${sItems.length > 0 ? `<table style="width:100%;font-size:12px;border-collapse:collapse;margin-bottom:6px;background:#fafafa;border:1px solid #e2e8f0;">
              <thead><tr style="background:#f0f4f8;">
                <th style="padding:4px 8px;text-align:left;font-weight:600;color:#4a5568;">สินค้า</th>
                <th style="padding:4px 8px;text-align:center;font-weight:600;color:#4a5568;width:70px;">จำนวน</th>
                <th style="padding:4px 8px;text-align:left;font-weight:600;color:#4a5568;width:50px;">หน่วย</th>
              </tr></thead>
              <tbody>${itemRows}</tbody>
            </table>` : ''}
            ${hasEv ? `<button onclick="var el=document.getElementById('${suid}');el.style.display=el.style.display==='none'?'flex':'none';" style="background:#f0fff4;border:1px solid #c6f6d5;color:#276749;padding:4px 12px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:5px;"><i class="fa-solid fa-images"></i> ดูหลักฐาน</button>
            <div id="${suid}" style="display:none;gap:12px;flex-wrap:wrap;margin-top:8px;">
              ${s.arrive_photo_path ? `<div><div style="font-size:11px;font-weight:700;color:#4a5568;margin-bottom:5px;">รูปหลังส่ง</div><div style="display:flex;flex-wrap:wrap;gap:6px;">${renderPhotoGrid(parsePhotoPaths(s.arrive_photo_path),'arrive-photos')}</div><div style="font-size:10px;color:#a0aec0;margin-top:3px;">กดที่รูปเพื่อดูเต็มจอ</div></div>` : ''}
              ${s.signature ? `<div><div style="font-size:11px;font-weight:700;color:#4a5568;margin-bottom:5px;">ลายเซ็น</div><img src="${s.signature}" style="max-width:180px;max-height:110px;border:1.5px solid #e2e8f0;background:#fff;padding:3px;display:block;cursor:zoom-in;" onclick="openPhotoViewer(this.src,'ลายเซ็น — ${s.branch?.name || ''}')"><div style="font-size:10px;color:#a0aec0;margin-top:3px;">กดที่รูปเพื่อดูเต็มจอ</div></div>` : ''}
            </div>` : ''}
          </div>
        </div>`;
      }
      // type === 'fixed'
      const p = node;
      const dotBorder = p.done ? (p.danger ? '#fc8181' : '#38a169') : '#cbd5e0';
      const dotBg     = p.done ? (p.danger ? '#fff5f5' : '#c6f6d5') : '#f7fafc';
      const txtColor  = p.done ? (p.danger ? '#c53030' : '#1e3a5f') : '#a0aec0';
      const timeColor = p.done ? (p.danger ? '#e53e3e' : '#276749') : '#a0aec0';
      const lineColor = p.done && !isLast ? '#38a169' : '#e2e8f0';
      const uid = 'tl-' + o.id + '-' + p.photoKey;
      let photoBlock = '';
      if (p.photoKey === 'depart' && o.depart_photo_path) {
        photoBlock = `<div style="margin-top:6px;">
          <button onclick="var el=document.getElementById('${uid}');el.style.display=el.style.display==='none'?'block':'none';" style="background:#ebf8ff;border:1px solid #bee3f8;color:#2a5298;padding:4px 12px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:5px;"><i class="fa-solid fa-camera"></i> ดูหลักฐาน</button>
          <div id="${uid}" style="display:none;margin-top:8px;">
            <div style="display:flex;flex-wrap:wrap;gap:6px;">${renderPhotoGrid(parsePhotoPaths(o.depart_photo_path),'depart-photos')}</div>
            <div style="font-size:10px;color:#a0aec0;margin-top:3px;">กดที่รูปเพื่อดูเต็มจอ</div>
          </div>
        </div>`;
      } else if (p.photoKey === 'arrive') {
        const rr = o.receive_record;
        const hasArrive = !!o.arrive_photo_path;
        const hasSig    = !!(rr && rr.signature);
        if (hasArrive || hasSig) {
          photoBlock = `<div style="margin-top:6px;">
            <button onclick="var el=document.getElementById('${uid}');el.style.display=el.style.display==='none'?'flex':'none';" style="background:#f0fff4;border:1px solid #c6f6d5;color:#276749;padding:4px 12px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:5px;"><i class="fa-solid fa-images"></i> ดูหลักฐาน</button>
            <div id="${uid}" style="display:none;gap:12px;flex-wrap:wrap;margin-top:8px;">
              ${hasArrive ? `<div><div style="font-size:11px;font-weight:700;color:#4a5568;margin-bottom:5px;">รูปหลังส่ง</div><div style="display:flex;flex-wrap:wrap;gap:6px;">${renderPhotoGrid(parsePhotoPaths(o.arrive_photo_path),'arrive-photos')}</div><div style="font-size:10px;color:#a0aec0;margin-top:3px;">กดที่รูปเพื่อดูเต็มจอ</div></div>` : ''}
              ${hasSig ? `<div><div style="font-size:11px;font-weight:700;color:#4a5568;margin-bottom:5px;">ลายเซ็น</div><img src="${rr.signature}" style="max-width:200px;max-height:130px;border:1.5px solid #e2e8f0;background:#fff;padding:3px;display:block;cursor:zoom-in;" onclick="openPhotoViewer(this.src,'ลายเซ็น')"><div style="font-size:10px;color:#a0aec0;margin-top:3px;">กดที่รูปเพื่อดูเต็มจอ</div></div>` : ''}
            </div>
          </div>`;
        }
      }
      return `<div style="display:flex;align-items:stretch;">
        <div style="width:76px;flex-shrink:0;text-align:right;padding-right:10px;padding-top:10px;">
          ${p.ts
            ? `<div style="font-size:10px;color:${timeColor};font-weight:700;line-height:1.5;">${formatDateTime(p.ts).replace(' ','\n').split('\n').join('<br>')}</div>`
            : `<div style="font-size:11px;color:#cbd5e0;">—</div>`}
        </div>
        <div style="display:flex;flex-direction:column;align-items:center;width:40px;flex-shrink:0;">
          <div style="width:2px;background:${i === 0 ? 'transparent' : '#e2e8f0'};flex:${i === 0 ? '0 0 10px' : '1'};min-height:10px;"></div>
          <div style="width:34px;height:34px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:${dotBg};border:2.5px solid ${dotBorder};">
            <i class="fa-solid ${p.icon}" style="font-size:13px;color:${p.done ? (p.danger ? '#c53030' : '#276749') : '#a0aec0'};"></i>
          </div>
          <div style="width:2px;background:${isLast ? 'transparent' : lineColor};flex:${isLast ? '0 0 10px' : '1'};min-height:10px;"></div>
        </div>
        <div style="padding:8px 0 12px 12px;flex:1;min-width:0;">
          <div style="font-size:13px;font-weight:700;color:${txtColor};padding-top:2px;">${p.label}</div>
          ${photoBlock}
        </div>
      </div>`;
    }).join('')}
  </div>`;



  document.getElementById('detail-content').innerHTML = `
    <div class="info-grid">
      <div class="info-item"><span class="label">ต้นทาง</span><span class="val"><i class="fa-solid fa-warehouse" style="color:#38a169;font-size:11px;margin-right:4px;"></i>${coSettings.hq_name}</span></div>
      <div class="info-item"><span class="label">ปลายทาง</span><span class="val">${orderBranchDisplay(o)}</span></div>
      <div class="info-item"><span class="label">สถานะ</span><span class="val">
        <span class="badge badge-${o.status}">${statusTH(o.status)}</span>
        ${o.sub_status ? `<span class="badge badge-${o.sub_status}" style="margin-left:4px;">${subStatusTH(o.sub_status)}</span>` : ''}
      </span></div>
      <div class="info-item"><span class="label">คนขับ</span><span class="val">${driverDisplay(o.driver)}</span></div>
      <div class="info-item"><span class="label">ทะเบียน</span><span class="val">${driverPlate(o.driver)}</span></div>
      <div class="info-item"><span class="label">วันที่นัดส่ง</span><span class="val">${formatDate(o.scheduled_date)}</span></div>
      <div class="info-item"><span class="label">สร้างโดย</span><span class="val">${o.creator?.name || '-'}</span></div>
      <div class="info-item"><span class="label">ส่งเมื่อ</span><span class="val">${formatDateTime(o.received_at)}</span></div>
    </div>
    ${(() => {
      const header = `<div style="font-size:11px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:.8px;margin-bottom:10px;"><i class="fa-solid fa-box-open" style="color:#4a90d9;"></i> รายการสินค้า</div>`;
      if (isMultiStop) {
        const blocks = sortedStops.map(s => {
          const items = s.items || [];
          if (!items.length) return '';
          const hasShortage = items.some(it => (it.shortage_qty || 0) > 0);
          const rows = items.map(it => {
            const sh = it.shortage_qty || 0;
            return `<tr>
              <td>${it.product_name}</td>
              <td style="text-align:center;">${it.qty_ordered}${sh > 0 ? `<span style="color:#c05621;font-weight:700;"> (-${sh})</span>` : ''}</td>
              <td style="text-align:center;">${it.unit}</td>
            </tr>`;
          }).join('');
          return `<div style="margin-bottom:8px;border:1px solid #e2e8f0;">
            <div style="display:flex;align-items:center;gap:6px;padding:6px 10px;background:#f0f4f8;border-bottom:1px solid #e2e8f0;">
              <span style="width:18px;height:18px;background:#2a5298;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:10px;font-weight:700;flex-shrink:0;">${s.sequence}</span>
              <span style="font-size:12px;font-weight:700;color:#1e3a5f;">${s.branch?.name || '-'}</span>
              ${hasShortage ? `<span style="font-size:10px;color:#c05621;font-weight:700;margin-left:auto;"><i class="fa-solid fa-triangle-exclamation"></i> มีของขาด</span>` : ''}
            </div>
            <table style="margin:0;"><thead><tr>
              <th>สินค้า</th><th style="text-align:center;">จำนวน</th><th style="text-align:center;">หน่วย</th>
            </tr></thead><tbody>${rows}</tbody></table>
          </div>`;
        }).join('');
        return `<div style="margin-top:16px;border-bottom:1.5px solid #e2e8f0;padding-bottom:16px;">${header}${blocks}</div>`;
      }
      const items = o.items || [];
      const totalShortage = items.reduce((s, i) => s + (i.shortage_qty || 0), 0);
      const hasShortage = totalShortage > 0;
      const hasNote = items.some(i => i.item_note || i.remark);
      const rows = items.map(i => {
        const shortage = i.shortage_qty || 0;
        const note = i.item_note || i.remark || '';
        return `<tr>
          <td>${i.product_name}</td>
          <td style="text-align:center;">${i.qty_ordered}</td>
          <td style="text-align:center;">${i.unit}</td>
          ${hasNote ? `<td style="font-size:12px;color:#718096;">${note || '-'}</td>` : ''}
        </tr>`;
      }).join('');
      const summaryRow = hasShortage
        ? `<tr style="background:#fff5f5;"><td colspan="${hasNote ? 4 : 3}" style="padding:8px 14px;">
            <span style="color:#c05621;font-weight:700;font-size:12px;">
              <i class="fa-solid fa-triangle-exclamation"></i>
              ยอดรวมสินค้าขาด: ${totalShortage} รายการ/หน่วย
            </span>
          </td></tr>`
        : '';
      return `<div style="margin-top:16px;border-bottom:1.5px solid #e2e8f0;padding-bottom:16px;">${header}<table style="margin-top:4px;"><thead><tr>
        <th>สินค้า</th><th style="text-align:center;">จำนวน</th>
        <th style="text-align:center;">หน่วย</th>
        ${hasNote ? '<th>หมายเหตุ</th>' : ''}
      </tr></thead><tbody>${rows}${summaryRow}</tbody></table></div>`;
    })()}
    ${tlHtml}
    ${o.receive_record ? (() => {
      const rr = o.receive_record;
      const rrStatusTH = {received_all:'รับครบ', partial:'รับไม่ครบ', rejected:'ปฏิเสธรับ'}[rr.status] || rr.status;
      const photos = rr.photos || [];
      if (!photos.length && !rr.remark) return '';
      const rrPhotosHtml = photos.length > 0
        ? `<div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:8px;">
            ${photos.map(p => {
              const fname = p.photo_path.split('/').pop();
              return `<img src="/api/evidence/${fname}" alt="หลักฐาน"
                style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:2px solid #c6f6d5;cursor:pointer;"
                onclick="window.open(this.src,'_blank')">`;
            }).join('')}
          </div>` : '';
      return `<div style="margin-top:12px;padding:10px 12px;background:#f0fff4;border-radius:8px;border:1px solid #c6f6d5;">
        <b style="color:#276749;font-size:12px;"><i class="fa-solid fa-circle-check"></i> บันทึกการรับสินค้า</b>
        <div style="margin-top:3px;font-size:12px;color:#2f855a;">สถานะ: <b>${rrStatusTH}</b>${rr.remark ? ` | หมายเหตุ: ${rr.remark}` : ''}</div>
        ${rrPhotosHtml}
      </div>`;
    })() : ''}
    ${boCardsHtml}`;
  setTimeout(initLocationNodes, 0);
  const actions = document.getElementById('detail-actions');
  actions.innerHTML = '';
  if (o.status === 'pending') {
    const _role = currentUser?.role;
    if (_role === 'admin' || _role === 'hq') {
      actions.innerHTML = `
        <div style="display:flex;flex-direction:column;gap:8px;width:100%;">
          <div style="background:#ebf8ff;border:1px solid #bee3f8;border-radius:8px;padding:10px 14px;font-size:13px;color:#2b6cb0;display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-clock"></i> รอไรเดอร์รับงาน
          </div>
          <button class="btn btn-danger" onclick="openCancelModal(${o.id})">
            <i class="fa-solid fa-xmark"></i> ยกเลิกออเดอร์
          </button>
        </div>`;
    }
  } else if (o.status === 'in_transit') {
    actions.innerHTML = `<button class="btn btn-success" onclick="quickStatus(${o.id},'completed');closeModal('modal-order-detail')"><i class="fa-solid fa-circle-check"></i> ส่งสำเร็จ</button>`;
  }
  document.getElementById('modal-order-detail').classList.add('open');
}


async function quickStatus(id, status) {
  const r = await api('PUT', '/api/delivery-orders/' + id, {status});
  if (r.ok) {
    markRecentAction(id);
    showToast('อัปเดตสถานะเรียบร้อย', 'success');
    loadOrders();
  } else {
    showToast(r.data.message || 'เกิดข้อผิดพลาด', 'error');
  }
}

function openCancelModal(id) {
  document.getElementById('cancel-order-id').value = id;
  document.getElementById('cancel-other-note').value = '';
  document.getElementById('cancel-other-wrap').style.display = 'none';

  // เหตุผลต่างกันตาม role
  const isDriver = currentUser?.role === 'driver';
  document.getElementById('cancel-reason-select').innerHTML = isDriver
    ? `<option value="">-- กรุณาเลือกเหตุผล --</option>
       <option value="รถเต็ม / ไม่มีที่บรรทุก">รถเต็ม / ไม่มีที่บรรทุก</option>
       <option value="รถมีปัญหา">รถมีปัญหา</option>
       <option value="เส้นทางมีปัญหา">เส้นทางมีปัญหา</option>
       <option value="ไม่สามารถเดินทางได้">ไม่สามารถเดินทางได้</option>
       <option value="อื่นๆ">อื่นๆ</option>`
    : `<option value="">-- กรุณาเลือกเหตุผล --</option>
       <option value="สินค้าหมดสต็อก">สินค้าหมดสต็อก</option>
       <option value="ลูกค้ายกเลิก">ลูกค้ายกเลิก</option>
       <option value="รถมีปัญหา">รถมีปัญหา</option>
       <option value="อื่นๆ">อื่นๆ</option>`;

  closeModal('modal-order-detail');
  closeModal('modal-driver-order-detail');
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
    markRecentAction(parseInt(id));
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
    markRecentAction(boId);
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
    markRecentAction(parseInt(id));
    showToast('ปฏิเสธ Backorder แล้ว', 'success');
    loadOrders();
  } else {
    showToast(r.data.message || 'เกิดข้อผิดพลาด', 'error');
  }
}

async function openCreateOrder() {
  await Promise.all([
    loadBranchOptions('mo-branch'),
    (async () => {
      if (allProductNamesCache.length === 0) {
        const r = await api('GET', '/api/delivery-items/products');
        if (r.ok) allProductNamesCache = r.data;
      }
    })(),
  ]);
  const _mo = new Date();
  document.getElementById('mo-date-native').value = new Date().toISOString().slice(0,10);
  syncDateToText(document.getElementById('mo-date-native'), 'mo-date-text');
  document.getElementById('mo-items-list').innerHTML = '';
  addItem();
  document.getElementById('modal-order').classList.add('open');
}

function addItem() {
  const list = document.getElementById('mo-items-list');
  const div = document.createElement('div');
  div.style.cssText = 'display:grid;grid-template-columns:1fr 80px 80px 32px;gap:6px;margin-bottom:6px;align-items:start;';
  div.innerHTML = `
    <div class="co-suggest-box">
      <input type="text" class="mo-item-name" placeholder="พิมพ์ชื่อสินค้า..." autocomplete="off"
        oninput="showCoSuggestions(this)" onblur="delayHideCoSuggestions(this)"
        style="border:1.5px solid #e2e8f0;border-radius:7px;padding:7px 10px;font-size:13px;font-family:inherit;width:100%;box-sizing:border-box;">
      <input type="hidden" class="co-item-id">
      <div class="co-suggestions"></div>
    </div>
    <input type="text" class="mo-item-unit" placeholder="หน่วย"
      style="border:1.5px solid #e2e8f0;border-radius:7px;padding:7px 8px;font-size:13px;font-family:inherit;width:100%;box-sizing:border-box;">
    <input type="number" class="mo-item-qty" placeholder="จำนวน" min="0.01" step="any"
      style="border:1.5px solid #e2e8f0;border-radius:7px;padding:7px 8px;font-size:13px;font-family:inherit;width:100%;box-sizing:border-box;">
    <button onclick="this.parentElement.remove()" class="btn btn-danger btn-sm" style="height:33px;">
      <i class="fa-solid fa-xmark"></i></button>`;
  list.appendChild(div);
}

async function submitOrder() {
  const rows = [...document.querySelectorAll('#mo-items-list > div')];
  if (rows.length === 0) { showToast('กรุณาเพิ่มสินค้าอย่างน้อย 1 รายการ', 'error'); return; }

  const items = rows.map(div => ({
    product_name: div.querySelector('.mo-item-name').value.trim(),
    unit: div.querySelector('.mo-item-unit').value.trim(),
    qty_ordered: parseFloat(div.querySelector('.mo-item-qty').value),
  }));

  const invalid = items.find(i => !i.product_name || !i.qty_ordered || i.qty_ordered <= 0);
  if (invalid) { showToast('กรุณาระบุชื่อสินค้าและจำนวนให้ครบ', 'error'); return; }

  const scheduled_date = document.getElementById('mo-date-native').value;
  if (!scheduled_date) { showToast('กรุณาเลือกวันที่นัดส่ง', 'error'); return; }
  const body = {
    branch_id: parseInt(document.getElementById('mo-branch').value),
    scheduled_date,
    items,
  };
  const r = await api('POST', '/api/delivery-orders', body);
  if (r.ok) { closeModal('modal-order'); if (r.data?.id) markRecentAction(r.data.id); showToast('สร้างออเดอร์เรียบร้อย', 'success'); loadOrders(); }
  else showToast(JSON.stringify(r.data.errors || r.data.message), 'error');
}

/* ---- CREATE ORDER PAGE ---- */
let coMap = null;
let coMarkersLayer = null;
let coRouteLayer   = null;
let coStopCounter  = 0;
let coDriverCache  = [];
let coSettings     = { hq_lat: 13.7563, hq_lng: 100.5018, hq_name: 'คลังสินค้ากลาง (HQ)' };

async function loadSettings() {
  const r = await api('GET', '/api/settings');
  if (r.ok) {
    coSettings.hq_lat  = parseFloat(r.data.hq_lat  || coSettings.hq_lat);
    coSettings.hq_lng  = parseFloat(r.data.hq_lng  || coSettings.hq_lng);
    coSettings.hq_name = r.data.hq_name || coSettings.hq_name;

    // Apply site branding globally on first load
    if (r.data.system_name) {
      const nameEl = document.getElementById('sidebar-system-name');
      if (nameEl) nameEl.textContent = r.data.system_name;
      document.title = r.data.system_name + ' — ระบบบริหารจัดการขนส่ง';
    }
    if (r.data.logo_base64) {
      applySiteLogo(r.data.logo_base64);
    }
  }
}

async function initCreateOrderPage() {
  const _t = new Date();
  document.getElementById('co-date-native').value = new Date().toISOString().slice(0,10);
  syncDateToText(document.getElementById('co-date-native'), 'co-date-text');

  // Load drivers + items + branches + settings in parallel
  const fetches = [loadSettings()];
  if (coDriverCache.length === 0) fetches.push(
    api('GET', '/api/users').then(r => { if (r.ok) coDriverCache = (r.data||[]).filter(u => u.role==='driver' && u.is_active); })
  );
  if (allProductNamesCache.length === 0) fetches.push(api('GET','/api/delivery-items/products').then(r => { if(r.ok) allProductNamesCache = r.data; }));
  if (allBranches.length === 0)   fetches.push(api('GET','/api/branches').then(r => { if(r.ok) allBranches = r.data; }));
  await Promise.all(fetches);

  const driverSel = document.getElementById('co-driver');
  driverSel.innerHTML = '<option value="">-- ไม่กำหนด (รอรับงาน) --</option>' +
    coDriverCache.map(d => `<option value="${d.id}">${d.name}</option>`).join('');

  // Update HQ label
  const hqLabel = document.getElementById('co-hq-label');
  if (hqLabel) hqLabel.textContent = coSettings.hq_name;

  // Reset stops
  document.getElementById('co-stops-list').innerHTML = '';
  coStopCounter = 0;
  addCoStop();

  // Init map
  setTimeout(() => {
    if (coMap) { coMap.remove(); coMap = null; coMarkersLayer = null; coRouteLayer = null; }
    coMap = L.map('co-map').setView([coSettings.hq_lat, coSettings.hq_lng], 11);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(coMap);
    coMarkersLayer = L.layerGroup().addTo(coMap);
    updateCoMap();
  }, 80);
}

function addCoStop() {
  coStopCounter++;
  const idx = coStopCounter;
  const branchOpts = '<option value="">-- เลือกสาขา --</option>' +
    (allBranches || []).filter(b => b.is_active !== false).map(b =>
      `<option value="${b.id}" data-lat="${b.lat||''}" data-lng="${b.lng||''}">${b.name}</option>`
    ).join('');

  const div = document.createElement('div');
  div.className = 'co-stop-card';
  div.dataset.idx = idx;
  div.innerHTML = `
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
      <span style="font-size:13px;font-weight:700;color:#2b6cb0;display:flex;align-items:center;gap:7px;">
        <span style="background:#4299e1;color:#fff;border-radius:50%;width:20px;height:20px;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;flex-shrink:0;" class="co-stop-num">${idx}</span>
        <span class="co-stop-label">จุดส่งที่ ${idx}</span>
      </span>
      <button onclick="removeCoStop(this.closest('.co-stop-card'))"
        style="background:#fed7d7;border:none;border-radius:6px;padding:4px 9px;cursor:pointer;color:#742a2a;font-size:12px;">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <div style="margin-bottom:10px;">
      <label style="font-size:12px;font-weight:600;color:#4a5568;display:block;margin-bottom:4px;">สาขาปลายทาง</label>
      <select class="co-branch-sel" onchange="updateCoMap()"
        style="border:1.5px solid #e2e8f0;border-radius:8px;padding:8px 10px;font-size:13px;font-family:inherit;background:#fff;width:100%;box-sizing:border-box;">
        ${branchOpts}
      </select>
    </div>
    <div style="font-size:12px;font-weight:600;color:#4a5568;margin-bottom:6px;">รายการสินค้า</div>
    <div class="co-items-list"></div>
    <button onclick="addCoItem(this.closest('.co-stop-card'))"
      style="margin-top:6px;width:100%;padding:6px;border:1.5px dashed #bee3f8;border-radius:7px;background:none;color:#4299e1;font-size:12px;font-weight:600;cursor:pointer;">
      <i class="fa-solid fa-plus"></i> เพิ่มสินค้าในจุดนี้
    </button>`;
  document.getElementById('co-stops-list').appendChild(div);
  addCoItem(div);
  updateCoMap();
}

function removeCoStop(card) {
  if (document.querySelectorAll('.co-stop-card').length <= 1) {
    showToast('ต้องมีอย่างน้อย 1 จุดส่ง', 'error'); return;
  }
  card.remove();
  document.querySelectorAll('.co-stop-card').forEach((c, i) => {
    const n = i + 1;
    const numEl = c.querySelector('.co-stop-num');
    const lblEl = c.querySelector('.co-stop-label');
    if (numEl) numEl.textContent = n;
    if (lblEl) lblEl.textContent = `จุดส่งที่ ${n}`;
  });
  updateCoMap();
}

function addCoItem(stopCard) {
  const list = stopCard.querySelector('.co-items-list');
  const row = document.createElement('div');
  row.className = 'co-item-row';
  row.style.cssText = 'display:grid;grid-template-columns:1fr 80px 80px 32px;gap:6px;margin-bottom:6px;align-items:start;';
  row.innerHTML = `
    <div class="co-suggest-box">
      <input type="text" class="co-item-name" placeholder="พิมพ์ชื่อสินค้า..." autocomplete="off"
        oninput="showCoSuggestions(this)" onblur="delayHideCoSuggestions(this)"
        style="border:1.5px solid #e2e8f0;border-radius:7px;padding:7px 10px;font-size:13px;font-family:inherit;width:100%;box-sizing:border-box;">
      <input type="hidden" class="co-item-id">
      <div class="co-suggestions"></div>
    </div>
    <input type="text" class="co-item-unit" placeholder="หน่วย"
      style="border:1.5px solid #e2e8f0;border-radius:7px;padding:7px 8px;font-size:13px;font-family:inherit;width:100%;box-sizing:border-box;">
    <input type="number" class="co-item-qty" placeholder="จำนวน" min="0.01" step="any"
      style="border:1.5px solid #e2e8f0;border-radius:7px;padding:7px 8px;font-size:13px;font-family:inherit;width:100%;box-sizing:border-box;">
    <button onclick="this.closest('.co-item-row').remove()"
      style="background:#fed7d7;border:none;border-radius:6px;padding:7px 8px;cursor:pointer;color:#742a2a;height:36px;">
      <i class="fa-solid fa-trash" style="font-size:11px;"></i>
    </button>`;
  list.appendChild(row);
}

function showCoSuggestions(input) {
  const q = input.value.trim().toLowerCase();
  const box = input.parentElement;
  const dropdown = box.querySelector('.co-suggestions');
  box.querySelector('.co-item-id').value = '';
  if (!q) { dropdown.style.display = 'none'; return; }
  const seen = new Set();
  const matches = allProductNamesCache.filter(i => {
    const n = (i.product_name||'').toLowerCase();
    return n.includes(q) && !seen.has(n) && seen.add(n);
  }).slice(0, 8);
  if (!matches.length) { dropdown.style.display = 'none'; return; }
  dropdown.innerHTML = matches.map(i =>
    `<div class="co-suggest-item" data-id="" data-unit="${i.unit||''}" data-name="${i.product_name}"
          onmousedown="selectCoItem(event,this)">
      <span><b>${i.product_name}</b></span>
      <span style="font-size:11px;color:#a0aec0;margin-left:8px;">${i.unit||''}</span>
    </div>`
  ).join('');
  dropdown.style.display = 'block';
}

function selectCoItem(e, el) {
  e.preventDefault();
  const row = el.closest('.co-item-row');
  row.querySelector('.co-item-name').value  = el.dataset.name;
  row.querySelector('.co-item-id').value    = el.dataset.id;
  row.querySelector('.co-item-unit').value  = el.dataset.unit;
  el.closest('.co-suggestions').style.display = 'none';
  row.querySelector('.co-item-qty').focus();
}

function delayHideCoSuggestions(input) {
  setTimeout(() => {
    const d = input.parentElement.querySelector('.co-suggestions');
    if (d) d.style.display = 'none';
  }, 200);
}

function coHaversineKm(lat1, lng1, lat2, lng2) {
  const R = 6371, toRad = d => d * Math.PI / 180;
  const dLat = toRad(lat2 - lat1), dLng = toRad(lng2 - lng1);
  const a = Math.sin(dLat/2)**2 + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLng/2)**2;
  return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
}

function coFmtDuration(minutes) {
  if (minutes < 60) return `~${Math.round(minutes)} นาที`;
  const h = Math.floor(minutes / 60), m = Math.round(minutes % 60);
  return m > 0 ? `~${h} ชม. ${m} นาที` : `~${h} ชั่วโมง`;
}

function updateCoMap() {
  if (!coMap) return;

  if (coMarkersLayer) coMarkersLayer.clearLayers(); else coMarkersLayer = L.layerGroup().addTo(coMap);
  if (coRouteLayer)   { coMap.removeLayer(coRouteLayer); coRouteLayer = null; }

  const hqIcon   = L.divIcon({className:'', html:'<div style="background:#48bb78;border:3px solid #fff;border-radius:50%;width:16px;height:16px;box-shadow:0 2px 6px rgba(0,0,0,.35);"></div>', iconSize:[16,16], iconAnchor:[8,8]});
  const stopIcon = L.divIcon({className:'', html:'<div style="background:#4299e1;border:3px solid #fff;border-radius:50%;width:14px;height:14px;box-shadow:0 2px 6px rgba(0,0,0,.3);"></div>', iconSize:[14,14], iconAnchor:[7,7]});

  const hqLat = coSettings.hq_lat, hqLng = coSettings.hq_lng;
  L.marker([hqLat, hqLng], {icon: hqIcon})
    .bindPopup(`<b>${coSettings.hq_name}</b>`)
    .addTo(coMarkersLayer);

  const points = [[hqLat, hqLng]];
  const infoLines = [];

  document.querySelectorAll('.co-branch-sel').forEach((sel, i) => {
    const opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) return;
    const lat = parseFloat(opt.dataset.lat);
    const lng = parseFloat(opt.dataset.lng);
    if (!lat || !lng) return;
    points.push([lat, lng]);
    L.marker([lat, lng], {icon: stopIcon})
      .bindPopup(`<b>จุดส่งที่ ${i+1}</b><br>${opt.text}`)
      .addTo(coMarkersLayer);
    infoLines.push(`<i class="fa-solid fa-location-dot"></i> จุดส่ง ${i+1}: ${opt.text}`);
  });

  if (points.length > 1) {
    coRouteLayer = L.polyline(points, {color:'#4299e1', weight:3, dashArray:'6,8', opacity:.8}).addTo(coMap);
    coMap.fitBounds(L.latLngBounds(points), {padding:[40,40]});
  }

  // คำนวณระยะทางรวม (haversine × road factor 1.35) และเวลาวิ่ง (25 km/h เฉลี่ยในเมือง)
  let totalKm = 0;
  for (let i = 0; i < points.length - 1; i++) {
    totalKm += coHaversineKm(points[i][0], points[i][1], points[i+1][0], points[i+1][1]);
  }
  const roadKm  = totalKm * 1.35;
  const minutes = (roadKm / 25) * 60;

  const driverSel  = document.getElementById('co-driver');
  const driverName = driverSel?.selectedIndex > 0 ? driverSel.options[driverSel.selectedIndex].text : null;
  const infoEl = document.getElementById('co-map-info');

  if (infoLines.length > 0) {
    infoEl.innerHTML =
      (driverName ? `<b style="color:#2d3748;"><i class="fa-solid fa-truck"></i> ${driverName}</b> &nbsp;·&nbsp; ` : '') +
      `<i class="fa-solid fa-location-dot"></i> จุดส่ง: ${infoLines.length} สาขา &nbsp;·&nbsp; ` +
      `<b style="color:#2b6cb0;"><i class="fa-solid fa-clock"></i> เวลาวิ่งรถรวม: ${coFmtDuration(minutes)}</b>` +
      `<br><span style="color:#718096;font-size:11px;">${infoLines.join(' &nbsp;›&nbsp; ')}</span>` +
      `<span style="color:#a0aec0;font-size:10px;margin-left:6px;">(ระยะ ~${roadKm.toFixed(1)} กม.)</span>`;
  } else {
    infoEl.innerHTML = '<span style="color:#a0aec0;">แผนที่จะอัปเดตเส้นทางอัตโนมัติเมื่อมีการเพิ่มจุดส่ง</span>';
  }
}

async function submitCreateOrder() {
  const date = document.getElementById('co-date-native').value;
  if (!date) { showToast('กรุณาเลือกวันที่นัดส่ง', 'error'); return; }

  const driverIdRaw = document.getElementById('co-driver').value;
  const driverId = driverIdRaw ? parseInt(driverIdRaw) : null;
  const stopCards = [...document.querySelectorAll('.co-stop-card')];
  if (!stopCards.length) { showToast('กรุณาเพิ่มจุดส่งอย่างน้อย 1 จุด', 'error'); return; }

  // รวบรวม stops พร้อมพิกัดสำหรับเรียงระยะทาง
  const rawStops = [];
  for (let i = 0; i < stopCards.length; i++) {
    const card = stopCards[i];
    const sel  = card.querySelector('.co-branch-sel');
    const branchId = parseInt(sel.value);
    if (!branchId) { showToast(`กรุณาเลือกสาขาสำหรับจุดส่งที่ ${i+1}`, 'error'); return; }

    const opt = sel.options[sel.selectedIndex];
    const lat = parseFloat(opt.dataset.lat) || 0;
    const lng = parseFloat(opt.dataset.lng) || 0;
    const distKm = (lat && lng)
      ? coHaversineKm(coSettings.hq_lat, coSettings.hq_lng, lat, lng)
      : Infinity;

    const itemRows = [...card.querySelectorAll('.co-item-row')];
    if (!itemRows.length) { showToast(`กรุณาเพิ่มสินค้าอย่างน้อย 1 รายการในจุดส่งที่ ${i+1}`, 'error'); return; }

    const items = [];
    for (const row of itemRows) {
      const itemId = parseInt(row.querySelector('.co-item-id').value) || null;
      const name   = row.querySelector('.co-item-name').value.trim();
      const unit   = row.querySelector('.co-item-unit').value.trim();
      const qty    = parseFloat(row.querySelector('.co-item-qty').value);
      if (!name) { showToast(`กรุณาระบุชื่อสินค้าในจุดส่งที่ ${i+1}`, 'error'); return; }
      if (!qty || qty <= 0) { showToast(`กรุณาระบุจำนวนสินค้าในจุดส่งที่ ${i+1}`, 'error'); return; }
      items.push(itemId ? {item_id:itemId, qty_ordered:qty} : {product_name:name, unit:unit||'', qty_ordered:qty});
    }
    rawStops.push({branch_id:branchId, items, distKm});
  }

  // เรียงใกล้สุดก่อน แล้วใส่ sequence
  rawStops.sort((a, b) => a.distKm - b.distKm);
  const stops = rawStops.map(({branch_id, items}, idx) => ({branch_id, sequence: idx + 1, items}));

  // ส่ง 1 request เดียว
  const r = await api('POST', '/api/delivery-orders', {driver_id: driverId, scheduled_date: date, stops});
  if (!r.ok) {
    showToast((r.data?.errors ? Object.values(r.data.errors).flat().join(', ') : r.data?.message) || 'เกิดข้อผิดพลาด', 'error');
    return;
  }

  if (r.data?.id) markRecentAction(r.data.id);
  showToast('สร้างออเดอร์สำเร็จ', 'success');
  navigate('orders');
}

/* ════════════════════════════════════════════════════════
   SETTINGS PAGE
   ════════════════════════════════════════════════════════ */
let stMap = null, stMarker = null;

function stSetPin(lat, lng) {
  document.getElementById('st-hq-lat').value  = lat.toFixed(6);
  document.getElementById('st-hq-lng').value  = lng.toFixed(6);
  document.getElementById('st-coords').value  = lat.toFixed(6) + ', ' + lng.toFixed(6);
  if (stMarker) stMap.removeLayer(stMarker);
  stMarker = L.marker([lat, lng]).addTo(stMap);
  stMap.setView([lat, lng], stMap.getZoom() < 13 ? 13 : stMap.getZoom());
}

async function pinFromCoordsSettings() {
  const val = document.getElementById('st-coords').value.trim();
  const parts = val.split(',');
  if (parts.length < 2) { showToast('รูปแบบพิกัดไม่ถูกต้อง เช่น 13.7563, 100.5018', 'error'); return; }
  const lat = parseFloat(parts[0].trim()), lng = parseFloat(parts[1].trim());
  if (isNaN(lat) || isNaN(lng)) { showToast('พิกัดไม่ถูกต้อง', 'error'); return; }
  stSetPin(lat, lng);
  showToast('ปักหมุดเรียบร้อย', 'success');
}

async function loadSettingsPage() {
  const r = await api('GET', '/api/settings');
  let lat = 13.7563, lng = 100.5018;

  if (r.ok) {
    const s = r.data;

    // HQ location
    document.getElementById('st-hq-name').value = s.hq_name || '';
    lat = parseFloat(s.hq_lat) || lat;
    lng = parseFloat(s.hq_lng) || lng;
    document.getElementById('st-hq-lat').value = lat;
    document.getElementById('st-hq-lng').value = lng;
    document.getElementById('st-coords').value = lat + ', ' + lng;

    // System info
    document.getElementById('sc-system-name').value = s.system_name || 'BH-Logistic';

    // Logo
    if (s.logo_base64) {
      applySiteLogo(s.logo_base64);
    }

    // Security
    document.getElementById('sc-login-attempts').value   = s.login_max_attempts        || 5;
    document.getElementById('sc-session-timeout').value  = s.session_timeout_minutes   || 480;
    document.getElementById('sc-password-days').value    = s.password_change_days      || 0;

    // Export dates
    const infoEl = document.getElementById('sc-export-info');
    if (s.last_export_csv || s.last_export_excel) {
      infoEl.style.display = 'block';
      if (s.last_export_csv)   document.getElementById('sc-last-csv').textContent   = 'CSV ล่าสุด: '   + s.last_export_csv;
      if (s.last_export_excel) document.getElementById('sc-last-excel').textContent = 'Excel ล่าสุด: ' + s.last_export_excel;
    }
  }

  // Map
  setTimeout(() => {
    if (stMap) { stMap.remove(); stMap = null; stMarker = null; }
    stMap = L.map('st-map').setView([lat, lng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(stMap);
    stMarker = L.marker([lat, lng]).addTo(stMap);
    stMap.on('click', function(e) { stSetPin(e.latlng.lat, e.latlng.lng); });
  }, 100);

  loadBroadcastHistory();
  loadExportDropdowns();
}

// ── Section 1: System Info ──────────────────────────────
// ── resize image via Canvas before use ─────────────────
function resizeImageToBase64(src, maxPx, quality, callback) {
  const img = new Image();
  img.onload = () => {
    const scale = Math.min(1, maxPx / Math.max(img.width, img.height));
    const w = Math.round(img.width  * scale);
    const h = Math.round(img.height * scale);
    const canvas = document.createElement('canvas');
    canvas.width = w; canvas.height = h;
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, w, h);
    ctx.drawImage(img, 0, 0, w, h);
    callback(canvas.toDataURL('image/png', quality));
  };
  img.src = src;
}

// Pending resized logo (after file select, before save)
let _pendingLogo = null;

function previewLogo(event) {
  const file = event.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    // Resize to max 256×256 PNG — fits comfortably in TEXT column
    resizeImageToBase64(e.target.result, 256, 1.0, resized => {
      _pendingLogo = resized;
      applySiteLogo(resized);
    });
  };
  reader.readAsDataURL(file);
}

let _pwaManifestUrl = null;

function applySiteLogo(base64) {
  const sysName = document.getElementById('sidebar-system-name')?.textContent || 'BH-Logistic';

  // ① Settings preview box
  const icon = document.getElementById('sc-logo-icon');
  const img  = document.getElementById('sc-logo-img');
  if (icon) icon.style.display = 'none';
  if (img)  { img.src = base64; img.style.display = ''; }
  const clearBtn = document.getElementById('sc-logo-clear');
  if (clearBtn) clearBtn.style.display = '';

  // ② Sidebar logo-box
  const sideBox = document.getElementById('sidebar-logo-box');
  if (sideBox) sideBox.innerHTML =
    `<img src="${base64}" style="width:28px;height:28px;object-fit:contain;border-radius:4px;" alt="logo">`;

  // ③ Login screen logo-box
  const loginBox = document.getElementById('login-logo-icon');
  if (loginBox) loginBox.innerHTML =
    `<img src="${base64}" style="width:40px;height:40px;object-fit:contain;border-radius:8px;" alt="logo">`;

  // ④ Favicon (browser tab) — base64 works fine here
  const faviconEl = document.getElementById('link-favicon');
  if (faviconEl) faviconEl.href = base64;

  // ⑤ Apple Touch Icon + ⑥ Manifest — must use HTTP URL (iOS/Android don't allow data: URIs)
  const logoUrl = '/api/public/logo?v=' + Date.now();
  const atEl = document.getElementById('link-apple-touch');
  if (atEl) atEl.href = logoUrl;

  // ⑥ Web App Manifest (Android/Chrome PWA)
  if (_pwaManifestUrl) URL.revokeObjectURL(_pwaManifestUrl);
  const manifest = {
    name: sysName,
    short_name: sysName,
    start_url: '/',
    display: 'standalone',
    background_color: '#ffffff',
    theme_color: '#1e3a5f',
    icons: [
      { src: logoUrl, sizes: '192x192', type: 'image/png', purpose: 'any maskable' },
      { src: logoUrl, sizes: '512x512', type: 'image/png', purpose: 'any maskable' },
    ],
  };
  const blob = new Blob([JSON.stringify(manifest)], { type: 'application/manifest+json' });
  _pwaManifestUrl = URL.createObjectURL(blob);
  const manifestEl = document.getElementById('link-manifest');
  if (manifestEl) manifestEl.href = _pwaManifestUrl;
}

function clearLogo() {
  _pendingLogo = null;

  // ① Settings preview box
  const icon = document.getElementById('sc-logo-icon');
  if (icon) icon.style.display = '';
  const img = document.getElementById('sc-logo-img');
  if (img) { img.src = 'data:,'; img.style.display = 'none'; }
  const clearBtn = document.getElementById('sc-logo-clear');
  if (clearBtn) clearBtn.style.display = 'none';
  const fileInput = document.getElementById('sc-logo-file');
  if (fileInput) fileInput.value = '';

  // ② Sidebar
  const sideBox = document.getElementById('sidebar-logo-box');
  if (sideBox) sideBox.innerHTML = '<i class="fa-solid fa-truck"></i>';

  // ③ Login screen
  const loginBox = document.getElementById('login-logo-icon');
  if (loginBox) loginBox.innerHTML = '<i class="fa-solid fa-truck"></i>';

  // ④⑤ Favicon + Apple touch
  const fav = document.getElementById('link-favicon');
  const at  = document.getElementById('link-apple-touch');
  if (fav) fav.href = '/favicon.ico';
  if (at)  at.href  = '/favicon.ico';
}

async function saveSystemInfo() {
  const name    = document.getElementById('sc-system-name').value.trim() || 'BH-Logistic';
  const imgEl   = document.getElementById('sc-logo-img');
  const hasLogo = imgEl?.style.display !== 'none' && imgEl?.src && !imgEl.src.startsWith('data:,');

  const payload = { system_name: name };
  if (hasLogo && _pendingLogo) {
    payload.logo_base64 = _pendingLogo;
  } else if (hasLogo && imgEl.src.startsWith('data:image/')) {
    // Already saved logo loaded from settings
    payload.logo_base64 = imgEl.src;
  } else if (!hasLogo) {
    payload.logo_base64 = '';
  }

  const r = await api('PUT', '/api/settings', payload);
  if (r.ok) {
    _pendingLogo = null;
    document.title = name + ' — ระบบบริหารจัดการขนส่ง';
    const sideNameEl  = document.getElementById('sidebar-system-name');
    const loginNameEl = document.getElementById('login-system-name');
    const appTitleEl  = document.getElementById('meta-app-title');
    if (sideNameEl)  sideNameEl.textContent  = name;
    if (loginNameEl) loginNameEl.textContent  = name;
    if (appTitleEl)  appTitleEl.content       = name;
    showToast('บันทึกข้อมูลระบบเรียบร้อย', 'success');
  } else showToast(r.data?.message || 'เกิดข้อผิดพลาด', 'error');
}

function resetSystemInfo() {
  document.getElementById('sc-system-name').value = 'BH-Logistic';
  clearLogo();
}

// ── Section 2: Security ─────────────────────────────────
async function saveSecurity() {
  const attempts = parseInt(document.getElementById('sc-login-attempts').value) || 5;
  const timeout  = parseInt(document.getElementById('sc-session-timeout').value) || 480;
  const days     = parseInt(document.getElementById('sc-password-days').value)   || 0;

  const r = await api('PUT', '/api/settings', {
    login_max_attempts:      attempts,
    session_timeout_minutes: timeout,
    password_change_days:    days,
  });
  if (r.ok) showToast('บันทึกการตั้งค่าความปลอดภัยเรียบร้อย', 'success');
  else showToast(r.data?.message || 'เกิดข้อผิดพลาด', 'error');
}

function resetSecurity() {
  document.getElementById('sc-login-attempts').value  = 5;
  document.getElementById('sc-session-timeout').value = 480;
  document.getElementById('sc-password-days').value   = 0;
}

// ── Section 3: Export ───────────────────────────────────
async function loadExportDropdowns() {
  const branchSel = document.getElementById('ex-branch');
  const driverSel = document.getElementById('ex-driver');
  // Only load once — check if options already populated
  if (branchSel.options.length > 1) return;

  const [bRes, uRes] = await Promise.all([
    fetch('/api/branches', { headers: { Authorization: 'Bearer ' + token } }),
    fetch('/api/users',    { headers: { Authorization: 'Bearer ' + token } }),
  ]);
  if (bRes.ok) {
    const branches = await bRes.json();
    (branches.data ?? branches).forEach(b => {
      const o = document.createElement('option');
      o.value = b.id; o.textContent = b.name;
      branchSel.appendChild(o);
    });
  }
  if (uRes.ok) {
    const users = await uRes.json();
    (users.data ?? users).filter(u => u.role === 'driver').forEach(u => {
      const o = document.createElement('option');
      o.value = u.id; o.textContent = u.name;
      driverSel.appendChild(o);
    });
  }
}

function clearExportFilters() {
  ['ex-date-from','ex-date-to'].forEach(id => document.getElementById(id).value = '');
  ['ex-branch','ex-driver','ex-status'].forEach(id => document.getElementById(id).value = '');
}

async function exportOrders(type) {
  const btnId = type === 'csv' ? 'sc-btn-csv' : 'sc-btn-excel';
  const btn   = document.getElementById(btnId);
  const orig  = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> กำลัง Export...';

  try {
    const params = new URLSearchParams();
    const dateFrom = document.getElementById('ex-date-from')?.value;
    const dateTo   = document.getElementById('ex-date-to')?.value;
    const branch   = document.getElementById('ex-branch')?.value;
    const driver   = document.getElementById('ex-driver')?.value;
    const status   = document.getElementById('ex-status')?.value;
    if (dateFrom) params.set('date_from', dateFrom);
    if (dateTo)   params.set('date_to',   dateTo);
    if (branch)   params.set('branch_id', branch);
    if (driver)   params.set('driver_id', driver);
    if (status)   params.set('status',    status);

    const base     = type === 'csv' ? '/api/admin/export/csv' : '/api/admin/export/excel';
    const endpoint = params.toString() ? `${base}?${params}` : base;
    const res = await fetch(endpoint, {
      headers: { Authorization: 'Bearer ' + token },
    });
    if (!res.ok) { showToast('เกิดข้อผิดพลาดในการ Export', 'error'); return; }

    const blob = await res.blob();
    const url  = URL.createObjectURL(blob);
    const ext  = type === 'csv' ? 'csv' : 'xls';
    const now  = new Date();
    const ts   = `${now.getFullYear()}${String(now.getMonth()+1).padStart(2,'0')}${String(now.getDate()).padStart(2,'0')}_${String(now.getHours()).padStart(2,'0')}${String(now.getMinutes()).padStart(2,'0')}`;
    const a    = document.createElement('a');
    a.href     = url;
    a.download = `orders_${ts}.${ext}`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);

    const now2 = new Date().toLocaleString('th-TH');
    const infoEl = document.getElementById('sc-export-info');
    infoEl.style.display = 'block';
    if (type === 'csv')   document.getElementById('sc-last-csv').textContent   = 'CSV ล่าสุด: '   + now2;
    else                  document.getElementById('sc-last-excel').textContent = 'Excel ล่าสุด: ' + now2;

    showToast(`Export ${type.toUpperCase()} เรียบร้อย`, 'success');
  } finally {
    btn.disabled = false;
    btn.innerHTML = orig;
  }
}

// ── Section 4: Broadcast ────────────────────────────────
function toggleAllRoles(cb) {
  ['sc-role-driver','sc-role-branch','sc-role-hq'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.checked = cb.checked;
  });
  updateCheckItemStyles();
}

function updateCheckItemStyles() {
  document.querySelectorAll('.sc-check-item').forEach(item => {
    const cb = item.querySelector('input[type=checkbox]');
    item.classList.toggle('checked', cb?.checked);
  });
}

document.addEventListener('change', e => {
  if (e.target.closest('.sc-check-item')) updateCheckItemStyles();
});

async function sendBroadcast() {
  const msg  = document.getElementById('sc-broadcast-msg').value.trim();
  if (!msg)  { showToast('กรุณาพิมพ์ข้อความก่อนส่ง', 'error'); return; }

  const roles = [];
  if (document.getElementById('sc-role-all')?.checked)    roles.push('all');
  if (document.getElementById('sc-role-driver')?.checked)  roles.push('driver');
  if (document.getElementById('sc-role-branch')?.checked)  roles.push('branch');
  if (document.getElementById('sc-role-hq')?.checked)      roles.push('hq');
  if (!roles.length) { showToast('กรุณาเลือกกลุ่มผู้รับ', 'error'); return; }

  const priority = document.getElementById('sc-broadcast-priority').value;

  const r = await api('POST', '/api/admin/broadcast', { message: msg, target_roles: roles, priority });
  if (r.ok) {
    showToast(`ส่งแจ้งเตือนถึง ${r.data.sent_to} คน เรียบร้อย`, 'success');
    document.getElementById('sc-broadcast-msg').value = '';
    document.querySelectorAll('.sc-check-item input').forEach(cb => { cb.checked = false; });
    updateCheckItemStyles();
    loadBroadcastHistory();
  } else showToast(r.data?.message || 'เกิดข้อผิดพลาด', 'error');
}

async function loadBroadcastHistory() {
  const r = await api('GET', '/api/admin/broadcast/history');
  const el = document.getElementById('sc-broadcast-history');
  if (!el) return;

  if (!r.ok || !r.data.length) {
    el.innerHTML = '<p style="color:#a0aec0;font-size:13px;text-align:center;padding:12px 0;">ยังไม่มีประวัติการส่ง</p>';
    return;
  }

  const priorityLabel = { normal:'ปกติ', important:'สำคัญ', urgent:'ด่วนมาก' };
  const priorityBadge = { normal:'badge-bc-normal', important:'badge-bc-important', urgent:'badge-bc-urgent' };

  const rolesLabel = r => {
    if (r === 'all' || r.includes('all')) return 'ทุกคน';
    return r.split(',').map(x => ({'driver':'Driver','branch':'Branch','hq':'HQ','admin':'Admin'}[x]||x)).join(', ');
  };

  el.innerHTML = r.data.map(b => `
    <div class="sc-broadcast-row">
      <div style="font-size:13px;color:#2d3748;line-height:1.4;">${b.message}</div>
      <div class="sc-broadcast-meta">
        <span class="badge ${priorityBadge[b.priority]||'badge-bc-normal'}">${priorityLabel[b.priority]||b.priority}</span>
        <span style="font-size:11px;color:#718096;"><i class="fa-solid fa-users"></i> ${rolesLabel(b.target_roles)}</span>
        <span style="font-size:11px;color:#a0aec0;"><i class="fa-solid fa-user-check"></i> ${b.recipient_count} คน</span>
        <span style="font-size:11px;color:#a0aec0;margin-left:auto;">${b.created_at||''}</span>
      </div>
    </div>`).join('');
}

// ── HQ Location (existing) ──────────────────────────────
async function saveHqSettings() {
  const hqLat  = parseFloat(document.getElementById('st-hq-lat').value);
  const hqLng  = parseFloat(document.getElementById('st-hq-lng').value);
  const hqName = document.getElementById('st-hq-name').value.trim() || 'คลังสินค้ากลาง (HQ)';
  if (!hqLat || !hqLng) { showToast('กรุณาปักหมุดตำแหน่ง HQ ก่อนบันทึก', 'error'); return; }
  const r = await api('PUT', '/api/settings', { hq_lat: hqLat, hq_lng: hqLng, hq_name: hqName });
  if (r.ok) {
    coSettings.hq_lat  = hqLat;
    coSettings.hq_lng  = hqLng;
    coSettings.hq_name = hqName;
    showToast('บันทึกการตั้งค่าเรียบร้อย', 'success');
  } else showToast(r.data?.message || 'เกิดข้อผิดพลาด', 'error');
}

// Keep backward compat alias
function saveSettings() { saveHqSettings(); }

/* ---- BRANCHES ---- */
async function loadBranches() {
  const r = await api('GET', '/api/branches');
  if (!r.ok) return;
  allBranches = r.data;
  const isAdmin = currentUser?.role === 'admin';
  document.getElementById('btn-add-branch').style.display = isAdmin ? '' : 'none';
  document.getElementById('branch-th-manage').style.display = isAdmin ? '' : 'none';
  document.getElementById('branch-count').textContent = `${r.data.length} สาขา`;
  filterBranches();
}

function filterBranches() {
  const search = (document.getElementById('branch-search')?.value || '').toLowerCase().trim();
  const isAdmin = currentUser?.role === 'admin';
  const cols = isAdmin ? 6 : 5;
  const list = search
    ? allBranches.filter(b =>
        (b.name || '').toLowerCase().includes(search) ||
        (b.address || '').toLowerCase().includes(search))
    : allBranches;
  document.getElementById('branches-tbody').innerHTML = list.length === 0
    ? `<tr><td colspan="${cols}"><div class="empty-state"><i class="fa-solid fa-building"></i><p>${search ? 'ไม่พบสาขาที่ค้นหา' : 'ยังไม่มีสาขา'}</p></div></td></tr>`
    : list.map(b => `<tr>
        <td>${b.id}</td>
        <td><b>${b.name}</b></td>
        <td style="color:#718096;font-size:12px;">${b.address || '-'}</td>
        <td style="font-size:12px;color:#718096;">${b.lat ? `${b.lat}, ${b.lng}` : '-'}</td>
        <td><span class="badge ${b.is_active ? 'badge-delivered':'badge-cancelled'}">${b.is_active ? 'ใช้งาน':'ปิด'}</span></td>
        ${isAdmin ? `<td>
          <button class="btn btn-secondary btn-sm" onclick="openEditBranch(${b.id})"><i class="fa-solid fa-pen"></i> แก้ไข</button>
        </td>` : ''}
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
  if (!await showConfirmDialog('ยืนยันลบสาขานี้? ข้อมูลจะถูกซ่อน (Soft Delete)', 'ลบสาขา')) return;
  const r = await api('DELETE', '/api/branches/' + id);
  if (r.ok) { showToast('ลบสาขาเรียบร้อย', 'success'); allBranches = []; loadBranches(); }
  else showToast(r.data.message || 'เกิดข้อผิดพลาด', 'error');
}

async function deleteBranchFromModal() {
  const id = parseInt(document.getElementById('eb-id').value);
  if (!id) return;
  if (!await showConfirmDialog('ยืนยันลบสาขานี้? ข้อมูลจะถูกซ่อน (Soft Delete)', 'ลบสาขา')) return;
  const r = await api('DELETE', '/api/branches/' + id);
  if (r.ok) { closeModal('modal-edit-branch'); showToast('ลบสาขาเรียบร้อย', 'success'); allBranches = []; loadBranches(); }
  else showToast(r.data.message || 'เกิดข้อผิดพลาด', 'error');
}


/* ════════════════════════════════════════════════════════════
   GPS — Multi-drop Live Tracking
   ════════════════════════════════════════════════════════════ */

/* ── Driver: ส่ง GPS ── */
function useMyLocation() {
  if (!navigator.geolocation) return showToast('Browser ไม่รองรับ GPS', 'error');
  navigator.geolocation.getCurrentPosition(p => {
    document.getElementById('gps-lat').value = p.coords.latitude.toFixed(6);
    document.getElementById('gps-lng').value = p.coords.longitude.toFixed(6);
    showToast('ได้รับพิกัดแล้ว', 'success');
  });
}

async function sendGps() {
  const body = {
    lat: parseFloat(document.getElementById('gps-lat').value),
    lng: parseFloat(document.getElementById('gps-lng').value),
  };
  if (!body.lat || !body.lng) { showToast('กรุณาใส่พิกัด GPS', 'error'); return; }
  const r = await api('POST', '/api/gps/update', body);
  const el = document.getElementById('gps-result');
  el.innerHTML = r.ok
    ? `<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> บันทึก GPS เรียบร้อย — ${body.lat}, ${body.lng}</div>`
    : `<div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> ${JSON.stringify(r.data.errors || r.data.message)}</div>`;
}

let pendingGpsVehicleId = null;

function navigateToGps(driverId) {
  pendingGpsVehicleId = driverId;
  navigate('gps');
}

/* ── State ── */
let driverVehicleId = null;
let gpsMap         = null;
let gpsMarkers     = {};
let gpsRoutes      = {};
let gpsMapInterval = null;
let watchId        = null;
let isTracking     = false;
let gpsTimerId     = null;
let autoStartGps   = false;

let gpsCurrentFilter = 'all';
let gpsAllDriverData = [];   // cache [{driver, gpsData, orders, status, distInfo}]

/* ── Init ── */
async function initGpsPage() {
  if (gpsMapInterval) { clearInterval(gpsMapInterval); gpsMapInterval = null; }
  const isDriver = currentUser?.role === 'driver';

  document.getElementById('gps-send-card').style.display   = isDriver ? 'block' : 'none';
  document.getElementById('gps-admin-panel').style.display = isDriver ? 'none'  : 'block';

  if (isDriver) {
    document.getElementById('gps-lat-group').style.display       = 'none';
    document.getElementById('gps-lng-group').style.display       = 'none';
    document.getElementById('gps-manual-btns').style.display     = 'none';
    document.getElementById('gps-tracking-section').style.display = 'block';
    driverVehicleId = currentUser?.id || null;
    if (autoStartGps && !isTracking && driverVehicleId) { autoStartGps = false; startTracking(); }
  } else {
    initGpsMapIfNeeded();
    // รอให้ DOM แสดง panel ก่อน แล้วค่อย recalculate size
    setTimeout(() => gpsMap?.invalidateSize(), 100);
    await loadAllDriversPanel();
    if (pendingGpsVehicleId) {
      const card = document.querySelector(`.gps-driver-card[data-driver-id="${pendingGpsVehicleId}"]`);
      if (card) card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      autoFocusDriverOnMap(pendingGpsVehicleId);
      pendingGpsVehicleId = null;
    } else {
      // เด้งไปที่คนขับที่กำลังวิ่งอยู่อัตโนมัติ
      const firstActive = gpsAllDriverData.find(d => d.driverStatus === 'active');
      if (firstActive) {
        setTimeout(() => {
          const card = document.querySelector(`.gps-driver-card[data-driver-id="${firstActive.driver.id}"]`);
          if (card) card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
          autoFocusDriverOnMap(firstActive.driver.id);
        }, 300);
      }
    }
    gpsMapInterval = setInterval(refreshGpsMapMarkers, 10000);
  }
}

/* ── Map init ── */
function initGpsMapIfNeeded() {
  if (gpsMap) return;
  gpsMap = L.map('gps-map').setView([13.7563, 100.5018], 10);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    maxZoom: 19,
  }).addTo(gpsMap);
}

/* ── Load all drivers panel (left side) ── */
async function loadAllDriversPanel() {
  const listEl = document.getElementById('gps-drivers-list');
  listEl.innerHTML = '<div class="loading">กำลังโหลดข้อมูลคนขับ...</div>';

  const ur = await api('GET', '/api/users');
  if (!ur.ok || !Array.isArray(ur.data)) {
    listEl.innerHTML = '<div class="empty-state"><i class="fa-solid fa-triangle-exclamation"></i><p>โหลดข้อมูลไม่ได้</p></div>';
    return;
  }
  const drivers = ur.data.filter(u => u.role === 'driver' && u.is_active);
  if (!drivers.length) {
    listEl.innerHTML = '<div class="empty-state"><i class="fa-solid fa-user-slash"></i><p>ยังไม่มีคนขับในระบบ</p></div>';
    return;
  }

  // Populate history select
  const sel = document.getElementById('gps-vid');
  if (sel) {
    sel.innerHTML = '<option value="">-- เลือกคนขับ --</option>';
    drivers.forEach(d => { const o = document.createElement('option'); o.value = d.id; o.textContent = d.name; sel.appendChild(o); });
  }

  // Fetch GPS + orders for each driver in parallel
  const now = Date.now();
  gpsAllDriverData = await Promise.all(drivers.map(async d => {
    const [gr, or, dr] = await Promise.all([
      api('GET', '/api/gps/driver/' + d.id),
      api('GET', '/api/delivery-orders?driver_id=' + d.id),
      api('GET', '/api/gps/distance/' + d.id),
    ]);

    const gpsData  = (gr.ok && Array.isArray(gr.data) && gr.data.length) ? gr.data[0] : null;
    const allOrders = (or.ok && or.data?.data) ? or.data.data
                    : (or.ok && Array.isArray(or.data)) ? or.data : [];
    // in_transit แสดงทั้งหมด, pending แสดงเฉพาะวันนี้ขึ้นไป (ตัดออเดอร์เก่าค้างออก)
    const todayStr = new Date().toISOString().slice(0, 10);
    const orders = allOrders.filter(o => {
      if (o.status === 'in_transit') return true;
      if (o.status === 'pending') return !o.scheduled_date || o.scheduled_date >= todayStr;
      return false;
    });
    const distInfo = (dr.ok && dr.data?.branch_lat) ? dr.data : null;

    // สถานะหลักกำหนดจากออเดอร์ ไม่ใช่ GPS
    let driverStatus;
    if (orders.some(o => o.status === 'in_transit')) {
      driverStatus = 'active';   // กำลังวิ่ง
    } else if (orders.some(o => o.status === 'pending')) {
      driverStatus = 'waiting';  // รองาน
    } else {
      driverStatus = 'idle';     // ว่างงาน
    }

    // GPS staleness warning (ไม่เปลี่ยนสถานะหลัก)
    let gpsStale = false;
    if (gpsData) {
      const ageMs = now - new Date(gpsData.recorded_at).getTime();
      gpsStale = ageMs > 15 * 60 * 1000; // ⚠️ ถ้าไม่อัปเดตเกิน 15 นาที
    }

    return { driver: d, gpsData, orders, distInfo, driverStatus, gpsStale };
  }));

  // Update counters
  const cnt = { active: 0, waiting: 0, idle: 0 };
  gpsAllDriverData.forEach(d => cnt[d.driverStatus]++);
  document.getElementById('cnt-active').textContent  = cnt.active;
  document.getElementById('cnt-waiting').textContent = cnt.waiting;
  document.getElementById('cnt-idle').textContent    = cnt.idle;

  renderDriverCards();
  await refreshGpsMapMarkers();
}

/* ── Render driver cards (left panel) ── */
function renderDriverCards() {
  const listEl  = document.getElementById('gps-drivers-list');
  const search  = (document.getElementById('gps-search')?.value || '').toLowerCase();
  const filter  = gpsCurrentFilter;

  const sorted = [...gpsAllDriverData].sort((a, b) => {
    const order = { active: 0, waiting: 1, idle: 2 };
    return (order[a.driverStatus] ?? 3) - (order[b.driverStatus] ?? 3);
  });

  const filtered = sorted.filter(d => {
    if (filter !== 'all' && d.driverStatus !== filter) return false;
    if (search) {
      const nameMatch    = d.driver.name.toLowerCase().includes(search);
      const branchMatch  = d.orders.some(o => (o.branch?.name || '').toLowerCase().includes(search));
      const productMatch = d.orders.some(o => (o.items || []).some(i => (i.product_name || '').toLowerCase().includes(search)));
      if (!nameMatch && !branchMatch && !productMatch) return false;
    }
    return true;
  });

  if (!filtered.length) {
    listEl.innerHTML = '<div class="empty-state" style="padding:32px;"><i class="fa-solid fa-magnifying-glass"></i><p>ไม่พบคนขับที่ตรงกับเงื่อนไข</p></div>';
    return;
  }

  listEl.innerHTML = filtered.map(d => renderDriverCard(d)).join('');
}


function renderDriverCard({ driver, gpsData, orders, distInfo, driverStatus, gpsStale }) {
  const statusMeta = {
    active:  { dot: '#38a169', bg: '#f0fff4', border: '#9ae6b4', label: 'กำลังวิ่ง' },
    waiting: { dot: '#d69e2e', bg: '#fffff0', border: '#f6e05e', label: 'รองาน' },
    idle:    { dot: '#a0aec0', bg: '#f7fafc', border: '#e2e8f0', label: 'ว่างงาน' },
  }[driverStatus] ?? { dot: '#a0aec0', bg: '#f7fafc', border: '#e2e8f0', label: 'ว่างงาน' };

  // GPS age text
  let gpsAgeText = 'ไม่มีข้อมูล GPS';
  let speedText  = '';
  if (gpsData) {
    const ageMs = Date.now() - new Date(gpsData.recorded_at).getTime();
    const mins  = Math.floor(ageMs / 60000);
    const hrs   = Math.floor(mins / 60), remMins = mins % 60;
    gpsAgeText = mins < 1 ? 'เพิ่งอัปเดต'
               : hrs > 0  ? `${hrs} ชม. ${remMins} น. ที่แล้ว`
                           : `${mins} น. ที่แล้ว`;
    if (gpsData.speed != null) speedText = `ความเร็ว: ${Math.round(gpsData.speed * 3.6)} กม./ชม.`;
  }

  // Stats
  const transitOrders  = orders.filter(o => o.status === 'in_transit');
  const completedToday = orders.filter(o => ['delivered', 'completed'].includes(o.status));
  const etaText  = distInfo?.eta_minutes  ? `ETA ~${distInfo.eta_minutes} น.` : '';
  const distText = distInfo?.distance_km  ? `${distInfo.distance_km} กม.`     : '';

  // Trip sequence — sort: completed → in_transit → pending
  const statusOrder = { completed:0, delivered:0, in_transit:1, pending:2 };
  const tripOrders  = [...orders].sort((a, b) => (statusOrder[a.status] ?? 9) - (statusOrder[b.status] ?? 9));

  // Expand multi-stop orders into individual stop entries
  const tripEntries = [];
  tripOrders.forEach(o => {
    const hasStops = o.stops && o.stops.length > 0;
    if (hasStops) {
      const sorted = [...o.stops].sort((a, b) => (a.sequence || 0) - (b.sequence || 0));
      sorted.forEach(s => {
        tripEntries.push({
          order: o, stop: s,
          branch: s.branch,
          isDone: ['delivered','completed'].includes(s.status),
          receivedAt: s.delivered_at,
          items: s.items || [],
        });
      });
    } else {
      tripEntries.push({
        order: o, stop: null,
        branch: o.branch,
        isDone: ['delivered','completed'].includes(o.status),
        receivedAt: o.received_at,
        items: o.items || [],
      });
    }
  });
  // Done entries first, then pending (stable sort)
  tripEntries.sort((a, b) => a.isDone === b.isDone ? 0 : a.isDone ? -1 : 1);

  // Accordion: active drivers start expanded, others collapsed
  const isExpanded = driverStatus === 'active';

  // Header badge
  const orderBadge = tripEntries.length
    ? `<span style="background:#ebf4ff;color:#2b6cb0;border-radius:20px;padding:2px 10px;font-size:11px;font-weight:700;white-space:nowrap;flex-shrink:0;"><i class="fa-solid fa-map-pin"></i> ${tripEntries.length} จุดส่ง</span>`
    : `<span style="background:#f0f4f8;color:#a0aec0;border-radius:20px;padding:2px 10px;font-size:11px;white-space:nowrap;flex-shrink:0;"><i class="fa-solid fa-clipboard-list"></i> ไม่มีงาน</span>`;

  // Group pending entries by branch (same branch = deliver together, no back-and-forth)
  const _done    = tripEntries.filter(e => e.isDone);
  const _pending = tripEntries.filter(e => !e.isDone);

  const _branchGroups = [];
  const _branchSeen   = new Map();
  _pending.forEach(e => {
    const key = e.branch?.id != null ? 'b' + e.branch.id : 'u' + e.order.id + '_' + (e.stop?.id || '');
    if (e.branch?.id != null && _branchSeen.has(key)) {
      _branchSeen.get(key).entries.push(e);
    } else {
      const grp = { branch: e.branch, entries: [e] };
      _branchSeen.set(key, grp);
      _branchGroups.push(grp);
    }
  });

  const firstPendingIdx = _done.length;
  const stopsHtml = [
    ..._done.map(({ order: o, branch, receivedAt, items }, idx) => {
      const num = idx + 1;
      const bLat = branch?.lat, bLng = branch?.lng;
      const safeName = (branch?.name || '').replace(/'/g,'');
      const focusCall = (bLat && bLng && gpsData) ? `focusOnStop(${driver.id},${gpsData.lat},${gpsData.lng},${bLat},${bLng},'${safeName}')` : '';
      const branchName = branch?.name || 'ไม่ระบุสาขา';
      const iSummary = items.slice(0,3).map(i=>i.product_name).join(', ') + (items.length>3?` +${items.length-3} รายการ`:'');
      const timeHtml = receivedAt ? `<div style="font-size:11px;color:#718096;margin-top:3px;">สำเร็จเมื่อ: ${formatDateTime(receivedAt)}</div>` : '';
      return `<div class="gps-stop" style="opacity:.55;">
        <div class="gps-stop-dot" style="background:#38a169;box-shadow:0 0 0 2px #38a169;"></div>
        <div style="flex:1;min-width:0;">
          <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;${focusCall?'cursor:pointer;':''}" ${focusCall?`onclick="${focusCall}" title="คลิกเพื่อซูมแผนที่"`:''}>
            <span style="background:#c6f6d5;color:#276749;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;flex-shrink:0;">จุดที่ ${num}</span>
            <span style="font-size:13px;font-weight:700;color:#718096;flex:1;min-width:0;">${branchName}</span>
            <span class="gps-badge gps-badge-done"><i class="fa-solid fa-circle-check"></i> ส่งสำเร็จ</span>
          </div>
          <div style="font-size:11px;color:#718096;margin-top:5px;">
            <i class="fa-solid fa-file-lines" style="color:#a0aec0;"></i> ${o.do_number}
            ${iSummary ? `<span style="color:#a0aec0;"> · </span>${iSummary}` : ''}
          </div>
          ${timeHtml}
        </div>
      </div>`;
    }),
    ..._branchGroups.map((grp, gi) => {
      const num = _done.length + gi + 1;
      const isCurrent = gi === 0;
      const { branch, entries } = grp;
      const bLat = branch?.lat, bLng = branch?.lng;
      const safeName = (branch?.name || '').replace(/'/g,'');
      const focusCall = (bLat && bLng && gpsData) ? `focusOnStop(${driver.id},${gpsData.lat},${gpsData.lng},${bLat},${bLng},'${safeName}')` : '';
      const branchName = branch?.name || 'ไม่ระบุสาขา';
      const dotColor = isCurrent ? '#3182ce' : '#a0aec0';
      const numBg    = isCurrent ? '#bee3f8' : '#edf2f7';
      const numColor = isCurrent ? '#2b6cb0' : '#718096';
      const badge = isCurrent
        ? `<span class="gps-badge gps-badge-transit"><i class="fa-solid fa-truck"></i> กำลังเดินทาง${etaText?' · '+etaText:''}</span>`
        : `<span class="gps-badge gps-badge-queue"><i class="fa-solid fa-hourglass-half"></i> รอจัดส่ง</span>`;
      const currentBanner = isCurrent
        ? `<div style="display:inline-flex;align-items:center;gap:5px;background:#ebf8ff;border:1px solid #bee3f8;color:#2b6cb0;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;margin-top:5px;"><i class="fa-solid fa-truck-moving"></i> กำลังมุ่งหน้าไปจุดนี้</div>` : '';
      const ordersSubHtml = entries.length > 1
        ? `<div style="margin-top:8px;border-left:2.5px solid ${isCurrent?'#bee3f8':'#e2e8f0'};padding-left:10px;display:flex;flex-direction:column;gap:5px;">
            ${entries.map(e => {
              const eItems = e.items.slice(0,3).map(i=>i.product_name).join(', ') + (e.items.length>3?` +${e.items.length-3} รายการ`:'');
              return `<div style="font-size:11px;color:#4a5568;"><i class="fa-solid fa-box" style="color:${isCurrent?'#3182ce':'#a0aec0'};margin-right:4px;font-size:10px;"></i><b>${e.order.do_number}</b>${eItems?` · ${eItems}`:''}</div>`;
            }).join('')}
          </div>`
        : (() => {
            const e = entries[0];
            const eItems = e.items.slice(0,3).map(i=>i.product_name).join(', ') + (e.items.length>3?` +${e.items.length-3} รายการ`:'');
            return `<div style="font-size:11px;color:#718096;margin-top:5px;"><i class="fa-solid fa-file-lines" style="color:#a0aec0;"></i> ${e.order.do_number}${eItems?`<span style="color:#a0aec0;"> · </span>${eItems}`:''}</div>`;
          })();
      return `<div class="gps-stop">
        <div class="gps-stop-dot" style="background:${dotColor};box-shadow:0 0 0 2px ${dotColor};"></div>
        <div style="flex:1;min-width:0;">
          <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;${focusCall?'cursor:pointer;':''}" ${focusCall?`onclick="${focusCall}" title="คลิกเพื่อซูมแผนที่"`:''}>
            <span style="background:${numBg};color:${numColor};font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;flex-shrink:0;">จุดที่ ${num}</span>
            <span style="font-size:13px;font-weight:700;color:#1e3a5f;flex:1;min-width:0;">${branchName}</span>
            ${badge}
            ${entries.length > 1 ? `<span style="background:#e9d8fd;color:#6b46c1;font-size:10px;font-weight:700;padding:1px 7px;border-radius:20px;flex-shrink:0;">${entries.length} ออเดอร์</span>` : ''}
          </div>
          ${currentBanner}
          ${ordersSubHtml}
        </div>
      </div>`;
    }),
  ].join('');

  return `
    <div class="gps-driver-card" data-driver-id="${driver.id}" data-status="${driverStatus}" data-expanded="${isExpanded}"
      style="border-color:${statusMeta.border};background:${statusMeta.bg};">

      <!-- ── Header (always visible, click to toggle) ── -->
      <div class="gps-card-header" onclick="toggleDriverCard(${driver.id})">
        <i class="fa-solid fa-chevron-right gps-chevron${isExpanded ? ' open' : ''}"></i>
        <i class="fa-solid fa-id-card" style="color:#4a90d9;font-size:13px;flex-shrink:0;"></i>
        <span style="font-size:14px;font-weight:700;color:#1e3a5f;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${driver.name}</span>
        <span style="display:flex;align-items:center;gap:4px;flex-shrink:0;">
          <span style="color:${statusMeta.dot};font-size:9px;">●</span>
          <span style="color:${statusMeta.dot};font-size:11px;font-weight:600;">${statusMeta.label}</span>
          ${gpsStale ? `<span title="GPS ไม่ได้อัปเดตเกิน 15 นาที" style="color:#d69e2e;font-size:12px;">⚠️</span>` : ''}
        </span>
        ${orderBadge}
        ${gpsData ? `
        <button onclick="event.stopPropagation();autoFocusDriverOnMap(${driver.id})"
          style="background:#1e3a5f;color:#fff;border:none;padding:5px 11px;font-size:11px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:5px;flex-shrink:0;font-family:inherit;">
          <i class="fa-solid fa-map-location-dot"></i> ดูบนแผนที่
        </button>` : ''}
      </div>

      <!-- ── Body (collapsible) ── -->
      <div class="gps-card-body" style="display:${isExpanded ? 'block' : 'none'};">
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:10px;">
          <span class="gps-stat-pill" style="${gpsStale ? 'color:#d69e2e;border-color:#f6e05e;background:#fffff0;' : ''}">
            <i class="fa-solid fa-satellite-dish"></i> ${gpsStale ? '⚠️ ' : ''}${gpsAgeText}
          </span>
          ${distText ? `<span class="gps-stat-pill"><i class="fa-solid fa-road"></i> ${distText}</span>` : ''}
          ${etaText  ? `<span class="gps-stat-pill"><i class="fa-solid fa-clock"></i> ${etaText}</span>`  : ''}
          ${speedText ? `<span class="gps-stat-pill"><i class="fa-solid fa-gauge-high"></i> ${speedText}</span>` : ''}
          <span class="gps-stat-pill"><i class="fa-solid fa-check-double" style="color:#38a169;"></i> ส่งแล้ว ${completedToday.length} จุด</span>
          ${transitOrders.length ? `<span class="gps-stat-pill"><i class="fa-solid fa-truck-moving" style="color:#3182ce;"></i> วิ่งอยู่ ${transitOrders.length} ออเดอร์</span>` : ''}
        </div>

        ${!gpsData ? `
          <div style="font-size:12px;color:#a0aec0;padding:4px 0;">
            <i class="fa-solid fa-satellite-dish"></i> ยังไม่มีข้อมูล GPS
          </div>` : ''}
      </div>
    </div>`;
}

/* ── Accordion Toggle ── */
function toggleDriverCard(driverId) {
  const card = document.querySelector(`.gps-driver-card[data-driver-id="${driverId}"]`);
  if (!card) return;
  const body    = card.querySelector('.gps-card-body');
  const chevron = card.querySelector('.gps-chevron');
  const wasOpen = card.dataset.expanded === 'true';
  if (wasOpen) {
    card.dataset.expanded = 'false';
    body.style.display    = 'none';
    chevron?.classList.remove('open');
  } else {
    card.dataset.expanded = 'true';
    body.style.display    = 'block';
    chevron?.classList.add('open');
    autoFocusDriverOnMap(driverId);
  }
}

function autoFocusDriverOnMap(driverId) {
  if (!gpsMap) return;
  const d = gpsAllDriverData.find(x => x.driver.id == driverId);
  if (!d) return;

  const points = [];
  if (d.gpsData) points.push([parseFloat(d.gpsData.lat), parseFloat(d.gpsData.lng)]);
  (d.orders || []).filter(o => o.branch?.lat && o.branch?.lng)
    .forEach(o => points.push([parseFloat(o.branch.lat), parseFloat(o.branch.lng)]));

  if (!points.length) return;
  points.length === 1
    ? gpsMap.setView(points[0], 14, { animate: true })
    : gpsMap.fitBounds(L.latLngBounds(points), { padding: [50, 50], animate: true });

  if (gpsMarkers[driverId]) gpsMarkers[driverId].openPopup();
  document.querySelectorAll('.gps-driver-card').forEach(c => c.classList.remove('gps-card-focused'));
  const card = document.querySelector(`.gps-driver-card[data-driver-id="${driverId}"]`);
  if (card) card.classList.add('gps-card-focused');
}

/* ── Filter & Search ── */
function setGpsFilter(filter) {
  gpsCurrentFilter = filter;
  document.querySelectorAll('.gps-filter-btn').forEach(b => {
    b.classList.toggle('gps-filter-active', b.dataset.filter === filter);
  });
  renderDriverCards();
}

function filterDriverCards() { renderDriverCards(); }

/* ── Map: refresh markers only (no re-init) ── */
async function refreshGpsMapMarkers() {
  if (!gpsMap) return;
  const now = Date.now();

  // Update map status
  const statusEl = document.getElementById('gps-map-status');
  if (statusEl) statusEl.innerHTML = `<i class="fa-solid fa-satellite-dish"></i> อัปเดต ${new Date().toLocaleTimeString('th-TH')}`;

  // Re-fetch GPS for each cached driver
  for (const d of gpsAllDriverData) {
    const gr = await api('GET', '/api/gps/driver/' + d.driver.id);
    if (!gr.ok || !Array.isArray(gr.data) || !gr.data.length) continue;
    const latest = gr.data[0];
    const lat  = parseFloat(latest.lat);
    const lng  = parseFloat(latest.lng);
    const time = formatDateTime(latest.recorded_at);

    const ageMs      = now - new Date(latest.recorded_at).getTime();
    const isOnline   = ageMs < 30 * 60 * 1000;
    const markerColor = isOnline ? '#e53e3e' : '#a0aec0';

    const driverIcon = L.divIcon({
      html: `<div style="background:${markerColor};color:#fff;padding:4px 9px;border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap;box-shadow:0 2px 6px rgba(0,0,0,.35);display:flex;align-items:center;gap:5px;">
               <i class="fa-solid fa-truck"></i> ${d.driver.name}
             </div>`,
      iconAnchor: [50, 14], className: '',
    });

    const phone = d.driver.phone || '';
    const contactBtn = phone
      ? `<a href="tel:${phone}" style="display:inline-flex;align-items:center;gap:5px;background:#38a169;color:#fff;padding:5px 11px;border-radius:6px;font-size:12px;font-weight:700;text-decoration:none;"><i class="fa-solid fa-phone"></i> ${phone}</a>`
      : `<div style="color:#a0aec0;font-size:11px;"><i class="fa-solid fa-phone-slash"></i> ยังไม่มีเบอร์ติดต่อ</div>`;
    const popup = `<div style="min-width:170px;">
      <div style="font-weight:700;font-size:13px;margin-bottom:4px;"><i class="fa-solid fa-truck" style="color:#2b6cb0;"></i> ${d.driver.name}</div>
      <div style="font-size:11px;color:#718096;margin-bottom:8px;">อัปเดต: ${time}</div>
      ${contactBtn}
    </div>`;

    if (gpsMarkers[d.driver.id]) {
      gpsMarkers[d.driver.id].setLatLng([lat, lng]).setIcon(driverIcon).setPopupContent(popup);
    } else {
      gpsMarkers[d.driver.id] = L.marker([lat, lng], { icon: driverIcon }).addTo(gpsMap).bindPopup(popup, { maxWidth: 220 });
    }
  }
}

/* ── Focus helpers ── */
function focusDriverOnMap(driverId, lat, lng) {
  if (!gpsMap) return;
  if (lat && lng) {
    gpsMap.setView([lat, lng], 14, { animate: true });
    if (gpsMarkers[driverId]) gpsMarkers[driverId].openPopup();
  } else {
    // Look up from cache
    const d = gpsAllDriverData.find(x => x.driver.id == driverId);
    if (d?.gpsData) gpsMap.setView([parseFloat(d.gpsData.lat), parseFloat(d.gpsData.lng)], 14, { animate: true });
  }
  // Highlight card
  document.querySelectorAll('.gps-driver-card').forEach(c => c.classList.remove('gps-card-focused'));
  const card = document.querySelector(`.gps-driver-card[data-driver-id="${driverId}"]`);
  if (card) { card.classList.add('gps-card-focused'); card.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); }
}

function focusOnStop(driverId, driverLat, driverLng, branchLat, branchLng, branchName) {
  if (!gpsMap) return;
  const bounds = L.latLngBounds([[driverLat, driverLng], [branchLat, branchLng]]);
  gpsMap.fitBounds(bounds, { padding: [60, 60], animate: true });
  // Open branch marker popup
  const key = Object.keys(gpsMarkers).find(k => k.startsWith(`branch_${driverId}_`) && gpsMarkers[k]);
  if (key) {
    const bLat = gpsMarkers[key].getLatLng().lat;
    if (Math.abs(bLat - branchLat) < 0.001) gpsMarkers[key].openPopup();
  }
}

/* ── OSRM Route ── */
async function fetchOsrmRoute(fromLat, fromLng, toLat, toLng) {
  try {
    const url = `https://router.project-osrm.org/route/v1/driving/${fromLng},${fromLat};${toLng},${toLat}?overview=full&geometries=geojson`;
    const res = await fetch(url, { signal: AbortSignal.timeout(6000) });
    const data = await res.json();
    if (data.code === 'Ok' && data.routes?.[0]) {
      return data.routes[0].geometry.coordinates.map(c => [c[1], c[0]]);
    }
  } catch {}
  return [[fromLat, fromLng], [toLat, toLng]];
}

/* ── GPS History (bottom table) ── */
function onDriverSelect(vid) {
  loadGpsHistory(vid);
  if (vid) focusDriverOnMap(parseInt(vid));
}

async function reverseGeocode(lat, lng) {
  try {
    const res = await fetch(
      `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`,
      { headers: { 'Accept-Language': 'th' } }
    );
    const d = await res.json();
    return d.display_name || `${lat}, ${lng}`;
  } catch { return `${lat}, ${lng}`; }
}

async function loadGpsHistory(vid) {
  if (!vid) return;
  const r = await api('GET', '/api/gps/driver/' + vid);
  const tbody = document.getElementById('gps-tbody');
  if (!r.ok || !r.data.length) {
    tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;color:#a0aec0;padding:24px;">ไม่มีข้อมูล GPS</td></tr>`;
    return;
  }
  tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;color:#a0aec0;padding:12px;"><i class="fa-solid fa-spinner fa-spin"></i> กำลังโหลดตำแหน่ง...</td></tr>`;
  const records = r.data.slice(0, 20);
  const rows = [];
  for (const g of records) {
    const place = await reverseGeocode(g.lat, g.lng);
    const speedKph = g.speed != null ? `${Math.round(g.speed * 3.6)} กม./ชม.` : '-';
    rows.push(`<tr>
      <td style="font-size:12px;white-space:nowrap;">${formatDateTime(g.recorded_at)}</td>
      <td style="font-size:12px;">${place}</td>
      <td style="font-size:12px;text-align:center;">${speedKph}</td>
      <td style="font-size:12px;color:#718096;">${g.delivery_order?.do_number || '-'}</td>
    </tr>`);
  }
  tbody.innerHTML = rows.join('');
}

/* ── Driver GPS tracking (role=driver) ── */
function haversineDistance(lat1, lng1, lat2, lng2) {
  const R = 6371000;
  const dLat = (lat2 - lat1) * Math.PI / 180;
  const dLng = (lng2 - lng1) * Math.PI / 180;
  const a = Math.sin(dLat/2) ** 2
    + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLng/2) ** 2;
  return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}

async function startTracking() {
  driverVehicleId = currentUser?.id || null;
  if (!driverVehicleId) { showToast('ไม่พบข้อมูลผู้ใช้ — กรุณาล็อกอินใหม่', 'error'); return; }
  if (!navigator.geolocation) { showToast('Browser ไม่รองรับ GPS', 'error'); return; }
  if (location.protocol !== 'https:' && location.hostname !== 'localhost') {
    showToast('GPS ต้องการ HTTPS', 'error');
    document.getElementById('gps-result').innerHTML =
      '<div class="alert alert-error"><i class="fa-solid fa-lock"></i> GPS ต้องการ HTTPS<br><small>กรุณาเข้าใช้งานผ่านลิงก์ https://</small></div>';
    return;
  }
  isTracking = true;
  let currentLat = null, currentLng = null, lastSentLat = null, lastSentLng = null;
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
      currentLat = pos.coords.latitude; currentLng = pos.coords.longitude;
      if (lastSentLat !== null && haversineDistance(lastSentLat, lastSentLng, currentLat, currentLng) < 50) return;
      lastSentLat = currentLat; lastSentLng = currentLng;
      await doSendGps(currentLat, currentLng);
    },
    (err) => {
      const msgs = { 1:'GPS ถูกบล็อก', 2:'ไม่สามารถระบุตำแหน่งได้', 3:'หมดเวลาดึงสัญญาณ GPS' };
      const msg = msgs[err.code] || 'ดึง GPS ไม่ได้ (code ' + err.code + ')';
      showToast(msg, 'error');
      document.getElementById('gps-result').innerHTML = `<div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> ${msg}</div>`;
      stopTracking();
    },
    { enableHighAccuracy: true, maximumAge: 0, timeout: 15000 }
  );

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


/* ---- BRANCH: ติดตามสินค้า ---- */
let trackOrdersCache = [];

async function loadTrackOrders() {
  const el = document.getElementById('track-list');
  const countEl = document.getElementById('track-count');
  if (!el) return;
  el.innerHTML = '<div class="loading">กำลังโหลด...</div>';
  const r = await api('GET', '/api/branch/delivery-orders');
  if (!r.ok) {
    el.innerHTML = '<div class="empty-state"><i class="fa-solid fa-circle-exclamation"></i><p>โหลดข้อมูลไม่สำเร็จ</p></div>';
    return;
  }
  const orders = (r.data.data || r.data || [])
    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
  trackOrdersCache = orders;
  if (countEl) countEl.textContent = `${orders.length} รายการ`;
  if (!orders.length) {
    el.innerHTML = '<div class="empty-state"><i class="fa-solid fa-inbox"></i><p>ยังไม่มีออเดอร์</p></div>';
    return;
  }

  el.innerHTML = orders.map(o => {
    const pts = [
      { val: o.dispatched_at, icon: 'fa-box-open' },
      { val: o.dispatched_at, icon: 'fa-truck' },
      { val: o.arrived_at,   icon: 'fa-location-dot' },
      { val: o.received_at,  icon: 'fa-circle-check' },
    ];
    const miniTimeline = `<div style="display:flex;align-items:center;margin-top:12px;">
      ${pts.map((p, i) => `
        <div style="width:26px;height:26px;border-radius:50%;flex-shrink:0;
          background:${p.val ? '#c6f6d5' : '#f7fafc'};
          border:2px solid ${p.val ? '#38a169' : '#cbd5e0'};
          display:flex;align-items:center;justify-content:center;">
          <i class="fa-solid ${p.icon}" style="font-size:10px;color:${p.val ? '#276749' : '#cbd5e0'};"></i>
        </div>
        ${i < pts.length - 1 ? `<div style="flex:1;height:2px;background:${p.val && pts[i+1]?.val ? '#38a169' : '#e2e8f0'};"></div>` : ''}
      `).join('')}
    </div>`;
    return `
    <div onclick="openBranchDoDetail(${o.id})"
      style="border:1.5px solid #e2e8f0;border-radius:12px;padding:16px;margin-bottom:10px;cursor:pointer;background:#fff;transition:all .15s;"
      onmouseover="this.style.borderColor='#63b3ed';this.style.background='#ebf8ff';"
      onmouseout="this.style.borderColor='#e2e8f0';this.style.background='#fff';">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;">
        <div style="min-width:0;flex:1;">
          <div style="font-weight:700;font-size:15px;color:#2d3748;">
            ${itemsSummary(o.items)}${o.is_backorder ? `<span style="font-size:11px;background:#e2e8f0;padding:1px 6px;border-radius:4px;margin-left:4px;color:#718096;font-weight:600;">BO</span>` : ''}
          </div>
          <div style="font-size:11px;color:#a0aec0;margin-top:1px;">${o.do_number}</div>
          <div style="color:#718096;font-size:12px;margin-top:4px;">
            <i class="fa-regular fa-calendar fa-sm"></i> ${formatDate(o.scheduled_date)}
            &nbsp;·&nbsp; <i class="fa-solid fa-boxes-stacked fa-sm"></i> ${(o.items||[]).length} รายการ
          </div>
        </div>
        <span class="badge badge-${o.status}" style="flex-shrink:0;margin-left:8px;">${statusTH(o.status)}</span>
      </div>
      ${miniTimeline}
    </div>`;
  }).join('');
}

/* ---- RETURNS (Branch) ---- */
const reasonTH = { damaged:'สินค้าชำรุด', expired:'สินค้าหมดอายุ', wrong_item:'ส่งผิดรายการ', other:'อื่นๆ' };
const returnStatusTH = { pending:'รอดำเนินการ', approved:'อนุมัติ', rejected:'ปฏิเสธ' };
const returnStatusBadge = { pending:'badge-pending', approved:'badge-delivered', rejected:'badge-cancelled' };

async function loadReturnProducts() {
  if (!branchProductNamesCache.length) {
    const r = await api('GET', '/api/delivery-items/products');
    if (r.ok && Array.isArray(r.data)) branchProductNamesCache = r.data;
  }
}

function showRetSuggestions(input) {
  const q = input.value.trim().toLowerCase();
  const dd = document.getElementById('ret-product-suggestions');
  if (!q) { dd.style.display = 'none'; return; }
  const seen = new Set();
  const matches = branchProductNamesCache.filter(i => {
    const n = (i.product_name||'').toLowerCase();
    return n.includes(q) && !seen.has(n) && seen.add(n);
  }).slice(0, 8);
  if (!matches.length) { dd.style.display = 'none'; return; }
  dd.innerHTML = matches.map(i =>
    `<div class="co-suggest-item" onmousedown="selectRetProduct(event,'${i.product_name.replace(/'/g,"\\'")}')">
      <span><b>${i.product_name}</b></span>
      <span style="font-size:11px;color:#a0aec0;">${i.unit || ''}</span>
    </div>`
  ).join('');
  dd.style.display = 'block';
}

function selectRetProduct(e, name) {
  e.preventDefault();
  document.getElementById('ret-product').value = name;
  document.getElementById('ret-product-suggestions').style.display = 'none';
  document.getElementById('ret-qty').focus();
}

function delayHideRetSuggestions() {
  setTimeout(() => {
    const dd = document.getElementById('ret-product-suggestions');
    if (dd) dd.style.display = 'none';
  }, 200);
}

function previewReturnPhoto(input) {
  const file = input.files[0];
  if (!file) return;
  const preview = document.getElementById('ret-photo-preview');
  const img = document.getElementById('ret-photo-img');
  img.src = URL.createObjectURL(file);
  preview.style.display = 'block';
}

function clearReturnPhoto() {
  document.getElementById('ret-photo').value = '';
  document.getElementById('ret-photo-img').src = '';
  document.getElementById('ret-photo-preview').style.display = 'none';
}

function toggleReturnRemark() {
  const reason = document.getElementById('ret-reason').value;
  document.getElementById('ret-remark-group').style.display = reason === 'other' ? 'block' : 'none';
}

async function loadReturns() {
  await loadReturnProducts();
  const el = document.getElementById('returns-list');
  const countEl = document.getElementById('returns-count');
  if (!el) return;
  el.innerHTML = '<div class="loading">กำลังโหลด...</div>';
  const r = await api('GET', '/api/returns');
  if (!r.ok) {
    el.innerHTML = '<div class="empty-state"><i class="fa-solid fa-circle-exclamation"></i><p>โหลดข้อมูลไม่สำเร็จ</p></div>';
    return;
  }
  const list = r.data || [];
  if (countEl) countEl.textContent = `ประวัติคำร้อง ${list.length} รายการ`;
  if (!list.length) {
    el.innerHTML = '<div class="empty-state"><i class="fa-solid fa-inbox"></i><p>ยังไม่มีคำร้องตีกลับ</p></div>';
    return;
  }
  el.innerHTML = list.map(item => `
    <div style="border:1.5px solid #e2e8f0;border-radius:12px;margin-bottom:10px;background:#fff;overflow:hidden;">
      <div style="padding:13px 16px;display:flex;justify-content:space-between;align-items:center;cursor:pointer;"
           onclick="var d=document.getElementById('ret-d-${item.id}');var c=document.getElementById('ret-c-${item.id}');var o=d.style.display!=='none';d.style.display=o?'none':'block';c.style.transform=o?'rotate(0deg)':'rotate(180deg)';">
        <div>
          <div style="font-weight:700;font-size:15px;color:#2d3748;">${item.product_name}</div>
          <div style="font-size:12px;color:#718096;margin-top:2px;">${reasonTH[item.reason] || item.reason} &nbsp;·&nbsp; ${formatDateTime(item.created_at)}</div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
          <span class="badge ${returnStatusBadge[item.status] || 'badge-pending'}">${returnStatusTH[item.status] || item.status}</span>
          <i id="ret-c-${item.id}" class="fa-solid fa-chevron-down" style="font-size:13px;color:#a0aec0;transition:transform .2s;"></i>
        </div>
      </div>
      <div id="ret-d-${item.id}" style="display:none;padding:0 16px 14px;border-top:1.5px solid #f0f4f8;">
        <div class="info-grid" style="margin-top:10px;">
          <div class="info-item"><span class="label">จำนวน</span><span class="val">${Math.round(item.quantity)}</span></div>
          <div class="info-item"><span class="label">สาเหตุ</span><span class="val">${reasonTH[item.reason] || item.reason}</span></div>
          ${item.remark ? `<div class="info-item" style="grid-column:1/-1;"><span class="label">รายละเอียด</span><span class="val">${item.remark}</span></div>` : ''}
          <div class="info-item"><span class="label">วันที่แจ้ง</span><span class="val">${formatDateTime(item.created_at)}</span></div>
        </div>
        ${item.photo_path ? `<div style="margin-top:10px;"><img src="/api/returns/photo/${item.photo_path.split('/').pop()}" style="max-width:100%;border-radius:8px;max-height:260px;object-fit:cover;cursor:zoom-in;" onclick="openPhotoViewer(this.src,'รูปหลักฐาน')"><div style="font-size:10px;color:#a0aec0;margin-top:4px;">กดที่รูปเพื่อดูเต็มจอ</div></div>` : ''}
      </div>
    </div>
  `).join('');
}

async function loadReturnHistory() {
  const el = document.getElementById('return-history-list');
  if (!el) return;
  el.innerHTML = '<div class="loading">กำลังโหลด...</div>';
  returnHistoryFilter = 'pending';
  const r = await api('GET', '/api/returns');
  if (!r.ok) {
    el.innerHTML = '<div class="empty-state"><i class="fa-solid fa-circle-exclamation"></i><p>โหลดข้อมูลไม่สำเร็จ</p></div>';
    return;
  }
  allReturnHistory = r.data || [];
  renderReturnHistoryTabs();
  renderReturnHistory();
}

function setReturnHistoryTab(status) {
  returnHistoryFilter = status;
  renderReturnHistoryTabs();
  renderReturnHistory();
}

function renderReturnHistoryTabs() {
  const el = document.getElementById('return-history-tabs');
  if (!el) return;
  const counts = { all: allReturnHistory.length, pending: 0, approved: 0 };
  allReturnHistory.forEach(i => {
    if (i.status === 'pending') counts.pending++;
    else if (i.status === 'approved') counts.approved++;
  });
  const tabs = [
    { key: 'all',      label: 'ทั้งหมด',      dataStatus: '',          count: counts.all },
    { key: 'pending',  label: 'รอดำเนินการ',  dataStatus: 'pending',   count: counts.pending },
    { key: 'approved', label: 'อนุมัติแล้ว',  dataStatus: 'completed', count: counts.approved },
  ];
  el.innerHTML = tabs.map(t => `
    <button class="order-tab ${returnHistoryFilter === t.key ? 'active' : ''}"
      data-status="${t.dataStatus}" onclick="setReturnHistoryTab('${t.key}')">
      ${t.label} <span class="order-tab-badge">${t.count}</span>
    </button>
  `).join('');
}

function renderReturnHistory() {
  const el = document.getElementById('return-history-list');
  if (!el) return;
  let list = allReturnHistory;
  if (returnHistoryFilter === 'pending')  list = list.filter(i => i.status === 'pending');
  else if (returnHistoryFilter === 'approved') list = list.filter(i => i.status === 'approved');
  const q = (document.getElementById('ret-history-search')?.value || '').toLowerCase().trim();
  const d = document.getElementById('ret-history-date')?.value || '';
  if (q) list = list.filter(i => (i.product_name||'').toLowerCase().includes(q));
  if (d) list = list.filter(i => i.created_at && i.created_at.startsWith(d));
  if (!list.length) {
    el.innerHTML = '<div class="empty-state"><i class="fa-solid fa-inbox"></i><p>ไม่มีรายการ</p></div>';
    return;
  }
  el.innerHTML = list.map(item => `
    <div style="border:1.5px solid #e2e8f0;border-radius:12px;margin-bottom:10px;background:#fff;overflow:hidden;">
      <div style="padding:13px 16px;display:flex;justify-content:space-between;align-items:center;cursor:pointer;"
           onclick="var d=document.getElementById('reth-d-${item.id}');var c=document.getElementById('reth-c-${item.id}');var o=d.style.display!=='none';d.style.display=o?'none':'block';c.style.transform=o?'rotate(0deg)':'rotate(180deg)';">
        <div>
          <div style="font-weight:700;font-size:15px;color:#2d3748;">${item.product_name}
            <span style="font-weight:500;color:#718096;font-size:14px;">× ${Math.round(item.quantity)}</span>
          </div>
          <div style="font-size:12px;color:#718096;margin-top:2px;">${reasonTH[item.reason] || item.reason} &nbsp;·&nbsp; ${formatDateTime(item.created_at)}</div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
          <span class="badge ${returnStatusBadge[item.status] || 'badge-pending'}">${returnStatusTH[item.status] || item.status}</span>
          <i id="reth-c-${item.id}" class="fa-solid fa-chevron-down" style="font-size:13px;color:#a0aec0;transition:transform .2s;"></i>
        </div>
      </div>
      <div id="reth-d-${item.id}" style="display:none;padding:0 16px 14px;border-top:1.5px solid #f0f4f8;">
        <div class="info-grid" style="margin-top:10px;">
          <div class="info-item"><span class="label">จำนวน</span><span class="val">${Math.round(item.quantity)}</span></div>
          <div class="info-item"><span class="label">สาเหตุ</span><span class="val">${reasonTH[item.reason] || item.reason}</span></div>
          ${item.remark ? `<div class="info-item" style="grid-column:1/-1;"><span class="label">รายละเอียด</span><span class="val">${item.remark}</span></div>` : ''}
          <div class="info-item"><span class="label">วันที่แจ้ง</span><span class="val">${formatDateTime(item.created_at)}</span></div>
        </div>
        ${item.photo_path ? `<div style="margin-top:10px;"><img src="/api/returns/photo/${item.photo_path.split('/').pop()}" style="max-width:100%;border-radius:8px;max-height:260px;object-fit:cover;cursor:zoom-in;" onclick="openPhotoViewer(this.src,'รูปหลักฐาน')"><div style="font-size:10px;color:#a0aec0;margin-top:4px;">กดที่รูปเพื่อดูเต็มจอ</div></div>` : ''}
      </div>
    </div>
  `).join('');
}

function openReturnDetailModal(item) {
  const reasonTH2 = { damaged:'สินค้าชำรุด', expired:'สินค้าหมดอายุ', wrong_item:'ส่งผิดรายการ', other:'อื่นๆ' };
  const stTH2 = { pending:'รอดำเนินการ', approved:'อนุมัติ', rejected:'ปฏิเสธ' };
  const stBadge2 = { pending:'badge-pending', approved:'badge-delivered', rejected:'badge-cancelled' };
  document.getElementById('ret-modal-content').innerHTML = `
    <div style="display:flex;flex-direction:column;gap:10px;">
      <div style="display:flex;justify-content:space-between;align-items:center;">
        <div style="font-weight:700;font-size:16px;color:#2d3748;">${item.product_name}</div>
        <span class="badge ${stBadge2[item.status] || 'badge-pending'}">${stTH2[item.status] || item.status}</span>
      </div>
      <div class="info-grid">
        <div class="info-item"><span class="label">จำนวน</span><span class="val">${Math.round(item.quantity)}</span></div>
        <div class="info-item"><span class="label">สาเหตุ</span><span class="val">${reasonTH2[item.reason] || item.reason}</span></div>
        ${item.remark ? `<div class="info-item" style="grid-column:1/-1;"><span class="label">รายละเอียด</span><span class="val">${item.remark}</span></div>` : ''}
        <div class="info-item"><span class="label">วันที่แจ้ง</span><span class="val">${formatDateTime(item.created_at)}</span></div>
      </div>
      ${item.photo_path ? `<img src="/api/returns/photo/${item.photo_path.split('/').pop()}" style="max-width:100%;border-radius:8px;max-height:260px;object-fit:cover;margin-top:4px;">` : ''}
    </div>`;
  document.getElementById('modal-return-detail').classList.add('open');
}

function compressImage(file, maxWidth = 1024, quality = 0.75) {
  return new Promise(resolve => {
    const img = new Image();
    img.onload = () => {
      const scale = Math.min(1, maxWidth / img.width);
      const canvas = document.createElement('canvas');
      canvas.width  = Math.round(img.width  * scale);
      canvas.height = Math.round(img.height * scale);
      canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);
      canvas.toBlob(blob => resolve(blob || file), 'image/jpeg', quality);
    };
    img.onerror = () => resolve(file);
    img.src = URL.createObjectURL(file);
  });
}

async function submitReturn() {
  const product = document.getElementById('ret-product').value;
  const qty     = document.getElementById('ret-qty').value;
  const reason  = document.getElementById('ret-reason').value;
  const remark  = document.getElementById('ret-remark').value.trim();
  const photo   = document.getElementById('ret-photo').files[0];

  if (!product) { showToast('กรุณาเลือกสินค้า', 'error'); return; }
  if (!qty || parseFloat(qty) <= 0) { showToast('กรุณาระบุจำนวน', 'error'); return; }
  if (!reason) { showToast('กรุณาเลือกหมายเหตุ', 'error'); return; }
  if (reason === 'other' && !remark) { showToast('กรุณาระบุรายละเอียด', 'error'); return; }
  if (!photo) { showToast('กรุณาถ่ายรูปหลักฐานประกอบก่อนส่งคำร้อง', 'error'); return; }

  const btn = document.querySelector('[onclick="submitReturn()"]');
  const origText = btn ? btn.textContent : '';
  if (btn) { btn.disabled = true; btn.textContent = 'กำลังส่ง...'; }

  try {
    const compressedPhoto = await compressImage(photo, 1024, 0.75);

    const fd = new FormData();
    fd.append('product_name', product);
    fd.append('quantity', qty);
    fd.append('reason', reason);
    if (remark) fd.append('remark', remark);
    fd.append('photo', compressedPhoto, 'photo.jpg');

    const resp = await fetch('/api/returns', {
      method: 'POST',
      headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' },
      body: fd,
    });
    if (resp.ok) {
      showToast('ส่งคำร้องตีกลับสินค้าเรียบร้อย', 'success');
      document.getElementById('ret-product').value = '';
      document.getElementById('ret-qty').value = '';
      document.getElementById('ret-reason').value = '';
      document.getElementById('ret-remark').value = '';
      clearReturnPhoto();
      document.getElementById('ret-remark-group').style.display = 'none';
      loadReturns();
    } else {
      const data = await resp.json().catch(() => ({}));
      showToast(JSON.stringify(data.errors || data.message || 'เกิดข้อผิดพลาด'), 'error');
    }
  } finally {
    if (btn) { btn.disabled = false; btn.textContent = origText; }
  }
}

async function openBranchDoDetail(doId) {
  const r = await api('GET', '/api/delivery-orders/' + doId);
  if (!r.ok) { showToast('โหลดข้อมูลไม่สำเร็จ', 'error'); return; }
  const o = r.data;

  document.getElementById('btrack-modal-title').textContent = o.do_number
    + (o.is_backorder ? '  [BO]' : '');

  // ข้อมูลคนขับ
  const driverInfoHtml = o.driver ? `
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;background:#f7fafc;border-radius:10px;padding:10px 14px;margin-bottom:12px;">
      <div><div style="font-size:10px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px;">คนขับ</div>
        <div style="font-size:13px;font-weight:700;color:#1e3a5f;">${o.driver.name}</div></div>
      <div><div style="font-size:10px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px;">ทะเบียน</div>
        <div style="font-size:13px;font-weight:700;color:#1e3a5f;">${o.driver.vehicle_plate || '-'}</div></div>
    </div>` : '';
  document.getElementById('btrack-driver').innerHTML = driverInfoHtml;

  // รายการสินค้า
  document.getElementById('btrack-items').innerHTML = `
    <div style="background:#f7fafc;border-radius:10px;padding:12px 16px;">
      <div style="font-size:11px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:.8px;margin-bottom:10px;">
        <i class="fa-solid fa-boxes-stacked"></i> รายการสินค้า
      </div>
      ${(o.items||[]).length ? o.items.map(i => `
        <div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid #e2e8f0;font-size:13px;">
          <span style="color:#2d3748;">${i.product_name}</span>
          <span style="font-weight:700;color:#2a5298;white-space:nowrap;">${i.qty_ordered} ${i.unit || ''}</span>
        </div>`).join('')
      : '<div style="color:#a0aec0;font-size:13px;">ไม่มีรายการสินค้า</div>'}
    </div>`;

  // Checkpoint timeline
  const checks = [
    {
      icon: 'fa-box-open',
      label: 'รับสินค้า',
      sub: 'ไรเดอร์รับของจาก HQ',
      ts: o.dispatched_at,
    },
    {
      icon: 'fa-truck',
      label: 'ออกเดินทาง',
      sub: (o.status === 'in_transit' && !o.arrived_at) ? 'กำลังเดินทาง...' : 'ระหว่างทาง',
      ts: o.dispatched_at,
      etaId: 'btrack-eta',
      showEta: o.status === 'in_transit' && !o.arrived_at && !!o.driver_id,
    },
    {
      icon: 'fa-location-dot',
      label: 'ถึงสาขา',
      sub: 'ไรเดอร์กดถึงแล้ว',
      ts: o.arrived_at,
    },
    {
      icon: 'fa-circle-check',
      label: 'ส่งสำเร็จ',
      sub: 'ยืนยันส่งมอบแล้ว',
      ts: o.received_at,
    },
  ];

  document.getElementById('btrack-timeline').innerHTML = `
    <div style="display:flex;align-items:flex-start;">
      ${checks.map((c, i) => `
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;position:relative;">
          ${i < checks.length - 1
            ? `<div style="position:absolute;top:21px;left:50%;right:-50%;height:2px;background:${c.ts && checks[i+1]?.ts ? '#38a169' : '#e2e8f0'};z-index:0;"></div>`
            : ''}
          <div style="width:44px;height:44px;border-radius:50%;flex-shrink:0;position:relative;z-index:1;
            background:${c.ts ? '#c6f6d5' : '#f7fafc'};
            border:2.5px solid ${c.ts ? '#38a169' : '#cbd5e0'};
            display:flex;align-items:center;justify-content:center;">
            <i class="fa-solid ${c.icon}" style="font-size:17px;color:${c.ts ? '#276749' : '#a0aec0'};"></i>
          </div>
          <div style="margin-top:8px;text-align:center;padding:0 4px;">
            <div style="font-size:12px;font-weight:700;color:${c.ts ? '#2d3748' : '#a0aec0'};">${c.label}</div>
            <div style="font-size:11px;color:${c.ts ? '#718096' : '#e2e8f0'};margin-top:1px;">${c.sub}</div>
            ${c.ts
              ? `<div style="font-size:11px;color:#276749;font-weight:600;margin-top:4px;">${formatDateTime(c.ts)}</div>`
              : ''}
            ${c.showEta
              ? `<div id="btrack-eta" style="font-size:11px;color:#3182ce;font-weight:600;margin-top:4px;"><i class="fa-solid fa-spinner fa-spin"></i> คำนวณ...</div>`
              : ''}
          </div>
        </div>`).join('')}
    </div>`;

  // โหลด ETA async
  if (o.status === 'in_transit' && !o.arrived_at && o.driver_id) {
    api('GET', '/api/gps/distance/' + o.driver_id).then(dr => {
      const etaEl = document.getElementById('btrack-eta');
      if (!etaEl) return;
      if (dr.ok && dr.data?.eta_minutes != null) {
        etaEl.innerHTML = `<i class="fa-solid fa-clock fa-sm"></i> อีก ~${dr.data.eta_minutes} นาที`;
      } else {
        etaEl.textContent = 'ไม่มีข้อมูล GPS';
      }
    });
  }

  // หลักฐาน
  let evHtml = '';
  const rr = o.receive_record;
  if (o.arrive_photo_path || rr?.signature || rr?.photos?.length) {
    evHtml += `<div style="font-size:11px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:.8px;margin-bottom:12px;margin-top:4px;">
      <i class="fa-solid fa-image"></i> หลักฐานการส่งมอบ
    </div>`;
    if (o.arrive_photo_path) {
      evHtml += `<div style="margin-bottom:12px;">
        <div style="font-size:12px;color:#4a5568;margin-bottom:6px;">รูปหลักฐานจากไรเดอร์</div>
        <div style="display:flex;flex-wrap:wrap;gap:6px;">${renderPhotoGrid(parsePhotoPaths(o.arrive_photo_path),'arrive-photos')}</div>
      </div>`;
    }
    if (rr?.photos?.length) {
      evHtml += `<div style="margin-bottom:12px;">
        <div style="font-size:12px;color:#4a5568;margin-bottom:6px;">รูปหลักฐานเพิ่มเติม</div>
        <div style="display:flex;flex-wrap:wrap;gap:8px;">
          ${rr.photos.map(p => `<img src="/api/evidence/${p.filename}" style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:1.5px solid #e2e8f0;">`).join('')}
        </div>
      </div>`;
    }
    if (rr?.signature) {
      evHtml += `<div>
        <div style="font-size:12px;color:#4a5568;margin-bottom:6px;">ลายเซ็นผู้รับ</div>
        <img src="${rr.signature}" style="max-width:100%;border:1.5px solid #e2e8f0;border-radius:8px;background:#fafafa;padding:4px;">
      </div>`;
    }
  }
  document.getElementById('btrack-evidence').innerHTML = evHtml;

  document.getElementById('modal-branch-track').classList.add('open');
}

/* ---- REPORTS ---- */
let rpTrendChart  = null;
let rpDonutChart  = null;
let rpBranchChart = null;
let rpStatsCache  = null;

function onReportModeChange() {
  const mode = document.getElementById('rp-mode').value;
  ['month','quarter','year','daterange'].forEach(w => {
    const el = document.getElementById('rp-wrap-' + w);
    if (el) el.style.display = w === mode ? 'flex' : 'none';
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
  return p;
}

function buildPreviousParams(p) {
  const mode = document.getElementById('rp-mode').value;
  const prev = new URLSearchParams();
  if (mode === 'month') {
    let y = +p.get('year'), m = +p.get('month') - 1;
    if (m < 1) { m = 12; y--; }
    prev.set('year', y); prev.set('month', m);
  } else if (mode === 'quarter') {
    let y = +p.get('year'), q = +p.get('quarter') - 1;
    if (q < 1) { q = 4; y--; }
    prev.set('year', y); prev.set('quarter', q);
  } else if (mode === 'year') {
    prev.set('year', +p.get('year') - 1);
  } else {
    const from = new Date(p.get('from')), to = new Date(p.get('to'));
    const ms = to - from;
    const pTo   = new Date(from - 86400000);
    const pFrom = new Date(pTo  - ms);
    prev.set('from', pFrom.toISOString().slice(0, 10));
    prev.set('to',   pTo.toISOString().slice(0, 10));
  }
  return prev;
}

function rpDelta(cur, prev, higherIsBetter = true) {
  if (!prev || prev === 0) return '';
  const diff = cur - prev;
  const pct  = Math.abs(Math.round(diff / prev * 100));
  const up   = diff > 0;
  const good = higherIsBetter ? up : !up;
  const col  = good ? '#276749' : '#c53030';
  return `<span style="color:${col};font-size:11px;font-weight:700;display:flex;align-items:center;gap:2px;justify-content:flex-end;"><i class="fa-solid fa-arrow-${up?'up':'down'}" style="font-size:9px;"></i>${pct}%</span>`;
}

function renderItemChart(canvasId, emptyId, items, colors) {
  const canvas = document.getElementById(canvasId);
  const empty  = document.getElementById(emptyId);
  if (!canvas) return;
  const existing = Chart.getChart(canvas);
  if (existing) existing.destroy();
  if (!items.length) { canvas.style.display = 'none'; empty.style.display = ''; return; }
  canvas.style.cssText = 'position:absolute;inset:0;width:100%!important;height:100%!important;';
  empty.style.display = 'none';
  new Chart(canvas, {
    type: 'bar',
    data: {
      labels: items.map(i => i.product_name.length > 22 ? i.product_name.slice(0,20)+'…' : i.product_name),
      datasets: [{ data: items.map(i => i.count), backgroundColor: items.map((_, idx) => colors[Math.min(idx, colors.length-1)]), borderRadius: 3 }],
    },
    options: {
      indexAxis: 'y', responsive: true, maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: {
          title: ctx => items[ctx[0].dataIndex].product_name,
          label: ctx => `${ctx.raw} ครั้ง`,
        }},
      },
      scales: {
        x: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#f0f4f8' } },
        y: { grid: { display: false } },
      },
    },
  });
}

function fmtMin(min) {
  if (min == null) return '-';
  if (min < 60) return `${min} น.`;
  const h = Math.floor(min / 60), m = min % 60;
  return m > 0 ? `${h} ชม. ${m} น.` : `${h} ชม.`;
}

function fmtSec(sec) {
  if (sec == null) return '-';
  if (sec < 60)   return `${sec} วิ`;
  const m = Math.floor(sec / 60), s = sec % 60;
  if (m < 60) return s > 0 ? `${m} น. ${s} วิ` : `${m} น.`;
  const h = Math.floor(m / 60), rm = m % 60;
  return rm > 0 ? `${h} ชม. ${rm} น.` : `${h} ชม.`;
}

function fmtHour(h) {
  if (h == null) return '-';
  if (h === 0)  return 'เที่ยงคืน';
  if (h === 12) return 'เที่ยง';
  return `${h}:00 ${h < 12 ? 'น.' : h < 18 ? 'บ่าย' : 'เย็น'}`;
}

function onBranchDropdownChange() {
  const dropdown = document.getElementById('rp-branch-dropdown');
  const branchId = dropdown.value;
  if (!branchId) {
    document.getElementById('rp-branch-detail-panel').style.display = 'none';
    document.getElementById('rp-branch-detail-empty').style.display = 'none';
    document.getElementById('rp-branch-detail-hint').style.display  = 'flex';
    // reset item charts to overall data
    const d = rpStatsCache;
    if (d) {
      renderItemChart('rp-shortage-chart', 'rp-shortage-empty', d.return_by_product || [],
        ['#c53030','#e53e3e','#fc8181','#feb2b2','#fed7d7','#ffe4e6','#fff5f5','#fffafa','#fff0f0','#fffcfc']);
      const rTH = window._rpReasonTH || {};
      const reasonData = (d.return_by_reason || []).map(r => ({ ...r, product_name: rTH[r.product_name] || r.product_name }));
      renderItemChart('rp-rejected-chart', 'rp-rejected-empty', reasonData,
        ['#9b2c2c','#c53030','#e53e3e','#fc8181']);
    }
    const lbl = document.getElementById('rp-item-chart-branch-label');
    if (lbl) lbl.style.display = 'none';
    return;
  }
  showBranchReturnDetail(branchId, window._rpBranchDetail || []);
}

function showBranchReturnDetail(branchId, branchDetail) {
  const panelEl = document.getElementById('rp-branch-detail-panel');
  const emptyEl = document.getElementById('rp-branch-detail-empty');
  const hintEl  = document.getElementById('rp-branch-detail-hint');

  const detail = branchDetail.find(d => d.branch_id == branchId);

  hintEl.style.display = 'none';
  if (!detail || (!detail.products.length && !detail.reasons.length)) {
    panelEl.style.display = 'none';
    emptyEl.style.display = 'flex';
    return;
  }

  panelEl.style.display = '';
  emptyEl.style.display = 'none';

  document.getElementById('rp-bd-title').textContent = `รายละเอียด: ${detail.branch_name}`;

  // sync item charts กับสาขาที่เลือก
  const productItems = detail.products.map(p => ({ product_name: p.name, count: p.count }));
  renderItemChart('rp-shortage-chart', 'rp-shortage-empty', productItems,
    ['#c53030','#e53e3e','#fc8181','#feb2b2','#fed7d7','#ffe4e6','#fff5f5','#fffafa','#fff0f0','#fffcfc']);
  const rTH = window._rpReasonTH || {};
  const reasonItems = detail.reasons.map(r => ({ product_name: rTH[r.reason] || r.reason, count: r.count }));
  renderItemChart('rp-rejected-chart', 'rp-rejected-empty', reasonItems,
    ['#9b2c2c','#c53030','#e53e3e','#fc8181']);
  const lbl = document.getElementById('rp-item-chart-branch-label');
  if (lbl) { lbl.textContent = detail.branch_name; lbl.style.display = ''; }

  const reasonLabels = { damaged:'สินค้าเสียหาย', wrong_item:'สินค้าผิด', expired:'หมดอายุ', missing:'ของขาด', other:'อื่นๆ' };

  document.getElementById('rp-bd-products').innerHTML = detail.products.length
    ? detail.products.map((p, i) =>
        `<div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid #f0f4f8;font-size:13px;">
          <span style="color:#2d3748;">${i+1}. ${p.name}</span>
          <span style="font-weight:700;color:#c53030;background:#fff5f5;border-radius:10px;padding:1px 8px;font-size:12px;white-space:nowrap;">${p.count} ครั้ง</span>
        </div>`
      ).join('')
    : '<div style="color:#a0aec0;font-size:12px;padding:8px 0;">ไม่มีข้อมูล</div>';

  document.getElementById('rp-bd-reasons').innerHTML = detail.reasons.length
    ? detail.reasons.map((r, i) =>
        `<div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid #f0f4f8;font-size:13px;">
          <span style="color:#2d3748;">${i+1}. ${reasonLabels[r.reason] || r.reason}</span>
          <span style="font-weight:700;color:#c53030;background:#fff5f5;border-radius:10px;padding:1px 8px;font-size:12px;white-space:nowrap;">${r.count} ครั้ง</span>
        </div>`
      ).join('')
    : '<div style="color:#a0aec0;font-size:12px;padding:8px 0;">ไม่มีข้อมูล</div>';
}

function switchItemTab(tab) {
  ['shortage','rejected'].forEach(t => {
    document.getElementById('rp-item-' + t).style.display = t === tab ? '' : 'none';
    const btn = document.getElementById('rp-tab-' + t);
    btn.style.background = t === tab ? '#3182ce' : '#f7fafc';
    btn.style.color      = t === tab ? '#fff'    : '#718096';
    btn.style.border     = t === tab ? '1.5px solid #3182ce' : '1.5px solid #e2e8f0';
  });
}

async function loadDeliveryStats() {
  const params = buildReportParams();
  if (params === null) return;

  document.getElementById('rp-empty').style.display   = 'none';
  document.getElementById('rp-content').style.display = 'none';

  const doCompare  = document.getElementById('rp-compare').checked;
  const prevParams = doCompare ? buildPreviousParams(params) : null;

  const [r, rPrev] = await Promise.all([
    api('GET', '/api/reports/delivery-stats?' + params),
    prevParams ? api('GET', '/api/reports/delivery-stats?' + prevParams) : Promise.resolve(null),
  ]);

  if (!r.ok) {
    showToast('โหลดรายงานไม่สำเร็จ', 'error');
    document.getElementById('rp-empty').style.display = '';
    return;
  }

  const d     = r.data;
  const dPrev = rPrev?.ok ? rPrev.data : null;
  rpStatsCache = d;

  // ── Period label ─────────────────────────────────────────────
  const mFull = ['','มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
  const mSh   = ['','ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
  const qLab  = ['','Q1 (ม.ค.–มี.ค.)','Q2 (เม.ย.–มิ.ย.)','Q3 (ก.ค.–ก.ย.)','Q4 (ต.ค.–ธ.ค.)'];
  const mode  = document.getElementById('rp-mode').value;

  let label = '', prevLabel = '';
  if (mode === 'month') {
    const m = +params.get('month');
    label = `${mFull[m]} ${params.get('year')}`;
    if (prevParams) prevLabel = `${mSh[+prevParams.get('month')]} ${prevParams.get('year')}`;
  } else if (mode === 'quarter') {
    label = `${qLab[+params.get('quarter')]} ปี ${params.get('year')}`;
    if (prevParams) prevLabel = `${qLab[+prevParams.get('quarter')]} ปี ${prevParams.get('year')}`;
  } else if (mode === 'year') {
    label = `ปี ${params.get('year')}`;
    if (prevParams) prevLabel = `ปี ${prevParams.get('year')}`;
  } else {
    label = `${d.filter.from} – ${d.filter.to}`;
    if (dPrev) prevLabel = `${dPrev.filter.from} – ${dPrev.filter.to}`;
  }

  document.getElementById('rp-period-label').textContent = label;
  document.getElementById('rp-period-range').textContent = `${d.filter.from} ถึง ${d.filter.to}`;
  const badge = document.getElementById('rp-compare-badge');
  if (dPrev) {
    badge.style.display = '';
    document.getElementById('rp-compare-label').textContent = `เทียบกับ ${prevLabel}`;
  } else {
    badge.style.display = 'none';
  }

  // ── KPI Cards ─────────────────────────────────────────────────
  const c = d.cards, cp = dPrev?.cards;

  const setDelta = (id, cur, prev, hib = true) => {
    const el = document.getElementById(id);
    el.innerHTML = rpDelta(cur, prev, hib);
  };

  // Total
  document.getElementById('rp-kpi-total').textContent = c.total;
  setDelta('rp-kpi-total-delta', c.total, cp?.total);

  // Success rate
  document.getElementById('rp-kpi-rate').textContent = c.success_rate + '%';
  const rateBar = document.getElementById('rp-kpi-rate-bar');
  rateBar.style.width      = c.success_rate + '%';
  rateBar.style.background = c.success_rate >= 80 ? 'linear-gradient(90deg,#48bb78,#68d391)'
                           : c.success_rate >= 60 ? 'linear-gradient(90deg,#ed8936,#f6ad55)'
                                                  : 'linear-gradient(90deg,#fc8181,#feb2b2)';
  document.getElementById('rp-kpi-rate-sub').textContent = `ยกเลิก ${c.cancel_rate||0}% · ทั้งหมด ${c.total}`;
  setDelta('rp-kpi-rate-delta', c.success_rate, cp?.success_rate);


  // Return requests (ตีกลับ)
  const retCount = c.return_count ?? 0;
  document.getElementById('rp-kpi-bo').textContent = retCount;
  document.getElementById('rp-kpi-bo-sub').textContent = retCount > 0
    ? `สาขาแจ้งตีกลับ ${retCount} รายการ`
    : 'ไม่มีตีกลับ';
  setDelta('rp-kpi-bo-delta', retCount, cp?.return_count, false);

  const t = d.timing;

  // ── Donut Chart ───────────────────────────────────────────────
  if (rpDonutChart) { rpDonutChart.destroy(); rpDonutChart = null; }
  const slices = [
    { label: 'ส่งสำเร็จ',   val: c.completed,     col: '#48bb78' },
    { label: 'กำลังส่ง',    val: c.in_transit||0, col: '#4299e1' },
    { label: 'รอดำเนินการ', val: c.pending||0,    col: '#90cdf4' },
    { label: 'ยกเลิก',      val: c.cancelled||0,  col: '#cbd5e0' },
  ].filter(s => s.val > 0);

  rpDonutChart = new Chart(document.getElementById('rp-donut-chart'), {
    type: 'doughnut',
    data: {
      labels: slices.map(s => s.label),
      datasets: [{ data: slices.map(s => s.val), backgroundColor: slices.map(s => s.col), borderWidth: 2, borderColor: '#fff', hoverOffset: 6 }],
    },
    options: {
      cutout: '65%', responsive: false,
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: ctx => `${ctx.label}: ${ctx.raw} (${c.total > 0 ? Math.round(ctx.raw/c.total*100) : 0}%)` } },
      },
    },
  });
  document.getElementById('rp-donut-center').textContent = c.total;
  document.getElementById('rp-donut-legend').innerHTML = slices.map(s =>
    `<div style="display:flex;justify-content:space-between;align-items:center;font-size:12px;padding:3px 0;">
       <span style="display:flex;align-items:center;gap:6px;"><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:${s.col};flex-shrink:0;"></span>${s.label}</span>
       <span style="font-weight:700;">${s.val}<span style="color:#718096;font-weight:400;"> (${c.total>0?Math.round(s.val/c.total*100):0}%)</span></span>
     </div>`
  ).join('');

  // ── Trend Chart (bar + success rate line) ─────────────────────
  if (rpTrendChart) { rpTrendChart.destroy(); rpTrendChart = null; }
  const trend = d.trend;
  document.getElementById('rp-trend-year').textContent = `(ปี ${d.filter.year})`;
  const rateLine = trend.map(t => {
    const tot = t.completed + t.cancelled;
    return tot > 0 ? Math.round(t.completed / tot * 100) : null;
  });

  rpTrendChart = new Chart(document.getElementById('rp-trend-chart'), {
    type: 'bar',
    data: {
      labels: trend.map(t => t.label),
      datasets: [
        { label: 'สำเร็จ',  data: trend.map(t => t.completed), backgroundColor: trend.map(t => t.highlight ? '#276749' : '#48bb78'), borderRadius: 3, yAxisID: 'y' },
        { label: 'ยกเลิก',  data: trend.map(t => t.cancelled), backgroundColor: trend.map(t => t.highlight ? '#718096' : '#cbd5e0'), borderRadius: 3, yAxisID: 'y' },
        { type: 'line', label: 'อัตราสำเร็จ %', data: rateLine, borderColor: '#3182ce', backgroundColor: 'rgba(49,130,206,.08)', borderWidth: 2, pointRadius: 3, pointBackgroundColor: '#3182ce', tension: 0.35, yAxisID: 'y2', spanGaps: true },
      ],
    },
    options: {
      responsive: true, interaction: { mode: 'index', intersect: false },
      plugins: { legend: { display: false }, tooltip: { callbacks: { title: ctx => `${ctx[0].label} ${d.filter.year}` } } },
      scales: {
        x:  { grid: { display: false } },
        y:  { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f0f4f8' } },
        y2: { position: 'right', min: 0, max: 100, ticks: { callback: v => v + '%', color: '#3182ce' }, grid: { display: false } },
      },
    },
  });

  // ── Branch Returns Bar + Dropdown Detail ──────────────────────
  if (rpBranchChart) { rpBranchChart.destroy(); rpBranchChart = null; }
  const byBranch     = d.by_branch || [];
  const branchDetail = d.branch_return_detail || [];
  window._rpBranchDetail = branchDetail;
  const branchWrap   = document.getElementById('rp-branch-wrap');
  const selectorWrap = document.getElementById('rp-branch-selector-wrap');
  const dropdown     = document.getElementById('rp-branch-dropdown');

  if (byBranch.length) {
    branchWrap.innerHTML = '<canvas id="rp-branch-chart" style="position:absolute;inset:0;width:100%!important;height:100%!important;"></canvas>';
    const barColors = byBranch.map(b => b.return_count > 0 ? '#fc8181' : '#e2e8f0');
    rpBranchChart = new Chart(document.getElementById('rp-branch-chart'), {
      type: 'bar',
      data: {
        labels: byBranch.map(b => b.name),
        datasets: [{ data: byBranch.map(b => b.return_count), backgroundColor: barColors, borderRadius: 3 }],
      },
      options: {
        indexAxis: 'y', responsive: true, maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: { callbacks: { label: ctx => ctx.raw > 0 ? `ตีกลับ: ${ctx.raw} ครั้ง` : 'ไม่มีตีกลับ' } },
        },
        scales: {
          x: { beginAtZero: true, grid: { color: '#f0f4f8' }, ticks: { stepSize: 1, precision: 0 } },
          y: { grid: { display: false } },
        },
      },
    });

    // Populate dropdown
    selectorWrap.style.display = 'flex';
    dropdown.innerHTML = '<option value="">-- เลือกสาขา --</option>' +
      byBranch.map(b =>
        `<option value="${b.id}">${b.name}${b.return_count > 0 ? ` (${b.return_count} ครั้ง)` : ' (ไม่มีตีกลับ)'}</option>`
      ).join('');
  } else {
    branchWrap.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#a0aec0;font-size:13px;"><span><i class="fa-solid fa-rotate-left" style="margin-right:8px;opacity:.35;"></i>ไม่มีข้อมูลสาขา</span></div>';
    selectorWrap.style.display = 'none';
  }

  // ตีกลับ: by product & reason (ภาพรวม)
  window._rpReasonTH = { damaged: 'สินค้าเสียหาย', expired: 'สินค้าหมดอายุ', wrong_item: 'ส่งผิดรายการ', other: 'อื่นๆ' };
  renderItemChart('rp-shortage-chart', 'rp-shortage-empty', d.return_by_product || [],
    ['#c53030','#e53e3e','#fc8181','#feb2b2','#fed7d7','#ffe4e6','#fff5f5','#fffafa','#fff0f0','#fffcfc']);
  const reasonData = (d.return_by_reason || []).map(r => ({ ...r, product_name: window._rpReasonTH[r.product_name] || r.product_name }));
  renderItemChart('rp-rejected-chart', 'rp-rejected-empty', reasonData,
    ['#9b2c2c','#c53030','#e53e3e','#fc8181']);

  // ── Timing Pipeline ───────────────────────────────────────────
  const totalSecs = (t.avg_prep_sec||0) + (t.avg_drive_sec||0) + (t.avg_receive_sec||0);
  document.getElementById('rp-t-count').textContent   = t.completed_count > 0 ? `(เฉลี่ยจาก ${t.completed_count} ออเดอร์ที่เสร็จสิ้นครบ)` : '';
  document.getElementById('rp-t-prep').textContent    = fmtSec(t.avg_prep_sec);
  document.getElementById('rp-t-drive').textContent   = fmtSec(t.avg_drive_sec);
  document.getElementById('rp-t-receive').textContent = fmtSec(t.avg_receive_sec);
  document.getElementById('rp-t-total').textContent   = totalSecs > 0 ? fmtSec(totalSecs) : '-';
  document.getElementById('rp-t-hour').textContent    = fmtHour(t.fastest_hour);

  // ── Delivery Funnel ───────────────────────────────────────────
  const fn = d.funnel || {};
  const funnelWrap = document.getElementById('rp-funnel-wrap');
  if (fn.created > 0) {
    funnelWrap.style.display = '';
    const fSteps = [
      { label: 'สร้าง DO',   key: 'created',    color: '#2b6cb0', dropLabel: null },
      { label: 'ออกรถ',      key: 'dispatched', color: '#975a16', dropLabel: 'ค้าง' },
      { label: 'ถึงสาขา',   key: 'arrived',    color: '#276749', dropLabel: 'ระหว่างทาง' },
      { label: 'รับสินค้า', key: 'received',   color: '#6b46c1', dropLabel: 'รอรับ' },
    ];
    const maxVal = fn.created || 1;
    const parts = [];
    fSteps.forEach((s, i) => {
      const val     = fn[s.key] ?? 0;
      const pct     = Math.round(val / maxVal * 100);
      const prev    = i > 0 ? (fn[fSteps[i-1].key] ?? 0) : val;
      const dropCnt = i > 0 ? prev - val : 0;
      const dropPct = i > 0 && prev > 0 ? Math.round((prev - val) / prev * 100) : null;
      const dropColor = dropPct === null ? '#a0aec0'
        : dropPct >= 40 ? '#c53030'
        : dropPct >= 20 ? '#e53e3e'
        : dropPct >= 10 ? '#dd6b20'
        : '#d69e2e';

      if (i > 0) {
        parts.push(`<div style="flex-shrink:0;display:flex;align-items:flex-start;padding-top:22px;padding-left:2px;padding-right:2px;">
          <i class="fa-solid fa-chevron-right" style="color:#bee3f8;font-size:16px;"></i>
        </div>`);
      }

      const dropBlock = dropPct !== null ? `
        <div style="margin-top:10px;text-align:center;">
          <div style="font-size:13px;color:${dropColor};"><i class="fa-solid fa-caret-down"></i></div>
          <div style="font-size:12px;font-weight:700;color:${dropColor};">${dropCnt} ${s.dropLabel}</div>
          <div style="font-size:11px;color:${dropColor};opacity:.85;">Drop ${dropPct}%</div>
        </div>` : '';

      parts.push(`<div style="flex:1;min-width:80px;display:flex;flex-direction:column;align-items:center;">
        <div style="font-size:12px;font-weight:700;color:#1e3a5f;text-align:center;margin-bottom:4px;">${s.label}</div>
        <div style="font-size:24px;font-weight:800;color:${s.color};line-height:1;">${val}</div>
        <div style="font-size:12px;color:#718096;margin-bottom:6px;">${pct}%</div>
        <div style="width:100%;background:#e2e8f0;height:8px;overflow:hidden;">
          <div style="width:${pct}%;height:100%;background:${s.color};transition:width .5s;"></div>
        </div>
        ${dropBlock}
      </div>`);
    });
    document.getElementById('rp-funnel-steps').innerHTML = parts.join('');
  } else {
    funnelWrap.style.display = 'none';
  }

  // ── Per-Driver Timing ──────────────────────────────────────────
  const dtWrap  = document.getElementById('rp-driver-timing-wrap');
  const dtTable = document.getElementById('rp-driver-timing-table');
  const dt = d.driver_timing || [];
  if (dt.length > 0) {
    dtWrap.style.display = '';
    const maxDrive = Math.max(...dt.map(r => r.avg_drive_sec || 0), 1);
    dtTable.innerHTML = `<table style="width:100%;border-collapse:collapse;font-size:13px;">
      <thead><tr style="background:#f7fafc;">
        <th style="padding:8px 10px;text-align:left;font-size:11px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:.4px;">คนขับ</th>
        <th style="padding:8px 10px;text-align:center;font-size:11px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:.4px;">DO สำเร็จ</th>
        <th style="padding:8px 10px;text-align:left;font-size:11px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:.4px;">เวลาขับเฉลี่ย</th>
        <th style="padding:8px 10px;text-align:left;font-size:11px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:.4px;">รวมทั้งกระบวนการ</th>
      </tr></thead>
      <tbody>${dt.map(r => {
        const barW = r.avg_drive_sec ? Math.round(r.avg_drive_sec / maxDrive * 100) : 0;
        return `<tr style="border-top:1px solid #f0f4f8;">
          <td style="padding:10px;font-weight:600;color:#1e3a5f;">${r.name}</td>
          <td style="padding:10px;text-align:center;">
            <span style="background:#c6f6d5;color:#22543d;border-radius:99px;padding:2px 10px;font-weight:700;font-size:12px;">${r.total}</span>
          </td>
          <td style="padding:10px;min-width:160px;">
            <div style="display:flex;align-items:center;gap:8px;">
              <div style="flex:1;background:#e2e8f0;border-radius:99px;height:7px;overflow:hidden;">
                <div style="width:${barW}%;height:100%;background:linear-gradient(90deg,#ed8936,#f6ad55);border-radius:99px;"></div>
              </div>
              <span style="font-size:12px;font-weight:700;color:#975a16;min-width:60px;">${fmtSec(r.avg_drive_sec)}</span>
            </div>
          </td>
          <td style="padding:10px;font-size:12px;font-weight:700;color:#4a5568;">${fmtSec(r.avg_total_sec)}</td>
        </tr>`;
      }).join('')}</tbody>
    </table>`;
  } else {
    dtWrap.style.display = 'none';
  }

  document.getElementById('rp-content').style.display = '';
}

/* ---- USERS ---- */
/* ── Users tab switcher ── */
function switchUserTab(tab) {
  const tabs   = { list: 'utab-list',    history: 'utab-history' };
  const panels = { list: 'upanel-list',  history: 'upanel-history' };
  Object.entries(tabs).forEach(([k, id]) => {
    const isActive = k === tab;
    const el = document.getElementById(id);
    el.style.color       = isActive ? '#2a5298' : '#718096';
    el.style.fontWeight  = isActive ? '700'     : '600';
    el.style.borderBottom = isActive ? '3px solid #2a5298' : '3px solid transparent';
    document.getElementById(panels[k]).style.display = isActive ? 'block' : 'none';
  });
  if (tab === 'history') loadLoginHistory();
}

async function loadLoginHistory() {
  const el = document.getElementById('lh-list');
  el.innerHTML = '<div class="loading">กำลังโหลด...</div>';
  const r = await api('GET', '/api/admin/login-history');
  if (!r.ok) { el.innerHTML = '<div class="empty-state"><i class="fa-solid fa-triangle-exclamation"></i><p>โหลดไม่ได้</p></div>'; return; }
  const { total, data } = r.data;
  document.getElementById('lh-count').textContent = `${total.toLocaleString()} รายการทั้งหมด`;

  const roleMeta = {
    admin:  { label: 'Admin',  bg: '#f3e8ff', color: '#6b21a8' },
    hq:     { label: 'HQ',     bg: '#dbeafe', color: '#1d4ed8' },
    driver: { label: 'คนขับ',  bg: '#dcfce7', color: '#15803d' },
    branch: { label: 'สาขา',  bg: '#e0f2fe', color: '#0369a1' },
  };

  const deviceIcon = d => {
    if (d === 'iPhone' || d === 'iPad' || d === 'Android' || d === 'มือถือ')
      return '<i class="fa-solid fa-mobile-screen-button" style="color:#6366f1;"></i>';
    return '<i class="fa-solid fa-desktop" style="color:#64748b;"></i>';
  };

  if (!data.length) {
    el.innerHTML = '<div class="empty-state"><i class="fa-solid fa-clock-rotate-left"></i><p>ยังไม่มีประวัติการเข้าใช้งาน</p></div>';
    return;
  }

  el.innerHTML = data.map(h => {
    const rm   = roleMeta[h.role] || { label: h.role, bg: '#f1f5f9', color: '#64748b' };
    const isIn = h.action === 'login';
    return `
    <div style="display:flex;align-items:center;gap:12px;padding:10px 4px;border-bottom:1px solid #f0f4f8;flex-wrap:wrap;">
      <div style="flex:0 0 140px;min-width:120px;">
        <div style="font-size:12px;font-weight:600;color:#1e3a5f;">${formatDateTime(h.created_at)}</div>
      </div>
      <div style="flex:1;min-width:160px;">
        <div style="font-size:13px;font-weight:700;color:#1e3a5f;">${h.name}</div>
        <div style="font-size:11px;color:#718096;">${h.email}</div>
      </div>
      <div style="flex:0 0 auto;display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
        <span style="background:${rm.bg};color:${rm.color};padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700;">${rm.label}</span>
        <span style="background:${isIn ? '#dcfce7' : '#f1f5f9'};color:${isIn ? '#15803d' : '#64748b'};padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700;">
          <i class="fa-solid ${isIn ? 'fa-right-to-bracket' : 'fa-right-from-bracket'}"></i> ${isIn ? 'เข้าระบบ' : 'ออกระบบ'}
        </span>
        <span style="font-size:11px;color:#94a3b8;display:inline-flex;align-items:center;gap:4px;">
          <i class="fa-solid fa-network-wired" style="color:#94a3b8;"></i> ${h.ip_address}
        </span>
        <span style="font-size:11px;color:#94a3b8;display:inline-flex;align-items:center;gap:4px;">
          ${deviceIcon(h.device)} ${h.device}
        </span>
      </div>
    </div>`;
  }).join('');
}

let allUsers = [];
let currentUserRoleFilter = '';

async function loadUsers() {
  const r = await api('GET', '/api/users');
  if (!r.ok) return;
  allUsers = r.data;
  updateUserRoleCounts();
  filterUsers();
}

function updateUserRoleCounts() {
  const roles = ['all','admin','hq','driver','branch'];
  roles.forEach(role => {
    const cnt = role === 'all' ? allUsers.length : allUsers.filter(u => u.role === role).length;
    const el = document.getElementById(`urole-cnt-${role}`);
    if (el) el.textContent = cnt;
  });
  document.getElementById('user-count').textContent = `${allUsers.length} ผู้ใช้`;
}

function setUserRoleFilter(role) {
  currentUserRoleFilter = role;
  ['all','admin','hq','driver','branch'].forEach(r => {
    const btn = document.getElementById(`urole-btn-${r}`);
    if (btn) btn.classList.toggle('active', (role === '' ? r === 'all' : r === role));
  });
  filterUsers();
}

function filterUsers() {
  const search = (document.getElementById('user-search')?.value || '').toLowerCase().trim();
  let list = currentUserRoleFilter ? allUsers.filter(u => u.role === currentUserRoleFilter) : allUsers;
  if (search) list = list.filter(u =>
    (u.name || '').toLowerCase().includes(search) ||
    (u.employee_code || '').toLowerCase().includes(search) ||
    (u.branch?.name || '').toLowerCase().includes(search));
  document.getElementById('users-tbody').innerHTML = list.length === 0
    ? `<tr><td colspan="7"><div class="empty-state"><i class="fa-solid fa-user"></i><p>${search || currentUserRoleFilter ? 'ไม่พบผู้ใช้ที่ค้นหา' : 'ยังไม่มีผู้ใช้'}</p></div></td></tr>`
    : list.map(u => `<tr>
    <td>${u.id}</td>
    <td><b>${u.name}</b><br><span style="font-size:11px;color:#718096;">${roleTH(u.role)}</span></td>
    <td style="font-family:monospace;font-size:13px;color:#2a5298;">${u.employee_code || '<span style="color:#a0aec0;font-size:12px;font-family:inherit;">-</span>'}</td>
    <td>${u.phone ? `<a href="tel:${u.phone}" style="color:#276749;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:4px;font-size:12px;"><i class="fa-solid fa-phone" style="font-size:10px;"></i> ${u.phone}</a>` : '<span style="color:#a0aec0;font-size:12px;">-</span>'}</td>
    <td>${u.vehicle_plate ? `<span style="font-size:12px;font-weight:700;background:#f1f5f9;border-radius:5px;padding:2px 8px;color:#1e3a5f;">${u.vehicle_plate}</span>` : '<span style="color:#a0aec0;font-size:12px;">-</span>'}</td>
    <td><span class="badge ${u.is_active ? 'badge-delivered':'badge-cancelled'}">${u.is_active ? 'ใช้งาน':'ปิด'}</span></td>
    <td><button class="btn btn-secondary btn-sm" onclick="openEditUser(${u.id})"><i class="fa-solid fa-pen"></i> แก้ไข</button></td>
  </tr>`).join('');
}

async function toggleUser(id, active) {
  const r = await api('PUT', '/api/users/' + id, {is_active: active});
  if (r.ok) { showToast('อัปเดตผู้ใช้แล้ว', 'success'); loadUsers(); }
}

async function deleteUser(id) {
  if (!await showConfirmDialog('ยืนยันลบผู้ใช้นี้?', 'ลบผู้ใช้')) return;
  const r = await api('DELETE', '/api/users/' + id);
  if (r.ok) { showToast('ลบผู้ใช้แล้ว', 'success'); loadUsers(); }
  else showToast(r.data.message, 'error');
}

function toggleContactFields(prefix) {
  const role = document.getElementById(prefix + '-role').value;
  const sec = document.getElementById(prefix + '-contact-section');
  if (sec) sec.style.display = role === 'driver' ? 'block' : 'none';
}

async function openCreateUser() {
  ['mu-name','mu-empcode','mu-pass','mu-phone','mu-plate'].forEach(id => document.getElementById(id).value = '');
  document.getElementById('mu-role').value = 'driver';
  toggleContactFields('mu');
  document.getElementById('modal-user').classList.add('open');
}

function buildAutoEmail(name) {
  return name.toLowerCase().replace(/\s+/g, '') + '_' + Date.now() + '@bh-logistic.com';
}

async function submitUser() {
  const name = document.getElementById('mu-name').value.trim();
  const pass = document.getElementById('mu-pass').value.trim();
  if (!name) { showToast('กรุณาใส่ชื่อ', 'error'); return; }
  if (!pass || pass.length < 8) { showToast('กรุณาใส่รหัสผ่านอย่างน้อย 8 ตัวอักษร', 'error'); return; }
  const muRole = document.getElementById('mu-role').value;
  const body = {
    name,
    phone:         muRole === 'driver' ? (document.getElementById('mu-phone').value.trim() || null) : null,
    vehicle_plate: muRole === 'driver' ? (document.getElementById('mu-plate').value.trim().toUpperCase() || null) : null,
    employee_code: document.getElementById('mu-empcode').value.trim() || null,
    email:     buildAutoEmail(name),
    password:  pass,
    role:      muRole,
  };
  const r = await api('POST', '/api/users', body);
  if (r.ok) { closeModal('modal-user'); showToast('สร้างผู้ใช้เรียบร้อย', 'success'); loadUsers(); }
  else showToast(JSON.stringify(r.data.errors || r.data.message), 'error');
}

async function openEditUser(id) {
  const user = allUsers.find(u => u.id === id);
  if (!user) return;
  document.getElementById('eu-id').value = user.id;
  document.getElementById('eu-name').value = user.name;
  document.getElementById('eu-empcode').value = user.employee_code || '';
  document.getElementById('eu-role').value = user.role;
  document.getElementById('eu-pass').value = '';
  document.getElementById('eu-phone').value = user.phone || '';
  document.getElementById('eu-plate').value = user.vehicle_plate || '';
  const cb = document.getElementById('eu-is-active');
  cb.checked = !!user.is_active;
  document.getElementById('eu-active-label').textContent = user.is_active ? 'ใช้งาน' : 'ปิดการใช้งาน';
  const isSelf = user.id === currentUser?.id;
  document.getElementById('eu-delete-btn').style.display = isSelf ? 'none' : '';
  toggleContactFields('eu');
  document.getElementById('modal-edit-user').classList.add('open');
}

async function submitEditUser() {
  const id   = document.getElementById('eu-id').value;
  const name = document.getElementById('eu-name').value.trim();
  const pass = document.getElementById('eu-pass').value.trim();
  const euRole = document.getElementById('eu-role').value;
  const body = {
    name,
    phone:         euRole === 'driver' ? (document.getElementById('eu-phone').value.trim() || null) : null,
    vehicle_plate: euRole === 'driver' ? (document.getElementById('eu-plate').value.trim().toUpperCase() || null) : null,
    employee_code: document.getElementById('eu-empcode').value.trim() || null,
    role:          euRole,
    is_active:     document.getElementById('eu-is-active').checked,
  };
  if (pass.length >= 8) body.password = pass;
  else if (pass.length > 0) { showToast('รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร', 'error'); return; }
  const r = await api('PUT', '/api/users/' + id, body);
  if (r.ok) { closeModal('modal-edit-user'); showToast('แก้ไขผู้ใช้เรียบร้อย', 'success'); loadUsers(); }
  else showToast(JSON.stringify(r.data.errors || r.data.message), 'error');
}

async function deleteUserFromModal() {
  const id = parseInt(document.getElementById('eu-id').value);
  if (!await showConfirmDialog('ยืนยันลบผู้ใช้นี้?', 'ลบผู้ใช้')) return;
  const r = await api('DELETE', '/api/users/' + id);
  if (r.ok) { closeModal('modal-edit-user'); showToast('ลบผู้ใช้แล้ว', 'success'); loadUsers(); }
  else showToast(r.data.message, 'error');
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


async function loadDriverOptions(selectId) {
  const r = await api('GET', '/api/users');
  const sel = document.getElementById(selectId);
  if (!sel) return;
  let html = '<option value="">-- ไม่ระบุ --</option>';
  if (r.ok) html += r.data.filter(u => u.role === 'driver').map(u => `<option value="${u.id}">${u.name}</option>`).join('');
  sel.innerHTML = html;
}

function closeModal(id) {
  const el = document.getElementById(id);
  el.classList.remove('open');
  el.querySelectorAll('[data-location-node]').forEach(n => stopLocationPolling(n.dataset.locationNode));
}
// ปิด modal ด้วย X เท่านั้น — ลบ click-outside-to-close ออก

// เพิ่มปุ่ม X ให้ทุก modal + ลบปุ่มที่ทำหน้าที่แค่ปิด modal
document.addEventListener('DOMContentLoaded', () => {
  let _confirmResolve = null;
  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    const modal = overlay.querySelector('.modal');
    if (!modal) return;
    const xBtn = document.createElement('button');
    xBtn.className = 'modal-x-btn';
    xBtn.innerHTML = '✕';
    xBtn.onclick = () => {
      overlay.classList.remove('open');
      if (overlay.id === 'modal-confirm' && _confirmResolve) { _confirmResolve(false); _confirmResolve = null; }
    };
    modal.insertBefore(xBtn, modal.firstChild);
    modal.querySelectorAll('.modal-footer .btn-secondary[onclick]').forEach(btn => {
      if ((btn.getAttribute('onclick') || '').includes('closeModal')) btn.remove();
    });
  });

  // confirm modal OK button
  document.getElementById('confirm-modal-ok').addEventListener('click', () => {
    document.getElementById('modal-confirm').classList.remove('open');
    if (_confirmResolve) { _confirmResolve(true); _confirmResolve = null; }
  });

  // showConfirmDialog — แทน confirm()
  window.showConfirmDialog = function(msg, title) {
    document.getElementById('confirm-modal-message').textContent = msg;
    document.getElementById('confirm-modal-title').innerHTML = '<i class="fa-solid fa-circle-question"></i> ' + (title || 'ยืนยัน');
    document.getElementById('modal-confirm').classList.add('open');
    return new Promise(r => { _confirmResolve = r; });
  };

  // date showPicker — คลิกที่ไหนก็ได้เปิดปฏิทิน (คอม + iPad)
  function attachShowPicker(el) {
    if (el._showPickerAttached) return;
    el._showPickerAttached = true;
    el.lang = 'en-GB';
    el.addEventListener('click', function() {
      try { this.showPicker && this.showPicker(); } catch(e) {}
    });
    // ถ้ามี text input คู่อยู่ใน parent เดียวกัน ให้คลิก text ก็เปิด picker ด้วย
    const parent = el.parentElement;
    if (parent) {
      const textInput = parent.querySelector('input[type="text"]');
      if (textInput && !textInput._datePickerLinked) {
        textInput._datePickerLinked = true;
        textInput.addEventListener('click', () => {
          try { el.showPicker && el.showPicker(); } catch(e) {}
        });
      }
    }
  }
  document.querySelectorAll('input[type="date"]').forEach(attachShowPicker);
  // ติดตาม date inputs ที่ถูกสร้างขึ้นภายหลัง (dynamic content)
  const _dpObserver = new MutationObserver(muts => {
    muts.forEach(m => m.addedNodes.forEach(n => {
      if (n.nodeType !== 1) return;
      if (n.matches('input[type="date"]')) attachShowPicker(n);
      n.querySelectorAll('input[type="date"]').forEach(attachShowPicker);
    }));
  });
  _dpObserver.observe(document.body, { childList: true, subtree: true });
});

function statusTH(s) {
  return {
    pending:    'รอดำเนินการ',
    in_transit: 'กำลังส่ง',
    delivered:  'ส่งแล้ว',
    completed:  'เสร็จสิ้น',
    cancelled:  'ยกเลิก',
  }[s] || s;
}

function subStatusTH(s) {
  return {
    incomplete: 'รับไม่ครบ',
    rejected:   'ปฏิเสธรับ',
  }[s] || s;
}

function roleTH(r) {
  return {admin:'ผู้ดูแลระบบ', hq:'สำนักงานใหญ่', driver:'คนขับ', branch:'สาขา'}[r] || r;
}

function parsePhotoPaths(val) {
  if (!val) return [];
  try { const p = JSON.parse(val); return Array.isArray(p) ? p : [val]; } catch { return [val]; }
}

function renderPhotoGrid(paths, folder) {
  if (!paths.length) return '';
  return paths.map(p =>
    `<img src="/storage/${folder}/${p}" style="width:80px;height:80px;object-fit:cover;border-radius:6px;border:1.5px solid #e2e8f0;cursor:zoom-in;" onclick="event.stopPropagation();openPhotoViewer(this.src,'หลักฐาน')">`
  ).join('');
}

function formatDate(dateStr) {
  if (!dateStr) return '-';
  const d = new Date(dateStr.length === 10 ? dateStr + 'T00:00:00' : dateStr);
  const dd = String(d.getDate()).padStart(2,'0');
  const mm = String(d.getMonth()+1).padStart(2,'0');
  return `${dd}/${mm}/${d.getFullYear()}`;
}

function autoFormatDateDMY(input) {
  let v = input.value.replace(/\D/g, '');
  if (v.length > 2) v = v.slice(0,2) + '/' + v.slice(2);
  if (v.length > 5) v = v.slice(0,5) + '/' + v.slice(5,9);
  input.value = v;
}

function syncDateToText(picker, textId) {
  if (!picker.value) return;
  const [y, m, d] = picker.value.split('-');
  document.getElementById(textId).value = `${d}/${m}/${y}`;
}

function parseDateDMY(str) {
  const parts = (str || '').split('/');
  if (parts.length !== 3 || parts[2].length !== 4) return '';
  return `${parts[2]}-${parts[1].padStart(2,'0')}-${parts[0].padStart(2,'0')}`;
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

/* ---- PRODUCT NAME CACHES ---- */
let allProductNamesCache = [];
let branchProductNamesCache = [];

/* ---- REJECTED ORDERS (สินค้าตีกลับ) ---- */
function clearHqReturnFilters() {
  const s = document.getElementById('hq-return-search');
  const dt = document.getElementById('hq-return-date');
  const dtt = document.getElementById('hq-return-date-text');
  if (s) s.value = '';
  if (dt) dt.value = '';
  if (dtt) dtt.value = '';
  renderHqReturns();
}

function clearRetHistoryFilters() {
  const s = document.getElementById('ret-history-search');
  const dt = document.getElementById('ret-history-date');
  const dtt = document.getElementById('ret-history-date-text');
  if (s) s.value = '';
  if (dt) dt.value = '';
  if (dtt) dtt.value = '';
  renderReturnHistory();
}

async function loadHqReturns() {
  const el = document.getElementById('hq-returns-list');
  if (!el) return;
  el.innerHTML = '<div class="loading">กำลังโหลด...</div>';
  hqReturnFilter = 'pending';
  const r = await api('GET', '/api/returns');
  if (!r.ok) { el.innerHTML = '<div class="empty-state"><p>โหลดไม่สำเร็จ</p></div>'; return; }
  allHqReturns = r.data || [];
  renderHqReturnTabs();
  renderHqReturns();
}

function setHqReturnTab(status) {
  hqReturnFilter = status;
  renderHqReturnTabs();
  renderHqReturns();
}

function renderHqReturnTabs() {
  const el = document.getElementById('hq-returns-tabs');
  if (!el) return;
  const counts = { all: allHqReturns.length, pending: 0, approved: 0 };
  allHqReturns.forEach(i => {
    if (i.status === 'pending') counts.pending++;
    else if (i.status === 'approved') counts.approved++;
  });
  const tabs = [
    { key: 'all',      label: 'ทั้งหมด',      dataStatus: '',          count: counts.all },
    { key: 'pending',  label: 'รอดำเนินการ',  dataStatus: 'pending',   count: counts.pending },
    { key: 'approved', label: 'อนุมัติแล้ว',  dataStatus: 'completed', count: counts.approved },
  ];
  el.innerHTML = tabs.map(t => `
    <button class="order-tab ${hqReturnFilter === t.key ? 'active' : ''}"
      data-status="${t.dataStatus}" onclick="setHqReturnTab('${t.key}')">
      ${t.label} <span class="order-tab-badge">${t.count}</span>
    </button>
  `).join('');
}

function renderHqReturns() {
  const el = document.getElementById('hq-returns-list');
  if (!el) return;
  const reasonTHq = { damaged:'สินค้าชำรุด', expired:'สินค้าหมดอายุ', wrong_item:'ส่งผิดรายการ', other:'อื่นๆ' };
  const stBadge = { pending:'badge-pending', approved:'badge-delivered', rejected:'badge-cancelled' };
  const stTH = { pending:'รอดำเนินการ', approved:'อนุมัติ', rejected:'ปฏิเสธ' };
  let list = allHqReturns;
  if (hqReturnFilter === 'pending')  list = list.filter(i => i.status === 'pending');
  else if (hqReturnFilter === 'approved') list = list.filter(i => i.status === 'approved');
  const q = (document.getElementById('hq-return-search')?.value || '').toLowerCase().trim();
  const d = document.getElementById('hq-return-date')?.value || '';
  if (q) list = list.filter(i => (i.product_name||'').toLowerCase().includes(q) || (i.branch?.name||'').toLowerCase().includes(q));
  if (d) list = list.filter(i => i.created_at && i.created_at.startsWith(d));
  if (!list.length) {
    el.innerHTML = '<div class="empty-state"><i class="fa-solid fa-inbox"></i><p>ไม่มีรายการ</p></div>';
    return;
  }
  const todayHqr = new Date().toISOString().split('T')[0];
  el.innerHTML = list.map(item => {
    const todayFmt = (([y,m,d]) => d+'/'+m+'/'+y)(todayHqr.split('-'));
    return `
    <div style="border:1.5px solid ${item.status==='pending'?'#fbd38d':'#e2e8f0'};border-radius:12px;margin-bottom:10px;background:#fff;overflow:hidden;">
      <div style="padding:13px 16px;display:flex;justify-content:space-between;align-items:center;cursor:pointer;"
           onclick="var d=document.getElementById('hqr-d-${item.id}');var c=document.getElementById('hqr-c-${item.id}');var o=d.style.display!=='none';d.style.display=o?'none':'block';c.style.transform=o?'rotate(0deg)':'rotate(180deg)';">
        <div>
          <div style="font-weight:700;font-size:14px;color:#2d3748;">${item.product_name}
            <span style="font-weight:500;color:#718096;font-size:13px;">× ${Math.round(item.quantity)}</span>
          </div>
          <div style="font-size:12px;color:#718096;margin-top:2px;">
            <i class="fa-solid fa-store fa-sm"></i> ${item.branch?.name || '-'}
            &nbsp;·&nbsp; ${reasonTHq[item.reason] || item.reason}
            &nbsp;·&nbsp; ${formatDateTime(item.created_at)}
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
          <span class="badge ${stBadge[item.status] || 'badge-pending'}">${stTH[item.status] || item.status}</span>
          <i id="hqr-c-${item.id}" class="fa-solid fa-chevron-down" style="font-size:13px;color:#a0aec0;transition:transform .2s;"></i>
        </div>
      </div>
      <div id="hqr-d-${item.id}" style="display:none;padding:0 16px 14px;border-top:1.5px solid #f0f4f8;">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:13px;margin-top:10px;margin-bottom:10px;">
          <div><span style="color:#718096;">สาขา:</span> <b>${item.branch?.name || '-'}</b></div>
          <div><span style="color:#718096;">จำนวนที่แจ้ง:</span> <b>${Math.round(item.quantity)}</b></div>
          <div><span style="color:#718096;">สาเหตุ:</span> <b>${reasonTHq[item.reason] || item.reason}</b></div>
          <div><span style="color:#718096;">แจ้งโดย:</span> <b>${item.creator?.name || '-'}</b></div>
          ${item.remark ? `<div style="grid-column:1/-1;"><span style="color:#718096;">รายละเอียด:</span> ${item.remark}</div>` : ''}
          <div style="grid-column:1/-1;color:#a0aec0;font-size:11px;">${formatDateTime(item.created_at)}</div>
        </div>
        ${item.photo_path ? `<div style="margin-bottom:12px;"><img src="/api/returns/photo/${item.photo_path.split('/').pop()}" style="max-width:100%;border-radius:8px;max-height:200px;object-fit:cover;cursor:zoom-in;" onclick="openPhotoViewer(this.src,'รูปหลักฐาน — ${item.product_name}')"><div style="font-size:10px;color:#a0aec0;margin-top:4px;">กดที่รูปเพื่อดูเต็มจอ</div></div>` : ''}
        ${item.status === 'pending' ? `
        <div style="background:#f0fff4;border:1.5px solid #c6f6d5;border-radius:10px;padding:14px;">
          <div style="font-size:13px;font-weight:700;color:#276749;margin-bottom:12px;"><i class="fa-solid fa-box"></i> สร้างออเดอร์ส่งคืนให้สาขา</div>
          <div class="form-row" style="grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
            <div class="form-group" style="margin:0;">
              <label style="font-size:12px;">สินค้า</label>
              <input type="text" value="${item.product_name}" disabled style="width:100%;border:1.5px solid #c6f6d5;border-radius:6px;padding:8px 10px;font-size:13px;font-family:inherit;background:#e6ffed;color:#276749;">
            </div>
            <div class="form-group" style="margin:0;">
              <label style="font-size:12px;">จำนวนที่จะส่งคืน</label>
              <input type="number" id="hqr-qty-${item.id}" value="${Math.round(item.quantity)}" min="1" step="1" style="width:100%;border:1.5px solid #c6f6d5;border-radius:6px;padding:8px 10px;font-size:13px;font-family:inherit;">
            </div>
          </div>
          <div class="form-group" style="margin-bottom:12px;">
            <label style="font-size:12px;">วันที่นัดส่ง</label>
            <div style="position:relative;">
              <input type="text" id="hqr-date-text-${item.id}" placeholder="วว/ดด/ปปปป" readonly value="${todayFmt}" style="width:100%;border:1.5px solid #c6f6d5;padding:8px 10px;font-size:13px;font-family:inherit;cursor:pointer;box-sizing:border-box;" onclick="try{document.getElementById('hqr-date-${item.id}').showPicker()}catch(e){}">
              <input type="date" id="hqr-date-${item.id}" value="${todayHqr}" tabindex="-1" style="position:absolute;inset:0;opacity:0;pointer-events:none;" onchange="syncDateToText(this,'hqr-date-text-${item.id}')">
            </div>
          </div>
          <button class="btn btn-success" style="width:100%;font-size:14px;" onclick="approveAndCreateOrder(${item.id},${item.branch_id},'${item.product_name}','hqr-qty-${item.id}','hqr-date-${item.id}')">
            <i class="fa-solid fa-circle-check"></i> อนุมัติและสร้างออเดอร์ใหม่
          </button>
        </div>` : ''}
      </div>
    </div>`;
  }).join('');
}

function openHqReturnModal(item) {
  const reasonTHq2 = { damaged:'สินค้าชำรุด', expired:'สินค้าหมดอายุ', wrong_item:'ส่งผิดรายการ', other:'อื่นๆ' };
  const stTH2 = { pending:'รอดำเนินการ', approved:'อนุมัติ', rejected:'ปฏิเสธ' };
  const stBadge2 = { pending:'badge-pending', approved:'badge-delivered', rejected:'badge-cancelled' };
  const today = new Date().toISOString().split('T')[0];
  document.getElementById('hq-ret-modal-content').innerHTML = `
    <div style="display:flex;flex-direction:column;gap:14px;">

      <!-- ข้อมูลคำร้อง -->
      <div style="background:#f7fafc;border-radius:10px;padding:14px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
          <div style="font-weight:700;font-size:15px;color:#2d3748;">${item.product_name}</div>
          <span class="badge ${stBadge2[item.status] || 'badge-pending'}">${stTH2[item.status] || item.status}</span>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:13px;">
          <div><span style="color:#718096;">สาขา:</span> <b>${item.branch?.name || '-'}</b></div>
          <div><span style="color:#718096;">จำนวนที่แจ้ง:</span> <b>${Math.round(item.quantity)}</b></div>
          <div><span style="color:#718096;">สาเหตุ:</span> <b>${reasonTHq2[item.reason] || item.reason}</b></div>
          <div><span style="color:#718096;">แจ้งโดย:</span> <b>${item.creator?.name || '-'}</b></div>
          ${item.remark ? `<div style="grid-column:1/-1;"><span style="color:#718096;">รายละเอียด:</span> ${item.remark}</div>` : ''}
          <div style="grid-column:1/-1;color:#a0aec0;font-size:11px;">${formatDateTime(item.created_at)}</div>
        </div>
      </div>

      ${item.photo_path ? `<img src="/api/returns/photo/${item.photo_path.split('/').pop()}" style="max-width:100%;border-radius:8px;max-height:200px;object-fit:cover;">` : ''}

      ${item.status === 'pending' ? `
      <!-- ฟอร์มสร้างออเดอร์ -->
      <div style="background:#f0fff4;border:1.5px solid #c6f6d5;border-radius:10px;padding:14px;">
        <div style="font-size:13px;font-weight:700;color:#276749;margin-bottom:12px;">
          <i class="fa-solid fa-box"></i> สร้างออเดอร์ส่งคืนให้สาขา
        </div>
        <div class="form-row" style="grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
          <div class="form-group" style="margin:0;">
            <label style="font-size:12px;">สินค้า</label>
            <input type="text" value="${item.product_name}" disabled
              style="width:100%;border:1.5px solid #c6f6d5;border-radius:6px;padding:8px 10px;font-size:13px;font-family:inherit;background:#e6ffed;color:#276749;">
          </div>
          <div class="form-group" style="margin:0;">
            <label style="font-size:12px;">จำนวนที่จะส่งคืน</label>
            <input type="number" id="hqr-qty-${item.id}" value="${Math.round(item.quantity)}" min="1" step="1"
              style="width:100%;border:1.5px solid #c6f6d5;border-radius:6px;padding:8px 10px;font-size:13px;font-family:inherit;">
          </div>
        </div>
        <div class="form-group" style="margin-bottom:12px;">
          <label style="font-size:12px;">วันที่นัดส่ง</label>
          <div style="position:relative;">
            <input type="text" id="hqr-date-text-${item.id}" placeholder="วว/ดด/ปปปป" readonly
              value="${(d => { const [y,m,dy]=today.split('-'); return dy+'/'+m+'/'+y; })()}"
              style="width:100%;border:1.5px solid #c6f6d5;padding:8px 10px;font-size:13px;font-family:inherit;cursor:pointer;box-sizing:border-box;"
              onclick="try{document.getElementById('hqr-date-${item.id}').showPicker()}catch(e){}">
            <input type="date" id="hqr-date-${item.id}" value="${today}" tabindex="-1"
              style="position:absolute;inset:0;opacity:0;pointer-events:none;"
              onchange="syncDateToText(this,'hqr-date-text-${item.id}')">
          </div>
        </div>
        <button class="btn btn-success" style="width:100%;font-size:14px;"
          onclick="approveAndCreateOrder(${item.id},${item.branch_id},'${item.product_name}','hqr-qty-${item.id}','hqr-date-${item.id}')">
          <i class="fa-solid fa-circle-check"></i> อนุมัติและสร้างออเดอร์ใหม่
        </button>
      </div>` : ''}
    </div>`;
  document.getElementById('modal-hq-return-detail').classList.add('open');
}

async function updateReturn(id, status) {
  const r = await api('PUT', '/api/returns/' + id, { status });
  if (r.ok) { showToast(status === 'approved' ? 'อนุมัติแล้ว' : 'ปฏิเสธแล้ว', 'success'); loadHqReturns(); }
  else showToast(r.data?.message || 'เกิดข้อผิดพลาด', 'error');
}

async function approveAndCreateOrder(returnId, branchId, productName, qtyInputId, dateInputId) {
  const qtyEl = document.getElementById(qtyInputId);
  const qty = parseInt(qtyEl?.value, 10);
  if (!qty || qty < 1) { showToast('กรุณาระบุจำนวนที่ถูกต้อง', 'error'); return; }

  const dateEl = document.getElementById(dateInputId || ('hr-date-' + returnId));
  const date = dateEl?.value;
  if (!date) { showToast('กรุณาเลือกวันที่นัดส่ง', 'error'); return; }

  const r = await api('PUT', '/api/returns/' + returnId, { status: 'approved' });
  if (!r.ok) { showToast(r.data?.message || 'อนุมัติไม่สำเร็จ', 'error'); return; }

  const orderR = await api('POST', '/api/delivery-orders', {
    branch_id: branchId,
    scheduled_date: date,
    items: [{ product_name: productName, qty_ordered: qty }],
  });
  if (orderR.ok) {
    showToast(`อนุมัติและสร้าง ${orderR.data.do_number} เรียบร้อย`, 'success');
  } else {
    showToast('อนุมัติแล้ว แต่สร้างออเดอร์ไม่สำเร็จ: ' + JSON.stringify(orderR.data?.errors || orderR.data?.message), 'error');
  }
  loadHqReturns();
}



// ── Web Push Notifications ────────────────────────────────────────────────
const VAPID_PUBLIC_KEY = '{{ config("webpush.vapid.public_key") }}';

function urlBase64ToUint8Array(base64String) {
  const padding = '='.repeat((4 - base64String.length % 4) % 4);
  const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
  const raw = atob(base64);
  return Uint8Array.from([...raw].map(c => c.charCodeAt(0)));
}

async function setupPushNotifications() {
  if (!('serviceWorker' in navigator) || !('PushManager' in window) || !VAPID_PUBLIC_KEY) return;
  try {
    const reg = await navigator.serviceWorker.register('/sw.js');
    await navigator.serviceWorker.ready;

    // ถ้า permission granted อยู่แล้ว ให้ subscribe เลย (ไม่ขอ permission ซ้ำ)
    if (Notification.permission === 'granted') {
      const existing = await reg.pushManager.getSubscription();
      if (existing) { await sendSubscriptionToServer(existing); }
      else {
        const sub = await reg.pushManager.subscribe({ userVisibleOnly: true, applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY) });
        await sendSubscriptionToServer(sub);
      }
    }
    updatePushButton();
  } catch (e) {
    console.warn('Push setup failed:', e);
  }
}

// เรียกเมื่อผู้ใช้กดปุ่ม "เปิดรับการแจ้งเตือน"
async function enablePushFromButton() {
  if (!('serviceWorker' in navigator) || !('PushManager' in window) || !VAPID_PUBLIC_KEY) return;
  try {
    const permission = await Notification.requestPermission();
    if (permission !== 'granted') { updatePushButton(); return; }
    const reg = await navigator.serviceWorker.ready;
    let sub = await reg.pushManager.getSubscription();
    if (!sub) sub = await reg.pushManager.subscribe({ userVisibleOnly: true, applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY) });
    await sendSubscriptionToServer(sub);
    updatePushButton();
  } catch (e) {
    console.warn('Push enable failed:', e);
  }
}

async function sendTestPush() {
  const r = await api('POST', '/api/push/test', {});
  if (r.ok) alert('ส่งแล้ว — ดูการแจ้งเตือนนอกแอป (ลองกดที่อื่นก่อน)');
  else alert('ส่งไม่ได้: ' + JSON.stringify(r.data));
}

function updatePushButton() {
  const btn = document.getElementById('btn-enable-push');
  const testBtn = document.getElementById('btn-test-push');
  if (!btn) return;
  const perm = ('Notification' in window) ? Notification.permission : 'unsupported';
  if (perm === 'granted') {
    btn.style.display = 'none';
    if (testBtn) testBtn.style.display = '';
  } else if (perm === 'denied') {
    btn.textContent = '🔕 การแจ้งเตือนถูกบล็อก';
    btn.disabled = true;
    if (testBtn) testBtn.style.display = 'none';
  } else {
    btn.style.display = '';
    if (testBtn) testBtn.style.display = 'none';
  }
}

async function sendSubscriptionToServer(sub) {
  const key = sub.getKey('p256dh');
  const auth = sub.getKey('auth');
  if (!key || !auth) return;
  const p256dh = btoa(String.fromCharCode(...new Uint8Array(key)));
  const authStr = btoa(String.fromCharCode(...new Uint8Array(auth)));
  await api('POST', '/api/push/subscribe', { endpoint: sub.endpoint, p256dh, auth: authStr });
}

window.onload = async () => {
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(() => {});
    // เมื่อ SW ได้รับ push และส่ง message กลับมา → reload bell ทันที
    navigator.serviceWorker.addEventListener('message', event => {
      if (event.data?.type === 'RELOAD_NOTIFICATIONS') loadNotifications();
    });
  }
  if (token) {
    const r = await api('GET', '/api/auth/me');
    if (r.ok) initApp(r.data);
    else { token = ''; localStorage.removeItem('bh_token'); }
  }
};

/* ════════════════════════════════════════════════════════
   CALENDAR — ปฏิทินงานขนส่ง
   ════════════════════════════════════════════════════════ */
let calYear = 0, calMonth = 0, calData = null;

const CAL_TH_MONTHS = ['','ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
const CAL_STATUS_COLOR = {
  pending:    '#f6e05e',
  in_transit: '#63b3ed',
  delivered:  '#63b3ed',
  completed:  '#68d391',
  cancelled:  '#fc8181',
};
const CAL_STATUS_TH = {
  pending:'รอดำเนินการ', in_transit:'กำลังส่ง', delivered:'ส่งแล้ว', completed:'สำเร็จ', cancelled:'ยกเลิก',
};
const CAL_STATUS_TC = {pending:'#744210',in_transit:'#1a365d',delivered:'#1a365d',completed:'#1c4532',cancelled:'#63171b'};

async function initCalendarPage() {
  const now = new Date();
  calYear  = now.getFullYear();
  calMonth = now.getMonth() + 1;

  // Branch filter (HQ only)
  const filterWrap = document.getElementById('cal-branch-filter');
  if (filterWrap) {
    filterWrap.style.display = currentUser?.role === 'hq' ? 'flex' : 'none';
    if (currentUser?.role === 'hq') {
      const br = await api('GET', '/api/branches');
      const sel = document.getElementById('cal-branch-sel');
      if (br.ok && sel) {
        sel.innerHTML = '<option value="">ทุกสาขา</option>' +
          br.data.map(b => `<option value="${b.id}">${b.name}</option>`).join('');
      }
    }
  }

  await loadCalendar();
}

function scrollToDriverCard(doId) {
  let tries = 0;
  const attempt = () => {
    filterDriverOrders('available');
    const el = document.querySelector(`[data-do-id="${doId}"]`);
    if (el) {
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      el.classList.add('highlight-pulse');
      setTimeout(() => el.classList.remove('highlight-pulse'), 2000);
    } else if (++tries < 20) {
      setTimeout(attempt, 200);
    }
  };
  setTimeout(attempt, 400);
}

function calOpenOrder(id, status) {
  closeCalDetail();
  if (currentUser?.role === 'branch') {
    setTimeout(() => openBranchDoDetail(id), 200);
  } else if (currentUser?.role === 'driver') {
    if (['pending', 'in_transit'].includes(status)) {
      navigate('dashboard');
      scrollToDriverCard(id);
    } else {
      setTimeout(() => viewOrder(id), 200);
    }
  } else {
    navigate('orders');
    setTimeout(() => viewOrder(id), 400);
  }
}

async function loadCalendar() {
  const spin = document.getElementById('cal-spin');
  if (spin) spin.classList.add('fa-spin');

  const branchId = document.getElementById('cal-branch-sel')?.value || '';
  const qs = `year=${calYear}&month=${calMonth}${branchId ? '&branch_id=' + branchId : ''}`;
  const r = await api('GET', '/api/calendar?' + qs);

  if (spin) spin.classList.remove('fa-spin');
  if (!r.ok) return;

  calData = r.data;
  renderCalSummary();
  renderCalLegend();
  renderCalGrid();
}

function renderCalSummary() {
  const s = calData.summary;
  document.getElementById('cal-sum-total').textContent = s.total;
  document.getElementById('cal-sum-done').textContent  = s.completed;
  document.getElementById('cal-sum-days').textContent  = s.days;

  // Month title
  document.getElementById('cal-title').textContent =
    `${CAL_TH_MONTHS[calMonth]} ${calYear + 543}`;
}

function renderCalLegend() {
  const legendEl = document.getElementById('cal-legend');
  if (!legendEl) return;

  // Collect unique statuses present this month
  const seen = new Set();
  (calData.days || []).forEach(d => {
    d.orders.forEach(o => seen.add(o.status));
  });

  if (!seen.size) { legendEl.innerHTML = ''; return; }

  legendEl.innerHTML = [...seen].map(s => {
    const bg = CAL_STATUS_COLOR[s] || '#e2e8f0';
    const tc = CAL_STATUS_TC[s] || '#4a5568';
    return `<span style="background:${bg};color:${tc};padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600;">${CAL_STATUS_TH[s]||s}</span>`;
  }).join('');
}

function renderCalGrid() {
  const body = document.getElementById('cal-body');
  const today = new Date();
  const todayStr = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}-${String(today.getDate()).padStart(2,'0')}`;

  // Build day-data map
  const dayMap = {};
  (calData.days || []).forEach(d => { dayMap[d.date] = d; });

  // First day of month (0=Sun…6=Sat → convert to Mon-start: Mon=0…Sun=6)
  const firstDay = new Date(calYear, calMonth - 1, 1);
  let startOffset = firstDay.getDay(); // 0=Sun
  startOffset = startOffset === 0 ? 6 : startOffset - 1; // Mon-start

  const daysInMonth   = new Date(calYear, calMonth, 0).getDate();
  const daysInPrevMonth = new Date(calYear, calMonth - 1, 0).getDate();

  const cells = [];

  // Prev-month padding
  for (let i = startOffset - 1; i >= 0; i--) {
    cells.push({ day: daysInPrevMonth - i, otherMonth: true });
  }
  // Current month
  for (let d = 1; d <= daysInMonth; d++) {
    cells.push({ day: d, otherMonth: false });
  }
  // Next-month padding
  const remaining = 42 - cells.length;
  for (let d = 1; d <= remaining; d++) {
    cells.push({ day: d, otherMonth: true });
  }

  body.innerHTML = cells.map(({ day, otherMonth }, idx) => {
    const colIdx  = idx % 7; // 0=Mon…6=Sun
    const isWeekend = colIdx >= 5;

    if (otherMonth) {
      return `<div class="cal-cell cal-other-month"><div class="cal-date-num" style="color:${isWeekend?'#e53e3e':'#a0aec0'}">${day}</div></div>`;
    }

    const dateStr = `${calYear}-${String(calMonth).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
    const isToday = dateStr === todayStr;
    const data    = dayMap[dateStr];

    let barHtml  = '';
    let countNum = '';

    if (data) {
      const statusCounts = {};
      data.orders.forEach(o => { statusCounts[o.status] = (statusCounts[o.status] || 0) + 1; });
      const BAR_ORDER = ['pending', 'in_transit', 'delivered', 'completed', 'cancelled'];
      const segs = BAR_ORDER
        .filter(s => statusCounts[s])
        .map(s => `<div style="flex:${statusCounts[s]};background:${CAL_STATUS_COLOR[s]||'#a0aec0'};" title="${CAL_STATUS_TH[s]||s}: ${statusCounts[s]}"></div>`)
        .join('');
      barHtml  = `<div class="cal-bar-wrap">${segs}</div>`;
      countNum = data.total;
    }

    const dateNumStyle = isWeekend ? 'color:#e53e3e;' : '';
    const classes = ['cal-cell', isToday ? 'cal-today' : '', data ? 'cal-has-work' : ''].filter(Boolean).join(' ');
    const onclick  = data ? `openCalDetail('${dateStr}')` : '';

    return `
      <div class="${classes}" ${onclick ? `onclick="${onclick}"` : ''} data-date="${dateStr}">
        ${barHtml}
        <div style="position:relative;z-index:1;display:flex;justify-content:space-between;align-items:flex-start;">
          <div class="cal-date-num" style="${isToday ? '' : dateNumStyle}">${day}</div>
          ${countNum ? `<span class="cal-count">${countNum}</span>` : ''}
        </div>
      </div>`;
  }).join('');
}

function calPrev() {
  calMonth--;
  if (calMonth < 1) { calMonth = 12; calYear--; }
  loadCalendar();
}
function calNext() {
  calMonth++;
  if (calMonth > 12) { calMonth = 1; calYear++; }
  loadCalendar();
}

function openCalDetail(dateStr) {
  // Highlight selected cell
  document.querySelectorAll('.cal-cell.cal-selected').forEach(c => c.classList.remove('cal-selected'));
  const cell = document.querySelector(`.cal-cell[data-date="${dateStr}"]`);
  if (cell) cell.classList.add('cal-selected');

  const data = (calData?.days || []).find(d => d.date === dateStr);
  if (!data) return;

  const [y, m, d] = dateStr.split('-');
  const thDate = `${parseInt(d)} ${CAL_TH_MONTHS[parseInt(m)]} ${parseInt(y) + 543}`;

  const isAdminHq = ['admin','hq'].includes(currentUser?.role);

  const ordersHtml = data.orders.map(o => {
    const sc = CAL_STATUS_COLOR[o.status] || '#a0aec0';
    const st = CAL_STATUS_TH[o.status]   || o.status;
    const productLine = o.product_summary
      ? o.product_summary
      : '<span style="color:#a0aec0;">ไม่มีรายการสินค้า</span>';
    return `
      <div style="display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid #f0f4f8;">
        <div style="flex:1;min-width:0;">
          <div style="font-size:13px;font-weight:700;color:#1e3a5f;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${productLine}</div>
          <div style="font-size:11px;color:#a0aec0;margin-top:2px;">
            <i class="fa-solid fa-file-lines"></i> ${o.do_number}
            &nbsp;·&nbsp;<i class="fa-solid fa-building" style="color:#4a90d9;"></i> ${o.branch_name}
            ${isAdminHq ? `&nbsp;·&nbsp;<i class="fa-solid fa-user" style="color:#68d391;"></i> ${o.driver_name}` : ''}
          </div>
        </div>
        <span style="background:${sc};color:${CAL_STATUS_TC[o.status]||'#4a5568'};padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap;">${st}</span>
      </div>`;
  }).join('');

  document.getElementById('cal-detail-content').innerHTML = `
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
      <div>
        <div style="font-size:17px;font-weight:800;color:#1e3a5f;">${thDate}</div>
        <div style="font-size:12px;color:#718096;margin-top:3px;">
          <i class="fa-solid fa-box"></i> ${data.total} ออเดอร์
          &nbsp;·&nbsp;<i class="fa-solid fa-circle-check" style="color:#38a169;"></i> สำเร็จ ${data.completed}
          ${data.in_transit ? `&nbsp;·&nbsp;<i class="fa-solid fa-truck" style="color:#3182ce;"></i> วิ่งอยู่ ${data.in_transit}` : ''}
          ${data.pending    ? `&nbsp;·&nbsp;<i class="fa-solid fa-hourglass-half" style="color:#d69e2e;"></i> รอ ${data.pending}` : ''}
        </div>
      </div>
      <button onclick="closeCalDetail()" style="border:none;background:#f1f5f9;border-radius:8px;padding:6px 10px;cursor:pointer;font-size:13px;color:#718096;"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div>${ordersHtml}</div>`;

  document.getElementById('cal-overlay').classList.add('open');
}

function closeCalDetail() {
  document.getElementById('cal-overlay').classList.remove('open');
  document.querySelectorAll('.cal-cell.cal-selected').forEach(c => c.classList.remove('cal-selected'));
}

// Mobile date input placeholder fix
function initDatePlaceholder(input) {
  if (input._datePHInit || input.dataset.noPh) return;
  input._datePHInit = true;
  const parent = input.parentNode;
  const isFull = input.style.width === '100%' || getComputedStyle(input).width === '100%';
  const wrap = document.createElement('div');
  wrap.className = 'date-input-wrap' + (isFull ? ' full' : '');
  parent.insertBefore(wrap, input);
  wrap.appendChild(input);
  if (isFull) input.style.width = '100%';
  const ph = document.createElement('span');
  ph.className = 'date-ph';
  ph.textContent = 'เลือกวันที่';
  wrap.appendChild(ph);
  const update = () => { ph.style.display = input.value ? 'none' : ''; };
  input.addEventListener('change', update);
  update();
}
document.querySelectorAll('input[type="date"]').forEach(initDatePlaceholder);
const _dateObserver = new MutationObserver(muts => {
  muts.forEach(m => m.addedNodes.forEach(n => {
    if (n.nodeType !== 1) return;
    if (n.matches('input[type="date"]')) initDatePlaceholder(n);
    n.querySelectorAll('input[type="date"]').forEach(initDatePlaceholder);
  }));
});
_dateObserver.observe(document.body, { childList: true, subtree: true });

</script>
</body>
</html>
