<?php
session_start();

// Initialize tasks if not set
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [
        ['id' => 1, 'name' => "Belajar PHP", 'status' => "belum"],
        ['id' => 2, 'name' => "Kerjakan tugas UX", 'status' => "selesai"]
    ];
}
$tasks = &$_SESSION['tasks'];

// Handle add task
if (isset($_POST['add_task'])) {
    $newTaskName = trim($_POST['task_name']);

    if ($newTaskName !== '') {
        $newId = count($tasks) > 0 ? max(array_column($tasks, 'id')) + 1 : 1;
        $tasks[] = ['id' => $newId, 'name' => $newTaskName, 'status' => 'belum'];

       
    }
}


// Handle delete
if (isset($_POST['delete_id'])) {
    $deleteId = (int) $_POST['delete_id'];
    $tasks = array_filter($tasks, fn($task) => $task['id'] !== $deleteId);
    $_SESSION['tasks'] = array_values($tasks); // Re-index after deletion
}

// Handle status toggle
if (isset($_POST['toggle_id'])) {
    $toggleId = (int) $_POST['toggle_id'];

    foreach ($tasks as &$task) {
        if ($task['id'] === $toggleId) {
            $task['status'] = $task['status'] === 'selesai' ? 'belum' : 'selesai';
            break;
        }
    }
    unset($task); // break reference

    // ✅ Redirect after processing to prevent resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Tugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/style.css" rel="stylesheet">
</head>
<body class="container py-5">

    <h2 class="mb-4 text-center fw-bold text-white py-3 rounded" style="background: linear-gradient(to right, #4CAF50, #2E7D32); box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
        Aplikasi To-Do-List
    </h2>

    <form method="post" class="d-flex mb-4">
        <input type="text" name="task_name" class="form-control me-2" placeholder="Tugas baru..." required>
        <button type="submit" name="add_task" class="btn btn-success">Tambah</button>
    </form>

    <ul class="list-group">
        <?php foreach ($tasks as $task): ?>
            <li class="list-group-item d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <form method="post" class="me-2">
                        <input type="hidden" name="toggle_id" value="<?= $task['id'] ?>">
                        <input
                            type="checkbox"
                            class="check-tugas"
                            name="status"
                            value="selesai"
                            onchange="this.form.submit()"
                            <?= $task['status'] === 'selesai' ? 'checked' : '' ?>
                        >
                    </form>
                    <span style="<?= $task['status'] === 'selesai' ? 'text-decoration: line-through;' : '' ?>">
                        <?= htmlspecialchars($task['name']) ?>
                    </span>
                </div>
                <form method="post">
                    <input type="hidden" name="delete_id" value="<?= $task['id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
    

</body>
</html>
