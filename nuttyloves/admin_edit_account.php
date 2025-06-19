<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['UserID'];
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $address = trim($_POST['address']);
    $password = $_POST['password'];

    if ($password) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare(
            "UPDATE users SET Name = ?, Email = ?, Address = ?, Password = ? WHERE UserID = ?"
        );
        $stmt->bind_param("ssssi", $name, $email, $address, $hashed, $user_id);
    } else {
        $stmt = $conn->prepare(
            "UPDATE users SET Name = ?, Email = ?, Address = ? WHERE UserID = ?"
        );
        $stmt->bind_param("sssi", $name, $email, $address, $user_id);
    }

    if ($stmt->execute()) {
        $success = "✅ Account updated successfully.";
        $_SESSION['user']['Name']  = $name;
        $_SESSION['user']['Email'] = $email;
        $_SESSION['user']['Address'] = $address;
    } else {
        $error = "❌ Update failed. Please try again.";
    }
}

$stmt = $conn->prepare("SELECT * FROM users WHERE UserID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Admin Account</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
/* —— Core Colors —— */
:root {
  --green:#2e7d32;
  --red:#c62828;
  --red-dark:#b71c1c;
}
/* —— Basic Layout —— */
body{font-family:'Segoe UI',sans-serif;margin:0;background:#f9f9f9;color:#333}
header{background:var(--green);color:#fff;padding:20px;text-align:center}
nav{
  background:var(--red);display:flex;justify-content:space-between;
  align-items:center;padding:10px 30px;flex-wrap:wrap
}
nav a{color:#fff;font-weight:bold;text-decoration:none;margin:6px 10px}
nav a:hover{text-decoration:underline}
.container{
  max-width:650px;margin:40px auto;background:#fff;padding:30px 35px;
  border-radius:12px;box-shadow:0 4px 14px rgba(0,0,0,.08)
}
/* —— Form —— */
h2{text-align:center;color:var(--green);margin-bottom:25px}
form{display:flex;flex-direction:column;gap:18px}
label{font-weight:bold}
input,textarea{
  width:100%;padding:11px;font-size:15px;border:1px solid #ccc;
  border-radius:6px
}
textarea{resize:vertical}
button{
  background:var(--red);color:#fff;padding:12px;font-size:16px;
  font-weight:bold;border:none;border-radius:6px;cursor:pointer;
  transition:.25s
}
button:hover{background:var(--red-dark)}
/* —— Messages —— */
.msg{
  text-align:center;padding:12px;border-radius:6px;font-weight:bold;margin-bottom:10px
}
.msg.ok{background:#e8f5e9;color:var(--green);border:1px solid #a5d6a7}
.msg.err{background:#ffebee;color:var(--red);border:1px solid #ef9a9a}
@media(max-width:600px){
  nav{padding:10px 15px}
  .container{margin:25px 10px;padding:25px}
}
</style>
</head>
<body>

<header>
  <h1>NuttyLoves Admin Panel</h1>
</header>

<nav>
  <div>
  <a href="admin_dashboard.php">Dashboard</a>
  <a href="admin_low_stock_alerts.php">Low Stock Alerts</a>
  <a href="admin_manage_products.php">Products</a>
  <a href="admin_manage_inventory.php">Inventory</a>
  <a href="admin_manage_ingredients.php">Ingredients</a>
  <a href="admin_manage_orders.php">Orders</a>
  <a href="admin_view_sales.php">Sales</a>
  <a href="admin_sales_chart.php">Reports</a>
  <a href="admin_verify_users.php">Verify Users</a>
  <a href="admin_grant_admin.php">Grant Admin</a>
  <a href="admin_edit_account.php">My Account</a>
  <a href="logout.php">Logout</a>
  </div>
</nav>

<div class="container">
  <h2>Edit Account</h2>

  <?php if ($success): ?>
      <div class="msg ok"><?= $success ?></div>
  <?php elseif ($error): ?>
      <div class="msg err"><?= $error ?></div>
  <?php endif; ?>

  <form method="post">
    <label>Full Name</label>
    <input type="text" name="name" value="<?= htmlspecialchars($user['Name']) ?>" required>

    <label>Email Address</label>
    <input type="email" name="email" value="<?= htmlspecialchars($user['Email']) ?>" required>

    <label>Office Address</label>
    <textarea name="address" rows="3" required><?= htmlspecialchars($user['Address']) ?></textarea>

    <label>New Password <small>(leave blank to keep current)</small></label>
    <input type="password" name="password">

    <button type="submit">Save Changes</button>
  </form>
</div>

</body>
</html>
