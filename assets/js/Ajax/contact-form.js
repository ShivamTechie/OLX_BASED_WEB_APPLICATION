$(document).ready(function () {
  $("#contactForm").on("submit", function (e) {
    e.preventDefault(); // Prevent the default form submission

    // Collect form data
    var formData = {
      action: "submitContactForm",
      name: $("#name").val(),
      email: $("#email").val(),
      subject: $("#msg_subject").val(),
      message: $("textarea").val(),
    };

    // Show loading indicator
    Swal.fire({
      title: "Sending...",
      text: "Please wait while we send your message.",
      allowOutsideClick: false,
      allowEscapeKey: false,
      showConfirmButton: false,
      didOpen: function () {
        Swal.showLoading();
      },
    });

    // Make AJAX request
    $.ajax({
      type: "POST",
      url: "/becho2/App/public/main.php", // Replace with your actual server-side script URL
      data: formData,
      success: function (response) {
        // Hide loading indicator and show success message
        Swal.fire({
          icon: "success",
          title: "Message Sent!",
          text: "Your message has been sent successfully.",
          showConfirmButton: false,
          timer: 2000,
        }).then(function () {
          // Reset the form after success message
          $("#contactForm")[0].reset();
        });
      },
      error: function () {
        // Hide loading indicator and show error message
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: "Something went wrong. Please try again later.",
          showConfirmButton: false,
          timer: 2000,
        });
      },
    });
  });
});
