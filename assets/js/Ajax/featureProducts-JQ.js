$(document).ready(function () {
  function fetchFeaturedProducts() {
    $.ajax({
      url: "/becho2/App/public/main.php",
      type: "GET",
      data: { action: "getFeaturedProducts" },
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          var products = response.products;
          var newProductsContainer = $("#new-products");

          // Destroy any existing Owl Carousel instance
          newProductsContainer.trigger("destroy.owl.carousel");
          newProductsContainer.html(""); // Clear existing items

          // Populate new items
          $.each(products, function (index, product) {
            var html = "";
            html += '<div class="item">';
            html += '    <div class="product-item">';
            html += '        <div class="carousel-thumb" style="height:190px">';
            html +=
              '            <img class="img-fluid" src="assets/img/product/' +
              product.image_path +
              '" alt="' +
              product.title +
              '" style="height:100%;width:100%;object-fit:fill;">';
            html += '            <div class="overlay">';
            html += "                <div>";
            html +=
              '                    <a class="btn btn-common feature-products" href="ads-details.php" data-id="' +
              product.listing_id +
              '">View Details</a>';
            html += "                </div>";
            html += "            </div>";
            html += '            <div class="btn-product bg-sale">';
            html += '                <a href="#">Sale</a>';
            html += "            </div>";
            html +=
              '            <span class="price">' +
              '<i class="fa-solid fa-indian-rupee-sign"></i>' +
              " " +
              product.price +
              "</span>";
            html += "        </div>";
            html += '        <div class="product-content">';
            html +=
              '            <h3 class="product-title"><a href="ads-details.php" class="feature-products" data-id="' +
              product.listing_id +
              '">' +
              product.title.substring(0, 25) +
              "</a></h3>";
            html += "            <span>" + product.category_name + "</span>";
            html += '            <div class="icon">';
            html +=
              '                <span class="save-icon" data-id="' +
              product.listing_id +
              '"><i class="lni-bookmark"></i></span>';
            html +=
              '                <span class="like-icon" data-id="' +
              product.listing_id +
              '"><i class="lni-heart"></i></span>';
            html += "            </div>";
            html += '            <div class="card-text">';
            html += '                <div class="float-left">';
            html += "                </div>";
            html += '                <div class="float-right">';
            html +=
              '                    <a class="address" href="#"><i class="lni-map-marker"></i> ' +
              product.location +
              "</a>";
            html += "                </div>";
            html += "            </div>";
            html += "        </div>";
            html += "    </div>";
            html += "</div>";

            newProductsContainer.append(html);
          });

          // Initialize Owl Carousel
          newProductsContainer.owlCarousel({
            items: 3,
            loop: true,
            margin: 10,
            nav: true,
            dots: true,
            autoplay: true,
            autoplayTimeout: 3000,
            autoplayHoverPause: true,
          });
        } else {
          console.error("Error fetching products: " + response.message);
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error: " + status + error);
      },
    });
  }

  fetchFeaturedProducts();

  // Handle product details click
  $("#new-products").on("click", ".feature-products", function (e) {
    e.preventDefault();
    var productId = $(this).data("id");
    window.location.href = "../../becho2/ads-details.php?id=" + productId;
  });

  // Event listener for liking a product
  $("#new-products").on("click", ".like-icon", function () {
    var productId = $(this).data("id");
    $.ajax({
      url: "/becho2/App/public/main.php",
      method: "POST",
      data: { action: "likedProduct", product_id: productId },
      success: function (response) {
        Swal.fire({
          icon: response.status === "success" ? "success" : "error",
          title: response.message,
          showConfirmButton: false,
          timer: 1000,
        });
      },
      error: function (xhr, status, error) {
        Swal.fire({
          icon: "error",
          title: "Error liking product",
          text: status + " - " + error,
          showConfirmButton: false,
          timer: 1000,
        });
      },
    });
  });

  // Event listener for saving a product
  $("#new-products").on("click", ".save-icon", function () {
    var productId = $(this).data("id");
    $.ajax({
      url: "/becho2/App/public/main.php",
      method: "POST",
      data: { action: "savedProduct", product_id: productId },
      success: function (response) {
        Swal.fire({
          icon: response.status === "success" ? "success" : "error",
          title: response.message,
          showConfirmButton: false,
          timer: 1000,
        });
      },
      error: function (xhr, status, error) {
        Swal.fire({
          icon: "error",
          title: "Error saving product",
          text: status + " - " + error,
          showConfirmButton: false,
          timer: 1000,
        });
      },
    });
  });
});
