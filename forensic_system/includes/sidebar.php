<?php
        include_once 'C:\sreya\xampp\htdocs\forensic_system\config.php';

$role = $_SESSION['role'] ?? 'guest';
$currentPage = basename($_SERVER['PHP_SELF']);
$showToggleBar = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;
?>

<style>
.sidebar {
  position: fixed;
  top: 60px;
  left: 0;
  width: 260px;
  height: calc(100vh - 60px);
  background: #014f86  url('../assets/sidebar-bg.png') no-repeat center center / cover;
  padding: 1.5rem 1rem;
  overflow-y: auto;
  box-shadow: 3px 0 10px #2a6f97(0, 0, 0, 0.6);
  display: flex;
  flex-direction: column;
  z-index: 1020;
}

.sidebar .nav-button {
  display: flex;
  align-items: center;
  padding: 12px 16px;
  margin-bottom: 10px;
  border-radius: 12px;
  background-color: #2a6f97;
  color: #ccc;
  font-weight: 500;
  transition: 0.3s ease;
  cursor: pointer;
  text-decoration: none;
}

.sidebar .nav-button:hover {
  background-color: #013a63(158, 15, 22);
  color: #fff;
  transform: translateX(4px);
}

.sidebar .nav-button.active {
  background-color: #012a4a;
  color: #fff;
  box-shadow: 0 0 10px #1d3557;
}

.sidebar .nav-button i {
  margin-right: 12px;
  font-size: 1.1rem;
}

/* Logout button */
.sidebar .logout-btn {
  margin-top: auto;
  padding: 12px 16px;
  display: flex;
  align-items: center;
  color: #f88;
  font-weight: 600;
  border-top: 1px solid #444;
  background: none;
   border-radius: 12px;
}
.sidebar .logout-btn:hover {
  color: #fff;
  background-color:rgb(173, 16, 16);
}
</style>

<div class="sidebar">

  <?php if ($role === 'admin'): ?>
    <a href="/forensic_system/admin/admin_dashboard.php" class="nav-button <?= $currentPage === 'admin_dashboard.php' ? 'active' : '' ?>">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <a href="/forensic_system/admin/manage_users.php" class="nav-button <?= $currentPage === 'manage_users.php' ? 'active' : '' ?>">
        <i class="bi bi-people-fill"></i> Manage Users
    </a>

    <a href="/forensic_system/admin/manage_cases.php" class="nav-button <?= $currentPage === 'manage_cases.php' ? 'active' : '' ?>">
        <i class="bi bi-folder-fill"></i> Manage Cases
    </a>

    <a href="/forensic_system/admin/manage_evidence.php" class="nav-button <?= $currentPage === 'manage_evidence.php' ? 'active' : '' ?>">
        <i class="bi bi-file-earmark-lock2-fill"></i> Manage Evidence
    </a>

    <a href="/forensic_system/admin/new_complaints.php" class="nav-button <?= $currentPage === 'new_complaints.php' ? 'active' : '' ?>">
        <i class="bi bi-envelope-fill"></i> New Complaints
    </a>

    <a href="/forensic_system/tools/ip_tracker.php" class="nav-button <?= $currentPage === 'ip_tracker.php' ? 'active' : '' ?>">
        <i class="bi bi-geo-alt"></i> IP Tracker
    </a>

    <a href="/forensic_system/admin/reports.php" class="nav-button <?= $currentPage === 'reports.php' ? 'active' : '' ?>">
        <i class="bi bi-file-earmark-text-fill me-2"></i> Reports
    </a>

    <a href="/forensic_system/admin/admin_view_reports.php" class="nav-button <?= $currentPage === 'admin_view_reports.php' ? 'active' : '' ?>">
        <i class="bi bi-journal-text"></i> Investigator Daily Reports
    </a>

<?php elseif ($role === 'investigator'): ?>
    <a href="/forensic_system/investigator_dashboard.php" class="nav-button <?= $currentPage === 'investigator_dashboard.php' ? 'active' : '' ?>">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <a href="/forensic_system/assigned_cases.php" class="nav-button <?= $currentPage === 'assigned_cases.php' ? 'active' : '' ?>">
        <i class="bi bi-folder-fill"></i> Assigned Cases
    </a>

    <a href="/forensic_system/submit_report.php" class="nav-button <?= $currentPage === 'submit_report.php' ? 'active' : '' ?>">
        <i class="bi bi-file-earmark-text-fill"></i> Submit Report
    </a>

    <a href="/forensic_system/reports_history.php" class="nav-button <?= $currentPage === 'reports_history.php' ? 'active' : '' ?>">
        <i class="bi bi-file-earmark-text-fill"></i> Reports History
    </a>

    <a href="/forensic_system/submit_daily_report.php" class="nav-button <?= $currentPage === 'submit_daily_report.php' ? 'active' : '' ?>">
        <i class="bi bi-journal-plus"></i> Submit Daily Report
    </a>

    <a href="/forensic_system/profile.php" class="nav-button <?= $currentPage === 'profile.php' ? 'active' : '' ?>">
        <i class="bi bi-person-circle"></i> Profile
    </a>

    <a href="/forensic_system/calendar.php" class="nav-button <?= $currentPage === 'calendar.php' ? 'active' : '' ?>">
        <i class="bi bi-calendar3"></i> Calendar
    </a>

    <a href="/forensic_system/manage_note.php" class="nav-button <?= $currentPage === 'manage_note.php' ? 'active' : '' ?>">
        <i class="bi bi-journal-text"></i> Forensics Notes
    </a>

    <a href="/forensic_system/tools/ip_tracker.php" class="nav-button <?= $currentPage === 'ip_tracker.php' ? 'active' : '' ?>">
        <i class="bi bi-geo-alt"></i> IP Tracker
    </a>



  <?php elseif ($role === 'user'): ?>

    <a href="/forensic_system/front_user.php" class="nav-button <?= $currentPage === 'front_user.php' ? 'active' : '' ?>">
      <i class="bi bi-house-door-fill"></i> Home
    </a>
    <a href="/forensic_system/user_dashboard.php" class="nav-button <?= $currentPage === 'user_dashboard.php' ? 'active' : '' ?>">
      <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="/forensic_system/submit_complaint.php" class="nav-button <?= $currentPage === 'submit_complaint.php' ? 'active' : '' ?>">
      <i class="bi bi-exclamation-circle-fill"></i> Submit Complaint
    </a>
    <a href="/forensic_system/profile_info.php" class="nav-button <?= $currentPage === 'profile_info.php' ? 'active' : '' ?>">
      <i class="bi bi-person-circle"></i> Profile
    </a>
    <a href="/forensic_system/case_conclusion.php" class="nav-button <?= $currentPage === 'case_conclusion.php' ? 'active' : '' ?>">
      <i class="bi bi-journal-check"></i> Case Conclusion
    </a>
    

    <?php if ($showToggleBar): ?>
        <div id="toggleBar">
            <p>Welcome, logged-in user!</p>
        </div>
    <?php endif; ?>

  <?php else: ?>
    <p class="text-light">No menu available.</p>
  <?php endif; ?>
<a href="/forensic_system/logout.php" class="logout-btn">

    <i class="bi bi-box-arrow-right me-2"></i> Logout
  </a>
</div>
</div>
