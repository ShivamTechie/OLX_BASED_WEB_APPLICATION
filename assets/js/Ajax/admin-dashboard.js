$(document).ready(function () {
  let listingIdToDelete; // Variable to store the listing ID to delete
  let totalProducts = parseInt($(".contentbox h3").text()); // Get the initial total products count

  // Initialize DataTable with server-side processing
  getAdminProfile();
  const table = $("#adminTable").DataTable({
    ajax: {
      url: "/becho2/App/public/main.php",
      type: "GET",
      dataType: "json",
      data: { action: "fetchAdminData" },
      dataSrc: function (response) {
        totalProducts = response.recordsTotal; // Update the total products count
        $(".contentbox h3").text(totalProducts);
        return response.data || []; // Use the fetched data or an empty array if not found
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error);
      },
    },
    columns: [
      {
        data: "owner_name",
        render: function (data, type, row) {
          return `<a href="../../../becho2/Admin/views/userProfileInAdmin.php?id=${row.user_id}">${data}</a>`;
        },
      },
      {
        data: "product_image",
        render: function (data) {
          return `<img class="img-fluid" src="../../assets/img/product/${data}" alt="Product Image" />`;
        },
      },
      {
        data: "product_title",
      },
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
        data: "category_name",
      },
      {
        data: "price",
        render: function (data) {
          return `<span><i class="fa-solid fa-indian-rupee-sign"></i>${data}</span>`;
        },
      },
      {
        data: "listing_id",
        orderable: false,
        render: function (data) {
          return `
            <div class="btns-actions">
              <a class="btn-action btn-view" href="../../../becho2/Admin/views/ads_detailsInAdmin.php?id=${data}"><i class="lni-eye"></i></a>
              <a class="btn-action btn-edit" href="../../../becho2/Admin/views/adminEditAdd.php?listing_id=${data}"><i class="lni-pencil"></i></a>
              <a class="btn-action btn-delete" data-listing-id="${data}" onclick="confirmDelete(${data});"><i class="lni-trash"></i></a>
            </div>`;
        },
      },
    ],
    order: [[6, "desc"]],
    destroy: true,
    responsive: true,
  });

  // Function to show the confirmation modal
  window.confirmDelete = function (listingId) {
    listingIdToDelete = listingId; // Store the listing ID
    $("#deleteConfirmationModal").modal("show"); // Show the modal
  };

  // Handle the confirmation button click
  $("#confirmDelete").click(function () {
    $.ajax({
      url: "/becho2/App/public/main.php",
      type: "POST",
      data: { action: "deleteProduct", listing_id: listingIdToDelete },
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          // Remove the row from the table
          table
            .row(
              $(`.btn-delete[data-listing-id="${listingIdToDelete}"]`).parents(
                "tr"
              )
            )
            .remove()
            .draw();

          // Manually subtract one from the total products count
          totalProducts--;
          $(".contentbox h3").text(totalProducts); // Update the total products displayed

          // Hide the modal before showing the success alert
          $("#deleteConfirmationModal").modal("hide");

          // Show success alert
          Swal.fire({
            icon: "success",
            title: "Deleted!",
            text: "Listing deleted successfully.",
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: false,
          });
        } else {
          console.error("Failed to delete product: " + response.message);
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error: " + status + " " + error);
      },
    });
  });
});

function getAdminProfile() {
  $.ajax({
    url: "/becho2/App/public/main.php",
    type: "POST",
    data: { action: "getProfileInfoAdmin" },
    dataType: "json", // Expect JSON response
    success: function (data) {
      try {
        // Check if the status is success
        if (
          data.status === "success" &&
          data.profileInfo.status === "success"
        ) {
          $("#user-image").attr(
            "src",
            "../../assets/img/admin/" + data.profileInfo.profile_picture
          );
          // Update the total number of products
          totalProducts = data.profileInfo.total_products;
          $(".contentbox h3").text(totalProducts);
        } else {
          console.error(
            "Failed to fetch user profile: " +
              (data.profileInfo.message || "No message available")
          );
        }
      } catch (e) {
        console.error("Error parsing response:", e);
      }
    },
    error: function () {
      console.error("Error fetching user profile.");
    },
  });
}
