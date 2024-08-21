$(document).ready(function () {
  // Function to get user ID from URL
  function getUserIdFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get("id");
  }

  // Function to fetch user profile
  function fetchUserProfile() {
    const userId = getUserIdFromUrl();
    $.ajax({
      url: "/becho2/App/public/main.php",
      method: "POST",
      data: { action: "getProfileInfoForChangeInAdmin", user_id: userId },
      dataType: "json",
      success: function (response) {
        console.log("Profile response:", response);
        if (response.status === "success") {
          const profile = response.profileInfo;
          $("#username").val(profile.username);
          $("#location").val(profile.location);
          $("#phone").val(profile.phone_number);
          if (profile.profile_picture) {
            $("#imagePreview").attr(
              "src",
              "../../../becho2/assets/img/author/" + profile.profile_picture
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

  // Call fetchUserProfile on page load
  fetchUserProfile();

  // Handle register button click to update profile
  $("#registerButton").click(function () {
    let formData = new FormData();
    formData.append("action", "updateProfileInAdmin");
    formData.append("username", $("#username").val());
    formData.append("location", $("#location").val());
    formData.append("phone_number", $("#phone").val());
    formData.append("user_id", getUserIdFromUrl());
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
          Swal.fire({
            icon: "success",
            title: "Profile Updated",
            text: "Profile updated successfully.",
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: false,
          }).then(() => {
            fetchUserProfile(); // Refresh profile data
          });
        } else {
          console.error("Error updating profile: " + response.message);
        }
      },
      error: function () {
        console.error("Error updating profile.");
      },
    });
  });

  // Handle file input change to preview image
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

  // Handle delete button click to show the confirmation modal
  $("#deleteBUtton").click(function () {
    $("#deleteConfirmationModal").modal("show");
  });

  // Handle confirmation button click to delete user account
  $("#confirmDelete").click(function () {
    const userId = getUserIdFromUrl();
    $.ajax({
      url: "/becho2/App/public/main.php",
      method: "POST",
      data: { action: "deleteUserAccount", user_id: userId },
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          Swal.fire({
            icon: "success",
            title: "Account Deleted",
            text: "Account deleted successfully.",
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: false,
          }).then(() => {
            window.location.href = "../../../becho2/admin.php";
          });
        } else {
          console.error("Error deleting account: " + response.message);
        }
      },
      error: function () {
        console.error("Error deleting account.");
      },
    });
  });
});
