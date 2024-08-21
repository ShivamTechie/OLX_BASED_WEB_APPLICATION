  <?php
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
  ?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>NexusPlus - Admin: Edit Ads</title>

    <link rel="stylesheet" type="text/css" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../../assets/fonts/line-icons.css">
    <link rel="stylesheet" type="text/css" href="../../assets/css/slicknav.css">

    <link rel="stylesheet" type="text/css" href="../../assets/css/summernote.css">
    <link rel="stylesheet" type="text/css" href="../../assets/css/animate.css">
    <link rel="stylesheet" type="text/css" href="../../assets/css/owl.carousel.css">
    <link rel="stylesheet" type="text/css" href="../../assets/css/main.css">
    <link rel="stylesheet" type="text/css" href="../../assets/css/responsive.css">
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
    </style>
  </head>

  <body>


    <div id="content" class="section-padding">
      <div class="container">
        <div class="row">
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
                          <!-- Options will be populated dynamically -->
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
                    <div class="form-group mb-3 tg-inputwithicon">
                      <label class="control-label">Status</label>
                      <div class="tg-select form-control">
                        <select id="Status">
                          <option value="active">active</option>
                          <option value="Not Active">Not Active</option>
                          <option value="Sold">Sold</option>
                        </select>
                      </div>
                    </div>
                    <label class="tg-fileuploadlabel mt-4" for="tg-photogallery">
                      <span>Select 3 Images for your Product</span>
                      <span>Or</span>
                      <span class="btn btn-common">Select Files</span>
                      <input id="tg-photogallery" class="tg-fileinput" type="file" name="file[]" accept="image/*" multiple>

                    </label>
                    <div id="existing-images">
                      <!-- Existing images will be displayed here -->
                    </div>
                  </div>
                </div>
              </div>
              <!-- Contact Details Section -->
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-5">
                <div class="inner-box">
                  <div class="tg-contactdetail">
                    <div class="dashboard-box">
                      <h2 class="dashbord-title">Contact Detail</h2>
                    </div>
                    <div class="dashboard-wrapper">
                      <div class="form-group mb-3">
                        <label class="control-label">Name*</label>
                        <input class="form-control input-md" name="name" id="name" type="text" readonly>
                        <span class="error" id="name-error">This field is required.</span>
                      </div>
                      <div class="form-group mb-3">
                        <label class="control-label">Phone*</label>
                        <input class="form-control input-md" name="phone" id="phone" type="text" readonly>
                        <span class="error" id="phone-error">This field is required.</span>
                      </div>
                      <div class="form-group mb-3">
                        <label class="control-label">Enter Full Address</label>
                        <input class="form-control input-md" name="address" id="address" type="text">
                        <span class="error" id="address-error">This field is required.</span>
                      </div>
                      <div class="error-msg">

                      </div>
                      <button class="btn btn-common" id="postAdBtn" type="button">Apply Changes</button>
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
            <h5 class="modal-title" id="deleteConfirmationLabel">Confirm Changes</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
          </div>
          <div class="modal-body">
            Are you sure you want to Change Info of this Ad?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" id="confirmDelete">Apply</button>
          </div>
        </div>
      </div>
    </div>



    <a href="#" class="back-to-top">
      <i class="lni-chevron-up"></i>
    </a>
    <div id="preloader">
      <div class="loader" id="loader-1"></div>
    </div>

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
    <!-- <script src="assets/js/Ajax/postAd.js"></script> -->
    <script src="../../assets/js/Ajax/categories.js"></script>
    <script src="../../assets/js/Ajax/updateProductAdmin.js"></script>

    <script>
      $(document).ready(function() {
        // Fetch categories dynamically
        // $.ajax({
        //   url: '/becho2/App/public/main.php', // Endpoint to fetch categories
        //   method: 'GET',
        //   data: {
        //     action: 'getCategories'
        //   },
        //   dataType: 'json',
        //   success: function(response) {
        //     if (response.status === "success") {
        //       var categories = response.categories;
        //       var categoriesSelect = $('#categories');

        //       // Clear any existing options
        //       categoriesSelect.empty();

        //       // Add default option
        //       categoriesSelect.append('<option value="none">Select Categories</option>');

        //       // Populate categories from the response
        //       categories.forEach(function(category) {
        //         categoriesSelect.append(`<option value="${category.category_name}">${category.category_name}</option>`);
        //       });
        //     } else {
        //       console.log(response.message);
        //     }
        //   },
        //   error: function(xhr, status, error) {
        //     console.log("AJAX Error: " + status + error);
        //   }
        // });

        // Assume the listing_id is passed in the URL or otherwise available
        var listingId = getQueryParam('listing_id');

        // AJAX request to fetch product data
        $.ajax({
          url: '/becho2/App/public/main.php', // Replace with your PHP file that fetches the product data
          method: 'GET',
          data: {
            action: "getProductDetaislForEdit",
            listing_id: listingId
          },
          dataType: 'json',
          success: function(response) {
            if (response.status === "success") {
              var product = response.product;
              var images = response.images;

              // Populate form fields
              $('#Title').val(product.title);
              $('#price').val(product.price);
              $('#description').val(product.description);
              $('#specifications').val(product.specifications.replace(/,/g, '\n')); // Convert <br> to new lines
              $('#categories').val(product.category_name); // Ensure this matches the value in the dropdown
              $('#condition').val(product.condition);
              $('#brand').val(product.brand);
              $('#Status').val(product.status);
              $('#name').val(product.user_name);
              $('#phone').val(product.user_phone);
              $('#address').val(product.location); // Make sure `location` is a valid field in the response

              // Display existing images
              var imagesHtml = '';
              images.forEach(function(image) {
                imagesHtml += `<img src="../../assets/img/product/${image.image_path}" alt="Product Image" style="width: 100px; height: auto; margin: 5px;">`;
              });
              $('#existing-images').html(imagesHtml);
            } else {
              console.log(response.message);
            }
          },
          error: function(xhr, status, error) {
            console.log("AJAX Error: " + status + error);
          }
        });

        // Helper function to get query parameters
        function getQueryParam(param) {
          var urlParams = new URLSearchParams(window.location.search);
          return urlParams.get(param);
        }
      });
    </script>
  </body>

  </html>