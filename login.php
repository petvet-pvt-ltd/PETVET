<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | PETVET</title>
  <link rel="stylesheet" href="styles/login.css">
</head>
<body>
  <div class="container">
    <div class="image-panel"></div>

    <div class="login-panel">

      <img src="images/petvet-logo-black.png" alt="">
      <h1>Login</h1>

      <form action="db/login-process.php" method="post">
        <div class="input-group">
          <img src="images/mail.png" alt="Email Icon">
          <input type="email" name="email" placeholder="Email" required>
        </div>

        <div class="input-group">
          <img src="images/lock.png" alt="Password Icon">
          <input type="password" name="password" placeholder="Password" required>
        </div>

        <div class="actions">
          <a href="#">Forgot password</a>
        </div>

        <button name="login-submit" type="submit" class="login-btn">Login</button>
      </form>
    </div>
  </div>
</body>
</html>
