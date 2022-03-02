<?php
  session_start();
  $error                = '';
  $_SESSION['loggedin'] = $_SESSION['loggedin'] ?? '';
  $username             = FILTER_INPUT( INPUT_POST, 'username', FILTER_SANITIZE_STRING );
  $password             = FILTER_INPUT( INPUT_POST, 'password', FILTER_SANITIZE_STRING );

  $fp = fopen( '.\database\users.txt', 'r' );

  if ( isset( $username ) && isset( $password ) ) {
    $_SESSION['loggedin'] = false;
    $_SESSION['user']     = false;
    $_SESSION['role']     = false;
    while ( $credential = fgetcsv( $fp ) ) {
      if ( $credential[0] == $username && $credential[1] == md5( $password ) ) {
        $_SESSION['loggedin'] = true;
        $_SESSION['user']     = $username;
        $_SESSION['role']     = $credential[2];
        header( 'location:index.php' );
      }else{
        $error = 1;
      }
    }
    if ( $_SESSION['loggedin'] == false ) {
      // $error            = 1;
      $_SESSION['user'] = false;
      $_SESSION['role'] = false;
    }
  }

  if ( $_SESSION['loggedin'] ) {
    header( 'location: index.php' );
  }

?>

<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic"> -->
    <link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap-confirm-delete.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <style>
    input {
      margin-bottom: 15px;
    }

    a,
    a:hover,
    a:focus {
      text-decoration: none;
      color: inherit;
    }

    </style>
  </head>

  <body>
    <div class="container mt-5">
      <div class="row">
        <div class="col-4"></div>
        <div class="col-4">
          <h2>Welcome To Our System</h2>
          <?php
          if ( $_SESSION['loggedin'] == true ):
        ?>
          <p>Hello Admin</p>
          <?php
          else:
        ?>
          <p>Hello Stranger, Login here</p>
          <?php
          endif;
        ?>
        </div>
        <div class="col-4"></div>
      </div>

      <div class="row mt-5">
        <div class="col-4"></div>
        <div class="col-4">
          <?php
          if ( $error == 1 ):
        ?>
          <div class="alert alert-danger">Username and Password didn't match</div>
          <?php
          endif;
        ?>

          <?php
          if ( false == $_SESSION['loggedin'] ):
        ?>
          <form method="POST">
            <div class="form-group input-group">
              <div class="input-group-prepend">
                <span class="input-group-text"> <i class="fa fa-user"></i> </span>
              </div>
              <input type="text" name="username" id="username" class="form-control" placeholder="Username">
            </div> <!-- form-group// -->

            <div class="form-group input-group">
              <div class="input-group-prepend">
                <span class="input-group-text"> <i class="fa fa-lock"></i> </span>
              </div>
              <input type="password" name="password" class="form-control" placeholder="Password">
            </div> <!-- form-group// -->
            <button type="submit" class="btn btn-primary">Login</button>
          </form>
          <?php
          endif;
        ?>
        </div>
        <div class="col-4"></div>
      </div>

      <div class="row mt-5">
        <div class="col-4"></div>
        <div class="col-4">
          <?php
          if ( !$_SESSION['loggedin'] ):
        ?>
          <div class="alert alert-info">
            Wanna Search Student? <a href="index.php" style="color: #007bff">Check it out</a>
          </div>
          <?php
          endif;
        ?>
        </div>
        <div class="col-4"></div>
      </div>
    </div>


    <script src="assets/js/jquery-3.5.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/bootstrap-confirm-delete.js"></script>
    <script src="assets/js/mian.js"></script>
  </body>

</html>
