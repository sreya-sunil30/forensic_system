<?php
require_once "../config.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /forensic_system/index.php"); 
    exit;
}

$currentAdminId = $_SESSION['user_id'];
$message = "";

// Handle role update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id'], $_POST['new_role'])) {
        $userId = intval($_POST['user_id']);
        $newRole = $_POST['new_role'];

        $validRoles = ['admin', 'user', 'investigator'];
        if (!in_array($newRole, $validRoles)) {
            $message = "Invalid role selected.";
        } else {
            $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $currentRole = $user['role'];

                if ($userId === $currentAdminId) {
                    $message = "You cannot change your own role.";
                } elseif ($currentRole === 'admin' && $newRole !== 'admin') {
                    $message = "You cannot demote another admin.";
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
                    $stmt->execute([$newRole, $userId]);
                    $message = "Role updated successfully.";
                }
            }
        }
    }

    // Handle status update
    if (isset($_POST['user_id'], $_POST['new_status'])) {
        $userId = intval($_POST['user_id']);
        $newStatus = $_POST['new_status'];

        $validStatuses = ['active', 'inactive'];
        if (!in_array($newStatus, $validStatuses)) {
            $message = "Invalid status selected.";
        } else {
            $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
                $stmt->execute([$newStatus, $userId]);
                $message = "Status updated successfully.";
            } else {
                $message = "User not found.";
            }
        }
    }
}

// Handle deletion
if (isset($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);

    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$deleteId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($deleteId === $currentAdminId) {
            $message = "You cannot delete your own account.";
        } elseif ($user['role'] === 'admin') {
            $message = "You cannot delete another admin.";
        } else {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$deleteId]);
            $message = "User deleted successfully.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Users | Forensic Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: url('https://www.transparenttextures.com/patterns/white-diamond.png') repeat, #f4f4f4;
            color: #343a40;
        }

        .content {
            padding: 2rem;
            margin-left: 250px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-top: 20px;
        }

        h2 {
            font-weight: 600;
            color: #212529;
        }

        .alert-info {
            background-color: #e0f7fa;
            border-left: 5px solid #00acc1;
            color: #006064;
        }

        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgb(0 0 0 / 0.1);
            background: white;
        }

        table.table {
            border-collapse: separate !important;
            border-spacing: 0 12px !important;
            width: 100%;
            font-size: 0.95rem;
        }

        table.table thead tr {
            background: linear-gradient(90deg, #4e9af1, #56ccf2);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 3px 6px rgb(0 0 0 / 0.15);
        }

        table.table thead tr th {
            border: none;
            padding: 14px 20px;
            text-align: center;
        }

        table.table tbody tr {
            background: #fefefe;
            box-shadow: 0 3px 10px rgb(0 0 0 / 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        table.table tbody tr:hover {
            background: #e7f1ff;
            box-shadow: 0 8px 24px rgb(78 154 241 / 0.25);
            transform: translateY(-3px);
        }

        table.table tbody td {
            padding: 14px 20px;
            text-align: center;
            vertical-align: middle;
            border-top: none;
            border-bottom: none;
            color: #333;
        }

        .btn-update {
            background-color: #1f78d1;
            border: none;
            color: #fff;
            padding: 0.45rem 1.1rem;
            font-size: 0.95rem;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(31, 120, 209, 0.2);
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-update:hover {
            background-color: #155d9c;
            transform: translateY(-1px);
        }

        .btn-danger {
            background-color: #e53935;
            color: #fff;
            border: none;
            padding: 0.45rem 1.1rem;
            font-size: 0.95rem;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(229, 57, 53, 0.2);
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-danger:hover {
            background-color: #b71c1c;
            transform: translateY(-1px);
        }

        select.form-select {
            min-width: 110px;
            font-size: 0.9rem;
            border-radius: 8px;
            border: 1.5px solid #ced4da;
            transition: border-color 0.3s ease;
        }

        select.form-select:focus {
            border-color: #4e9af1;
            box-shadow: 0 0 5px #4e9af1aa;
            outline: none;
        }

        .btn-sm i {
            margin-right: 6px;
            font-size: 0.9rem;
        }

        .text-muted {
            font-style: italic;
            color: #6c757d !important;
        }

        @media (max-width: 768px) {
            .content {
                margin-left: 0;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="content">
    <h2 class="mb-4">Manage Users</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th>S No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Role</th>
                    <th>Change Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $stmt = $pdo->query("SELECT * FROM users");
            $count = 1;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . $count++ . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";

                $status = $row['status'] ?? 'active';
                echo "<td>
                    <form method='post' class='d-flex align-items-center gap-2 justify-content-center'>
                        <input type='hidden' name='user_id' value='" . $row['id'] . "'>
                        <select name='new_status' class='form-select form-select-sm'>
                            <option value='active'" . ($status === 'active' ? ' selected' : '') . ">Active</option>
                            <option value='inactive'" . ($status === 'inactive' ? ' selected' : '') . ">Inactive</option>
                        </select>
                        <button type='submit' style='background-color: #2a6f97; color: #fff;' class='btn btn-sm btn-update' title='Update Status'><i class='bi bi-check-circle'></i> Update</button>
                    </form>
                </td>";

                echo "<td>" . htmlspecialchars($row['role']) . "</td>";

                $canChange = true;
                $canDelete = true;
                $userId = $row['id'];
                $userRole = $row['role'];

                if ($userId == $currentAdminId || $userRole === 'admin') {
                    $canChange = false;
                    $canDelete = false;
                }

                echo "<td>";
                if ($canChange) {
                    echo "<form method='post' class='d-flex justify-content-center'>
                            <input type='hidden' name='user_id' value='" . $userId . "'>
                            <select name='new_role' class='form-select me-2'>
                                <option value='user'" . ($userRole === 'user' ? ' selected' : '') . ">User</option>
                                <option value='investigator'" . ($userRole === 'investigator' ? ' selected' : '') . ">Investigator</option>
                                <option value='admin'" . ($userRole === 'admin' ? ' selected' : '') . ">Admin</option>
                            </select>
                            <button type='submit' style='background-color: #2a6f97; color: #fff;' class='btn btn-sm btn-update'><i class='bi bi-check-circle'></i> Update</button>
                          </form>";
                } else {
                    echo "<span class='text-muted'>Cannot be changed</span>";
                }
                echo "</td>";

                echo "<td>";
                if ($canDelete) {
                    echo "<a href='?delete=$userId' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this user?\");'>
                            <i class='bi bi-trash'></i> Delete
                          </a>";
                } else {
                    echo "<span class='text-muted'>Protected</span>";
                }
                echo "</td>";

                echo "</tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
