<?php
// session_start();

// If the user is not logged in, redirect to the login page
// if (!isset($_SESSION['username'])) {
//   header("Location: login.php");
//   exit();
// }
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Change Password</title>

  <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="assets/fonts/line-icons.css">
  <link rel="stylesheet" type="text/css" href="assets/css/slicknav.css">

  <link rel="stylesheet" type="text/css" href="assets/css/animate.css">
  <link rel="stylesheet" type="text/css" href="assets/css/owl.carousel.css">
  <link rel="stylesheet" type="text/css" href="assets/css/main.css">
  <link rel="stylesheet" type="text/css" href="assets/css/responsive.css">
  <link rel="stylesheet" href="assets/css/custom.css">

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
            <h3>Enter OTP</h3>
            <form id="otpForm">
              <div class="form-group">
                <div class="input-icon">
                  <i class="lni-lock"></i>
                  <input type="text" id="otp" class="form-control" name="otp" placeholder="Enter OTP">
                </div>
                <div id="otpError" class="error-message" style="display:none;">Please enter OTP</div>
              </div>
              <div class="text-center">
                <button type="submit" class="btn btn-common log-btn" id="verifyotpBtn">Verify OTP</button>
                <!-- Resend OTP button -->
                <button type="button" id="resendOtpButton" class="btn btn-secondary mt-2 d-none" style="margin-bottom: 10px;">Resend OTP</button>
                <div id="otpMessage" class="error-message" style="display:none;"></div>
              </div>
            </form>
            <!-- Message display -->


            <form id="changePasswordForm" style="display:none;">
              <h3>Change Password</h3>
              <div class="form-group">
                <div class="input-icon">
                  <i class="lni-lock"></i>
                  <input type="password" id="newPassword" class="form-control" name="newPassword" placeholder="New Password">
                </div>
                <div id="newPasswordError" class="error-message" style="display:none;">New password required</div>
              </div>
              <div class="form-group">
                <div class="input-icon">
                  <i class="lni-lock"></i>
                  <input type="password" id="confirmPassword" class="form-control" name="confirmPassword" placeholder="Confirm Password">
                </div>
                <div id="confirmPasswordError" class="error-message" style="display:none;">Confirm password required</div>
              </div>
              <div class="text-center">
                <button type="submit" class="btn btn-common log-btn">Change Password</button>
              </div>
            </form>
            <div id="changePasswordMessage" class="error-message" style="display:none;"></div> <!-- Message display -->
          </div>
        </div>
      </div>
    </div>
  </section>

  <div id='card' class="animated fadeIn ">
    <div id='upper-side'>
      <svg version="1.1" id="checkmark" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" xml:space="preserve">
        <path d="M131.583,92.152l-0.026-0.041c-0.713-1.118-2.197-1.447-3.316-0.734l-31.782,20.257l-4.74-12.65
        c-0.483-1.29-1.882-1.958-3.124-1.493l-0.045,0.017c-1.242,0.465-1.857,1.888-1.374,3.178l5.763,15.382
        c0.131,0.351,0.334,0.65,0.579,0.898c0.028,0.029,0.06,0.052,0.089,0.08c0.08,0.073,0.159,0.147,0.246,0.209
        c0.071,0.051,0.147,0.091,0.222,0.133c0.058,0.033,0.115,0.069,0.175,0.097c0.081,0.037,0.165,0.063,0.249,0.091
        c0.065,0.022,0.128,0.047,0.195,0.063c0.079,0.019,0.159,0.026,0.239,0.037c0.074,0.01,0.147,0.024,0.221,0.027
        c0.097,0.004,0.194-0.006,0.292-0.014c0.055-0.005,0.109-0.003,0.163-0.012c0.323-0.048,0.641-0.16,0.933-0.346l34.305-21.865
        C131.967,94.755,132.296,93.271,131.583,92.152z" />
        <circle fill="none" stroke="#ffffff" stroke-width="5" stroke-miterlimit="10" cx="109.486" cy="104.353" r="32.53" />
      </svg>
      <h3 id='status'>Success</h3>
    </div>
    <div id='lower-side'>
      <p id='message'>Password change Successfull</p>
      <a href="logout" id="contBtn">Continue</a>
    </div>
  </div>

  <?php include 'footer.php'; ?>

  <a href="#" class="back-to-top">
    <i class="lni-chevron-up"></i>
  </a>

  <div id="preloader">
    <div class="loader" id="loader-1"></div>
  </div>

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
  <script src="assets/js/Ajax/changePass.js"></script>
</body>

</html>