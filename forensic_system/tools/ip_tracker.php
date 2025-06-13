<?php
require_once "../config.php";
session_start();

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'investigator'])) {
    header("Location: /forensic_system/index.php"); 
    exit;
}

require_once "../includes/header.php";
require_once "../includes/sidebar.php";

$ipList = [];
$ipDataList = [];
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["ips"])) {
    $ipInput = trim($_POST["ips"]);
    $ipList = array_filter(array_map('trim', explode(',', $ipInput)));

    foreach ($ipList as $ip) {
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            $url = "https://ipinfo.io/{$ip}/json";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response !== false) {
                $ipData = json_decode($response, true);
                if (!empty($ipData['loc'])) {
                    $ipDataList[] = $ipData;
                } else {
                    $error = "No location data for IP: {$ip}";
                    break;
                }
            } else {
                $error = "Could not fetch details for IP: {$ip}";
                break;
            }
        } else {
            $error = "Invalid IP address format: {$ip}";
            break;
        }
    }
}
?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
.main-container {
  margin-left: 260px; 
  padding: 20px 30px 30px 30px;
  height: calc(100vh - 60px); 
  overflow-y: auto; 
  box-sizing: border-box;
  background: #fff;
  border-radius: 0.375rem;
  box-shadow: 0 0.15rem 1.75rem rgba(58,59,69,.15);
  max-width: calc(100vw - 280px); 
}

.card {
  max-width: 900px;    
  margin: 0 auto 20px; 
  border-radius: 0.375rem;
  box-shadow: 0 0.15rem 1.75rem rgba(58,59,69,.15);
  box-sizing: border-box;
  opacity: 0;
  animation: fadeInUp 0.6s ease forwards;
  animation-delay: 0.2s;
  transition: box-shadow 0.3s ease, transform 0.3s ease;
}

#map {
  height: 350px;
  width: 100%;
  max-width: 900px; 
  margin: 20px auto 0; 
  border-radius: 0.375rem;
  box-shadow: 0 0.15rem 1.75rem rgba(58,59,69,.15);
  animation-name: fadeInUpZoom;
  box-sizing: border-box;
}

body, html {
  overflow-x: hidden; 
}

h2 {
  font-weight: 700;
  color: #014f86;
  margin-bottom: 1rem;
}

@keyframes fadeInUp {
  0% { opacity: 0; transform: translateY(20px); }
  100% { opacity: 1; transform: translateY(0); }
}

@keyframes fadeInUpZoom {
  0% { opacity: 0; transform: translateY(20px) scale(0.95); }
  100% { opacity: 1; transform: translateY(0) scale(1); }
}

.card:hover {
  box-shadow: 0 0.5rem 1.5rem rgba(78, 115, 223, 0.3);
  transform: translateY(-5px);
}

.input-group .btn-primary {
  background-color: #014f86;
  border-color: #014f86;
  font-weight: 600;
}

.form-control:focus {
  border-color: #2a6f97;
  box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}
</style>

<div class="container main-container">
    <h2 class="mb-4">IP Tracker Tool</h2>

    <form method="post" class="mb-4">
        <div class="input-group">
            <input
              type="text"
              name="ips"
              class="form-control"
              placeholder="Enter IP addresses separated by commas (e.g., 8.8.8.8,1.1.1.1)"
              required
              value="<?= htmlspecialchars(implode(',', $ipList)) ?>"
            >
            <button class="btn btn-primary" type="submit">Track IPs</button>
        </div>
    </form>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($ipDataList): ?>
        <?php foreach ($ipDataList as $ipData): ?>
            <div class="card shadow-sm mb-3">
                <div class="card-header text-white" style="background-color: #2a6f97;">                    
                    IP Info for <?= htmlspecialchars($ipData['ip'] ?? 'N/A') ?>
                </div>
                <div class="card-body">
                    <p><strong>City:</strong> <?= htmlspecialchars($ipData['city'] ?? 'N/A') ?></p>
                    <p><strong>Region:</strong> <?= htmlspecialchars($ipData['region'] ?? 'N/A') ?></p>
                    <p><strong>Country:</strong> <?= htmlspecialchars($ipData['country'] ?? 'N/A') ?></p>
                    <p><strong>Location (Lat, Long):</strong> <?= htmlspecialchars($ipData['loc'] ?? 'N/A') ?></p>
                    <p><strong>Postal Code:</strong> <?= htmlspecialchars($ipData['postal'] ?? 'N/A') ?></p>
                    <p><strong>Timezone:</strong> <?= htmlspecialchars($ipData['timezone'] ?? 'N/A') ?></p>
                    <p><strong>Organization:</strong> <?= htmlspecialchars($ipData['org'] ?? 'N/A') ?></p>
                </div>
            </div>
        <?php endforeach; ?>

        <div id="map"></div>
    <?php endif; ?>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<?php if ($ipDataList): ?>
<script>
    var firstLoc = <?= json_encode(explode(',', $ipDataList[0]['loc'])) ?>;
    var map = L.map('map').setView([parseFloat(firstLoc[0]), parseFloat(firstLoc[1])], 4);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var customIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
        iconSize: [35, 45],
        iconAnchor: [17, 45],
        popupAnchor: [0, -40]
    });

    var ipDataList = <?= json_encode($ipDataList) ?>;

    ipDataList.forEach(function(ipData) {
        var coords = ipData.loc.split(',');
        var lat = parseFloat(coords[0]);
        var lng = parseFloat(coords[1]);

        L.marker([lat, lng], {icon: customIcon}).addTo(map)
          .bindPopup(
            "<b>IP:</b> " + ipData.ip + "<br>" +
            "<b>City:</b> " + ipData.city + "<br>" +
            "<b>Region:</b> " + ipData.region + "<br>" +
            "<b>Country:</b> " + ipData.country + "<br>" +
            "<b>Organization:</b> " + ipData.org
          );
    });
</script>
<?php endif; ?>

<?php require_once "../includes/footer.php"; ?>
