<?php
require_once "config.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'investigator') {
    header("Location: /forensic_system/index.php"); 
    exit;
}

$investigator_id = $_SESSION['user_id'];

// Fetch assigned cases with deadlines
$stmt = $pdo->prepare("SELECT custom_case_id, title, deadline FROM cases WHERE assigned_to = ? AND deadline IS NOT NULL");
$stmt->execute([$investigator_id]);
$cases = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch investigator notes
$stmt = $pdo->prepare("SELECT cn.id, cn.note, cn.confidential, cn.created_at, c.custom_case_id, c.title 
    FROM calendar_notes cn 
    JOIN cases c ON cn.custom_case_id = c.custom_case_id 
    WHERE cn.investigator_id = ?");
$stmt->execute([$investigator_id]);
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Investigator Calendar</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/index.global.min.css" rel="stylesheet">
  <style>
    body {
      padding-left: 270px;
      background-color: #f4f4f4;
    }
    .calendar-container {
      max-width: 900px;
      margin: 20px auto;
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    }
    .modal textarea {
      resize: vertical;
    }
  </style>
</head>
<body>
<?php include 'includes/header.php'; ?>    
<?php include 'includes/sidebar.php'; ?>

<div class="calendar-container">
  <div id="calendar"></div>
</div>



<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      height: 'auto',
      dateClick: function (info) {
        document.getElementById('noteDate').value = info.dateStr;
        const modal = new bootstrap.Modal(document.getElementById('addNoteModal'));
        modal.show();
      },
      events: [
        <?php foreach ($cases as $case):
          $deadline = $case['deadline'];
          $today = date('Y-m-d');
          $color = ($deadline < $today) ? '#dc3545' : (($deadline == $today) ? '#ffc107' : '#0d6efd');
        ?>
          {
            title: <?= json_encode("Deadline: " . $case['custom_case_id']) ?>,
            start: <?= json_encode($deadline) ?>,
            allDay: true,
            color: '<?= $color ?>'
          },
        <?php endforeach; ?>
        <?php foreach ($notes as $note): ?>
          {
            title: <?= json_encode("Note: " . $note['custom_case_id']) ?>,
            start: <?= json_encode(date('Y-m-d', strtotime($note['created_at']))) ?>,
            allDay: true,
            color: '#198754' // green for notes
          },
        <?php endforeach; ?>
      ]
    });
    calendar.render();
  });
</script>
</body>
</html>
