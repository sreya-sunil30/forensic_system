<?php
session_start();
$loggedIn = isset($_SESSION['user_id']) && $_SESSION['role'] === 'user';
$name = $_SESSION['name'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forensic Portal | USER</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

  <style>
    :root {
      --primary-color: #014f86;
      --secondary-color: #2a6f97;
      --highlight: #cce3f2;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f7fa url('https://www.transparenttextures.com/patterns/squairy-light.png');
      background-size: 120px;
    }

    .navbar {
      background-color: var(--primary-color);
    }

    .navbar-brand, .nav-link, .navbar-text {
      color: #fff !important;
    }

    .hero {
      background: linear-gradient(to right, rgba(1, 79, 134, 0.9), rgba(42, 111, 151, 0.9)), 
                  url('https://images.unsplash.com/photo-1535223289827-42f1e9919769?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80') no-repeat center center;
      background-size: cover;
      color: white;
      padding: 120px 20px;
      text-align: center;
    }

    .hero h1 {
      animation: floatText 3s ease-in-out infinite;
      font-weight: bold;
      font-size: 3rem;
    }

    @keyframes floatText {
      0% { transform: translateY(0); }
      50% { transform: translateY(-6px); }
      100% { transform: translateY(0); }
    }

    .section-title {
      color: var(--primary-color);
      font-weight: 600;
      margin-bottom: 20px;
    }

    .toggle-menu {
      width: 250px;
      height: 100vh;
      background-color: var(--secondary-color);
      position: fixed;
      top: 0;
      left: 0;
      transform: translateX(-260px);
      transition: transform 0.3s ease;
      z-index: 2000;
    }

    .toggle-menu.open {
      transform: translateX(0);
    }

    .toggle-menu ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .toggle-menu a {
      display: block;
      padding: 15px 20px;
      color: #fff;
      text-decoration: none;
      transition: background 0.3s ease;
    }

    .toggle-menu a:hover {
      background: rgba(255, 255, 255, 0.1);
    }

    .toggle-button {
      position: fixed;
      top: 20px;
      left: 20px;
      background: var(--secondary-color);
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 4px;
      z-index: 2100;
      display: none; 
    }

    .features .bi {
      color: var(--primary-color);
      transition: transform 0.3s;
    }

    .features .col-md-4:hover .bi {
      transform: scale(1.2);
    }

    .testimonial-card {
      transition: all 0.3s ease-in-out;
      border: none;
      border-radius: 12px;
      background: white;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
    }

    .testimonial-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
    }

    footer {
      background: var(--primary-color);
      color: white;
      text-align: center;
      padding: 20px 0;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <span class="navbar-brand fw-bold">Forensic Portal</span>
    <div class="ms-auto d-flex align-items-center">
      <?php if ($loggedIn): ?>
        <span class="navbar-text me-3 d-none d-md-inline">Welcome, <?php echo htmlspecialchars($name); ?></span>
        <a href="logout.php" class="btn btn-outline-light btn-sm">
          <i class="bi bi-box-arrow-right me-1"></i> Logout
        </a>
      <?php else: ?>
        <a href="index.php" class="btn btn-light btn-sm">
          <i class="bi bi-box-arrow-in-right me-1"></i> Login
        </a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- Toggle Menu - Only shown when logged in -->
<?php if ($loggedIn): ?>
<button class="toggle-button" id="sidebarToggle" style="display: block;">
  <i class="bi bi-list"></i>
</button>

<div class="toggle-menu" id="sidebar">
  <ul><br><br><br>
    <li><a href="user_dashboard.php"><i class="bi bi-house-door"></i> Dashboard</a></li>
    <li><a href="submit_complaint.php"><i class="bi bi-pencil-square"></i> Submit Complaint</a></li>
    <li><a href="profile_info.php"><i class="bi bi-person"></i> Profile</a></li>
  </ul>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    
    toggleBtn.addEventListener('click', function() {
      sidebar.classList.toggle('open');
    });
  });
</script>
<?php endif; ?>


<!-- Hero Section -->
<section class="hero d-flex align-items-center justify-content-center text-white position-relative" style="min-height: 90vh; background: linear-gradient(135deg, #014f86, #2a6f97); overflow: hidden;">
  
  <!-- Subtle Wave Overlay -->
  <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('https://www.transparenttextures.com/patterns/graphy.png'); opacity: 0.05; z-index: 1;"></div>
  
  <!-- Hero Content -->
  <div class="container position-relative text-center px-4 py-5" style="z-index: 2;">
    <div class="bg-white bg-opacity-10 p-5 rounded-4 shadow-lg border border-white border-opacity-25" style="backdrop-filter: blur(10px); animation: fadeUp 1.2s ease-out;">
      <h1 class="display-4 fw-bold text-white mb-3 animate-title">Welcome to the Forensic Digital Investigation System</h1>
      <p class="lead text-light">Secure. Transparent. Efficient. Empowering citizens and investigators.</p>
    </div>
  </div>
</section>

<!-- CSS Animations -->
<style>
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes shimmer {
  0% { background-position: -500px 0; }
  100% { background-position: 500px 0; }
}
.animate-title {
  background: linear-gradient(90deg, #ffffff, #d0e9ff, #ffffff);
  background-size: 200% auto;
  color: transparent;
  background-clip: text;
  -webkit-background-clip: text;
  animation: shimmer 4s infinite linear;
}
</style>


<!-- About Section -->
<section class="py-5 bg-light">
  <div class="container text-center" data-aos="fade-up">
    <h2 class="section-title">About Us</h2>
    <p class="lead">Our platform offers a secure digital gateway for reporting and tracking cybercrime complaints. We prioritize data integrity, transparency, and efficient handling of digital evidence by authorized investigators.</p>
  </div>
</section>

<!-- Features -->
<section class="py-5 features">
  <div class="container">
    <h2 class="section-title text-center mb-4" data-aos="fade-up">Core Features</h2>
    <div class="row text-center">
      <div class="col-md-4 mb-4" data-aos="zoom-in-up">
        <i class="bi bi-file-earmark-text fs-1"></i>
        <h5 class="mt-3">Digital Complaint Filing</h5>
        <p>Securely file digital crime complaints and upload attachments with traceability.</p>
      </div>
      <div class="col-md-4 mb-4" data-aos="zoom-in-up">
        <i class="bi bi-shield-check fs-1"></i>
        <h5 class="mt-3">Secure Case Management</h5>
        <p>Role-based access, evidence hashing, and real-time tracking ensure integrity.</p>
      </div>
      <div class="col-md-4 mb-4" data-aos="zoom-in-up">
        <i class="bi bi-bell fs-1"></i>
        <h5 class="mt-3">Live Case Updates</h5>
        <p>View updates, investigator notes, and admin actions as they happen.</p>
      </div>
    </div>
  </div>
</section>

<!-- Testimonials -->
<section class="py-5 bg-light">
  <div class="container" data-aos="fade-up">
    <h2 class="section-title text-center mb-4">What Users Say</h2>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="p-4 testimonial-card">
          <p>“Very easy to use and responsive. Got my complaint addressed quickly.”</p>
          <h6 class="mt-3 mb-0">– Ramesh</h6>
          <small>User from Chennai</small>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-4 testimonial-card">
          <p>“I love how secure and professional this system is. Well done team!”</p>
          <h6 class="mt-3 mb-0">– Sneha</h6>
          <small>User from Pune</small>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-4 testimonial-card">
          <p>“Great initiative by authorities to make complaint filing so smooth.”</p>
          <h6 class="mt-3 mb-0">– Aarav</h6>
          <small>User from Delhi</small>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- FAQ Section Styled Like Core Features -->
<section class="py-5" style="background-color: #f4f9fb;">
  <div class="container">
    <h2 class="section-title text-center mb-4" style="color: #2a6f97;" data-aos="fade-up">Frequently Asked Questions</h2>
    <div class="row text-center">
      
      <div class="col-md-4 mb-4" data-aos="zoom-in-up">
        <i class="bi bi-pencil-square fs-1" style="color: #2a6f97;"></i>
        <h5 class="mt-3" style="color: #2a6f97;">How do I file a complaint?</h5>
        <p>Login to your account, go to <strong>Submit Complaint</strong>, fill the form, and upload any evidence.</p>
      </div>

      <div class="col-md-4 mb-4" data-aos="zoom-in-up">
        <i class="bi bi-lock-shield fs-1" style="color: #2a6f97;"></i>
        <h5 class="mt-3" style="color: #2a6f97;">Is my data secure?</h5>
        <p>Yes. We use encryption, hashing, and secure file storage to protect your personal information.</p>
      </div>

      <div class="col-md-4 mb-4" data-aos="zoom-in-up">
        <i class="bi bi-person-lock fs-1" style="color: #2a6f97;"></i>
        <h5 class="mt-3" style="color: #2a6f97;">Who can view my complaint?</h5>
        <p>Only authorized investigators and admin staff assigned to your case can access your complaint.</p>
      </div>

    </div>
  </div>
</section>


<!-- Footer -->
<footer>
  <div class="container">
    <p class="mb-1">© <?php echo date("Y"); ?> Forensic Digital Investigation System</p>
    <small>Empowering secure and efficient cybercrime resolution.</small>
  </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>AOS.init({ duration: 1000 });</script>
</body>
</html>
