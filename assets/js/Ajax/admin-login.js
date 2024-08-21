$(document).ready(function () {
  $("#loginForm").on("submit", function (e) {
    e.preventDefault(); // Prevent form submission

    // Reset error messages
    $("#usernameError").hide();
    $("#passwordError").hide();
    $("#loginMessage").hide(); // Hide previous messages

    let isValid = true;

    // Get values
    const username = $("#username").val().trim();
    const password = $("#password").val().trim();

    // Validate username
    if (username === "") {
      $("#usernameError").show();
      isValid = false;
    }

    // Validate password
    if (password === "") {
      $("#passwordError").show();
      isValid = false;
    }

    // If valid, send the AJAX request
    if (isValid) {
      $.ajax({
        url: "/becho2/App/public/main.php",
        method: "POST",
        data: {
          action: "admin-login", // Added action parameter
          username: username,
          password: password,
        },
        success: function (response) {
          // Log response for debugging
          console.log(response);

          // No need to parse JSON, response is already an object
          if (response.status === "success") {
            window.location.href = "../../../becho2/Admin/views/admin.php";
          } else {
            // Display error message
            $("#loginMessage").text(response.message).show();
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.error("AJAX error:", textStatus, errorThrown);
          $("#loginMessage")
            .text("An error occurred: " + errorThrown)
            .show();
        },
      });
    }
  });

  // Remove error message when typing
  $("#username").on("input", function () {
    $("#usernameError").hide(); // Hide username error
  });

  $("#password").on("input", function () {
    $("#passwordError").hide(); // Hide password error
  });
});
