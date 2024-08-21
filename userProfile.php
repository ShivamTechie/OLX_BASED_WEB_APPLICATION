<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: /becho2/");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>NexusPlus - User Profile</title>

  <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="assets/fonts/line-icons.css">
  <link rel="stylesheet" type="text/css" href="assets/css/slicknav.css">

  <link rel="stylesheet" type="text/css" href="assets/css/animate.css">
  <link rel="stylesheet" type="text/css" href="assets/css/owl.carousel.css">
  <link rel="stylesheet" type="text/css" href="assets/css/main.css">
  <link rel="stylesheet" type="text/css" href="assets/css/responsive.css">
  <link rel="stylesheet" href="assets/css/custom.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

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

      margin-top: 10px;
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
            <h3 style="border-bottom: 0 !important;">Edit Info</h3>
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

              </div>
              <div class="form-group">
                <label for="profilePicture">Profile Picture (70x70 pixels required)</label>
                <input type="file" id="profilePicture" name="profile_picture" accept="image/*" class="form-control" style="padding-left: 5px !important;">
                <div class="error-message" id="profilePictureError"></div>
                <img id="imagePreview" alt="Image Preview" />
              </div>
              <div class="form-group mb-3">
                <div class="error-message" id="termsError"></div>
              </div>
              <div id="registrationMessage" style="color: red; display: none;" class="text-center"></div>
              <div class="text-center">
                <button type="button" id="registerButton" class="btn btn-common log-btn">Apply Changes</button>
              </div>
              <div id="registrationMessage" style="color: red; display: none;"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>



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

  <!-- Popup message -->
  <!-- <div id="popupMessage">Registration Sucessfull</div> -->



  <footer>
    <section class="footer-Content">
      <div class="container">
        <div class="row">
          <div class="col-lg-4 col-md-4 col-xs-6 col-mb-12">
            <div class="widget">
              <div class="footer-logo"><img src="assets/img/logo.png" alt=""></div>
              <div class="textwidget">
                <p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt consectetur, adipisci velit.</p>
              </div>
              <ul class="mt-3 footer-social">
                <li><a class="facebook" href="#"><i class="lni-facebook-filled"></i></a></li>
                <li><a class="twitter" href="#"><i class="lni-twitter-filled"></i></a></li>
                <li><a class="linkedin" href="#"><i class="lni-linkedin-fill"></i></a></li>
                <li><a class="google-plus" href="#"><i class="lni-google-plus"></i></a></li>
              </ul>
            </div>
          </div>
          <div class="col-lg-4 col-md-4 col-xs-6 col-mb-12">
            <div class="widget">
              <h3 class="block-title">Quick Link</h3>
              <ul class="menu">
                <li><a href="#">- About Us</a></li>
                <li><a href="#">- Blog</a></li>
                <li><a href="#">- Events</a></li>
                <li><a href="#">- Shop</a></li>
                <li><a href="#">- FAQ's</a></li>
              </ul>
            </div>
          </div>
          <div class="col-lg-4 col-md-4 col-xs-6 col-mb-12">
            <div class="widget">
              <h3 class="block-title">Contact Info</h3>
              <ul class="contact-footer">
                <li>
                  <strong><i class="lni-phone"></i></strong><span>+1 555 444 66647 <br> +1 555 444 66647</span>
                </li>
                <li>
                  <strong><i class="lni-envelope"></i></strong><span><a href="mailto:example@example.com">example@example.com</a></span>
                </li>
                <li>
                  <strong><i class="lni-map-marker"></i></strong><span>9870 St Vincent Place, Glasgow, DC 45</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

    <div id="copyright">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="text-center">
              <p>&copy; 2024 Templates Hub. All Rights Reserved. <br> <a href="https://templateshub.net">Templates Hub</a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>

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
  <script src="assets/js/Ajax/changeProfile.js"></script>


</body>


</html>