<?php

session_start();

if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$adminName = $_SESSION['user_name'] ?? 'Admin';

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pinterest Admin Panel</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
  :root {
    --pin-red: #E60023;
    --pin-dark: #111;
    --sidebar-w: 240px;
  }
  body { background: #f5f5f5; font-family: Arial, sans-serif; }

  /* Sidebar  */
  .sidebar {
    position: fixed; top: 0; left: 0;
    width: var(--sidebar-w); height: 100vh;
    background: var(--pin-dark); color: #fff;
    display: flex; flex-direction: column;
    z-index: 1000; overflow-y: auto;
  }
  .sidebar-logo {
    padding: 20px 24px 16px;
    font-size: 22px; font-weight: 800;
    color: var(--pin-red);
    border-bottom: 1px solid #2a2a2a;
  }
  .sidebar-logo span { color: #fff; font-size: 13px; display: block; font-weight: 400; margin-top: 2px; }
  .nav-section { padding: 12px 16px 4px; font-size: 10px; text-transform: uppercase; color: #666; letter-spacing: 1px; }
  .nav-link {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 20px; color: #ccc; text-decoration: none;
    border-radius: 8px; margin: 2px 8px;
    transition: background .15s, color .15s;
    font-size: 14px;
  }
  .nav-link:hover, .nav-link.active { background: #E60023; color: #fff; }
  .nav-link i { font-size: 18px; width: 22px; }
  .sidebar-footer { margin-top: auto; padding: 16px; border-top: 1px solid #2a2a2a; }
  .sidebar-footer a { color: #aaa; font-size: 13px; text-decoration: none; }
  .sidebar-footer a:hover { color: var(--pin-red); }

  /* ── Main ── */
  .main { margin-left: var(--sidebar-w); min-height: 100vh; }
  .topbar {
    background: #fff; padding: 16px 28px;
    display: flex; align-items: center; justify-content: space-between;
    border-bottom: 1px solid #e8e8e8; position: sticky; top: 0; z-index: 100;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
  }
  .topbar h4 { margin: 0; font-size: 18px; font-weight: 700; }
  .page-content { padding: 28px; }

  /* ── Stat cards ── */
  .stat-card {
    background: #fff; border-radius: 12px; padding: 20px 24px;
    box-shadow: 0 1px 6px rgba(0,0,0,.06);
    display: flex; align-items: center; gap: 18px;
  }
  .stat-icon {
    width: 52px; height: 52px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; flex-shrink: 0;
  }
  .stat-val { font-size: 28px; font-weight: 800; line-height: 1; }
  .stat-label { font-size: 13px; color: #666; margin-top: 4px; }

  /* ── Table ── */
  .content-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 6px rgba(0,0,0,.06); overflow: hidden; }
  .content-card-header { padding: 18px 24px; border-bottom: 1px solid #f0f0f0; font-weight: 700; font-size: 15px; display: flex; align-items: center; justify-content: space-between; }
  .table { margin: 0; }
  .table th { font-size: 12px; text-transform: uppercase; color: #888; letter-spacing: .5px; border-bottom: 1px solid #f0f0f0; background: #fafafa; }
  .table td { font-size: 13.5px; vertical-align: middle; }
  .badge-personal  { background: #e8f4fd; color: #1a73e8; }
  .badge-business  { background: #fff3e0; color: #e65100; }
  .badge-active    { background: #e6f4ea; color: #1e7e34; }
  .badge-suspended { background: #fce8e6; color: #c62828; }
  .badge-pending   { background: #fff8e1; color: #f57f17; }
  .badge-approved  { background: #e6f4ea; color: #1e7e34; }
  .badge-rejected  { background: #fce8e6; color: #c62828; }
  .badge-none      { background: #f0f0f0; color: #555; }

  /* ── Tabs ── */
  .tab-btn {
    padding: 8px 18px; border-radius: 20px; border: none;
    background: #f0f0f0; cursor: pointer; font-size: 13px; font-weight: 600;
    transition: background .15s;
  }
  .tab-btn.active { background: var(--pin-red); color: #fff; }

  /* ── Alert toast ── */
  #toast {
    position: fixed; bottom: 24px; right: 24px;
    min-width: 260px; padding: 14px 20px; border-radius: 10px;
    background: #333; color: #fff; font-size: 14px;
    box-shadow: 0 4px 16px rgba(0,0,0,.2);
    display: none; z-index: 9999;
  }
  #toast.success { background: #1e7e34; }
  #toast.error   { background: var(--pin-red); }

  .section { display: none; }
  .section.active { display: block; }

  .search-box {
    border: 1.5px solid #e0e0e0; border-radius: 24px;
    padding: 8px 16px; font-size: 13px; width: 240px;
    outline: none; transition: border .2s;
  }
  .search-box:focus { border-color: var(--pin-red); }

  .modal-header { background: var(--pin-red); color: #fff; }
  .modal-header .btn-close { filter: invert(1); }
</style>
</head>
<body>

<!--  SIDEBAR-->
<div class="sidebar">
  <div class="sidebar-logo">
    📌 Pinterest
    <span>Admin Panel</span>
  </div>

  <div class="nav-section">Overview</div>
  <a href="#" class="nav-link active" onclick="showSection('dashboard', this)">
    <i class="bi bi-speedometer2"></i> Dashboard
  </a>

  <div class="nav-section">Management</div>
  <a href="#" class="nav-link" onclick="showSection('users', this)">
    <i class="bi bi-people"></i> Users
    <span class="badge bg-secondary ms-auto" id="sb-user-count">—</span>
  </a>
  <a href="#" class="nav-link" onclick="showSection('business', this)">
    <i class="bi bi-briefcase"></i> Business Requests
    <span class="badge bg-warning text-dark ms-auto" id="sb-biz-count">—</span>
  </a>
  <a href="#" class="nav-link" onclick="showSection('pins', this)">
    <i class="bi bi-pin"></i> Pins
  </a>
  <a href="#" class="nav-link" onclick="showSection('boards', this)">
    <i class="bi bi-collection"></i> Boards
  </a>
  <a href="#" class="nav-link" onclick="showSection('categories', this)">
    <i class="bi bi-tag"></i> Categories
  </a>

  <div class="sidebar-footer">
    <div class="d-flex align-items-center gap-2 mb-3">
      <div style="width:34px;height:34px;border-radius:50%;background:#E60023;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px;">
        <?= strtoupper(substr($adminName, 0, 1)) ?>
      </div>
      <div>
        <div style="font-size:13px;font-weight:600;color:#fff;"><?= $adminName ?></div>
        <div style="font-size:11px;color:#888;">Administrator</div>
      </div>
    </div>
    <a href="../controller/AdminController.php?action=adminLogout"><i class="bi bi-box-arrow-right me-1"></i>Logout</a>
  </div>
</div>

<!-- MAIN  -->
<div class="main">
  <div class="topbar">
    <h4 id="page-title">Dashboard</h4>
    <div class="d-flex align-items-center gap-3">
      <input class="search-box" type="text" id="globalSearch" placeholder="🔍 Search users…" oninput="searchUsers(this.value)">
      <span style="font-size:13px;color:#888;">Welcome, <strong><?= $adminName ?></strong></span>
    </div>
  </div>

  <div class="page-content">

    <!-- DASHBOARD -->
    <div id="section-dashboard" class="section active">
      <div class="row g-3 mb-4" id="stat-cards"><!-- Populated by JS --></div>
      <div class="content-card">
        <div class="content-card-header">📋 Recent Business Requests (Pending)</div>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead><tr><th>User</th><th>Business</th><th>Requested</th><th>Actions</th></tr></thead>
            <tbody id="dash-biz-table"><tr><td colspan="4" class="text-center text-muted py-3">Loading…</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>

    <!--  USERS  -->
    <div id="section-users" class="section">
      <div class="content-card">
        <div class="content-card-header">
          <span>All Users</span>
          <div id="user-search-results" style="font-size:12px;color:#888;"></div>
        </div>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Type</th><th>Status</th><th>Joined</th><th>Actions</th></tr></thead>
            <tbody id="users-table"><tr><td colspan="7" class="text-center text-muted py-3">Loading…</td></tr></tbody>
          </table>
        </div>
        <div class="p-3 d-flex gap-2 justify-content-end" id="user-pagination"></div>
      </div>
    </div>

    <!--  BUSINESS REQUESTS  -->
    <div id="section-business" class="section">
      <div class="content-card">
        <div class="content-card-header">
          <span>Business Requests</span>
          <div class="d-flex gap-2">
            <button class="tab-btn active" id="tab-pending"  onclick="loadBizRequests('pending',  this)">Pending</button>
            <button class="tab-btn"        id="tab-approved" onclick="loadBizRequests('approved', this)">Approved</button>
            <button class="tab-btn"        id="tab-rejected" onclick="loadBizRequests('rejected', this)">Rejected</button>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead><tr><th>#</th><th>User</th><th>Business Name</th><th>Type</th><th>Status</th><th>Requested</th><th>Actions</th></tr></thead>
            <tbody id="biz-table"><tr><td colspan="7" class="text-center text-muted py-3">Loading…</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- PINS  -->
    <div id="section-pins" class="section">
      <div class="content-card">
        <div class="content-card-header">All Pins</div>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead><tr><th>#</th><th>Title</th><th>Creator</th><th>Category</th><th>Saves</th><th>Comments</th><th>Created</th><th>Actions</th></tr></thead>
            <tbody id="pins-table"><tr><td colspan="8" class="text-center text-muted py-3">Loading…</td></tr></tbody>
          </table>
        </div>
        <div class="p-3 d-flex gap-2 justify-content-end" id="pin-pagination"></div>
      </div>
    </div>

    <!--  BOARDS  -->
    <div id="section-boards" class="section">
      <div class="content-card">
        <div class="content-card-header">All Boards</div>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead><tr><th>#</th><th>Name</th><th>Owner</th><th>Pins</th><th>Visibility</th><th>Created</th><th>Actions</th></tr></thead>
            <tbody id="boards-table"><tr><td colspan="7" class="text-center text-muted py-3">Loading…</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- CATEGORIES -->
    <div id="section-categories" class="section">
      <div class="content-card">
        <div class="content-card-header">
          <span>Categories</span>
          <button class="btn btn-sm" style="background:var(--pin-red);color:#fff;border-radius:20px;" onclick="openAddCategory()">+ Add Category</button>
        </div>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead><tr><th>#</th><th>Name</th><th>Description</th><th>Pins</th><th>Actions</th></tr></thead>
            <tbody id="categories-table"><tr><td colspan="5" class="text-center text-muted py-3">Loading…</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>

  </div><!-- /page-content -->
</div><!-- /main -->


<!-- MODALS -->

<!-- USER DETAIL MODAL -->
<div class="modal fade" id="userDetailModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">👤 User Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="userModalBody">
        <div class="text-center py-5 text-muted">
          <div class="spinner-border text-danger mb-3" role="status"></div>
          <div>Loading user details…</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!--  BUSINESS DETAIL MODAL -->
<div class="modal fade" id="bizDetailModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">💼 Business Request Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="bizModalBody">
        <div class="text-center py-5 text-muted">
          <div class="spinner-border text-danger mb-3" role="status"></div>
          <div>Loading details…</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- REJECT MODAL -->
<div class="modal fade" id="rejectModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Reject Business Request</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <label class="form-label fw-bold">Reason for rejection <span class="text-muted fw-normal">(optional)</span></label>
        <textarea class="form-control" id="rejectReason" rows="3" placeholder="e.g. Incomplete information, duplicate request…"></textarea>
        <input type="hidden" id="rejectRequestId">
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-danger" onclick="confirmReject()">Reject &amp; Notify User</button>
      </div>
    </div>
  </div>
</div>

<!--  CATEGORY MODAL -->
<div class="modal fade" id="categoryModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cat-modal-title">Add Category</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="catId">
        <div class="mb-3">
          <label class="form-label fw-bold">Name</label>
          <input type="text" class="form-control" id="catName">
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Description</label>
          <textarea class="form-control" id="catDesc" rows="2"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn" style="background:var(--pin-red);color:#fff;" onclick="saveCategory()">Save</button>
      </div>
    </div>
  </div>
</div>

<!-- Toast -->
<div id="toast"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const API = '../controller/AdminController.php';
let userPage = 1, pinPage = 1;

// ── Toast 
function toast(msg, type = 'success') {
  const t = document.getElementById('toast');
  t.textContent = (type === 'success' ? '✅ ' : '❌ ') + msg;
  t.className = type;
  t.style.display = 'block';
  setTimeout(() => t.style.display = 'none', 3500);
}

//  Navigation 
function showSection(name, el) {
  document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.nav-link').forEach(a => a.classList.remove('active'));
  document.getElementById('section-' + name).classList.add('active');
  if (el) el.classList.add('active');
  document.getElementById('page-title').textContent = el ? el.textContent.trim() : name;

  const loaders = {
    dashboard:  loadDashboard,
    users:      () => loadUsers(1),
    business:   () => loadBizRequests('pending'),
    pins:       () => loadPins(1),
    boards:     loadBoards,
    categories: loadCategories,
  };
  if (loaders[name]) loaders[name]();
}

// API helpers 
async function apiGet(params) {
  const url = API + '?' + new URLSearchParams(params);
  const r = await fetch(url);
  return r.json();
}
async function apiPost(params, body) {
  const url = API + '?' + new URLSearchParams(params);
  const fd = new FormData();
  Object.entries(body).forEach(([k,v]) => fd.append(k, v));
  const r = await fetch(url, { method: 'POST', body: fd });
  return r.json();
}

// DASHBOARD 
async function loadDashboard() {
  const d = await apiGet({ action: 'getDashboardStats' });
  if (!d.success) return;
  const s = d.data;
  document.getElementById('sb-user-count').textContent = s.totalUsers;
  document.getElementById('sb-biz-count').textContent  = s.pendingBiz;

  document.getElementById('stat-cards').innerHTML = `
    ${statCard('bi-people-fill',       '#e8f4fd','#1a73e8', s.totalUsers,     'Total Users')}
    ${statCard('bi-person-check-fill', '#e6f4ea','#1e7e34', s.activeUsers,    'Active Users')}
    ${statCard('bi-briefcase-fill',    '#fff3e0','#e65100', s.businessAccts,  'Business Accounts')}
    ${statCard('bi-clock-history',     '#fce8e6','#c62828', s.pendingBiz,     'Pending Biz Requests')}
    ${statCard('bi-pin-fill',          '#f3e5f5','#7b1fa2', s.totalPins,      'Total Pins')}
    ${statCard('bi-collection-fill',   '#e8eaf6','#283593', s.totalBoards,    'Total Boards')}
    ${statCard('bi-person-plus-fill',  '#e0f7fa','#00796b', s.newUsersToday,  'New Users Today')}
    ${statCard('bi-person-slash-fill', '#fafafa','#555',    s.suspendedUsers, 'Suspended')}
  `;

  const biz = await apiGet({ action: 'getBusinessRequests', status: 'pending' });
  const tbody = document.getElementById('dash-biz-table');
  if (!biz.data || !biz.data.length) {
    tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">No pending requests 🎉</td></tr>';
    return;
  }
  tbody.innerHTML = biz.data.slice(0,5).map(r => `
    <tr>
      <td><strong>${esc(r.user_name)}</strong><br><small class="text-muted">${esc(r.user_email)}</small></td>
      <td>${esc(r.business_name)}</td>
      <td><small>${fmtDate(r.created_at)}</small></td>
      <td class="d-flex gap-1 flex-wrap">
        <button class="btn btn-sm" style="border:2px solid #1a73e8;border-radius:8px;color:#1a73e8;padding:4px 10px;" onclick="viewBizUser(${r.user_id})" title="View Details"><i class="bi bi-eye"></i></button>
        <button class="btn btn-sm btn-success" onclick="quickApprove(${r.id})">Approve</button>
        <button class="btn btn-sm btn-danger"  onclick="openReject(${r.id})">Reject</button>
      </td>
    </tr>`).join('');
}

function statCard(icon, bg, color, val, label) {
  return `
  <div class="col-xl-3 col-md-4 col-sm-6">
    <div class="stat-card">
      <div class="stat-icon" style="background:${bg};color:${color}"><i class="bi ${icon}"></i></div>
      <div>
        <div class="stat-val">${val ?? '—'}</div>
        <div class="stat-label">${label}</div>
      </div>
    </div>
  </div>`;
}

//  USERS 
async function loadUsers(page = 1) {
  userPage = page;
  const d = await apiGet({ action: 'getAllUsers', page });
  const tbody = document.getElementById('users-table');
  if (!d.success) {
    tbody.innerHTML = '<tr><td colspan="7" class="text-danger text-center py-3">Error loading users</td></tr>';
    return;
  }
  if (!d.data || !d.data.length) {
    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-3">No users found</td></tr>';
    return;
  }

  tbody.innerHTML = d.data.map(u => `
    <tr>
      <td>${u.id}</td>
      <td><strong>${esc(u.name)}</strong><br><small class="text-muted">@${esc(u.username||'')}</small></td>
      <td>${esc(u.email)}</td>
      <td><span class="badge badge-${u.account_type}">${u.account_type}</span></td>
      <td><span class="badge badge-${statusClass(u.status)}">${u.status}</span></td>
      <td><small>${fmtDate(u.created_at)}</small></td>
      <td class="d-flex gap-1 flex-wrap">
        <button class="btn btn-sm btn-outline-primary" onclick="viewUser(${u.id})" title="View Details"><i class="bi bi-eye"></i></button>
        ${u.status === 'suspended'
          ? `<button class="btn btn-sm btn-success"       onclick="activateUser(${u.id})">Activate</button>`
          : `<button class="btn btn-sm btn-warning"       onclick="suspendUser(${u.id})">Suspend</button>`}
        <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(${u.id}, '${esc(u.name)}')">Delete</button>
      </td>
    </tr>`).join('');

  buildPagination('user-pagination', d.meta, p => loadUsers(p));
}

async function searchUsers(q) {
  if (!q.trim()) { loadUsers(1); return; }
  const d = await apiGet({ action: 'searchUsers', q });
  document.getElementById('user-search-results').textContent = d.data ? `${d.data.length} result(s)` : '';
  const tbody = document.getElementById('users-table');
  if (!d.data || !d.data.length) {
    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-3">No users found</td></tr>';
    return;
  }
  tbody.innerHTML = d.data.map(u => `
    <tr>
      <td>${u.id}</td>
      <td><strong>${esc(u.name)}</strong><br><small class="text-muted">@${esc(u.username||'')}</small></td>
      <td>${esc(u.email)}</td>
      <td><span class="badge badge-${u.account_type}">${u.account_type}</span></td>
      <td><span class="badge badge-${statusClass(u.status)}">${u.status}</span></td>
      <td><small>${fmtDate(u.created_at)}</small></td>
      <td class="d-flex gap-1 flex-wrap">
        <button class="btn btn-sm btn-outline-primary" onclick="viewUser(${u.id})" title="View Details"><i class="bi bi-eye"></i></button>
        ${u.status === 'suspended'
          ? `<button class="btn btn-sm btn-success"       onclick="activateUser(${u.id})">Activate</button>`
          : `<button class="btn btn-sm btn-warning"       onclick="suspendUser(${u.id})">Suspend</button>`}
        <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(${u.id}, '${esc(u.name)}')">Delete</button>
      </td>
    </tr>`).join('');
}

async function suspendUser(id) {
  if (!confirm('Suspend this user?')) return;
  const d = await apiPost({ action: 'suspendUser' }, { user_id: id });
  toast(d.message, d.success ? 'success' : 'error');
  if (d.success) loadUsers(userPage);
}
async function activateUser(id) {
  const d = await apiPost({ action: 'activateUser' }, { user_id: id });
  toast(d.message, d.success ? 'success' : 'error');
  if (d.success) loadUsers(userPage);
}
async function deleteUser(id, name) {
  if (!confirm(`Delete user "${name}"? This cannot be undone.`)) return;
  const d = await apiPost({ action: 'deleteUser' }, { user_id: id });
  toast(d.message, d.success ? 'success' : 'error');
  if (d.success) loadUsers(userPage);
}

//  BUSINESS REQUESTS 
async function loadBizRequests(status = 'pending', btn = null) {
  if (btn) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
  }
  const d = await apiGet({ action: 'getBusinessRequests', status });
  const tbody = document.getElementById('biz-table');
  if (!d.data || !d.data.length) {
    tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-3">No ${status} requests</td></tr>`;
    return;
  }
  tbody.innerHTML = d.data.map(r => `
    <tr>
      <td>${r.id}</td>
      <td><strong>${esc(r.user_name)}</strong><br><small>${esc(r.user_email)}</small></td>
      <td>${esc(r.business_name)}</td>
      <td>${esc(r.business_type || '—')}</td>
      <td><span class="badge badge-${r.status}">${r.status}</span></td>
      <td><small>${fmtDate(r.created_at)}</small></td>
      <td class="d-flex gap-1 flex-wrap">
        <button class="btn btn-sm btn-outline-primary" onclick="viewBizUser(${r.user_id})" title="View Details"><i class="bi bi-eye"></i></button>
        ${r.status === 'pending' ? `
          <button class="btn btn-sm btn-success" onclick="quickApprove(${r.id})">Approve</button>
          <button class="btn btn-sm btn-danger"  onclick="openReject(${r.id})">Reject</button>
        ` : r.status === 'rejected'
            ? `<span class="text-muted small">${esc(r.admin_note || '—')}</span>`
            : '<span class="text-success small fw-bold"> Approved</span>'}
      </td>
    </tr>`).join('');
}

async function quickApprove(id) {
  if (!confirm('Approve this business request? An email will be sent to the user.')) return;
  const d = await apiPost({ action: 'approveBusinessRequest' }, { request_id: id });
  toast(d.message, d.success ? 'success' : 'error');
  if (d.success) { loadBizRequests('pending'); loadDashboard(); }
}

function openReject(id) {
  document.getElementById('rejectRequestId').value = id;
  document.getElementById('rejectReason').value = '';
  new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
async function confirmReject() {
  const id     = document.getElementById('rejectRequestId').value;
  const reason = document.getElementById('rejectReason').value;
  const d = await apiPost({ action: 'rejectBusinessRequest' }, { request_id: id, reason });
  bootstrap.Modal.getInstance(document.getElementById('rejectModal')).hide();
  toast(d.message, d.success ? 'success' : 'error');
  if (d.success) { loadBizRequests('pending'); loadDashboard(); }
}

// PINS 
async function loadPins(page = 1) {
  pinPage = page;
  const d = await apiGet({ action: 'getAllPins', page });
  const tbody = document.getElementById('pins-table');
  if (!d.data) { tbody.innerHTML = '<tr><td colspan="8" class="text-danger text-center py-3">Error loading pins</td></tr>'; return; }
  if (!d.data.length) { tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-3">No pins found</td></tr>'; return; }
  tbody.innerHTML = d.data.map(p => `
    <tr>
      <td>${p.id}</td>
      <td>${esc(p.title || 'Untitled')}</td>
      <td>${esc(p.creator_name)}</td>
      <td>${esc(p.board_name || '—')}</td>
      <td>${p.save_count}</td>
      <td>${p.comment_count}</td>
      <td><small>${fmtDate(p.created_at)}</small></td>
      <td><button class="btn btn-sm btn-outline-danger" onclick="deletePin(${p.id})">Delete</button></td>
    </tr>`).join('');
  buildPagination('pin-pagination', d.meta, p => loadPins(p));
}
async function deletePin(id) {
  if (!confirm('Delete this pin permanently?')) return;
  const d = await apiPost({ action: 'deletePin' }, { pin_id: id });
  toast(d.message, d.success ? 'success' : 'error');
  if (d.success) loadPins(pinPage);
}

//  BOARDS 
async function loadBoards() {
  const d = await apiGet({ action: 'getAllBoards' });
  const tbody = document.getElementById('boards-table');
  if (!d.data) { tbody.innerHTML = '<tr><td colspan="7" class="text-danger text-center py-3">Error loading boards</td></tr>'; return; }
  if (!d.data.length) { tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-3">No boards found</td></tr>'; return; }
  tbody.innerHTML = d.data.map(b => `
    <tr>
      <td>${b.id}</td>
      <td>${esc(b.name)}</td>
      <td>${esc(b.owner_name)}</td>
      <td>${b.pin_count}</td>
      <td><span class="badge ${b.is_public ? 'badge-active' : 'badge-suspended'}">${b.is_public ? 'Public' : 'Private'}</span></td>
      <td><small>${fmtDate(b.created_at)}</small></td>
      <td><button class="btn btn-sm btn-outline-danger" onclick="deleteBoard(${b.id})">Delete</button></td>
    </tr>`).join('');
}
async function deleteBoard(id) {
  if (!confirm('Delete this board?')) return;
  const d = await apiPost({ action: 'deleteBoard' }, { board_id: id });
  toast(d.message, d.success ? 'success' : 'error');
  if (d.success) loadBoards();
}

// CATEGORIES 
async function loadCategories() {
  const d = await apiGet({ action: 'getCategories' });
  const tbody = document.getElementById('categories-table');
  if (!d.data) { tbody.innerHTML = '<tr><td colspan="5" class="text-danger text-center py-3">Error loading categories</td></tr>'; return; }
  if (!d.data.length) { tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">No categories found</td></tr>'; return; }
  tbody.innerHTML = d.data.map(c => `
    <tr>
      <td>${c.id}</td>
      <td><strong>${esc(c.name)}</strong></td>
      <td>${esc(c.description || '—')}</td>
      <td>${c.pin_count}</td>
      <td>
        <button class="btn btn-sm btn-outline-secondary me-1" onclick="openEditCategory(${c.id},'${esc(c.name)}','${esc(c.description||'')}')">Edit</button>
        <button class="btn btn-sm btn-outline-danger" onclick="deleteCategory(${c.id})">Delete</button>
      </td>
    </tr>`).join('');
}

function openAddCategory() {
  document.getElementById('catId').value = '';
  document.getElementById('catName').value = '';
  document.getElementById('catDesc').value = '';
  document.getElementById('cat-modal-title').textContent = 'Add Category';
  new bootstrap.Modal(document.getElementById('categoryModal')).show();
}
function openEditCategory(id, name, desc) {
  document.getElementById('catId').value = id;
  document.getElementById('catName').value = name;
  document.getElementById('catDesc').value = desc;
  document.getElementById('cat-modal-title').textContent = 'Edit Category';
  new bootstrap.Modal(document.getElementById('categoryModal')).show();
}
async function saveCategory() {
  const id   = document.getElementById('catId').value;
  const name = document.getElementById('catName').value.trim();
  const desc = document.getElementById('catDesc').value.trim();
  if (!name) { toast('Category name required', 'error'); return; }
  const action = id ? 'updateCategory' : 'addCategory';
  const body   = id ? { category_id: id, name, description: desc } : { name, description: desc };
  const d = await apiPost({ action }, body);
  bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
  toast(d.message, d.success ? 'success' : 'error');
  if (d.success) loadCategories();
}
async function deleteCategory(id) {
  if (!confirm('Delete category? Pins in this category will become uncategorised.')) return;
  const d = await apiPost({ action: 'deleteCategory' }, { category_id: id });
  toast(d.message, d.success ? 'success' : 'error');
  if (d.success) loadCategories();
}

//  VIEW USER DETAIL 
async function viewUser(id) {
  // Show modal with spinner first
  const modalEl = document.getElementById('userDetailModal');
  document.getElementById('userModalBody').innerHTML = `
    <div class="text-center py-5 text-muted">
      <div class="spinner-border text-danger mb-3" role="status"></div>
      <div>Loading user details…</div>
    </div>`;
  new bootstrap.Modal(modalEl).show();

  const d = await apiGet({ action: 'getUserDetails', user_id: id });
  if (!d.success) {
    document.getElementById('userModalBody').innerHTML = `<div class="alert alert-danger">Could not load user details: ${esc(d.message)}</div>`;
    return;
  }
  const u = d.data;
  const s = u.stats || {};
  const avatar = u.profile_image
    ? `<img src="../${esc(u.profile_image)}" style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #E60023;">`
    : `<div style="width:80px;height:80px;border-radius:50%;background:#E60023;display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:800;color:#fff;">${esc(u.name.charAt(0).toUpperCase())}</div>`;

  document.getElementById('userModalBody').innerHTML = `
    <div class="text-center mb-4">
      ${avatar}
      <h5 class="mt-3 mb-0 fw-bold">${esc(u.name)}</h5>
      <div class="text-muted small">@${esc(u.username||'—')}</div>
      <div class="mt-2 d-flex justify-content-center gap-2">
        <span class="badge badge-${u.account_type} px-3 py-2">${u.account_type}</span>
        <span class="badge badge-${statusClass(u.status)} px-3 py-2">${u.status}</span>
      </div>
    </div>

    <div class="row g-2 mb-4">
      <div class="col-3 text-center p-3" style="background:#f8f8f8;border-radius:10px;">
        <div class="fw-bold fs-5">${s.pins||0}</div><div class="text-muted small">Pins</div>
      </div>
      <div class="col-3 text-center p-3" style="background:#f8f8f8;border-radius:10px;">
        <div class="fw-bold fs-5">${s.boards||0}</div><div class="text-muted small">Boards</div>
      </div>
      <div class="col-3 text-center p-3" style="background:#f8f8f8;border-radius:10px;">
        <div class="fw-bold fs-5">${s.followers||0}</div><div class="text-muted small">Followers</div>
      </div>
      <div class="col-3 text-center p-3" style="background:#f8f8f8;border-radius:10px;">
        <div class="fw-bold fs-5">${s.following||0}</div><div class="text-muted small">Following</div>
      </div>
    </div>

    <table class="table table-sm table-borderless" style="font-size:13.5px;">
      <tr><td class="fw-bold text-muted" style="width:36%">Email</td><td>${esc(u.email)}</td></tr>
      <tr><td class="fw-bold text-muted">Date of Birth</td><td>${esc(u.date_of_birth||'—')}</td></tr>
      <tr><td class="fw-bold text-muted">Gender</td><td>${esc(u.gender||'—')}</td></tr>
      <tr><td class="fw-bold text-muted">Website</td><td>${u.website ? `<a href="${esc(u.website)}" target="_blank">${esc(u.website)}</a>` : '—'}</td></tr>
      <tr><td class="fw-bold text-muted">Bio</td><td>${esc(u.bio||'—')}</td></tr>
      <tr><td class="fw-bold text-muted">Joined</td><td>${fmtDate(u.created_at)}</td></tr>
      <tr><td class="fw-bold text-muted">Admin</td><td>${u.is_admin == 1 ? '<span class="text-danger fw-bold">Yes</span>' : 'No'}</td></tr>
    </table>
  `;
}

// ── VIEW BUSINESS USER DETAIL ──────────────────────────────
async function viewBizUser(id) {
  // Show modal with spinner first
  const modalEl = document.getElementById('bizDetailModal');
  document.getElementById('bizModalBody').innerHTML = `
    <div class="text-center py-5 text-muted">
      <div class="spinner-border text-danger mb-3" role="status"></div>
      <div>Loading details…</div>
    </div>`;
  new bootstrap.Modal(modalEl).show();

  const d = await apiGet({ action: 'getUserDetails', user_id: id });
  if (!d.success) {
    document.getElementById('bizModalBody').innerHTML = `<div class="alert alert-danger">Could not load details: ${esc(d.message)}</div>`;
    return;
  }
  const u = d.data;
  const s = u.stats || {};

  const statusColors = { pending:'#f57f17', approved:'#1e7e34', rejected:'#c62828', none:'#888', suspended:'#c62828' };
  const statusColor  = statusColors[u.business_status] || '#888';

  const avatar = u.profile_image
    ? `<img src="../${esc(u.profile_image)}" style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid #E60023;">`
    : `<div style="width:72px;height:72px;border-radius:50%;background:#E60023;display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:800;color:#fff;">${esc(u.name.charAt(0).toUpperCase())}</div>`;

  document.getElementById('bizModalBody').innerHTML = `
    <div class="d-flex align-items-center gap-3 mb-4 p-3" style="background:#fafafa;border-radius:12px;">
      ${avatar}
      <div>
        <h5 class="mb-0 fw-bold">${esc(u.name)}</h5>
        <div class="text-muted small">@${esc(u.username||'—')} · ${esc(u.email)}</div>
        <div class="mt-2">
          <span class="badge" style="background:#fff3e0;color:#e65100;padding:5px 12px;">${u.account_type}</span>
          <span class="badge ms-1" style="background:${statusColor}20;color:${statusColor};padding:5px 12px;">${u.business_status}</span>
        </div>
      </div>
    </div>

    <h6 class="fw-bold text-muted mb-2" style="font-size:11px;text-transform:uppercase;letter-spacing:1px;">Account Details</h6>
    <table class="table table-sm" style="font-size:13.5px;">
      <tr style="background:#fafafa"><td class="fw-bold text-muted" style="width:38%">Full Name</td><td>${esc(u.name)}</td></tr>
      <tr><td class="fw-bold text-muted">Email</td><td>${esc(u.email)}</td></tr>
      <tr style="background:#fafafa"><td class="fw-bold text-muted">Username</td><td>@${esc(u.username||'—')}</td></tr>
      <tr><td class="fw-bold text-muted">Account Type</td><td>${esc(u.account_type)}</td></tr>
      <tr style="background:#fafafa"><td class="fw-bold text-muted">Business Status</td>
        <td><span class="fw-bold" style="color:${statusColor}">${u.business_status.toUpperCase()}</span></td>
      </tr>
      <tr><td class="fw-bold text-muted">Date of Birth</td><td>${esc(u.date_of_birth||'—')}</td></tr>
      <tr style="background:#fafafa"><td class="fw-bold text-muted">Gender</td><td>${esc(u.gender||'—')}</td></tr>
      <tr><td class="fw-bold text-muted">Website</td><td>${u.website ? `<a href="${esc(u.website)}" target="_blank">${esc(u.website)}</a>` : '—'}</td></tr>
      <tr style="background:#fafafa"><td class="fw-bold text-muted">Bio</td><td>${esc(u.bio||'—')}</td></tr>
      <tr><td class="fw-bold text-muted">Requested On</td><td>${fmtDate(u.created_at)}</td></tr>
    </table>

    <h6 class="fw-bold text-muted mt-3 mb-2" style="font-size:11px;text-transform:uppercase;letter-spacing:1px;">Activity Stats</h6>
    <div class="row g-2 mb-3">
      <div class="col-3 text-center p-3" style="background:#f8f8f8;border-radius:10px;">
        <div class="fw-bold fs-5">${s.pins||0}</div><div class="text-muted small">Pins</div>
      </div>
      <div class="col-3 text-center p-3" style="background:#f8f8f8;border-radius:10px;">
        <div class="fw-bold fs-5">${s.boards||0}</div><div class="text-muted small">Boards</div>
      </div>
      <div class="col-3 text-center p-3" style="background:#f8f8f8;border-radius:10px;">
        <div class="fw-bold fs-5">${s.followers||0}</div><div class="text-muted small">Followers</div>
      </div>
      <div class="col-3 text-center p-3" style="background:#f8f8f8;border-radius:10px;">
        <div class="fw-bold fs-5">${s.following||0}</div><div class="text-muted small">Following</div>
      </div>
    </div>

    ${u.business_status === 'pending' ? `
    <div class="d-flex gap-2 mt-2">
      <button class="btn btn-success flex-fill fw-bold" onclick="bootstrap.Modal.getInstance(document.getElementById('bizDetailModal')).hide(); quickApprove(${u.user_id})">
         Approve Business Account
      </button>
      <button class="btn btn-danger flex-fill fw-bold" onclick="bootstrap.Modal.getInstance(document.getElementById('bizDetailModal')).hide(); openReject(${u.user_id})">
         Reject
      </button>
    </div>` : ''}
  `;
}

// ── HELPERS ────────────────────────────────────────────────
function esc(str) {
  return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function fmtDate(dt) {
  return dt ? new Date(dt).toLocaleDateString('en-IN', {day:'2-digit',month:'short',year:'numeric'}) : '—';
}
function statusClass(s) {
  const map = { active:'active', suspended:'suspended', pending:'pending', approved:'approved', rejected:'rejected', none:'none' };
  return map[s] || 'none';
}
function buildPagination(containerId, meta, cb) {
  const el = document.getElementById(containerId);
  if (!el || !meta) return;
  let html = '';
  for (let i = 1; i <= meta.pages; i++) {
    html += `<button class="btn btn-sm ${i === meta.page ? 'btn-danger' : 'btn-outline-secondary'}" onclick="(${cb})(${i})">${i}</button>`;
  }
  el.innerHTML = html;
}

// ── Init ───────────────────────────────────────────────────
loadDashboard();
</script>
</body>
</html>