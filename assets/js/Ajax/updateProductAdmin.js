$(document).ready(function () {
  // Function to handle file input changes and display image previews
  $("#tg-photogallery").on("change", function () {
    var files = this.files;
    var imageContainer = $("#existing-images");
    imageContainer.empty(); // Clear previous images

    if (files.length > 0) {
      $("#file-error").hide();
      for (var i = 0; i < files.length; i++) {
        var reader = new FileReader();
        reader.onload = function (e) {
          var img = $("<img>", {
            src: e.target.result,
            class: "img-thumbnail",
            style: "width: 100px; height: auto; margin: 5px;",
          });
          imageContainer.append(img);
        };
        reader.readAsDataURL(files[i]);
      }
    } else {
      imageContainer.html("<p>No images selected.</p>");
    }
  });

  $("#postAdBtn").on("click", function () {
    // Open the confirmation modal
    $("#deleteConfirmationModal").modal("show");
  });

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
    formData.append("status", $("#Status").val());
    formData.append("category", $("#categories").val()); // Include category
    formData.append("location", $("#address").val()); // Only location

    // Check for file inputs
    if (files.length > 0) {
      $("#file-error").hide();
      for (var i = 0; i < files.length; i++) {
        formData.append("images[]", files[i]);
      }
    } else {
      // Indicate no new images are being uploaded
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
        // Assuming the response is a JSON object
        try {
          if (response.status === "success") {
            // Close the modal
            $("#deleteConfirmationModal").modal("hide");
            // Show success message with SweetAlert
            Swal.fire({
              title: "Success!",
              text: "Product updated successfully.",
              icon: "success",
              confirmButtonText: "OK",
            }).then(() => {
              // Redirect after confirmation
              window.location.href = "../../../becho2/Admin/views/admin.php";
            });
          } else {
            $(".error-msg").text(response.message);
          }
        } catch (e) {
          console.error("Parsing error:", e);
        }
      },
      error: function (xhr, status, error) {
        console.log("AJAX Error: " + status + " " + error);
        alert("An error occurred while updating the product.");
      },
    });
  });

  // Function to get query parameter from URL
  function getQueryParam(param) {
    var urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
  }
});
