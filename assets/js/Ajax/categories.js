$(document).ready(function () {
  const imageMap = {
    "Books, Sports & Hobbies": "books.png",
    "Electronics & Appliances": "electronic.png",
    Fashion: "fashion.png",
    Furniture: "furnutire.png",
    "Home Decor & Garden": "hnd.png",
    Jobs: "jobs.png",
    Mobiles: "mobiles.png",
    Pets: "pets.png",
    Properties: "properties.png",
    Services: "services.png",
    Vehicles: "vehicles.png",
  };

  // Function to fetch and display categories
  function fetchCategories() {
    $.ajax({
      url: "/becho2/App/public/main.php",
      type: "GET",
      data: { action: "getCategories" },
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          var categories = response.categories;
          var categoryHtml = "";
          var dropdownHtml = '<option value="none">Select Category</option>';

          $.each(categories, function (index, category) {
            var categoryName = category.category_name;
            var imageName = imageMap[categoryName] || "default.png";
            var imagePath = "assets/img/my-category/" + imageName;

            // Generate category card HTML
            categoryHtml += '<div class="item">';
            categoryHtml +=
              '    <a href="#" class="category-link" data-id="' +
              category.category_id +
              '">';
            categoryHtml += '        <div class="category-icon-item">';
            categoryHtml += '            <div class="icon-box">';
            categoryHtml += '                <div class="icon">';
            categoryHtml +=
              '                    <img src="' +
              imagePath +
              '" alt="' +
              categoryName +
              '">';
            categoryHtml += "                </div>";
            categoryHtml += "                <h4>" + categoryName + "</h4>";
            categoryHtml += "            </div>";
            categoryHtml += "        </div>";
            categoryHtml += "    </a>";
            categoryHtml += "</div>";

            // Generate category dropdown HTML
            dropdownHtml +=
              '<option value="' +
              category.category_id +
              '">' +
              categoryName +
              "</option>";
          });

          // Update category card display
          var categoriesContainer = $("#categories-icon-slider");
          categoriesContainer.trigger("destroy.owl.carousel").empty();
          categoriesContainer.html(categoryHtml);
          categoriesContainer.owlCarousel({
            loop: true,
            margin: 10,
            nav: true,
            responsive: {
              0: { items: 2 },
              600: { items: 3 },
              1000: { items: 4 },
            },
          });

          // Populate category dropdown
          var categoryDropdown = $("select[name='category']");
          categoryDropdown.empty(); // Clear existing options
          categoryDropdown.html(dropdownHtml);

          // Attach click event to category links
        $(".category-link").click(function (e) {
    e.preventDefault(); // Prevent default link behavior
    var categoryId = $(this).data("id");
    var page = 1; // Set the page number if needed

    // Extract the category name, ignoring any child elements like <span>
    var categoryName = $(this).contents().filter(function() {
        return this.nodeType === 3; // NodeType 3 is the text node (ignores elements like <span>)
    }).text().trim();

    // Update the document title
    document.title = "NexusPlus: " + categoryName;

    // Fetch products for the category
    fetchProductsByCategory(categoryId, page, function () {
        // After fetching products, redirect to category.php
        window.location.href = "All-products?categoryId=" + categoryId;
    });
});

        } else {
          console.error("Error fetching categories: " + response.message);
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error: " + status + error);
      },
    });
  }

  // Function to fetch and display locations
  function fetchLocations() {
    $.ajax({
      url: "http://api.geonames.org/searchJSON",
      type: "GET",
      data: {
        country: "IN",
        featureCode: "ADM1",
        maxRows: 50,
        username: "shivam2001",
      },
      dataType: "json",
      success: function (response) {
        if (response.geonames) {
          var locations = response.geonames;
          var html = '<option value="none">Select Location</option>';

          $.each(locations, function (index, location) {
            html +=
              '<option value="' +
              location.name +
              '">' +
              location.name +
              "</option>";
          });

          $("select[name='location']").html(html); // Populate the location dropdown
        } else {
          console.error("Error fetching locations.");
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error: " + status + error);
      },
    });
  }

  // Function to fetch products by category
  function fetchProductsByCategory(categoryId, page, callback) {
    $.ajax({
      url: "/becho2/App/public/main.php",
      type: "GET",
      data: {
        action: "getProductsByCategory",
        categoryId: categoryId,
        page: page,
      },
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          // Handle the products data here, e.g., update the page with the products
          console.log(response.products); // Example action

          // Call the callback function after handling the products
          if (typeof callback === "function") {
            callback();
          }
        } else {
          console.error("Error fetching products: " + response.message);
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error: " + status + error);
      },
    });
  }

  function handleSearch() {
    // Get form values
    const customword = $("#customword").val();
    const location = $("#location").val();
    const category = $("#category").val();

    // Create an array to hold the non-empty values
    let searchParams = [];

    // Add values to the array if they are not 'none' or empty
    if (customword && customword !== "none") searchParams.push(customword);
    if (location && location !== "none") searchParams.push(location);
    if (category && category !== "none") searchParams.push(category);

    // Join the array into a single string without spaces
    const searchString = searchParams.join(",");

    // Redirect to search.php with the search string in the URL
    if (searchString) {
      window.location.href = `search?search=${encodeURIComponent(
        searchString
      )}`;
    }
  }

  // Handle search button click
  $("#search-btn-glob").click(function () {
    handleSearch();
  });

  // Handle form submission via Enter key
  $("#search-form-glob").submit(function (event) {
    event.preventDefault(); // Prevent the default form submission
    handleSearch();
  });

  // Function to search products

  // Initialize categories and locations
  fetchCategories();
  fetchLocations();
});
