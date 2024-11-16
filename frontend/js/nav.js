const sidebar = document.querySelector(".sidebar");
const toggle = document.querySelector(".toggle");
const modeSwitch = document.querySelector(".toggle-switch");
const sidebarLinks = document.querySelectorAll(".sidebar .nav-link");
const topNavLinks = document.querySelectorAll(".top-nav .nav-link");

// Sidebar toggle
toggle.addEventListener("click", () => {
  sidebar.classList.toggle("close");
  document.body.classList.toggle("sidebar-expanded");
});

// Dark mode switch
modeSwitch.addEventListener("click", () => {
  document.body.classList.toggle("dark-mode");
});

// Set active for sidebar and top-nav links
sidebarLinks.forEach((link) => {
  link.addEventListener("click", async function () {
    setActiveLink(link);
    const page =
      link.querySelector("a").getAttribute("id").replace("-tab", "").trim() +
      ".php";
    await loadContent(page);
  });
});

topNavLinks.forEach((link) => {
  link.addEventListener("click", async function () {
    setActiveLink(link);
    const page =
      link.querySelector("a").getAttribute("id").replace("-tab", "").trim() +
      ".php";
    await loadContent(page);
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
