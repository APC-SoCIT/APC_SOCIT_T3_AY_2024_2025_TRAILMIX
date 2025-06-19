<?php
session_start();
include 'config.php';

require __DIR__ . '/phpmailer/src/Exception.php';
require __DIR__ . '/phpmailer/src/PHPMailer.php';
require __DIR__ . '/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);

    $conn->query("UPDATE users SET Verified = 1 WHERE UserID = $user_id");

    $result = $conn->query("SELECT Email, Name FROM users WHERE UserID = $user_id");
    if ($result && $user = $result->fetch_assoc()) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'francisconjd.csa@gmail.com'; 
            $mail->Password = 'wyyh bgys pvhh nsqm';    
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                ]
            ];

            $mail->setFrom('francisconjd@gmail.com', 'NuttyLoves');
            $mail->addAddress($user['Email'], $user['Name']);

            $mail->isHTML(true);
            $mail->Subject = "Your NuttyLoves Account Has Been Verified!";
            $mail->Body = "
                <h2 style='color: #2e7d32;'>Welcome, {$user['Name']}!</h2>
                <p>Your NuttyLoves account has been verified. 🎉</p>
                <p>You may now login and enjoy our treats!</p>
                <a href='http://localhost/nuttyloves/login.php' style='padding:10px 20px; background:#c62828; color:#fff; border-radius:5px; text-decoration:none;'>Login Now</a>
                <p style='margin-top:20px;'>Thank you,<br>NuttyLoves Team</p>
            ";

            $mail->send();
        } catch (Exception $e) {
            echo "Verification email failed: {$mail->ErrorInfo}";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>NuttyLoves Admin - Verify Users</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; background: #fff; }
    header, footer { background: #2e7d32; color: white; padding: 15px; text-align: center; }
    nav { background: #c62828; display: flex; justify-content: center; padding: 10px; }
    nav a { color: white; margin: 0 15px; text-decoration: none; font-weight: bold; }
    .container { max-width: 1000px; margin: 30px auto; padding: 20px; }
    h2 { color: #2e7d32; margin-bottom: 20px; text-align: center; }
    table { width: 100%; border-collapse: collapse; background: #fdfdfd; }
    th, td { border: 1px solid #ccc; padding: 12px; text-align: left; }
    th { background-color: #2e7d32; color: white; }
    .btn { padding: 6px 12px; background: #c62828; color: white; border: none; border-radius: 3px; cursor: pointer; }
    .btn:hover { background-color: #b71c1c; }
    form { display: inline; }
  </style>
</head>
<body>

<header>
  <h1>NuttyLoves Admin Panel</h1>
</header>

<nav>
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
</nav>

<div class="container">
  <h2>Verify New Customer Accounts</h2>

  <table>
    <tr>
      <th>User ID</th>
      <th>Name</th>
      <th>Email</th>
      <th>Action</th>
    </tr>
    <?php
    $users = $conn->query("SELECT * FROM users WHERE Verified = 0 AND Role = 'customer'");
    if ($users->num_rows > 0):
        while ($row = $users->fetch_assoc()):
    ?>
      <tr>
        <td><?= $row['UserID'] ?></td>
        <td><?= htmlspecialchars($row['Name']) ?></td>
        <td><?= htmlspecialchars($row['Email']) ?></td>
        <td>
          <form method="post">
            <input type="hidden" name="user_id" value="<?= $row['UserID'] ?>">
            <button type="submit" class="btn">Verify</button>
          </form>
        </td>
      </tr>
    <?php endwhile; else: ?>
      <tr><td colspan="4" style="text-align:center;">No unverified users found.</td></tr>
    <?php endif; ?>
  </table>
</div>

<footer>
  &copy; <?= date('Y') ?> NuttyLoves. All rights reserved.
</footer>

</body>
</html>
