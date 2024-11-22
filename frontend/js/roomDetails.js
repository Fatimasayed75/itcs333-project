const homeBtn = document.getElementById("backToHomeBtn");
homeBtn.addEventListener("click", navigateToHomePage);

async function navigateToHomePage() {
  await loadContent("home.php");
  initializeHomeEventListeners();
}
