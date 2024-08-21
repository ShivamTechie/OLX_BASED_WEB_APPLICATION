$(document).ready(function () {
  var currentPage = 1;
  var totalPages = 1;
  var totalCount = 0;
  var selectedCategory = new URLSearchParams(window.location.search).get(
    "categoryId"
  );
  var searchQuery = new URLSearchParams(window.location.search).get("search");

  // Fetch and display categories with product counts
  $.ajax({
    url: "/becho2/App/public/main.php",
    type: "GET",
    data: { action: "getCategorieswithcount" },
    success: function (response) {
      if (response.status === "success") {
        const categories = response.categories;
        const $categoriesList = $(".categories-list");
        $categoriesList.empty();

        // Add "All Categories" option
        const allCategoriesItem = `
                  <li>
                    <a href="#" class="category-link" data-id="">
                      All Categories
                    </a>
                  </li>
                `;
        $categoriesList.append(allCategoriesItem);

        // Append other categories
        categories.forEach(function (category) {
          const categoryItem = `
                      <li>
                        <a href="#" class="category-link" data-id="${category.category_id}">
                          ${category.category_name} 
                          <span class="category-counter">(${category.count})</span>
                        </a>
                      </li>
                    `;
          $categoriesList.append(categoryItem);
        });

        // Fetch products for the selected category or all products initially
        fetchProducts(currentPage, selectedCategory, searchQuery);
      } else {
        console.error("Error fetching categories:", response.message);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", status, error);
    },
  });

  // Click event handler for category links
  $(".categories-list").on("click", ".category-link", function (e) {
    e.preventDefault();

    selectedCategory = $(this).data("id") || null; // Set to null for "All Categories"
    currentPage = 1; // Reset to the first page when changing category
    searchQuery = ""; // Reset search query when changing category

    // Update the page title based on the selected category
    var categoryName = $(this)
      .contents()
      .filter(function () {
        return this.nodeType === 3; // NodeType 3 is the text node (ignores elements like <span>)
      })
      .text()
      .trim();
    document.title = "NexusPlus: " + categoryName;

    // Fetch products for the selected category
    fetchProducts(currentPage, selectedCategory);
  });

  // Update the selector to match your form's id
  $(".individual-search").on("submit", function (e) {
    e.preventDefault();
    var searchQuery = $(this).find("input[name='query']").val().trim();

    fetchProducts(1, null, searchQuery);
    $(this).find("input[name='query']").val("");
  });

  function fetchProducts(page, category = null, searchQuery = "") {
    $.ajax({
      url: "/becho2/App/public/main.php",
      data: {
        action: searchQuery
          ? "searchProducts"
          : category
          ? "getProductsByCategory"
          : "getAllProducts",
        page: page,
        categoryId: category,
        query: searchQuery, // Include the search query
      },
      type: "GET",
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          var products = response.products;
          var gridViewHtml = "";
          var listViewHtml = "";

          if (products.length === 0) {
            // No products found
            gridViewHtml = `
                        <div class="col-12">
                            <p class="text-center">No products found. Try something else...</p>
                        </div>`;
            listViewHtml = gridViewHtml;
          } else {
            products.forEach(function (product) {
              var productHtml = `
                           <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 card-box">
                                <div class="featured-box">
                                    <figure class="d-flex justify-content-center" style="height:239px;">
                                        <div class="icon">
                                            <span class="bg-green"><i class="lni-heart like-icon" data-id="${
                                              product.listing_id
                                            }"></i></span>
                                            <span><i class="lni-bookmark save-icon" data-id="${
                                              product.listing_id
                                            }"></i></span>
                                        </div>
                                        <a href="#" class="product-link" data-id="${
                                          product.listing_id
                                        }">
                                            <img class="img-fluid" src="../../becho2/assets/img/product/${
                                              product.image_path
                                            }" alt="" style="height:100%;"/>
                                        </a>
                                    </figure>
                                    <div class="feature-content">
                                        <div class="product">
                                            <a href="#" class="product-link" data-id="${
                                              product.listing_id
                                            }">
                                                ${product.category_name} >
                                            </a>
                                            <a href="#" class="product-link" data-id="${
                                              product.listing_id
                                            }">
                                                ${
                                                  product.brand ||
                                                  "Unknown Brand"
                                                }
                                            </a>
                                        </div>
                                        <h4>
                                            <a href="#" class="product-link" data-id="${
                                              product.listing_id
                                            }">
                                                ${product.title}
                                            </a>
                                        </h4>
                                        <div class="meta-tag">
                                            <span><a href="#"><i class="lni-user"></i> ${
                                              product.username
                                            }</a></span>
                                            <span><a href="#"><i class="lni-map-marker"></i> ${
                                              product.location
                                            }</a></span>
                                            <span><a href="#"><i class="lni-tag"></i> ${
                                              product.category_name
                                            }</a></span>
                                        </div>
                                        <p class="dsc">${product.description.substring(
                                          0,
                                          35
                                        )}</p>
                                        <div class="listing-bottom">
                                            <h3 class="price float-left"><i class="fa-solid fa-indian-rupee-sign"></i>${
                                              product.price
                                            }</h3>
                                            <a href="#" class="btn btn-common float-right product-link" data-id="${
                                              product.listing_id
                                            }">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

              // Append to both views
              gridViewHtml += productHtml;
              listViewHtml += productHtml;
            });
          }

          // Insert HTML into the DOM
          $("#grid-view .row").html(gridViewHtml);
          $("#list-view .row").html(listViewHtml);

          // Update pagination and short-name
          totalCount = response.totalCount;
          totalPages = response.totalPages;
          updatePagination(response.currentPage, totalPages, totalCount);

          // Adjust view styles
          adjustViewStyles();
        } else {
          console.error("Error fetching products:", response.message);
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX error:", status, error);
      },
    });
  }

  function updatePagination(currentPage, totalPages, totalCount) {
    var paginationHtml = "";

    if (currentPage > 1) {
      paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${
        currentPage - 1
      }">Previous</a></li>`;
    }

    for (var i = 1; i <= totalPages; i++) {
      paginationHtml += `<li class="page-item ${
        i === currentPage ? "active" : ""
      }">
                                <a class="page-link" href="#" data-page="${i}">${i}</a>
                             </li>`;
    }

    if (currentPage < totalPages) {
      paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${
        currentPage + 1
      }">Next</a></li>`;
    }

    $(".pagination").html(paginationHtml);

    // Update the short-name div
    $(".short-name span").text(
      `Showing (${(currentPage - 1) * 10 + 1} - ${Math.min(
        currentPage * 10,
        totalCount
      )} products of ${totalCount} products)`
    );
  }

  // Event handler for pagination clicks
  $(document).on("click", ".pagination .page-link", function (e) {
    e.preventDefault();
    var page = $(this).data("page");
    if (page) {
      currentPage = page;
      fetchProducts(currentPage, selectedCategory, searchQuery);
    }
  });

  // Event handler for product link clicks
  $(document).on("click", ".product-link", function (e) {
    e.preventDefault();
    var productId = $(this).data("id");
    if (productId) {
      window.location.href = "../../becho2/ads-details?id=" + productId;
    }
  });

  // Function to adjust styles based on the active view
  function adjustViewStyles() {
    if ($(".nav-link.active").attr("href") === "#list-view") {
      $(".card-box").addClass("card-box-adjust");
      $(".feature-content").addClass("feature-content-adjust");
    } else {
      $(".card-box").removeClass("card-box-adjust");
      $(".feature-content").removeClass("feature-content-adjust");
    }
  }

  // Call adjustViewStyles after initial fetch
  $(window).on("load", function () {
    adjustViewStyles();
  });

  // Adjust view styles when view is switched
  $(".nav-link").on("click", function () {
    // Delay adjustment to ensure view switch is complete
    setTimeout(adjustViewStyles, 100);
  });

  $(document).on("click", ".like-icon", function () {
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

  // Use event delegation to handle save-icon clicks
  $(document).on("click", ".save-icon", function () {
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
