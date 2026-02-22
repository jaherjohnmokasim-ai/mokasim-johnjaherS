<?php

session_start();



// create users storage

if (!isset($_SESSION["users"])) {

  $_SESSION["users"] = [];

}



$page = $_GET["page"] ?? "login";

$error = "";

$success = "";



/* ---------- AUTO LOGIN VIA COOKIE ---------- */

if (!isset($_SESSION["user"]) && isset($_COOKIE["user"])) {

  $_SESSION["user"] = $_COOKIE["user"];

}



/* ---------- LOGOUT ---------- */

if ($page === "logout") {

  session_destroy();

  setcookie("user", "", time() - 3600, "/");

  header("Location: mokasim.php?page=login");

  exit();

}



/* ---------- REGISTER ---------- */

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["action"] === "register") {

  $username = trim($_POST["username"]);

  $password = trim($_POST["password"]);



  if (isset($_SESSION["users"][$username])) {

    $error = "Username already exists.";

    $page = "register";

  } else {

    $_SESSION["users"][$username] = password_hash($password, PASSWORD_DEFAULT);

    $success = "Registered successfully. You can login.";

    $page = "login";

  }

}



/* ---------- LOGIN ---------- */

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["action"] === "login") {

  $username = trim($_POST["username"]);

  $password = trim($_POST["password"]);



  if (isset($_SESSION["users"][$username]) &&

    password_verify($password, $_SESSION["users"][$username])) {



    $_SESSION["user"] = $username;

    setcookie("user", $username, time() + (86400 * 3), "/");



    header("Location: mokasim.php?page=profile");

    exit();

  } else {

    $error = "Invalid login.";

    $page = "login";

  }

}



/* ---------- PROTECT PROFILE ---------- */

if ($page === "profile" && !isset($_SESSION["user"])) {

  header("Location: mokasim.php?page=login");

  exit();

}

?>



<!DOCTYPE html>

<html>

<head>

  <title>Auth App</title>

</head>

<body>



<?php if($page === "register"): ?>



  <h2>Register</h2>

  <?php if($error) echo "<p>$error</p>"; ?>

  <?php if($success) echo "<p>$success</p>"; ?>



  <form method="POST">

    <input name="username" placeholder="Username" required><br><br>

    <input name="password" type="password" placeholder="Password" required><br><br>

    <input type="hidden" name="action" value="register">

    <button type="submit">Register</button>

  </form>



  <p><a href="mokasim.php?page=login">Already have account? Login</a></p>



<?php elseif($page === "profile"): ?>



  <h2>Welcome, <?php echo htmlspecialchars($_SESSION["user"]); ?></h2>

  <p>You are logged in.</p>

  <p><a href="mokasim.php?page=logout">Logout</a></p>



<?php else: ?>



  <h2>Login</h2>

  <?php if($error) echo "<p>$error</p>"; ?>



  <form method="POST">

    <input name="username" placeholder="Username" required><br><br>

    <input name="password" type="password" placeholder="Password" required><br><br>

    <input type="hidden" name="action" value="login">

    <button type="submit">Login</button>

  </form>



  <p><a href="mokasim.php?page=register">Create account</a></p>



<?php endif; ?>



</body>

</html>