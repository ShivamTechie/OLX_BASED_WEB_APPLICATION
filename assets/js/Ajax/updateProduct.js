$(document).ready(function () {
  // Show the modal when "Apply Changes" button is clicked
  $("#postAdBtn").on("click", function () {
    $("#deleteConfirmationModal").modal("show");
  });

  // Handle the modal confirmation
  $("#confirmDelete").on("click", function () {
    var files = $("#tg-photogallery")[0].files;
    var formData = new FormData();

    // Collect form data
    formData.append("action", "updateProductDetails");
    formData.append("listing_id", getQueryParam("listing_id"));
    formData.append("title", $("#Title").val());
    formData.append("price", $("#price").val());
    formData.append("description", $("#description").val());
    formData.append(
      "specifications",
      $("#specifications").val().replace(/\n/g, ", ")
    );
    formData.append("condition", $("#condition").val());
    formData.append("brand", $("#brand").val());

    // Check if status is empty, set to "Active" if it is
    var status = $("#Status").val() || "Active";
    formData.append("status", status);

    formData.append("category", $("#categories").val());
    formData.append("location", $("#address").val());

    // If there are files selected, append them to the formData
    if (files.length > 0) {
      for (var i = 0; i < files.length; i++) {
        formData.append("images[]", files[i]);
      }
    } else {
      formData.append("images", "none");
    }

    // Send AJAX request
    $.ajax({
      url: "/becho2/App/public/main.php",
      method: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        $("#deleteConfirmationModal").modal("hide");

        try {
          if (response.status === "success") {
            Swal.fire({
              icon: "success",
              title: "Success",
              text: "Your ad has been updated successfully.",
              timer: 2000,
              showConfirmButton: false,
            }).then(() => {
              window.location.href = "../../../becho2/UserDashboard.php";
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: response.message || "There was an error updating the ad.",
            });
          }
        } catch (e) {
          console.error("Parsing error:", e);
        }
      },
      error: function (xhr, status, error) {
        $("#deleteConfirmationModal").modal("hide");
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "An error occurred while updating the product.",
        });
      },
    });
  });

  // Function to get query parameter from URL
  function getQueryParam(param) {
    var urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
  }
});
