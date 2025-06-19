<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['UserID'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $address = trim($_POST['address']);
    $password = $_POST['password'];

    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET Name = ?, Email = ?, Address = ?, Password = ? WHERE UserID = ?");
        $stmt->bind_param("ssssi", $name, $email, $address, $hashed, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET Name = ?, Email = ?, Address = ? WHERE UserID = ?");
        $stmt->bind_param("sssi", $name, $email, $address, $user_id);
    }

    if ($stmt->execute()) {
        $message = "✅ Account updated successfully.";
        $_SESSION['user']['Name'] = $name;
        $_SESSION['user']['Email'] = $email;
    } else {
        $message = "❌ Failed to update account.";
    }
}

$result = $conn->query("SELECT * FROM users WHERE UserID = $user_id");
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Account - NuttyLoves</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f9f9f9;
      margin: 0;
      color: #333;
    }

    header {
      background-color: #2e7d32;
      color: white;
      padding: 20px;
      text-align: center;
    }

    nav {
      background-color: #c62828;
      display: flex;
      justify-content: space-between;
      padding: 10px 30px;
      align-items: center;
    }

    nav a {
      color: white;
      text-decoration: none;
      margin: 0 10px;
      font-weight: bold;
    }

    nav a:hover {
      text-decoration: underline;
    }

    .container {
      max-width: 600px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.08);
    }

    h2 {
      text-align: center;
      color: #2e7d32;
      margin-bottom: 20px;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    label {
      font-weight: bold;
    }

    input, textarea {
      padding: 10px;
      font-size: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    textarea {
      resize: vertical;
    }

    button {
      background-color: #c62828;
      color: white;
      padding: 12px;
      font-size: 16px;
      font-weight: bold;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background 0.3s;
    }

    button:hover {
      background-color: #b71c1c;
    }

    .message {
      text-align: center;
      margin-bottom: 20px;
      padding: 12px;
      border-radius: 5px;
      font-weight: bold;
    }

    .message.success {
      background-color: #e8f5e9;
      color: #2e7d32;
      border: 1px solid #a5d6a7;
    }

    .message.error {
      background-color: #ffebee;
      color: #c62828;
      border: 1px solid #ef9a9a;
    }
  </style>
</head>
<body>

<header>
  <h1>NuttyLoves</h1>
  <p>Manage Your Account</p>
</header>

<nav>
  <div>
    <a href="index.php">Home</a>
    <a href="shop.php">Shop</a>
    <a href="cart.php">Cart</a>
	<a href="my_orders.php">My Orders</a>
    <a href="account_settings.php">My Account</a>
  </div>
  <div>
    <?php if (isset($_SESSION['user'])): ?>
      <span style="color:white; margin-right: 10px;">Welcome, <?= htmlspecialchars($_SESSION['user']['Name']) ?></span>
      <a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="login.php">Login</a>
    <?php endif; ?>
  </div>
</nav>

<div class="container">
  <h2>Account Details</h2>

  <?php if ($message): ?>
    <div class="message <?= strpos($message, 'Failed') !== false ? 'error' : 'success' ?>">
      <?= $message ?>
    </div>
  <?php endif; ?>

  <form method="post">
    <label for="name">Full Name</label>
    <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['Name']) ?>" required>

    <label for="email">Email Address</label>
    <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['Email']) ?>" required>

    <label for="address">Shipping Address</label>
    <textarea name="address" id="address" rows="3" required><?= htmlspecialchars($user['Address']) ?></textarea>

    <label for="password">New Password (leave blank to keep current)</label>
    <input type="password" name="password" id="password">

    <button type="submit">Save Changes</button>
  </form>
</div>

</body>
</html>
