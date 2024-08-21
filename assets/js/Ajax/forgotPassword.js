$(document).ready(function () {
  // Initial setup on page load
  $("#otpSection, #changePasswordSection, #card").addClass("d-none");
  $("#emailSection").removeClass("d-none");

  // Function to reset all forms
  function resetForms() {
    $("#forgotPasswordForm")[0].reset();
    $("#otpForm")[0].reset();
    $("#changePasswordForm")[0].reset();
    $(
      "#emailError, #otpError, #newPasswordError, #confirmPasswordError, #changePasswordMessage, #otpMessage"
    ).hide();
  }

  // Function to send OTP when the user enters their email and clicks the 'Send OTP' button
  function sendOtp(e) {
    if (e) e.preventDefault(); // Prevent the default form submission if event is passed
    let email = $("#email").val().trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailRegex.test(email)) {
      $("#forgotPasswordMessage")
        .text("Please enter a valid email address")
        .show();
      resetForms();
      return;
    }

    $.ajax({
      url: "/becho2/App/public/main.php", // Ensure this path is correct
      method: "POST",
      data: { action: "sendForgotOtp", email: email },
      success: function (response) {
        console.log("Raw response:", response); // Log the raw response
        try {
          // Use response directly if it's already an object
          if (response.status === "success") {
            $("#otpMessage")
              .text("OTP has been sent to your email.")
              .css("color", "green")
              .show();
            $("#emailSection").addClass("d-none");
            $("#otpSection").removeClass("d-none");
            $("#changePasswordSection").addClass("d-none");
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
            $("#forgotPasswordMessage").text(response.message).show();
            resetForms();
          }
        } catch (e) {
          console.error("Failed to parse JSON response:", e);
          $("#otpMessage").text("Failed to process the response.").show();
          resetForms();
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $("#otpMessage")
          .text("An error occurred: " + errorThrown)
          .show();
        console.error("AJAX request failed:", textStatus, errorThrown);
        resetForms();
      },
    });
  }

  // Attach event handler to the send OTP form submission
  $("#forgotPasswordForm").on("submit", sendOtp);

  // OTP form submission
  $("#otpForm").on("submit", function (e) {
    e.preventDefault();

    // Reset error messages
    $("#otpError").hide();
    $("#otpMessage").hide();

    let otp = $("#otp").val().trim();

    if (otp === "") {
      $("#otpError").show();
      resetForms();
    } else {
      // Make AJAX call to verify OTP
      $.ajax({
        url: "/becho2/App/public/main.php", // Ensure this path is correct
        method: "POST",
        data: { action: "verifyForgotOtp", otp: otp },
        success: function (response) {
          console.log("OTP verification response:", response); // Debugging output
          try {
            const jsonResponse = JSON.parse(response);
            if (jsonResponse.status === "success") {
              $("#otpSection").addClass("d-none");
              $("#changePasswordSection").removeClass("d-none");
            } else {
              $("#otpMessage").text(jsonResponse.message).show();
              resetForms();
            }
          } catch (e) {
            console.error("Failed to parse JSON response:", e);
            $("#otpMessage").text("Failed to process the response.").show();
            resetForms();
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $("#otpMessage")
            .text("An error occurred: " + errorThrown)
            .show();
          console.error("AJAX request failed:", textStatus, errorThrown);
          resetForms();
        },
      });
    }
  });

  // Resend OTP button click handler
  $("#resendOtpButton").on("click", function () {
    $(this).addClass("d-none"); // Hide the resend button
    $(".text-center #verifyotpBtn").removeClass("d-none"); // Show the verify button
    $("#otpMessage").text("Sending OTP again...").css("color", "orange").show(); // Show message indicating OTP is being sent

    // Call sendOtp function to resend OTP
    sendOtp();

    // Hide the expired message
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
          action: "forgotChangePass",
          newPassword: newPassword,
          confirmPassword: confirmPassword,
        },
        success: function (response) {
          console.log("Change password response:", response); // Debugging output
          try {
            const jsonResponse = JSON.parse(response);
            if (jsonResponse.status === "success") {
              $("#changePasswordSection").addClass("d-none");
              $("#card").removeClass("d-none");
            } else {
              $("#changePasswordMessage").text(jsonResponse.message).show();
              resetForms();
            }
          } catch (e) {
            console.error("Failed to parse JSON response:", e);
            $("#changePasswordMessage")
              .text("Failed to process the response.")
              .show();
            resetForms();
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $("#changePasswordMessage")
            .text("An error occurred: " + errorThrown)
            .show();
          console.error("AJAX request failed:", textStatus, errorThrown);
          resetForms();
        },
      });
    } else {
      resetForms();
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

  // Hide error message when email input field changes
  $("#email").on("input", function () {
    $("#forgotPasswordMessage").hide();
  });

  // Send OTP button click handler
  $("#sendOtpButton").on("click", function () {
    sendOtp();
  });
});
