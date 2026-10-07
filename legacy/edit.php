<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin.php");
    exit;
}

$file = "tools.json";
$tools = json_decode(file_get_contents($file), true);

$index = $_GET['index'] ?? null;
if ($index === null || !isset($tools[$index])) {
    echo "Invalid tool index.";
    exit;
}

// Update Tool
if (isset($_POST['update'])) {
    $tools[$index] = [
        "title" => $_POST['title'],
        "link" => $_POST['link'],
        "category" => $_POST['category'],
        "status" => $_POST['status']
    ];
    file_put_contents($file, json_encode($tools, JSON_PRETTY_PRINT));
    header("Location: admin.php");
    exit;
}

$tool = $tools[$index];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Tool</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css">
</head>
<body class="p-6 bg-gray-100">

<h1 class="text-2xl font-bold mb-4">Edit Tool</h1>

<form method="post" class="bg-white p-4 rounded shadow">
    <input type="text" name="title" value="<?= htmlspecialchars($tool['title']) ?>" required class="border p-2 m-1">
    <input type="text" name="link" value="<?= htmlspecialchars($tool['link']) ?>" required class="border p-2 m-1">
    <input type="text" name="category" value="<?= htmlspecialchars($tool['category']) ?>" required class="border p-2 m-1">
    <select name="status" class="border p-2 m-1">
        <option value="published" <?= $tool['status'] === 'published' ? 'selected' : '' ?>>Published</option>
        <option value="draft" <?= $tool['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
    </select>
    <button type="submit" name="update" class="bg-blue-500 text-white px-4 py-2 rounded">Update Tool</button>
</form>

<p class="mt-4"><a href="admin.php" class="text-blue-600 underline">Back to Admin Panel</a></p>

</body>
</html>
