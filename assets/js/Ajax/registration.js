$(document).ready(function () {
  $("#otpSection").hide();
  $("#resendOtpButton").hide();
  $(".register-form").show();
  let otpExpiryTime = 5 * 60 * 1000; // 5 minutes
  let otpGeneratedAt = null;
  let otpTimeout = null;

  function resetOtpSection() {
    $("#otpSection").hide();
    $("#otp").val("");
    $("#emailError")
      .text("OTP has expired. Request a new one.")
      .css("color", "red")
      .show();
    $("#resendOtpButton").show();
  }

  function resendOtp() {
    const email = $("#email").val().trim();
    if (email === "") {
      $("#emailError").text("Email is required").css("color", "red").show();
      return;
    }

    $("#resendOtpButton").hide(); // Hide the resend button while the OTP is being sent
    $.ajax({
      url: "/becho2/App/public/main.php",
      type: "POST",
      data: {
        action: "sendRegisterOtp",
        email: email,
      },
      success: function (response) {
        try {
          const parsedResponse = JSON.parse(response);
          $("#registrationMessage")
            .text(parsedResponse.message)
            .css("color", "green")
            .show();
          if (parsedResponse.status === "success") {
            otpGeneratedAt = new Date();
            clearTimeout(otpTimeout); // Clear any existing timeout
            otpTimeout = setTimeout(resetOtpSection, otpExpiryTime);
          } else {
            $("#otpSection").hide();
          }
        } catch (e) {
          $("#registrationMessage")
            .text("Invalid server response")
            .css("color", "red")
            .show();
        }
      },
      error: function () {
        $("#emailError").text("Try again").css("color", "red").show();
        $("#otpSection").hide();
      },
    });
  }

  // Send OTP
  $("#verifyEmailButton").on("click", function () {
    const email = $("#email").val().trim();
    if (email === "") {
      $("#emailError").text("Email is required").css("color", "red").show();
      return;
    }

    $("#verifyEmailButton").hide();
    $("#otpSection").show();
    $("#emailError")
      .text("OTP sent to your email")
      .css("color", "green")
      .show();

    $.ajax({
      url: "/becho2/App/public/main.php",
      type: "POST",
      data: {
        action: "sendRegisterOtp",
        email: email,
      },
      success: function (response) {
        try {
          const parsedResponse = JSON.parse(response);
          $("#emailError")
            .text(parsedResponse.message)
            .css("color", "green")
            .show();
          if (parsedResponse.status === "success") {
            otpGeneratedAt = new Date();
            otpTimeout = setTimeout(resetOtpSection, otpExpiryTime);
          } else {
            $("#otpSection").hide();
          }
        } catch (e) {
          $("#registrationMessage")
            .text("Invalid server response")
            .css("color", "red")
            .show();
        }
      },
      error: function () {
        $("#emailError").text("Try again").css("color", "red").show();
        $("#otpSection").hide();
      },
    });
  });

  // Verify OTP and Register User
  $("#registerButton").on("click", function () {
    let isValid = true;

    // Validate fields
    isValid &= validateField(
      $("#username").val(),
      "usernameError",
      "Username is required"
    );
    isValid &= validateField(
      $("#email").val(),
      "emailError",
      "Email is required"
    );
    isValid &= validateField(
      $("#password").val(),
      "passwordError",
      "Password is required"
    );
    isValid &= validateField(
      $("#confirmPassword").val(),
      "confirmPasswordError",
      "Confirm Password is required"
    );
    isValid &= validateField(
      $("#location").val(),
      "locationError",
      "Location is required"
    );
    isValid &= validateField(
      $("#phone").val(),
      "phoneError",
      "Phone Number is required"
    );

    // Check if passwords match
    if ($("#password").val() !== $("#confirmPassword").val()) {
      $("#confirmPasswordError")
        .text("Passwords do not match")
        .css("color", "red")
        .show();
      isValid = false;
    } else {
      $("#confirmPasswordError").text("").hide();
    }

    // Check if OTP is provided
    if ($("#otpSection").is(":visible") && $("#otp").val().trim() === "") {
      $("#otpError").text("Please enter OTP").css("color", "red").show();
      isValid = false;
    } else {
      $("#otpError").text("").hide();
    }

    if (!isValid) {
      return;
    }

    const otp = $("#otp").val().trim();
    $.ajax({
      url: "/becho2/App/public/main.php",
      type: "POST",
      data: {
        action: "verifyOtp",
        otp: otp,
      },
      success: function (response) {
        try {
          const parsedResponse = JSON.parse(response);
          $("#otpMessage")
            .text(parsedResponse.message)
            .css("color", "green")
            .show();
          if (parsedResponse.status === "success") {
            // Proceed with registration
            const formData = new FormData();
            formData.append("action", "registerUser");
            formData.append("username", $("#username").val());
            formData.append("email", $("#email").val());
            formData.append("password", $("#password").val());
            formData.append("location", $("#location").val());
            formData.append("phone", $("#phone").val());
            formData.append("otp", otp);

            const profilePicture = $("#profilePicture").prop("files")[0];
            if (profilePicture) {
              formData.append("profilePicture", profilePicture);
            }

            $.ajax({
              url: "/becho2/App/public/main.php",
              type: "POST",
              data: formData,
              processData: false,
              contentType: false,
              success: function (response) {
                try {
                  const parsedResponse = JSON.parse(response);
                  $("#registrationMessage")
                    .text(parsedResponse.message)
                    .css("color", "green")
                    .show();
                  if (parsedResponse.status === "success") {
                    $("#card").removeClass("d-none");
                    $("#registrationDiv").addClass("d-none");
                    $(".register-form").hide();
                  } else {
                    $("#registrationMessage")
                      .text(parsedResponse.message)
                      .css("color", "red")
                      .show();
                    $("#email").val("");
                    $("#otpSection").hide();
                    $("#verifyEmailButton").show();
                    $("#otp").val("");
                  }
                } catch (e) {
                  $("#registrationMessage")
                    .text("Invalid server response")
                    .css("color", "red")
                    .show();
                }
              },
              error: function () {
                $("#registrationMessage")
                  .text("An error occurred. Please try again.")
                  .css("color", "red")
                  .show();
              },
            });
          } else {
            $("#otpMessage")
              .text(parsedResponse.message)
              .css("color", "red")
              .show();
          }
        } catch (e) {
          $("#otpMessage")
            .text("Invalid server response")
            .css("color", "red")
            .show();
        }
      },
      error: function () {
        $("#otpMessage")
          .text("An error occurred. Please try again.")
          .css("color", "red")
          .show();
      },
    });
  });

  function validateField(value, fieldId, errorMessage) {
    if (value.trim() === "") {
      $(`#${fieldId}`).text(errorMessage).css("color", "red").show();
      return false;
    } else {
      $(`#${fieldId}`).text("").hide();
      return true;
    }
  }

  // Resend OTP Button
  $("#resendOtpButton").on("click", resendOtp);

  // Handle OTP expiration
  function handleOtpExpiration() {
    $("#otpSection").hide();
    $("#verifyEmailButton").hide();
    $("#resendOtpButton").show();
    $("#emailError")
      .text("OTP has expired. Request a new one.")
      .css("color", "red")
      .show();
  }

  otpTimeout = setTimeout(handleOtpExpiration, otpExpiryTime);

  // Image preview
  $("#profilePicture").on("change", function () {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        $("#imagePreview").attr("src", e.target.result).show();
      };
      reader.readAsDataURL(file);
    } else {
      $("#imagePreview").attr("src", "").hide();
    }
  });
});
