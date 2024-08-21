$(document).ready(function () {
  // Fetch the latest products from random categories
  $.ajax({
    url: "/becho2/App/public/main.php?action=getLatestProductsFromRandomCategories",
    method: "GET",
    dataType: "json",
    success: function (response) {
      console.log("AJAX response:", response);

      if (response.status === "success") {
        var products = response.products;
        var html = "";

        // Generating product cards with like and save buttons
        $.each(products, function (index, product) {
          html += '<div class="col-xs-6 col-sm-6 col-md-6 col-lg-4" >';
          html += '    <div class="featured-box">';
          html += "        <figure style='height:190px;'>";
          html += '            <div class="icon">';
          html +=
            '                <span class="bg-green like-icon" data-id="' +
            product.listing_id +
            '"><i class="lni-heart"></i></span>';
          html +=
            '                <span class="save-icon" data-id="' +
            product.listing_id +
            '"><i class="lni-bookmark"></i></span>';
          html += "            </div>";
          html +=
            '            <a href="ads-details.html" class="product-link" data-id="' +
            product.listing_id +
            '">';
          html +=
            '                <img class="img-fluid" src="' +
            product.image_path +
            '" alt="" style="height:100%;width:100%;object-fit:fill;backgrund-color:ghostwhite;">';
          html += "            </a>";
          html += "        </figure>";
          html += '        <div class="feature-content">';
          html += '            <div class="product">';
          html +=
            '                <a href="ads-details.html" class="product-link" data-id="' +
            product.listing_id +
            '">' +
            product.category_name +
            " > </a>";
          html += "            </div>";
          html +=
            '            <h4><a href="ads-details.html" class="product-link" data-id="' +
            product.listing_id +
            '">' +
            product.title.substring(0, 30) +
            "...</a></h4>";
          html += '            <div class="meta-tag">';
          html +=
            '                <span><a href="#"><i class="lni-user"></i> ' +
            product.username +
            "</a></span>";
          html +=
            '                <span><a href="#"><i class="lni-map-marker"></i> ' +
            product.location +
            "</a></span>";
          html += "            </div>";
          html +=
            '            <p class="dsc" style="  overflow: hidden;max-width:200px;height:25px;">' +
            product.description +
            "</p>";
          html += '            <div class="listing-bottom">';
          html +=
            '                <h3 class="price float-left"><i class="fa-solid fa-indian-rupee-sign"></i>' +
            parseFloat(product.price).toFixed(2) +
            "</h3>";
          html +=
            '                <a href="ads-details.html" class="btn btn-common float-right product-link" data-id="' +
            product.listing_id +
            '">View Details</a>';
          html += "            </div>";
          html += "        </div>";
          html += "    </div>";
          html += "</div>";
        });

        $(".featured .row").html(html);
      } else {
        console.log(
          "Error fetching products. Status: " +
            response.status +
            ", Message: " +
            response.message
        );
      }
    },
    error: function (xhr, status, error) {
      console.log("AJAX error: " + status + " - " + error);
    },
  });

  // Event listener for liking a product
  $(".featured").on("click", ".like-icon", function () {
    var productId = $(this).data("id");
    $.ajax({
      url: "/becho2/App/public/main.php",
      method: "POST",
      data: { action: "likedProduct", product_id: productId },
      success: function (response) {
        if (response.status === "success") {
          Swal.fire({
            icon: "success",
            title: response.message,
            showConfirmButton: false,
            timer: 1000,
          });
        } else {
          Swal.fire({
            icon: "error",
            title: response.message,
            showConfirmButton: false,
            timer: 1000,
          });
        }
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
  $(".featured").on("click", ".save-icon", function () {
    var productId = $(this).data("id");
    $.ajax({
      url: "/becho2/App/public/main.php",
      method: "POST",
      data: { action: "savedProduct", product_id: productId },
      success: function (response) {
        if (response.status === "success") {
          Swal.fire({
            icon: "success",
            title: response.message,
            showConfirmButton: false,
            timer: 1000,
          });
        } else {
          Swal.fire({
            icon: "error",
            title: response.message,
            showConfirmButton: false,
            timer: 1000,
          });
        }
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

  // Event listener for viewing product details
  $(".featured").on("click", ".product-link", function (e) {
    e.preventDefault();
    var productId = $(this).data("id");
    window.location.href = "../../becho2/ads-details.php?id=" + productId;
  });
});
