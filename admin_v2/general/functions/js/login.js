$(document).ready(function () {
  // Load remembered ID
  loadRememberedId();

  // Password visibility toggle
  $(".show-password-btn").click(function () {
    const input = $("#password");
    const icon = $(this).find("i");

    if (input.attr("type") === "password") {
      input.attr("type", "text");
      icon.removeClass("bi-eye").addClass("bi-eye-slash");
    } else {
      input.attr("type", "password");
      icon.removeClass("bi-eye-slash").addClass("bi-eye");
    }
  });

  // Remember ID checkbox change
  $(".jw-checkbox input").change(function () {
    if (!$(this).is(":checked")) {
      localStorage.removeItem("rememberedId");
    }
  });

  // Login form submission
  $("#loginForm").on("submit", function (e) {
    e.preventDefault(); // VERY IMPORTANT

    let identifier = $("#identifier").val().trim();
    let password = $("#password").val().trim();
    let rememberMe = $("#remember_me").is(":checked");

    // Disable login button while processing
    setButtonLoading(true);

    $.ajax({
      url: "./general/functions/login-process.php",
      type: "POST",
      data: { identifier: identifier, password: password },
      dataType: "json",
      success: function (response) {
        if (response.success) {

          // If successful → revert button back to normal
          setButtonLoading(false);

          if (rememberMe) {
            localStorage.setItem("rememberedId", identifier);
          } else {
            localStorage.removeItem("rememberedId");
          }

          showMessage(response.message, "success");

          setTimeout(() => {
            window.location.href = response.redirect;
          }, 1000);

        } else {
          // Show error above login button, clear password
          showMessage(response.message, "error");
          $("#password").val("");
          setButtonLoading(false);
        }
      },
      error: function (xhr) {
        console.log(xhr.responseText);
        showMessage("An error occurred. Please try again.", "error");
        setButtonLoading(false);
      },
    });

  });
});

// Load remembered ID from localStorage
function loadRememberedId() {
  const rememberedId = localStorage.getItem("rememberedId");
  if (rememberedId) {
    $("#identifier").val(rememberedId);
    $("#remember_me").prop("checked", true);
  }
}

function showMessage(message, type) {
  const box = $(".login-message-box");
  box.removeClass("error success").addClass(type);
  box.text(message).fadeIn(200);

  // Clear password field if error
  if (type === "error") {
    $("#password").val("");
  }

  // Auto-hide after 3 seconds
  setTimeout(() => {
    box.fadeOut(300);
  }, 3000);
}




// Set button loading state
function setButtonLoading(isLoading) {
  const loginButton = $(".login-button");

  if (isLoading) {
    loginButton.prop("disabled", true);
    loginButton.data("original-text", loginButton.text());
    loginButton.text("Logging in...");
    loginButton.css("opacity", "0.6");
  } else {
    loginButton.prop("disabled", false);

    // restore original text (Korean → English → fallback)
    loginButton.text(
      loginButton.data("original-text") ||
      loginButton.attr("data-lan-eng") ||
      "Login"
    );

    loginButton.css("opacity", "1");
  }
}

// Reset button on normal page load
$(document).ready(function () {
  setButtonLoading(false);
});

// Reset button on browser BACK (bfcache restore)
window.addEventListener("pageshow", function (event) {
  if (event.persisted) {
    setButtonLoading(false);
  }
});

