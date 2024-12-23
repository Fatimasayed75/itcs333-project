const sidebar = document.querySelector(".sidebar");
const toggle = document.querySelector(".toggle");
const modeSwitch = document.querySelector(".toggle-switch");
const sidebarLinks = document.querySelectorAll(".sidebar .nav-link");
const topNavLinks = document.querySelectorAll(".top-nav .nav-link");
let isDashboardInitialized = false; // Flag to track dashboard initialization state

// Sidebar toggle
toggle.addEventListener("click", () => {
  sidebar.classList.toggle("close");
  document.body.classList.toggle("sidebar-expanded");
});

// Dark mode functionality
function updateDarkModeUI(isDark) {
  document.body.classList.toggle("dark-mode", isDark);
  document.querySelectorAll('[role="switch"]').forEach((toggle) => {
    toggle.setAttribute("aria-checked", isDark.toString());
  });
}

function initializeDarkMode() {
  // Check if user has a saved preference
  const savedTheme = localStorage.getItem("theme");
  if (savedTheme) {
    updateDarkModeUI(savedTheme === "dark");
  } else {
    // Check system preference
    const prefersDark = window.matchMedia(
      "(prefers-color-scheme: dark)"
    ).matches;
    updateDarkModeUI(prefersDark);
    localStorage.setItem("theme", prefersDark ? "dark" : "light");
  }
}

// Initialize dark mode on page load
initializeDarkMode();

// Handle dark mode toggle clicks
function handleDarkModeToggle() {
  const isDark = !document.body.classList.contains("dark-mode");
  updateDarkModeUI(isDark);
  localStorage.setItem("theme", isDark ? "dark" : "light");
}

// Add click listeners to all dark mode toggles
document
  .querySelectorAll(".toggle-switch, .toggle-dark-mode")
  .forEach((toggle) => {
    toggle.addEventListener("click", handleDarkModeToggle);

    // Add keyboard support
    toggle.addEventListener("keydown", (e) => {
      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        handleDarkModeToggle();
      }
    });
  });

// Listen for system theme changes
window
  .matchMedia("(prefers-color-scheme: dark)")
  .addEventListener("change", (e) => {
    if (!localStorage.getItem("theme")) {
      // Only react if user hasn't set a preference
      updateDarkModeUI(e.matches);
      localStorage.setItem("theme", e.matches ? "dark" : "light");
    }
  });

// Set active for sidebar and top-nav links
sidebarLinks.forEach((link) => {
  link.addEventListener("click", async function () {
    setActiveLink(link);
    const page =
      link.querySelector("a").getAttribute("id").replace("-tab", "").trim() +
      ".php";
    await loadContent(page);

    if (page === "home.php") {
      initializeHomeEventListeners();
    } else if (page === "dashboard.php") {
      initializeDashboard(); // Initialize dashboard only once
      isDashboardInitialized = true; // Set flag to true to prevent reinitialization
    }
  });
});

topNavLinks.forEach((link) => {
  link.addEventListener("click", async function () {
    setActiveLink(link);
    const page =
      link.querySelector("a").getAttribute("id").replace("-tab", "").trim() +
      ".php";
    await loadContent(page);

    if (page === "home.php") {
      initializeHomeEventListeners();
    } else if (page === "dashboard.php") {
      initializeDashboard(); // Initialize dashboard only once
      isDashboardInitialized = true; // Set flag to true to prevent reinitialization
    }
  });
});

async function loadContent(page) {
  try {
    const response = await fetch(`../components/${page}`);
    if (!response.ok) {
      throw new Error("Page not found");
    }
    const data = await response.text();
    document.getElementById("main-content").innerHTML = data;
    localStorage.setItem("current-page", page);

    // Reset dashboard initialization flag when leaving the dashboard page
    if (page !== "dashboard.php") {
      isDashboardInitialized = false;
    }

    // Initialize page-specific JavaScript
    if (page === "profile.php") {
      // Reinitialize profile event listeners
      document
        .getElementById("editProfileForm")
        ?.addEventListener("submit", function (e) {
          e.preventDefault();
          const formData = new FormData(this);

          fetch("../components/editProfile.php", {
            method: "POST",
            body: formData,
          })
            .then((response) => {
              if (response.ok) {
                alert("Profile updated successfully!");
                closeEditProfileModal();
                window.location.reload();
                return response.json();
              }
              throw new Error("Response not okay");
            })
            .then((data) => {
              alert("Profile updated successfully!");
              closeEditProfileModal();
            })
            .catch((error) => {
              console.error("Error:", error);
              // alert('An error occurred while updating the profile');
            });
        });

      // Initialize modal close on outside click
      document
        .getElementById("editProfileModal")
        ?.addEventListener("click", function (e) {
          if (e.target === this) {
            closeEditProfileModal();
          }
        });

        document.getElementById("changePasswordForm")?.addEventListener("submit", function (e) {
          e.preventDefault();
      
          const currentPassword = document.getElementById("currentPassword").value;
          const newPassword = document.getElementById("newPassword").value;
          const confirmPassword = document.getElementById("confirmPassword").value;
          const errorMessage = document.getElementById("errorMessage");

           // Clear any existing error messages before making the request
          errorMessage.textContent = "";
          errorMessage.classList.add("hidden");
      
          // Send AJAX request to update password
          const formData = new FormData();
          formData.append("currentPassword", currentPassword);
          formData.append("newPassword", newPassword);
          formData.append("confirmPassword", confirmPassword);
      
          fetch("../../../backend/server/changePassword.php", {
              method: "POST",
              body: formData,
          })
              .then((response) => {
                  if (!response.ok) {
                      throw new Error(`HTTP error! Status: ${response.status}`);
                  }
                  return response.json();
              })
              .then((data) => {
                  if (data.success) {
                      alert(data.message);
                      closeChangePasswordModal();
                  } else {
                      errorMessage.textContent = data.message;
                      errorMessage.classList.remove("hidden");
                  }
              })
              .catch((error) => {
                  console.error("Error updating password:", error);
                  errorMessage.textContent = "An error occurred. Please try again.";
                  errorMessage.classList.remove("hidden");
              });
      });
      

    }
  } catch (error) {
    console.error("Error loading content: ", error);
    document.getElementById("main-content").innerHTML =
      "Content not available.";
  }
}

async function loadEditProfile() {
  try {
    const response = await fetch("../components/editProfile.php");
    if (!response.ok) {
      throw new Error("Page not found");
    }
    const data = await response.text();
    document.getElementById("main-content").innerHTML = data;
    localStorage.setItem("current-page", "editProfile.php");
  } catch (error) {
    console.error("Error loading content: ", error);
    document.getElementById("main-content").innerHTML =
      "Content not available.";
  }
}

function setActiveLink(link) {
  // Remove active class from all sidebar and top-nav links
  [...sidebarLinks, ...topNavLinks].forEach((item) =>
    item.classList.remove("active")
  );

  // Add active class to the clicked link
  link.classList.add("active");

  const linkId = link.querySelector("a").getAttribute("id");

  // Sync active state between sidebar and top-nav
  if (link.closest(".sidebar")) {
    const matchingTopLink = document.querySelector(
      `.top-nav .nav-link a[id="${linkId}"]`
    );
    if (matchingTopLink) {
      matchingTopLink.parentElement.classList.add("active");
    }
  } else if (link.closest(".top-nav")) {
    const matchingSidebarLink = document.querySelector(
      `.sidebar .nav-link a[id="${linkId}"]`
    );
    if (matchingSidebarLink) {
      matchingSidebarLink.parentElement.classList.add("active");
    }
  }
}
