<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start(); // Start the session if it hasn't been started already
}

// Determine the current page
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="top-bar">
  <div class="container">
    <div class="row">
      <div class="col-lg-7 col-md-5 col-xs-12">

        <ul class="list-inline">
          <li><i class="lni-phone"></i> +0123 456 789</li>
          <li><i class="lni-envelope"></i> <a href="http://preview.uideck.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="5e2d2b2e2e312c2a1e39333f3732703d3133">[email&#160;protected]</a></li>
        </ul>

      </div>
      <div class="col-lg-5 col-md-7 col-xs-12">
        <div class="roof-social float-right">
          <a class="facebook" href="https://www.facebook.com/olxglobal" target="_blank" rel="noopener noreferrer"><i class="lni-facebook-filled"></i></a>
          <a class="twitter" href="https://twitter.com/olx" target="_blank" rel="noopener noreferrer"><i class="lni-twitter-filled"></i></a>
          <a class="instagram" href="https://www.instagram.com/olx" target="_blank" rel="noopener noreferrer"><i class="lni-instagram-filled"></i></a>
          <a class="linkedin" href="https://www.linkedin.com/company/olx" target="_blank" rel="noopener noreferrer"><i class="lni-linkedin-fill"></i></a>
          <a class="google" href="https://plus.google.com/103676242055150793331" target="_blank" rel="noopener noreferrer"><i class="lni-google-plus"></i></a>

        </div>
        <div class="header-top-right float-right">
          <?php if (isset($_SESSION['username'])) : ?>
            <div class="dropdown">
              <a href="#" class="header-top-button dropdown-toggle" id="profileDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="lni-user"></i> <?php echo $_SESSION['username'] ?>
              </a>
              <div class="dropdown-menu" aria-labelledby="profileDropdown" style="z-index: 100000;">
                <a class="dropdown-item" href="UserDashboard"><i class="lni-user"></i> Account Info</a>
                <a class="dropdown-item" href="changePassword"><i class="lni-lock"></i> Change Password</a>

                <a class="dropdown-item" href="logout"><i class="lni-exit"></i> Logout</a>
              </div>
            </div>
          <?php else : ?>
            <a href="login" class="header-top-button"><i class="lni-lock"></i> Log In</a> |
            <a href="register" class="header-top-button"><i class="lni-pencil"></i> Register</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<nav class="navbar navbar-expand-lg bg-white fixed-top scrolling-navbar" style="border-bottom: 1px solid black;">
  <div class="container">

    <div class="navbar-header">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-navbar" aria-controls="main-navbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        <span class="lni-menu"></span>
        <span class="lni-menu"></span>
        <span class="lni-menu"></span>
      </button>
      <a href="../becho2/" class="navbar-brand"><img src="assets/img/logo.png" alt=""></a>
    </div>
    <div class="collapse navbar-collapse" id="main-navbar">
      <ul class="navbar-nav mr-auto w-100 justify-content-center">
        <li class="nav-item <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
          <a class="nav-link " href="../becho2/">Home</a>
        </li>
        <li class="nav-item <?php echo ($current_page == 'All-Products.php') ? 'active' : ''; ?>">
          <a class="nav-link " href="All-Products">All Products</a>
        </li>
        <li class="nav-item <?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">
          <a class="nav-link " href="about">ABOUT</a>
        </li>
        <li class="nav-item    <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">
          <a class="nav-link" href="contact">CONTACT</a>
        </li>
        <li class="nav-item   <?php echo ($current_page == 'blog.php') ? 'active' : ''; ?>">
          <a class="nav-link" href="blog">BLOGS</a>
        </li>
      </ul>
      <div class="post-btn">
        <a class="btn btn-common" href="post-ads"><i class="lni-pencil-alt"></i> Post an Ad</a>
      </div>
    </div>
  </div>

  <ul class="mobile-menu">
    <li class="nav-item <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
      <a class="nav-link " href="../becho2/">Home</a>
    </li>
    <li class="nav-item <?php echo ($current_page == 'All-Products.php') ? 'active' : ''; ?>">
      <a class="nav-link " href="All-Products">All Products</a>
    </li>
    <li class="nav-item <?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">
      <a class="nav-link " href="about">ABOUT</a>
    </li>
    <li class="nav-item <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">
      <a class="nav-link " href="contact">CONTACT</a>
    </li>
    <li class="nav-item  <?php echo ($current_page == 'blog.php') ? 'active' : ''; ?>">
      <a class="nav-link" href="blog">BLOGS</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="post-ads" style="background-color: #E91E63; color:white;"><i class="lni-pencil-alt"></i> Post an Ad</a>
    </li>
  </ul>
</nav>