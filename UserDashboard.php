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
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>NexusPlus - User DashBoard</title>

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css" />

  <!-- Icon Fonts -->
  <link rel="stylesheet" type="text/css" href="assets/fonts/line-icons.css" />

  <!-- Additional CSS Files -->
  <link rel="stylesheet" type="text/css" href="assets/css/slicknav.css" />
  <link rel="stylesheet" type="text/css" href="assets/css/animate.css" />
  <link rel="stylesheet" type="text/css" href="assets/css/owl.carousel.css" />
  <link rel="stylesheet" type="text/css" href="assets/css/main.css" />
  <link rel="stylesheet" type="text/css" href="assets/css/responsive.css" />

  <!-- DataTables CSS -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <!-- SweetAlert2 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

  <style>
    .user figure {
      width: 120px;
      height: 120px;
      overflow: hidden;
      margin: 0;
      padding: 0;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
    }

    .user figure img {
      background-color: whitesmoke;
      width: 100%;
      height: 100%;
      object-fit: contain;
      border-radius: 50%;
    }

    .adstatusactive {
      background-color: green;
    }

    .adstatussold {
      background-color: grey;
    }

    .adstatusexpired {
      background-color: red;
    }
  </style>
</head>

<body>
  <header id="header-wrap">
    <?php include 'header.php'; ?>
  </header>

  <div id="content" class="section-padding" style="margin-top: 100px;">
    <div class="container">
      <div class="row">
        <div class="col-sm-12 col-md-4 col-lg-3 page-sidebar">
          <aside>
            <div class="sidebar-box">
              <div class="user d-flex align-items-center flex-column">
                <figure>
                  <img src="assets/img/author/img1.jpg" alt="" id="user-image" />
                </figure>
                <div class="usercontent">
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
                    <a href="showPostedAdd" id="showPostedAds">
                      <i class="lni-bullhorn"></i>
                      <span>Posted Ads</span>
                    </a>
                  </li>
                  <li>
                    <a href="likedProducts" id="showLikedProducts">
                      <i class="lni-heart"></i>
                      <span>Liked Products</span>
                    </a>
                  </li>
                  <li>
                    <a href="savedProducts" id="showSavedProducts">
                      <i class="lni-bookmark"></i>
                      <span>Saved Products</span>
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
          <div class="page-content">
            <div class="inner-box">
              <div class="dashboard-box">
                <h2 class="dashbord-title">Dashboard</h2>
              </div>
              <div class="dashboard-wrapper">
                <div class="dashboard-sections">
                  <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-4">
                      <div class="dashboardbox">
                        <div class="icon"><i class="lni-write"></i></div>
                        <div class="contentbox">
                          <h2><a href="#">Total Ad Posted</a></h2>
                          <h3 id="totalAds">0</h3>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <table class="table table-responsive dashboardtable tablemyads">
                  <thead>
                    <tr>
                      <th style="padding: 15px 40px !important;">Photo</th>
                      <th style="padding: 15px 40px !important;">Title</th>
                      <th style="padding: 15px 40px !important;">Category</th>
                      <th style="padding: 15px 40px !important;">Ad Status</th>
                      <th style="padding: 15px 40px !important;">Price</th>
                      <th style="padding: 15px 40px !important;">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Dynamic content will be inserted here -->
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal for delete confirmation -->
  <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationLabel" aria-hidden="true" style="top:110px !important;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteConfirmationLabel">Confirm Deletion</h5>
          <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this Listing?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
        </div>
      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>

  <a href="#" class="back-to-top">
    <i class="lni-chevron-up"></i>
  </a>

  <!-- Scripts -->
  <script data-cfasync="false" src="cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
  <script src="assets/js/jquery-min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>

  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

  <!-- Additional JS Libraries -->
  <script src="assets/js/jquery.counterup.min.js"></script>
  <script src="assets/js/waypoints.min.js"></script>
  <script src="assets/js/wow.js"></script>
  <script src="assets/js/owl.carousel.min.js"></script>
  <script src="assets/js/jquery.slicknav.js"></script>
  <script src="assets/js/main.js"></script>
  <script src="assets/js/form-validator.min.js"></script>
  <script src="assets/js/contact-form-script.min.js"></script>
  <script src="assets/js/summernote.js"></script>

  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

  <script src="assets/js/Ajax/userDashBoard.js"></script>
</body>

</html>