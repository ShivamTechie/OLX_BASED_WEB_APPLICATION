<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  // Start the session if it's not already started
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }

  // Check if full_name is set in the session
  if (!isset($_SESSION['full_name'])) {
    // Redirect to login page if full_name is not set
    header("Location: admin-login.php");
    exit();
  }
  ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NexusPlus - Admin Panel</title>

  <link rel="stylesheet" type="text/css" href="../../assets/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="../../assets/fonts/line-icons.css">
  <link rel="stylesheet" type="text/css" href="../../assets/css/slicknav.css">
 
  <link rel="stylesheet" type="text/css" href="../../assets/css/animate.css">
  <link rel="stylesheet" type="text/css" href="../../assets/css/owl.carousel.css">
  <link rel="stylesheet" type="text/css" href="../../assets/css/main.css">
  <link rel="stylesheet" type="text/css" href="../../assets/css/responsive.css">

  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

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
      color: white;
      padding: 2px 5px;
      border-radius: 5px;
    }

    .adstatussold {
      background-color: grey;
      color: white;
      padding: 2px 5px;
      border-radius: 5px;
    }

    .adstatusexpired {
      background-color: red;
      color: white;
      padding: 2px 5px;
      border-radius: 5px;
    }

    #status {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 53px 0px;
    }
  </style>
</head>

<body>
  <div id="content" class="section-padding">
    <div class="container">
      <div class="row">
        <div class="col-sm-12 col-md-4 col-lg-3 page-sidebar">
          <aside>
            <div class="sidebar-box">
              <div class="user d-flex align-items-center flex-column">
                <figure>
                  <img src="../../assets/img/admin/" alt="" id="user-image" />
                </figure>
                <div class="usercontent">
                  <!-- Display full_name and "Admin" label -->
                  <h3><?php echo htmlspecialchars($_SESSION['full_name']); ?></h3>
                  <h3>Admin</h3>
                </div>
              </div>
              <nav class="navdashboard">
                <ul>
                  <li>
                    <a class="active" href="#">
                      <i class="lni-dashboard"></i>
                      <span>Dashboard</span>
                    </a>
                  </li>

                  <li>
                    <a href="logout.php">
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
                          <h3></h3>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <table id="adminTable" class="table table-responsive dashboardtable tablemyads">
                  <thead>
                    <tr>
                      <th class="text-center">Owner</th>
                      <th class="text-center">Product Image</th>
                      <th class="text-center">Product Title</th>
                      <th class="text-center">Status</th>
                      <th class="text-center">Category</th>
                      <th class="text-center">Price</th>
                      <th class="text-center">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Dynamic content will be loaded here via AJAX -->
                  </tbody>
                </table>
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
          <h5 class="modal-title" id="deleteConfirmationLabel">Confirm Deletion</h5>
          <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this listing?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
        </div>
      </div>
    </div>
  </div>

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
  <script src="../../assets/js/summernote.js"></script>
  <script src="../../assets/js/Ajax/admin-dashboard.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

</body>

</html>