$(document).ready(function () {
  $("#card").addClass("d-none");
  // Function to send OTP when page loads
  function sendOtpOnLoad() {
    $.ajax({
      url: "/becho2/App/public/main.php", // Ensure this path is correct
      method: "POST",
      data: { action: "sendOtp" },
      success: function (response) {
        console.log("Send OTP response:", response); // Debugging output
        try {
          if (response.status === "success") {
            $("#otpMessage")
              .text("OTP has been sent to your email.")
              .css("color", "green")
              .show();
            $("#otpForm").show();

            // Start the timer to show the resend OTP button after 2 minutes
            setTimeout(function () {
              $("#otpMessage").text(
                "OTP has expired. Please request a new one."
              );
              $(".text-center #resendOtpButton").removeClass("d-none"); // Show the resend button
              $(".text-center #verifyotpBtn").addClass("d-none"); // Hide the verify button
              $("#otpForm")[0].reset(); // Reset the form
            }, 120000); // 2 minutes in milliseconds
          } else {
            $("#otpMessage").text(jsonResponse.message).show();
          }
        } catch (e) {
          console.error("Failed to parse JSON response:", e);
          $("#otpMessage").text("Failed to process the response.").show();
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $("#otpMessage")
          .text("An error occurred: " + errorThrown)
          .show();
        console.error("AJAX request failed:", textStatus, errorThrown);
      },
    });
  }

  // Call the function on page load
  sendOtpOnLoad();

  // OTP form submission
  $("#otpForm").on("submit", function (e) {
    e.preventDefault();

    // Reset error messages
    $("#otpError").hide();
    $("#otpMessage").hide();

    let otp = $("#otp").val().trim();

    if (otp === "") {
      $("#otpError").show();
    } else {
      // Make AJAX call to verify OTP
      $.ajax({
        url: "/becho2/App/public/main.php", // Ensure this path is correct
        method: "POST",
        data: { action: "otpVerify", otp: otp },
        success: function (response) {
          console.log("OTP verification response:", response); // Debugging output
          try {
            const jsonResponse = JSON.parse(response);
            if (jsonResponse.status === "success") {
              $("#otpForm").hide();
              $("#changePasswordForm").show();
            } else {
              $("#otpMessage").text(jsonResponse.message).show();
            }
          } catch (e) {
            console.error("Failed to parse JSON response:", e);
            $("#otpMessage").text("Failed to process the response.").show();
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $("#otpMessage")
            .text("An error occurred: " + errorThrown)
            .show();
          console.error("AJAX request failed:", textStatus, errorThrown);
        },
      });
    }
  });

  // Resend OTP button click handler
  $("#resendOtpButton").on("click", function () {
    $(this).addClass("d-none"); // Hide the resend button
    $(".text-center #verifyotpBtn").removeClass("d-none"); // Show the verify button
    sendOtpOnLoad(); // Resend OTP

    // Hide the OTP message if the button is clicked
    $("#otpMessage")
      .text("OTP has been sent to your email.")
      .css("color", "green")
      .show();
  });

  // Change password form submission
  $("#changePasswordForm").on("submit", function (e) {
    e.preventDefault();

    // Reset error messages
    $("#newPasswordError").hide();
    $("#confirmPasswordError").hide();
    $("#changePasswordMessage").hide();

    let newPassword = $("#newPassword").val().trim();
    let confirmPassword = $("#confirmPassword").val().trim();
    let isValid = true;

    if (newPassword === "") {
      $("#newPasswordError").show();
      isValid = false;
    }

    if (confirmPassword === "") {
      $("#confirmPasswordError").show();
      isValid = false;
    }

    if (newPassword !== confirmPassword) {
      $("#confirmPasswordError").text("Passwords do not match").show();
      isValid = false;
    }

    if (isValid) {
      // Make AJAX call to change password
      $.ajax({
        url: "/becho2/App/public/main.php", // Ensure this path is correct
        method: "POST",
        data: {
          action: "changePass",
          newPassword: newPassword,
          confirmPassword: confirmPassword,
        },
        success: function (response) {
          console.log("Change password response:", response); // Debugging output
          try {
            const jsonResponse = JSON.parse(response);
            if (jsonResponse.status === "success") {
              $("#changePasswordForm").hide();
              $(".login-form").hide();
              $("#card").removeClass("d-none");
            } else {
              $("#changePasswordMessage").text(jsonResponse.message).show();
            }
          } catch (e) {
            console.error("Failed to parse JSON response:", e);
            $("#changePasswordMessage")
              .text("Failed to process the response.")
              .show();
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $("#changePasswordMessage")
            .text("An error occurred: " + errorThrown)
            .show();
          console.error("AJAX request failed:", textStatus, errorThrown);
        },
      });
    }
  });

  // Hide error messages when input fields change
  $("#newPassword, #confirmPassword").on("input", function () {
    $(
      "#newPasswordError, #confirmPasswordError, #changePasswordMessage"
    ).hide();
  });

  // Hide error message when OTP input field changes
  $("#otp").on("input", function () {
    $("#otpError, #otpMessage").hide();
  });
});
