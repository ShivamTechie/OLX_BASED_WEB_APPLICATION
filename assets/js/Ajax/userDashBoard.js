$(document).ready(function () {
  let listingIdToDelete = null;
  let currentAction = "getUserProducts"; // Default action to load posted ads

  // Function to fetch the user profile
  function getUserProfile() {
    $.ajax({
      url: "/becho2/App/public/main.php",
      type: "POST",
      data: { action: "getProfileInfo" }, // Use the "getUserProfile" action
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          // Update the UI with user profile data
          $("#user-image").attr(
            "src",
            "assets/img/author/" + response.profileInfo.profile_picture
          );
          // Add more UI updates as needed
        } else {
          console.error("Failed to load user profile.");
        }
      },
      error: function () {
        console.error("An error occurred while fetching the user profile.");
      },
    });
  }

  // Call getUserProfile on page load
  getUserProfile();

  // Initialize the DataTable with AJAX source and responsive feature
  const table = $(".dashboardtable").DataTable({
    responsive: true, // Enable responsive mode
    autoWidth: false, // Disable auto width for better responsiveness
    columnDefs: [
      { targets: [0, 5], orderable: false }, // Disable ordering on Photo and Action columns
    ],
    ajax: {
      url: "/becho2/App/public/main.php",
      type: "POST",
      data: function () {
        return { action: currentAction }; // Send the current action in the request
      },
      dataSrc: function (json) {
        // Update the total ads count or other relevant data
        $(".contentbox h3").text(json.recordsTotal + " Ads Posted");

        // Return the data for DataTables
        return json.data;
      },
    },
    columns: [
      {
        data: "image",
        render: function (data) {
          return `<img class="img-fluid" src="assets/img/product/${data}" alt="" />`;
        },
      },
      {
        data: "title",
        render: function (data) {
          // Limit the title to 20 characters
          return data.length > 20 ? data.substring(0, 20) + "..." : data;
        },
      },
      { data: "category_name" },
      {
        data: "status",
        render: function (data) {
          let statusClass = "adstatusactive";
          let statusText = "Active";
          if (data === "Not Active") {
            statusClass = "adstatusexpired";
            statusText = "Not Active";
          } else if (data === "Sold") {
            statusClass = "adstatussold";
            statusText = "Sold";
          } else if (data === "active") {
            statusClass = "adstatusactive";
            statusText = "Active";
          }
          return `<span class="adstatus ${statusClass}">${statusText}</span>`;
        },
      },
      {
        data: "price",
        render: function (data) {
          return `<h3><i class="fa-solid fa-indian-rupee-sign"></i>${data}</h3>`;
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          let buttons = `
            <div class="btns-actions">
              <a class="btn-action btn-view" href="ads-details?id=${row.listing_id}"><i class="lni-eye"></i></a>
          `;

          // Show edit button only for user's posted ads
          if (currentAction === "getUserProducts") {
            buttons += `<a class="btn-action btn-edit" href="../../../becho2/editAd?listing_id=${row.listing_id}"><i class="lni-pencil"></i></a>`;
          }

          // Add the delete button for all actions
          buttons += `<a class="btn-action btn-delete" data-listing-id="${row.listing_id}" href="#"><i class="lni-trash"></i></a></div>`;

          return buttons;
        },
      },
    ],
  });

  // Handle navigation link clicks
  $("#showPostedAds").on("click", function (e) {
    e.preventDefault(); // Prevent the default behavior of the link
    currentAction = "getUserProducts";
    table.ajax.reload(); // Reload the table data
  });

  $("#showLikedProducts").on("click", function (e) {
    e.preventDefault(); // Prevent the default behavior of the link
    currentAction = "getLikedProducts";
    table.ajax.reload(); // Reload the table data
  });

  $("#showSavedProducts").on("click", function (e) {
    e.preventDefault(); // Prevent the default behavior of the link
    currentAction = "getSavedProducts";
    table.ajax.reload(); // Reload the table data
  });

  // Open the modal and set the listingIdToDelete
  $(document).on("click", ".btn-delete", function () {
    listingIdToDelete = $(this).data("listing-id");
    $("#deleteConfirmationModal").modal("show");
  });

  // Handle the modal confirmation
  $("#confirmDelete").on("click", function () {
    if (listingIdToDelete) {
      deleteProduct(listingIdToDelete);
    }
    $("#deleteConfirmationModal").modal("hide"); // Hide the modal
  });

  // Function to delete the product
  function deleteProduct(listingId) {
    let deleteAction;

    // Determine the delete action based on the current action
    if (currentAction === "getUserProducts") {
      deleteAction = "deleteProduct"; // Delete from listings table
    } else if (currentAction === "getLikedProducts") {
      deleteAction = "unlikeProduct"; // Remove from product_likes table
    } else if (currentAction === "getSavedProducts") {
      deleteAction = "unsaveProduct"; // Remove from saved_products table
    }

    $.ajax({
      url: "/becho2/App/public/main.php",
      type: "POST",
      data: { action: deleteAction, listing_id: listingId },
      dataType: "json",
      success: function (response) {
        console.log("Delete response:", response); // Debugging line
        let message = "";
        if (response.status === "success") {
          if (deleteAction === "deleteProduct") {
            message = "Your listing has been deleted successfully.";
          } else if (deleteAction === "unlikeProduct") {
            message = "Product unliked successfully.";
          } else if (deleteAction === "unsaveProduct") {
            message = "Product unsaved successfully.";
          }

          Swal.fire({
            icon: "success",
            title: "Deleted!",
            text: message,
            timer: 2000,
            showConfirmButton: false,
          }).then(() => {
            table.ajax.reload(); // Reload the table after deletion
          });
        } else {
          // Handle error case if needed
          Swal.fire({
            icon: "error",
            title: "Error!",
            text: "An error occurred while deleting the product.",
            timer: 2000,
            showConfirmButton: false,
          });
        }
      },
    });
  }
});
