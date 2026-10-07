<?php
session_start();

// Simple login
if (!isset($_SESSION['admin'])) {
  if ($_POST['password'] ?? '' === 'yourpassword') {
    $_SESSION['admin'] = true;
  } else {
    echo '<form method="post"><input type="password" name="password" placeholder="Admin Password"><button>Login</button></form>';
    exit;
  }
}

$file = "tools.json";
$tools = json_decode(file_get_contents($file), true);

// Add Tool
if (isset($_POST['add'])) {
  $tools[] = [
    "title" => $_POST['title'],
    "link" => $_POST['link'],
    "category" => $_POST['category'],
    "status" => $_POST['status']
  ];
  file_put_contents($file, json_encode($tools, JSON_PRETTY_PRINT));
  header("Location: admin.php");
}

// Delete Tool
if (isset($_GET['delete'])) {
  unset($tools[$_GET['delete']]);
  file_put_contents($file, json_encode(array_values($tools), JSON_PRETTY_PRINT));
  header("Location: admin.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css">
</head>
<body class="p-6 bg-gray-100">
  <h1 class="text-2xl font-bold mb-4">Admin Panel - Manage Tools</h1>

  <!-- Add Tool -->
  <form method="post" class="bg-white p-4 rounded shadow mb-6">
    <input type="text" name="title" placeholder="Tool Title" required class="border p-2 m-1">
    <input type="text" name="link" placeholder="Tool Link" required class="border p-2 m-1">
    <input type="text" name="category" placeholder="Category" required class="border p-2 m-1">
    <select name="status" class="border p-2 m-1">
      <option value="published">Published</option>
      <option value="draft">Draft</option>
    </select>
    <button type="submit" name="add" class="bg-green-500 text-white px-4 py-2 rounded">Add Tool</button>
  </form>

  <!-- Tool List -->
  <table class="bg-white w-full shadow rounded">
    <tr class="bg-gray-200 text-left">
      <th class="p-2">Title</th>
      <th class="p-2">Category</th>
      <th class="p-2">Status</th>
      <th class="p-2">Actions</th>
    </tr>
    <?php foreach ($tools as $i => $tool): ?>
    <tr>
      <td class="p-2"><?= htmlspecialchars($tool['title']) ?></td>
      <td class="p-2"><?= htmlspecialchars($tool['category']) ?></td>
      <td class="p-2"><?= htmlspecialchars($tool['status']) ?></td>
      <td class="p-2">
        <a href="?delete=<?= $i ?>" class="bg-red-500 text-white px-2 py-1 rounded">Delete</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</body>
</html>
