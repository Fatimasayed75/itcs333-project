document.addEventListener("DOMContentLoaded", () => {
  initializeProfile(); // Initialize the dashboard only once when the page is loaded
});


function initializeProfile() {
  // Function to open the edit profile modal
  window.openEditProfileModal = function () {
    const modal = document.getElementById("editProfileModal");
    if (modal) {
      modal.classList.remove("hidden");
      document.body.style.overflow = "hidden";
    }
  };

  // Function to close the edit profile modal
  window.closeEditProfileModal = function () {
    const modal = document.getElementById("editProfileModal");
    if (modal) {
      modal.classList.add("hidden");
      document.body.style.overflow = "";
    }
  };

  // Function to preview the selected image
  window.previewImage = function (input) {
    const preview = document.getElementById("profilePicPreview");

    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function (e) {
        preview.src = e.target.result; 
      };
      reader.readAsDataURL(input.files[0]); 
    } else {
      console.error("No file selected or input is empty.");
    }
  };

  // Handle Edit Profile form submission
  const editProfileForm = document.getElementById("editProfileForm");
  if (editProfileForm) {
    editProfileForm.addEventListener("submit", function (e) {
      e.preventDefault();

      fetch("../components/editProfile.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            alert(data.message);
            closeEditProfileModal();
            location.reload();
          } else {
            alert("Error: " + data.message);
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("An error occurred while updating the profile.");
        });
    });
  }
  // Close modal when clicking outside
  const editProfileModal = document.getElementById("editProfileModal");
  if (editProfileModal) {
    editProfileModal.addEventListener("click", function (e) {
      if (e.target === this) {
        closeEditProfileModal();
      }
    });
  }

  // Prevent modal close when clicking inside
  const modalContent = document.querySelector(".inline-block");
  if (modalContent) {
    modalContent.addEventListener("click", function (e) {
      e.stopPropagation();
    });
  }

  // Close modal with Escape key
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      closeEditProfileModal();
    }
  });

  // Open Change Password Modal
  window.openChangePasswordModal = function () {
    const modal = document.getElementById("changePasswordModal");
    if (modal) {
      modal.classList.remove("hidden");
      document.body.style.overflow = "hidden";
    }
  };

  // Close Change Password Modal
  window.closeChangePasswordModal = function () {
    const modal = document.getElementById("changePasswordModal");
    if (modal) {
        modal.classList.add("hidden");
        document.body.style.overflow = "";

        // Reset form inputs
        const form = document.getElementById("changePasswordForm");
        if (form) {
            form.reset();
        }

        // Clear any error messages
        const errorMessage = document.getElementById("errorMessage");
        if (errorMessage) {
            errorMessage.textContent = "";
            errorMessage.classList.add("hidden");
        }
    }
};


  // Handle Change Password form submission and validation
  const changePasswordForm = document.getElementById("changePasswordForm");
  const passwordError = document.getElementById("passwordError");
  const successMessage = document.getElementById("successMessage");
  const errorMessage = document.getElementById("errorMessage");

  if (changePasswordForm) {
    changePasswordForm.addEventListener("submit", function (e) {
      e.preventDefault();  // This is crucial to prevent the default form submission
      console.log("Form submission prevented");

      const currentPassword = document.getElementById("currentPassword").value;
      const newPassword = document.getElementById("newPassword").value;
      const confirmPassword = document.getElementById("confirmPassword").value;

      if (newPassword !== confirmPassword) {
        passwordError.classList.remove("hidden");
        console.log("Passwords do not match.");
      } else {
        passwordError.classList.add("hidden");

        // Send AJAX request to update password
        const formData = new FormData();
        formData.append("currentPassword", currentPassword);
        formData.append("newPassword", newPassword);

        fetch("../../../backend/server/changePassword.php", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              successMessage.textContent = data.message;
              successMessage.classList.remove("hidden");
              errorMessage.classList.add("hidden");
              setTimeout(() => {
                closeChangePasswordModal(); // Close the modal after success
              }, 1500);
            } else {
              errorMessage.textContent = data.message;
              errorMessage.classList.remove("hidden");
              successMessage.classList.add("hidden");
            }
          })
          .catch((error) => {
            console.error("Error updating password:", error);
            errorMessage.textContent = "An error occurred. Please try again later.";
            errorMessage.classList.remove("hidden");
            successMessage.classList.add("hidden");
          });
      }
    });
  } else {
    console.log("Form not found!"); // Check if form was found
  }

  // Close Change Password modal when clicking outside
  const changePasswordModal = document.getElementById("changePasswordModal");
  if (changePasswordModal) {
    changePasswordModal.addEventListener("click", function (e) {
      if (e.target === this) {
        closeChangePasswordModal();
      }
    });
  }

  // Prevent modal close when clicking inside
  const changePasswordModalContent = document.querySelector(".inline-block");
  if (changePasswordModalContent) {
    changePasswordModalContent.addEventListener("click", function (e) {
      e.stopPropagation();
    });
  }

  // Close modal with Escape key
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      closeChangePasswordModal();
    }
  });
}