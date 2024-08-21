<?php
// Check if the session is already started
if (session_status() === PHP_SESSION_NONE) {
  // If not started, start the session
  session_start();
}

// Check if the user is already logged in
if (isset($_SESSION['username'])) {
  // Redirect to the index page if the user is logged in
  header("Location: /becho2/");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>NexusPlus - Login</title>

  <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="assets/fonts/line-icons.css">
  <link rel="stylesheet" type="text/css" href="assets/css/slicknav.css">

  <link rel="stylesheet" type="text/css" href="assets/css/animate.css">
  <link rel="stylesheet" type="text/css" href="assets/css/owl.carousel.css">
  <link rel="stylesheet" type="text/css" href="assets/css/main.css">
  <link rel="stylesheet" type="text/css" href="assets/css/responsive.css">

  <style>
    .error-message {
      color: red;
      font-size: 0.875em;
      margin-top: 5px;
    }

    .input-icon {
      position: relative;
    }

    .input-icon i {
      position: absolute;
      margin-top: 0 !important;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #aaa;
    }

    .form-control {
      padding-left: 40px;
      /* Adjust padding to accommodate the icon */
      padding-right: 15px;
      /* Add some right padding */
    }
  </style>
</head>

<body>

  <header id="header-wrap">
    <?php include 'header.php'; ?>
  </header>

  <section class="login section-padding" style="margin-top: 100px; margin-bottom: 100px;">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-5 col-md-12 col-xs-12">
          <div class="login-form login-area" style="padding: 25px;">
            <h3>Login Now</h3>
            <form id="loginForm" action="login" method="POST">
              <div class="form-group">
                <div class="input-icon">
                  <i class="lni-user"></i>
                  <input type="text" id="username" class="form-control" name="username" placeholder="Username">
                </div>
                <div id="usernameError" class="error-message" style="display:none;">Username required</div>
              </div>
              <div class="form-group">
                <div class="input-icon">
                  <i class="lni-lock"></i>
                  <input type="password" id="password" class="form-control" name="password" placeholder="Password">
                </div>
                <div id="passwordError" class="error-message" style="display:none;">Password required</div>
              </div>
              <div class="form-group mt-4">
                <ul class="form-links">
                  <li class="float-left"><a href="register">Don't have an account?</a></li>
                  <li class="float-right"><a href="forgot-password">Forgot Password</a></li>
                </ul>
              </div>
              <div class="text-center " style="margin-top: 60px;">
                <button type="submit" class="btn btn-common log-btn">Submit</button>
              </div>
            </form>
            <div id="loginMessage" class="error-message text-center" style="display:none;"></div> <!-- Message display -->
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php include 'footer.php'; ?>

  <a href="#" class="back-to-top">
    <i class="lni-chevron-up"></i>
  </a>




  <script data-cfasync="false" src="cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
  <script src="assets/js/jquery-min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>

  <script src="assets/js/jquery.counterup.min.js"></script>
  <script src="assets/js/waypoints.min.js"></script>
  <script src="assets/js/wow.js"></script>
  <script src="assets/js/owl.carousel.min.js"></script>
  <script src="assets/js/jquery.slicknav.js"></script>
  <script src="assets/js/main.js"></script>
  <script src="assets/js/form-validator.min.js"></script>
  <script src="assets/js/contact-form-script.min.js"></script>
  <script src="assets/js/summernote.js"></script>
  <script src="assets/js/Ajax/login.js"></script>
</body>


</html>