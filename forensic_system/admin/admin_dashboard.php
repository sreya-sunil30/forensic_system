<?php
require_once "../config.php";
session_start();


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /forensic_system/index.php"); 
    exit;
}

$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalInvestigators = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'investigator'")->fetchColumn();
$totalCases = $pdo->query("SELECT COUNT(*) FROM cases")->fetchColumn();
$totalEvidence = $pdo->query("SELECT COUNT(*) FROM evidence")->fetchColumn();
$totalAdmins = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
$totalNormalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
?>

<?php require_once "../includes/header.php"; ?>
<?php require_once "../includes/sidebar.php"; ?>

<style>
  /* Dashboard enhancement styles */
  .dashboard-header {
    color: #222;
    font-weight: 900;
    letter-spacing: 0.06em;
    margin-bottom: 2rem;
    text-shadow: 0 1px 4px rgba(0,0,0,0.1);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-size: 2.5rem;
  }
  .card-stats {
    background: #fff;
    border-radius: 16px;
    padding: 2rem 1rem 1rem 1rem;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    color: #444;
    font-weight: 700;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    overflow: hidden;
  }
  .card-stats:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 35px rgba(0,0,0,0.15);
    cursor: pointer;
  }
  .card-stats i {
    font-size: 3.5rem;
    color: #6c757d;
    filter: drop-shadow(0 1px 2px rgba(0,0,0,0.1));
    transition: color 0.3s ease;
  }
  .card-stats:hover i {
    color: #495057;
  }
  .card-stats h4 {
    margin-top: 1.5rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #495057;
    font-size: 1.1rem;
  }
  .card-stats p.display-6 {
    margin-top: 0.25rem;
    font-weight: 900;
    font-size: 3rem;
    color: #2c3e50;
    text-shadow: 0 1px 4px rgba(0,0,0,0.05);
  }
  /* Background with subtle pattern and gradient overlay */
  .dashboard-container {
    margin-left: 260px;
    padding: 3rem 3rem 4rem 3rem;
    min-height: 100vh;
    background: linear-gradient(135deg, #f0f4f8 60%, #d9e2ec 100%), url('../assets/content-bg-light.png') repeat;
    background-blend-mode: overlay;
  }
  /* Chart containers with shadow and rounded corners */
  .chart-card {
    background: #fff;
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    box-shadow: 0 10px 18px rgba(0,0,0,0.08);
  }
</style>

<div class="dashboard-container">

  <h1 class="dashboard-header" aria-label="Dashboard Overview">Dashboard Overview</h1>

  <div class="row g-4 mb-5">
    <div class="col-md-3">
      <div class="card-stats" tabindex="0" aria-label="Total Users">
        <i class="bi bi-people-fill"></i>
        <h4>Total Users</h4>
        <p class="display-6"><?= htmlspecialchars($totalUsers) ?></p>
        <svg style="position:absolute; bottom:-20px; right:-20px; opacity:0.1; width:120px; height:120px;" viewBox="0 0 64 64">
          <circle cx="32" cy="32" r="30" stroke="#3498db" stroke-width="2" fill="none" />
          <circle cx="32" cy="32" r="22" stroke="#3498db" stroke-width="1" fill="none" />
        </svg>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card-stats" tabindex="0" aria-label="Investigators">
        <i class="bi bi-person-badge-fill"></i>
        <h4>Investigators</h4>
        <p class="display-6"><?= htmlspecialchars($totalInvestigators) ?></p>
        <svg style="position:absolute; bottom:-20px; right:-20px; opacity:0.1; width:120px; height:120px;" viewBox="0 0 64 64">
          <rect x="10" y="10" width="44" height="44" stroke="#e74c3c" stroke-width="2" fill="none" />
          <rect x="18" y="18" width="28" height="28" stroke="#e74c3c" stroke-width="1" fill="none" />
        </svg>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card-stats" tabindex="0" aria-label="Total Cases">
        <i class="bi bi-folder-fill"></i>
        <h4>Total Cases</h4>
        <p class="display-6"><?= htmlspecialchars($totalCases) ?></p>
        <svg style="position:absolute; bottom:-20px; right:-20px; opacity:0.1; width:120px; height:120px;" viewBox="0 0 64 64">
          <polygon points="8,48 8,16 20,8 56,8 56,48" stroke="#27ae60" stroke-width="2" fill="none" />
          <polygon points="20,24 48,24 48,40 20,40" stroke="#27ae60" stroke-width="1" fill="none" />
        </svg>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card-stats" tabindex="0" aria-label="Total Evidence">
        <i class="bi bi-file-earmark-lock2-fill"></i>
        <h4>Total Evidence</h4>
        <p class="display-6"><?= htmlspecialchars($totalEvidence) ?></p>
        <svg style="position:absolute; bottom:-20px; right:-20px; opacity:0.1; width:120px; height:120px;" viewBox="0 0 64 64">
          <path d="M16 12h32v40H16z" stroke="#f39c12" stroke-width="2" fill="none"/>
          <circle cx="32" cy="32" r="10" stroke="#f39c12" stroke-width="1" fill="none"/>
        </svg>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-md-6">
      <div class="chart-card">
        <h5 class="mb-3" style="color:#34495E;">Cases and Evidence Overview</h5>
        <canvas id="barChart" aria-label="Bar chart showing total cases and total evidence" role="img"></canvas>
      </div>
    </div>

    <div class="col-md-6">
      <div class="chart-card">
        <h5 class="mb-3" style="color:#34495E;">User Roles Distribution</h5>
        <canvas id="pieChart" aria-label="Pie chart showing user roles distribution" role="img"></canvas>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const barChart = document.getElementById('barChart').getContext('2d');
const barGradient = barChart.createLinearGradient(0, 0, 0, 400);
barGradient.addColorStop(0, '#461220');
barGradient.addColorStop(1, '#2980B9');
const barGradient2 = barChart.createLinearGradient(0, 0, 0, 400);
barGradient2.addColorStop(0, '#8c2f39');
barGradient2.addColorStop(1, '#1e8449');
new Chart(barChart, {
    type: 'bar',
    data: {
        labels: ['Cases', 'Evidence'],
        datasets: [{
            label: 'Total',
            data: [<?= $totalCases ?>, <?= $totalEvidence ?>],
            backgroundColor: [barGradient, barGradient2],
            borderRadius: 8,
            barPercentage: 0.6,
            categoryPercentage: 0.5,
            hoverOffset: 10
        }]
    },
    options: {
        responsive: true,
        plugins: { 
            legend: { display: false },
            tooltip: {
                backgroundColor: '#34495E',
                titleFont: { size: 16 },
                bodyFont: { size: 14 }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { color: '#34495E', stepSize: 1, font: { size: 14 } },
                grid: { color: '#ecf0f1', drawBorder: false }
            },
            x: {
                ticks: { color: '#34495E', font: { size: 14 } },
                grid: { color: '#ecf0f1', drawBorder: false }
            }
        },
        animation: {
            duration: 1200,
            easing: 'easeOutQuart'
        }
    }
});

const pieChart = document.getElementById('pieChart').getContext('2d');
const pieColors = [
  '#5c4d7d',
  '#a01a58',
  '#985277'
];
new Chart(pieChart, {
    type: 'pie',
    data: {
        labels: ['Admins', 'Investigators', 'Users'],
        datasets: [{
            data: [<?= $totalAdmins ?>, <?= $totalInvestigators ?>, <?= $totalNormalUsers ?>],
            backgroundColor: pieColors,
            borderColor: '#fff',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { 
              position: 'bottom', 
              labels: { color: '#34495E', font: { size: 14, weight: '600' } } 
            },
            tooltip: {
                backgroundColor: '#34495E',
                titleFont: { size: 16 },
                bodyFont: { size: 14 }
            }
        },
        animation: {
            duration: 1500,
            easing: 'easeOutQuart'
        }
    }
});
</script>

<?php require_once "../includes/footer.php"; ?>

