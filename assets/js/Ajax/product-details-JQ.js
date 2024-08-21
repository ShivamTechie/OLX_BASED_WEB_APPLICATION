$(document).ready(function () {
  // Extract the product ID from the URL
  const urlParams = new URLSearchParams(window.location.search);
  const productId = urlParams.get("id");

  if (productId) {
    $.ajax({
      url: "/becho2/App/public/main.php",
      method: "GET",
      data: {
        action: "getProductDetails",
        id: productId,
      },
      dataType: "json",
      success: function (response) {
        console.log("AJAX response:", response);

        if (response.status === "success") {
          const product = response.product;

          // Set the page title dynamically
          document.title = `NexusPlus.in: ${product.title}`;

          // Use the image name from the response for all three carousel images
          const imagePath = product.images;

          // Update image carousel
          const owlCarousel = $("#owl-demo");
          owlCarousel.trigger("destroy.owl.carousel"); // Destroy existing Owl Carousel instance
          owlCarousel.empty(); // Clear existing items

          if (imagePath.length > 0) {
            imagePath.forEach((image) => {
              owlCarousel.append(`
                <div class="item">
                    <div class="product-img">
                        <img class="img-fluid" src="assets/img/product/${image}" alt="" style="width:100%;">
                    </div>
                    <span class="price"><i class="fa-solid fa-indian-rupee-sign"></i> ${product.price}</span>
                </div>
              `);
            });

            // Reinitialize Owl Carousel
            owlCarousel.owlCarousel({
              loop: true,
              margin: 10,
              nav: true,
              items: 1,
            });
          } else {
            console.log("No images found for the product.");
          }

          // Update product details
          $(".ads-details-info h2").text(product.title);
          $(".details-meta span")
            .eq(0)
            .html(
              `<a href="#"><i class="lni-alarm-clock"></i> ${product.formatted_date}</a>`
            );
          $(".details-meta span")
            .eq(1)
            .html(
              `<a href="#"><i class="lni-map-marker"></i> ${product.location}</a>`
            );
          $(".details-meta span")
            .eq(2)
            .html(
              `<a href="#"><i class="lni-eye"></i> ${product.views} View</a>`
            );
          $(".ads-details-info p").text(product.description);

          // Update specifications
          let specifications = product.specifications;
          if (typeof specifications === "string") {
            try {
              specifications = specifications
                .split(",")
                .map((spec) => spec.trim()) // Trim whitespace from each specification
                .filter((spec) => spec.length > 0); // Remove empty or whitespace-only specifications
            } catch (e) {
              console.error("Error processing specifications:", e);
              specifications = [];
            }
          }

          const specsList = $(".list-specification");
          specsList.empty();

          if (Array.isArray(specifications) && specifications.length > 0) {
            specifications.forEach((spec) => {
              specsList.append(
                `<li><i class="lni-check-mark-circle"></i> ${spec}</li>`
              );
            });
          } else {
            specsList.append(
              `<li><i class="lni-check-mark-circle"></i> No specifications found for this product</li>`
            );
          }

          // Update more ads from seller
          const postsList = $(".posts-list");
          postsList.empty();
          if (Array.isArray(response.randomProducts)) {
            response.randomProducts.forEach((product) => {
              postsList.append(`
                <li>
                    <div class="widget-thumb">
                        <a href="ads-details.html" data-id="${product.listing_id}" class="similar-product">
                            <img src="assets/img/product/${product.image_path}" alt="" />
                        </a>
                    </div>
                    <div class="widget-content" style="display:grid;">
                        <h4>
                            <a href="ads-details.html" data-id="${product.listing_id}" class="similar-product">
                                ${product.title}
                            </a>
                        </h4>
                        <div class="meta-tag">
                            <span><a href="#"><i class="lni-user"></i> ${product.username}</a></span>
                            <span><a href="#"><i class="lni-map-marker"> ${product.location}</i></a></span>
                            <span><a href="#"><i class="lni-tag"></i> ${product.category_name}</a></span>
                            <span class="mx-2" style="color:#E91E63;font-weight:bold">$${product.price}</span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </li>
              `);
            });
          } else {
            console.log("No random products found.");
          }

          // Update 'Ad Posted By' section
          $(".agent-details h3 a").text(product.username);
          $(".agent-details span").html(
            `<i class="lni-phone-handset"></i> ${product.phone_number}`
          );
          $(".agent-inner p").html(
            `I'm interested in this product [ID ${productId}] and I'd like to know more details.`
          );
          $(".agent-photo").html(
            `<a href="userProfile"><img src="./assets/img/author/${
              product.profile_picture ? product.profile_picture : "img1.jpg"
            }" alt=""></a>`
          );

          // Update advertisement section
          $("#category-name").text(product.category_name);
          $("#condition").text(product.condition);
          $("#brand-name").text(product.brand);

          // Handle the Send Message button click
          $(".agent-inner .btn").on("click", function (e) {
            e.preventDefault();

            // Check if session has username
            $.ajax({
              url: "/becho2/App/public/main.php",
              method: "GET",
              data: {
                action: "sendMessageToUser", // Action name for checking session
                productId: productId, // Send product ID for further operations
              },
              dataType: "json",
              success: function (sessionResponse) {
                if (sessionResponse.status === "loggedIn") {
                  const username = sessionResponse.username;
                  const phoneNumber = sessionResponse.phoneNumber;
                  const location = sessionResponse.location;

                  const recipientEmail = sessionResponse.recipientEmail; // Email of the person who posted the product
                  const subject = `Interested in your NexusPlus product: ${product.title}`;
                  const body = `I am interested in your product titled "${product.title}".
                  
                  User Details:
                  Username: ${username}
                  Phone Number: ${phoneNumber}
                  Location: ${location}`;

                  // Open the default email client
                  window.location.href = `mailto:${recipientEmail}?subject=${encodeURIComponent(
                    subject
                  )}&body=${encodeURIComponent(body)}`;
                } else {
                  // Redirect to login page if not logged in
                  window.location.href = "/becho2/login";
                }
              },
              error: function (xhr, status, error) {
                console.log("AJAX error: " + status + " - " + error);
              },
            });
          });
        } else {
          console.log(
            "Error fetching product details. Status: " +
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
  } else {
    console.log("No product ID specified in the URL.");
  }

  $(".posts-list").on("click", ".similar-product", function (e) {
    e.preventDefault();
    var productId = $(this).data("id");
    window.location.href = "../../becho2/ads-details.php?id=" + productId;
  });
});
