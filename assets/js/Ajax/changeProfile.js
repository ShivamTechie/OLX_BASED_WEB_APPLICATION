$(document).ready(function () {
  function fetchUserProfile() {
    $.ajax({
      url: "/becho2/App/public/main.php",
      method: "POST",
      data: { action: "getProfileInfoForChange" },
      dataType: "json",
      success: function (response) {
        console.log("Profile response:", response); // Log the response for debugging
        if (response.status === "success") {
          const profile = response.profileInfo;
          $("#username").val(profile.username);
          $("#location").val(profile.location);
          $("#phone").val(profile.phone_number);
          if (profile.profile_picture) {
            $("#imagePreview").attr(
              "src",
              "assets/img/author/" + profile.profile_picture
            );
          }
        } else {
          console.error("Error fetching profile: " + response.message);
        }
      },
      error: function () {
        console.error("Error fetching profile.");
      },
    });
  }

  fetchUserProfile();

  $("#registerButton").click(function () {
    let formData = new FormData();
    formData.append("action", "updateProfile");
    formData.append("username", $("#username").val());
    formData.append("location", $("#location").val());
    formData.append("phone_number", $("#phone").val());
    if ($("#profilePicture")[0].files[0]) {
      formData.append("profile_picture", $("#profilePicture")[0].files[0]);
    }

    $.ajax({
      url: "/becho2/App/public/main.php",
      method: "POST",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          fetchUserProfile();

          // Show SweetAlert on successful update
          Swal.fire({
            icon: "success",
            title: "Profile Updated Successfully",
            text: "Your profile has been updated.",
            timer: 2000,
            showConfirmButton: false,
          });
        } else {
          // Display error message without alert
          console.error("Error updating profile: " + response.message);
        }
      },
      error: function () {
        // Display error message without alert
        console.error("Error updating profile.");
      },
    });
  });

  // Preview image on file input change
  $("#profilePicture").change(function () {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        $("#imagePreview").attr("src", e.target.result);
      };
      reader.readAsDataURL(file);
    }
  });
});
