<?php
require_once "config.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'investigator') {
    header("Location: /forensic_system/index.php"); 
    exit;
}

$investigator_id = $_SESSION['user_id'];
$message = "";

// Fetch all cases assigned to this investigator (for dropdown)
$stmt = $pdo->prepare("SELECT custom_case_id, title FROM cases WHERE assigned_to = ?");
$stmt->execute([$investigator_id]);
$cases = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle Add / Update / Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note = trim($_POST['note'] ?? '');
    $case_id = $_POST['case_id'] ?? null; // This is custom_case_id
    $confidential = isset($_POST['confidential']) ? 1 : 0;

    if (isset($_POST['save_note'])) {
        if (!$note || !$case_id) {
            $message = "Please fill all required fields.";
        } else {
            if (!empty($_POST['note_id'])) {
                // Update note
                $note_id = intval($_POST['note_id']);
                $stmt = $pdo->prepare("UPDATE calendar_notes SET note = ?, confidential = ?, custom_case_id = ?, updated_at = NOW() WHERE id = ? AND investigator_id = ?");
                $stmt->execute([$note, $confidential, $case_id, $note_id, $investigator_id]);
                $message = "Note updated successfully.";
            } else {
                // Insert note
                $stmt = $pdo->prepare("INSERT INTO calendar_notes (investigator_id, custom_case_id, note, confidential, created_at) VALUES (?, ?, ?, ?, NOW())");
                $stmt->execute([$investigator_id, $case_id, $note, $confidential]);
                $message = "Note added successfully.";
            }
        }
    }

    if (isset($_POST['delete_note'])) {
        $note_id = intval($_POST['note_id']);
        $stmt = $pdo->prepare("DELETE FROM calendar_notes WHERE id = ? AND investigator_id = ?");
        $stmt->execute([$note_id, $investigator_id]);
        $message = "Note deleted successfully.";
    }
}

// Editing note?
$edit_note = null;
if (isset($_GET['edit'])) {
    $note_id = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM calendar_notes WHERE id = ? AND investigator_id = ?");
    $stmt->execute([$note_id, $investigator_id]);
    $edit_note = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all notes
$stmt = $pdo->prepare("SELECT cn.*, c.title FROM calendar_notes cn JOIN cases c ON cn.custom_case_id = c.custom_case_id WHERE cn.investigator_id = ? ORDER BY cn.created_at DESC");
$stmt->execute([$investigator_id]);
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Notes | Investigator</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      padding-left: 270px;
      background: #f4f4f4;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .container {
      max-width: 900px;
      margin-top: 30px;
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgb(0 0 0 / 0.1);
    }
  </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="container">
  <h3><?= $edit_note ? 'Edit Note' : 'Add New Note' ?></h3>
  <?php if ($message): ?>
    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <form method="post" class="mb-4">
    <input type="hidden" name="note_id" value="<?= $edit_note['id'] ?? '' ?>">
    <div class="mb-3">
      <label for="case_id" class="form-label">Select Case <span class="text-danger">*</span></label>
      <select id="case_id" name="case_id" class="form-select" required>
        <option value="">-- Select Case --</option>
        <?php foreach ($cases as $case): ?>
          <option value="<?= $case['custom_case_id'] ?>" <?= (isset($edit_note['custom_case_id']) && $edit_note['custom_case_id'] == $case['custom_case_id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($case['custom_case_id'] . " - " . $case['title']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="mb-3">
      <label for="note" class="form-label">Note <span class="text-danger">*</span></label>
      <textarea id="note" name="note" rows="4" class="form-control" required><?= htmlspecialchars($edit_note['note'] ?? '') ?></textarea>
    </div>

    <div class="form-check mb-3">
      <input type="checkbox" id="confidential" name="confidential" class="form-check-input" <?= (!empty($edit_note) && $edit_note['confidential']) ? 'checked' : '' ?>>
      <label class="form-check-label" for="confidential">Confidential Note</label>
    </div>

    <button type="submit" style="background-color: #2a6f97; " name="save_note" class="btn btn-primary"><?= $edit_note ? 'Update Note' : 'Add Note' ?></button>
    <?php if ($edit_note): ?>
      <button type="submit" name="delete_note" value="1" class="btn btn-danger ms-2" onclick="return confirm('Delete this note?');">Delete Note</button>
      <a href="manage_note.php" class="btn btn-secondary ms-2">Cancel</a>
    <?php endif; ?>
  </form>

  <h4>Your Notes</h4>
  <?php if (!$notes): ?>
    <p>No notes added yet.</p>
  <?php else: ?>
    <ul class="list-group">
      <?php foreach ($notes as $n): ?>
        <li class="list-group-item">
          <strong><?= htmlspecialchars($n['custom_case_id']) ?> - <?= htmlspecialchars($n['title']) ?></strong><br />
          <small class="text-muted"><?= date('d M Y, H:i', strtotime($n['created_at'])) ?> <?= $n['confidential'] ? '(Confidential)' : '' ?></small><br />
          <?= nl2br(htmlspecialchars($n['note'])) ?><br />
          <a href="?edit=<?= $n['id'] ?>">Edit</a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
