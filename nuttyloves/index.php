<?php
// Optional: include database connection if needed for dynamic products
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>NuttyLoves</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #ffffff;
      color: #333;
    }
    header {
      background-color: #2e7d32;
      padding: 20px;
      color: #fff;
      text-align: center;
    }
    nav {
      background-color: #c62828;
      display: flex;
      justify-content: center;
      padding: 10px;
    }
    nav a {
      color: #fff;
      margin: 0 15px;
      text-decoration: none;
      font-weight: bold;
    }
    nav a:hover {
      text-decoration: underline;
    }
    .container {
      padding: 30px;
      max-width: 1000px;
      margin: auto;
    }
    .product {
      border: 1px solid #eee;
      padding: 15px;
      margin: 15px 0;
      border-radius: 10px;
      background: #f5f5f5;
      display: flex;
      align-items: center;
    }
    .product img {
      width: 150px;
      height: 150px;
      object-fit: cover;
      border-radius: 10px;
      margin-right: 20px;
    }
    .product h3 {
      margin: 0;
      color: #2e7d32;
    }
    .product p {
      margin: 5px 0;
    }
    .btn {
      background-color: #c62828;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      margin-top: 10px;
      text-decoration: none;
      display: inline-block;
    }
    .btn:hover {
      background-color: #b71c1c;
    }
    footer {
      text-align: center;
      padding: 20px;
      background-color: #2e7d32;
      color: white;
      margin-top: 40px;
    }
  </style>
</head>
<body>

<header>
  <h1>NuttyLoves</h1>
  <p>Natural Goodness in Every Bite</p>
</header>

<nav>
  <a href="index.php">Home</a>
  <a href="products.php">Shop</a>
  <a href="cart.php">Cart</a>
  <a href="login.php">Login</a>
</nav>

<div>
  <?php
  $sql = "SELECT * FROM products";         
  $result = $conn->query($sql);             

  if ($result->num_rows > 0) {              
    while ($row = $result->fetch_assoc()) { 
      echo '<div class="product">
              <img src="images/nuttyloves' . $row['ProductID'] . '.jpg" alt="' . htmlspecialchars($row['Name']) . '">
              <div>
                <h3>' . htmlspecialchars($row['Name']) . '</h3>
                <p>' . htmlspecialchars($row['Description']) . '</p>
                <p><strong>₱' . number_format($row['Price'], 2) . '</strong></p>
                <a href="add_to_cart.php?product_id=' . $row['ProductID'] . '" class="btn">Add to Cart</a>
              </div>
            </div>';
    }
  } else {
    echo "<p>No products found.</p>";
  }
  ?>
</div>

<footer>
  &copy; <?= date('Y') ?> NuttyLoves. All rights reserved.
</footer>

</body>
</html>
