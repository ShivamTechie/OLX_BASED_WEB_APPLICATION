$(document).ready(function () {
  function getUserProfile() {
    $.ajax({
      url: "/becho2/App/public/main.php",
      type: "POST",
      data: { action: "getProfileInfo" },
      dataType: "json",
      success: function (data) {
        try {
          if (
            data.status === "success" &&
            data.profileInfo.status === "success"
          ) {
            $("#user-image").attr(
              "src",
              "assets/img/author/" + data.profileInfo.profile_picture
            );
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

  getUserProfile();

  $("#file-error").hide();

  function validateForm() {
    let isValid = true;

    // Validate all required fields
    $('input[type="text"], textarea').each(function () {
      if ($.trim($(this).val()) === "") {
        isValid = false;
        $(this).next(".error").show();
      }
    });

    // Validate category selection
    if ($("#categories").val() === "none") {
      isValid = false;
      $("#categories-error").show();
    }

    // Validate file selection
    let files = $("#tg-photogallery")[0].files;
    if (files.length === 0) {
      isValid = false;
      $("#file-error").show();
    }

    if (!isValid) {
      $("#form-error").show();
    }

    return isValid;
  }

  // Remove error message on typing
  $('input[type="text"], textarea').on("input", function () {
    $(this).next(".error").hide();
    $("#form-error").hide();
  });

  // Remove category error on change
  $("#categories").on("change", function () {
    $("#categories-error").hide();
    $("#form-error").hide();
  });

  // Remove file error on change
  $("#tg-photogallery").on("change", function () {
    $("#file-error").hide();
    $("#form-error").hide();
    previewImages();
  });

  function previewImages() {
    let files = $("#tg-photogallery")[0].files;
    let existingImagesContainer = $("#existing-images");
    existingImagesContainer.empty();

    if (files.length > 0) {
      for (let i = 0; i < files.length; i++) {
        let file = files[i];
        let reader = new FileReader();
        let img = $("<img>").addClass("preview-image").css({
          width: "100px",
          height: "100px",
          "object-fit": "cover",
          margin: "5px",
        });

        reader.onload = function (e) {
          img.attr("src", e.target.result);
        };

        reader.readAsDataURL(file);
        existingImagesContainer.append(img);
      }
    }
  }

  $("#postAdBtn").click(function () {
    if (validateForm()) {
      $("#deleteConfirmationModal").modal("show"); // Show modal if form is valid
    }
  });

  $("#confirmDelete").click(function () {
    if (validateForm()) {
      let formData = new FormData();
      formData.append("action", "post-ad");
      formData.append("Title", $("#Title").val());
      formData.append("category", $("#categories").val());
      formData.append("price", $("#price").val());
      formData.append("description", $("#description").val());
      formData.append(
        "specifications",
        $("#specifications").val().replace(/\n/g, "<br>")
      );
      formData.append("condition", $("#condition").val());
      formData.append("brand", $("#brand").val());
      formData.append("name", $("#name").val());
      formData.append("phone", $("#phone").val());
      formData.append("address", $("#address").val());

      let files = $("#tg-photogallery")[0].files;
      for (let i = 0; i < files.length; i++) {
        formData.append("files[]", files[i]);
      }

      $.ajax({
        url: "/becho2/App/public/main.php",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
          $("#deleteConfirmationModal").modal("hide");

          Swal.fire({
            icon: "success",
            title: "Ad Posted Successfully",
            text: "Your ad has been posted successfully.",
            timer: 2000,
            showConfirmButton: false,
          }).then(() => {
            window.location.href = "../../../becho2/UserDashboard";
          });
        },
        error: function () {
          $("#deleteConfirmationModal").modal("hide");
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "There was an error posting the ad.",
          });
        },
      });
    }
  });
});
