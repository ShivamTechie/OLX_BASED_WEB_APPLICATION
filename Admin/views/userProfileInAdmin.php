<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>NexusPlus - Classified Ads and Listing Template</title>

  <link rel="stylesheet" type="text/css" href="../../assets/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="../../assets/fonts/line-icons.css">
  <link rel="stylesheet" type="text/css" href="../../assets/css/slicknav.css">

  <link rel="stylesheet" type="text/css" href="../../assets/css/animate.css">
  <link rel="stylesheet" type="text/css" href="../../assets/css/owl.carousel.css">
  <link rel="stylesheet" type="text/css" href="../../assets/css/main.css">
  <link rel="stylesheet" type="text/css" href="../../assets/css/responsive.css">
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











  <section class="register section-padding" style="margin-top: 60px;">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-5 col-md-12 col-xs-12">
          <div class="register-form login-area">
            <h3 style="border-bottom: 0 !important;">User Info</h3>
            <div id="registrationDiv">
              <div class="form-group">
                <label for="username">Username</label>
                <div class="input-icon">
                  <i class="lni-user"></i>
                  <input type="text" id="username" class="form-control" name="username" placeholder="Username" readonly>
                </div>
                <div class="error-message" id="usernameError"></div>
              </div>
              <div class="form-group">
                <label for="location">Location</label>
                <div class="input-icon">
                  <i class="lni-map-marker"></i>
                  <input type="text" id="location" class="form-control" placeholder="Location" readonly>
                </div>
                <div class="error-message" id="locationError"></div>
              </div>
              <div class="form-group">
                <label for="phone">Phone Number</label>
                <div class="input-icon">
                  <i class="lni-phone"></i>
                  <input type="text" id="phone" class="form-control" placeholder="Phone Number" readonly>
                </div>

              </div>
              <div class="form-group">

                <img id="imagePreview" alt="Image Preview" />
              </div>
              <div class="form-group mb-3">
                <div class="error-message" id="termsError"></div>
              </div>
              <div id="registrationMessage" style="color: red; display: none;" class="text-center"></div>
              <div class="text-center">


              </div>
              <div id="registrationMessage" style="color: red; display: none;"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>


  <!-- Delete Confirmation Modal -->





  <!-- Popup message -->
  <!-- <div id="popupMessage">Registration Sucessfull</div> -->



  <a href="#" class="back-to-top">
    <i class="lni-chevron-up"></i>
  </a>

  <div id="preloader">
    <div class="loader" id="loader-1"></div>
  </div>
  <script data-cfasync="false" src="cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
  <script src="../../assets/js/jquery-min.js"></script>
  <script src="../../assets/js/popper.min.js"></script>
  <script src="../../assets/js/bootstrap.min.js"></script>
  
  <script src="../../assets/js/jquery.counterup.min.js"></script>
  <script src="../../assets/js/waypoints.min.js"></script>
  <script src="../../assets/js/wow.js"></script>
  <script src="../../assets/js/owl.carousel.min.js"></script>
  <script src="../../assets/js/jquery.slicknav.js"></script>
  <script src="../../assets/js/main.js"></script>
  <script src="../../assets/js/form-validator.min.js"></script>
  <script src="../../assets/js/contact-form-script.min.js"></script>
  <script src="../../assets/js/Ajax/changeProfileInAdmin.js"></script>


</body>


</html>