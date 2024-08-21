<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>NexusPlus - Classified Ads and Listing Template</title>

  <!-- Include stylesheets -->
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
      font-size: 0.85em;
      margin-top: 5px;
    }

    .input-icon {
      position: relative;
      margin-bottom: 5px;
    }

    .input-icon i {
      position: absolute;
      left: 10px;
      top: 50%;
      transform: translateY(-50%);
      pointer-events: none;
      color: #888;
    }

    .form-control {
      padding-left: 40px;
      padding-top: 10px;
      height: 40px;
      width: 100%;
      box-sizing: border-box;
    }

    .form-control::placeholder {
      color: #aaa;
      opacity: 1;
    }

    .register-form {
      padding: 20px;
      border: 1px solid #ddd;
      border-radius: 5px;
      background-color: #fff;
    }

    .form-group {
      margin-bottom: 20px;
    }

    label {
      margin-bottom: 5px;
      display: block;
    }

    #imagePreview {
      max-width: 100%;
      height: auto;
      display: none;
      margin-top: 10px;
    }



    .otp-field {
      width: 100%;
    
      box-sizing: border-box;
    }
  </style>
</head>

<body>
  <header id="header-wrap">
    <?php include 'header.php'; ?>
  </header>

  <section class="register section-padding" style="margin-top: 60px;">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-5 col-md-12 col-xs-12">
          <div class="register-form login-area">
            <h3 style="border-bottom: 0 !important;">Register</h3>
            <div id="registrationDiv">
              <div class="form-group">
                <label for="username">Username</label>
                <div class="input-icon">
                  <i class="lni-user"></i>
                  <input type="text" id="username" class="form-control" name="username" placeholder="Username">
                </div>
                <div class="error-message" id="usernameError"></div>
              </div>
              <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-icon">
                  <i class="lni-envelope"></i>
                  <input type="email" id="email" class="form-control" name="email" placeholder="Email Address">

                </div>
                <button type="button" id="verifyEmailButton" class="btn btn-secondary">Verify</button>
                <button type="button" id="resendOtpButton" class="btn btn-secondary">Resend OTP</button>
                <div class="error-message" id="emailError"></div>

              </div>

              <!-- OTP input section -->
              <div id="otpSection">
                <div class="form-group">
                  <label for="otp">Enter OTP</label>
                  <div class="input-icon">
                    <i class="lni-key"></i>
                    <input type="text" id="otp" class="form-control otp-field" placeholder="Enter OTP">
                  </div>
                  <div class="error-message" id="otpError"></div>

                </div>
              </div>

              <div class="form-group">
                <label for="password">Password</label>
                <div class="input-icon">
                  <i class="lni-lock"></i>
                  <input type="password" id="password" class="form-control" placeholder="Password">
                </div>
                <div class="error-message" id="passwordError"></div>
              </div>
              <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <div class="input-icon">
                  <i class="lni-lock"></i>
                  <input type="password" id="confirmPassword" class="form-control" placeholder="Retype Password">
                </div>
                <div class="error-message" id="confirmPasswordError"></div>
              </div>
              <div class="form-group">
                <label for="location">Location</label>
                <div class="input-icon">
                  <i class="lni-map-marker"></i>
                  <input type="text" id="location" class="form-control" placeholder="Location">
                </div>
                <div class="error-message" id="locationError"></div>
              </div>
              <div class="form-group">
                <label for="phone">Phone Number</label>
                <div class="input-icon">
                  <i class="lni-phone"></i>
                  <input type="text" id="phone" class="form-control" placeholder="Phone Number">
                </div>
                <div class="error-message" id="phoneError"></div>
              </div>
              <div class="form-group">
                <label for="profilePicture">Profile Picture (70x70 pixels required)</label>
                <input type="file" id="profilePicture" accept="image/*" class="form-control" style="padding-left: 5px !important;">
                <div class="error-message" id="profilePictureError"></div>
                <img id="imagePreview" alt="Image Preview" />
              </div>
              <div class="form-group mb-3">
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input" id="termsCheckbox">
                  <label class="custom-control-label" for="termsCheckbox">By registering, you accept our Terms & Conditions</label>
                </div>
                <div class="error-message" id="termsError"></div>
              </div>
              <div id="registrationMessage" style="color: red;" class="text-center"></div>
              <div class="text-center">
                <button type="button" id="registerButton" class="btn btn-common log-btn">Register</button>
              </div>
            </div>
          </div>

          <!-- Success Card -->
          <div id='card' class="animated fadeIn d-none">
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
              <p id='message'>Congratulations, your account has been successfully created.</p>
              <a href="login" id="contBtn">Continue</a>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>

  <!-- Include your existing footer content -->
  <?php include 'footer.php'; ?>
  <a href="#" class="back-to-top">
    <i class="lni-chevron-up"></i>
  </a>


  <!-- Include JavaScript files -->
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
  <script src="assets/js/Ajax/registration.js"></script>

</body>

</html>