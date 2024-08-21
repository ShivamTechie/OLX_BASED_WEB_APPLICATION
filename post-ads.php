  <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['username'])) {
        header("Location: login");
        exit();
    }
    ?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <title>NexusPlus - Post Ads</title>
      <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
      <link rel="stylesheet" type="text/css" href="assets/fonts/line-icons.css">
      <link rel="stylesheet" type="text/css" href="assets/css/slicknav.css">

      <link rel="stylesheet" type="text/css" href="assets/css/summernote.css">
      <link rel="stylesheet" type="text/css" href="assets/css/animate.css">
      <link rel="stylesheet" type="text/css" href="assets/css/owl.carousel.css">
      <link rel="stylesheet" type="text/css" href="assets/css/main.css">
      <link rel="stylesheet" type="text/css" href="assets/css/responsive.css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
      <style>
          .error {
              color: red;
              display: none;
          }

          .user figure {
              width: 120px;
              height: 120px;
              overflow: hidden;
              /* Hide any part of the image that overflows */
              margin: 0;
              padding: 0;
              border-radius: 50%;
              /* Makes the figure circular */
              display: flex;
              align-items: center;
              justify-content: center;
              position: relative;
              /* Position relative for any potential adjustments */
          }

          .user figure img {
              background-color: whitesmoke;
              width: 100%;
              height: 100%;
              object-fit: contain;
              /* Ensures the image covers the circle */
              border-radius: 50%;
              /* Makes the image fit within the circular div */
          }

          .preview-image {
              width: 100px;
              height: 100px;
              object-fit: cover;
              margin: 5px;
          }
      </style>
  </head>

  <body>
      <header id="header-wrap">
          <?php include 'header.php'; ?>
      </header>

      <div class="page-header" style="background: url(assets/img/banner1.jpg);">
          <div class="container">
              <div class="row">
                  <div class="col-md-12">
                      <div class="breadcrumb-wrapper">
                          <h2 class="product-title">Post your Ads</h2>
                          <ol class="breadcrumb">
                              <li><a href="#">Home /</a></li>
                              <li class="current">Post your Ads</li>
                          </ol>
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <div id="content" class="section-padding">
          <div class="container">
              <div class="row">
                  <div class="col-sm-12 col-md-4 col-lg-3 page-sidebar">
                      <aside>
                          <div class="sidebar-box">
                              <div class="user d-flex align-items-center flex-column">
                                  <figure>
                                      <img src="assets/img/author/img1.jpg" alt="" id="user-image">
                                  </figure>
                                  <div class="usercontent" id="user-name">
                                      <h3><?php echo $_SESSION['username'] ?></h3>
                                  </div>
                              </div>
                              <nav class="navdashboard">
                                  <ul>

                                      <li>
                                          <a href="userProfile">
                                              <i class="lni-cog"></i>
                                              <span>Profile Settings</span>
                                          </a>
                                      </li>
                                      <li>
                                          <a href="UserDashboard">
                                              <i class="lni-layers"></i>
                                              <span>My Ads</span>
                                          </a>
                                      </li>
                                      <li>
                                          <a href="logout">
                                              <i class="lni-enter"></i>
                                              <span>Logout</span>
                                          </a>
                                      </li>
                                  </ul>
                              </nav>
                          </div>
                      </aside>
                  </div>
                  <div class="col-sm-12 col-md-8 col-lg-9">
                      <div class="row page-content">
                          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-7">
                              <div class="inner-box">
                                  <div class="dashboard-box">
                                      <h2 class="dashbord-title">Ad Detail</h2>
                                  </div>
                                  <div class="dashboard-wrapper">
                                      <div class="form-group mb-3">
                                          <label class="control-label">Product Title</label>
                                          <input class="form-control input-md" name="Title" id="Title" placeholder="Title" type="text">
                                          <span class="error" id="title-error">This field is required.</span>
                                      </div>
                                      <div class="form-group mb-3 tg-inputwithicon">
                                          <label class="control-label">Categories</label>
                                          <div class="tg-select form-control">
                                              <select id="categories" name="category">
                                                  <option value="none">Select Categories</option>
                                                  <option value="Mobiles">Mobiles</option>
                                                  <option value="Electronics">Electronics</option>
                                                  <option value="Training">Training</option>
                                                  <option value="Real Estate">Real Estate</option>
                                                  <option value="Services">Services</option>
                                                  <option value="Vehicles">Vehicles</option>
                                              </select>
                                          </div>
                                          <span class="error" id="categories-error">This field is required.</span>
                                      </div>
                                      <div class="form-group mb-3">
                                          <label class="control-label">Price</label>
                                          <input class="form-control input-md" name="price" id="price" placeholder="Add your Price" type="text">
                                          <span class="error" id="price-error">This field is required.</span>
                                      </div>
                                      <div class="form-group md-3">
                                          <label class="control-label">Description</label>
                                          <textarea name="description" class="form-control" id="description"></textarea>
                                          <span class="error" id="description-error">This field is required.</span>
                                      </div>
                                      <div class="form-group md-3">
                                          <label class="control-label">Specifications</label>
                                          <textarea name="specifications" class="form-control" id="specifications"></textarea>
                                          <span class="error" id="specifications-error">This field is required.</span>
                                      </div>
                                      <div class="form-group mb-3">
                                          <label class="control-label">Condition</label>
                                          <input class="form-control input-md" name="condition" id="condition" placeholder="Specified condition" type="text">
                                          <span class="error" id="condition-error">This field is required.</span>
                                      </div>
                                      <div class="form-group mb-3">
                                          <label class="control-label">Brand</label>
                                          <input class="form-control input-md" name="brand" id="brand" placeholder="Name of brand" type="text">
                                          <span class="error" id="brand-error">This field is required.</span>
                                      </div>
                                      <label class="tg-fileuploadlabel" for="tg-photogallery">
                                          <span>Select  Images for your Product</span>
                                          <span>Or</span>
                                          <span class="btn btn-common">Select Files</span>
                                          <input id="tg-photogallery" class="tg-fileinput" type="file" name="file[]" accept="image/*" multiple>
                                          <span class="error" id="file-error">Please select 3 images.</span>
                                      </label>
                                      <div id="existing-images">
                                          <!-- Existing images will be displayed here -->
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-5">
                              <div class="inner-box">
                                  <div class="tg-contactdetail">
                                      <div class="dashboard-box">
                                          <h2 class="dashbord-title">Contact Detail</h2>
                                      </div>
                                      <div class="dashboard-wrapper">
                                          <div class="form-group mb-3">
                                              <label class="control-label">Name*</label>
                                              <input class="form-control input-md" name="name" id="name" type="text">
                                              <span class="error" id="name-error">This field is required.</span>
                                          </div>
                                          <div class="form-group mb-3">
                                              <label class="control-label">Phone*</label>
                                              <input class="form-control input-md" name="phone" id="phone" type="text">
                                              <span class="error" id="phone-error">This field is required.</span>
                                          </div>
                                          <div class="form-group mb-3">
                                              <label class="control-label">Enter Full Address</label>
                                              <input class="form-control input-md" name="address" id="address" type="text">
                                              <span class="error" id="address-error">This field is required.</span>
                                          </div>



                                          <div class="tg-checkbox">
                                              <div class="custom-control custom-checkbox">
                                                  <input type="checkbox" class="custom-control-input" id="tg-agreetermsandrules">
                                                  <label class="custom-control-label" for="tg-agreetermsandrules">I agree to all <a href="javascript:void(0);">Terms of Use &amp; Posting Rules</a></label>
                                              </div>
                                          </div>
                                          <span class="error" id="form-error">All fields are required.</span>
                                          <button class="btn btn-common" id="postAdBtn" type="button">Post Ad</button>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationLabel" aria-hidden="true" style="top:110px !important;">
          <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title" id="deleteConfirmationLabel">Confirm Post</h5>
                      <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                  </div>
                  <div class="modal-body">
                      Are you sure you want to Post this Ad?
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                      <button type="button" class="btn btn-danger" id="confirmDelete">Post</button>
                  </div>
              </div>
          </div>
      </div>





      <?php include 'footer.php'; ?>

      <a href="#" class="back-to-top">
          <i class="lni-chevron-up"></i>
      </a>

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
      <script src="assets/js/Ajax/postAd.js"></script>
      <script src="assets/js/Ajax/categories.js"></script>
  </body>

  </html>