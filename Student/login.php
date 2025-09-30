<?php 
session_start();

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Sambung ke database
$host = "localhost";
$user = "root";
$password = "";
$dbname = "KanbanSystem";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Proses daftar
if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $numPhone = $_POST['numPhone'];
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT id FROM userlogin WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['message'] = "⚠️ Username or Email already exists!";
        $stmt->close();
        header("Location: login.php");
        exit();
    }
    $stmt->close();

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO userlogin (username, numPhone, email, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username,$numPhone, $email, $password_hashed);

    if ($stmt->execute()) {
        $_SESSION['message'] = "✅ Register successful! Please log in.";
        $stmt->close();
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['message'] = "❌ Something went wrong, try again!";
        $stmt->close();
        header("Location: login.php");
        exit();
    }
}

// Proses login
if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT password FROM userlogin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($password_hashed);
        $stmt->fetch();

        if (password_verify($password, $password_hashed)) {
            $_SESSION['username'] = $username;
            $stmt->close();
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['message'] = "❌ Wrong password!";
            $stmt->close();
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['message'] = "❌ Username does not exist!";
        $stmt->close();
        header("Location: login.php");
        exit();
    }
    
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login Register</title>
  <link rel="stylesheet" href="Login.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    .message {
      margin: 10px 0;
      padding: 10px;
      border-radius: 5px;
      font-weight: bold;
      text-align: center;
    }
    .message.error { background: #f8d7da; color: #721c24; }
    .message.success { background: #d4edda; color: #155724; }
  </style>
</head>
<body>
  <h1>WELCOME TO MY STUDY FLOW</h1>
  <div class="container">
    <div class="container__buttons">
      <div class="container__btn-highlight"></div>
      <button type="button" class="container__toggle-btn container__toggle-btn--login">log in</button>
      <button type="button" class="container__toggle-btn container__toggle-btn--register">register</button>
    </div>

    <!-- Show message -->
    <?php if (!empty($message)): ?>
      <div class="message 
        <?php echo (strpos($message, '✅') !== false) ? 'success' : 'error'; ?>">
        <?php echo $message; ?>
      </div>
    <?php endif; ?>

    <!-- Login Form -->
    <form id="login" class="form" method="post">
      <input type="text" class="form__input" name="username" placeholder="Username" required>
      <input type="password" class="form__input" name="password" placeholder="Password" required>
      <button type="submit" class="form__submit-btn" name="login">log in</button>
   

    </form>

    <!-- Register Form -->
    <form id="register" class="form" method="post">
      <input type="text" class="form__input" name="username" placeholder="Username" required>
      <input type="num" class="form__input" name="numPhone" placeholder="Number Phone" required>
      <input type="email" class="form__input" name="email" placeholder="Email" required>
      <input type="password" class="form__input" name="password" placeholder="Password" required>
      <button type="submit" class="form__submit-btn" name="register">register</button>
    </form>
  </div>
  <script src="login.js"></script>
</body>
</html>
