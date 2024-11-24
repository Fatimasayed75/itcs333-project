// Call this function after re-rendering the room cards
document.addEventListener("DOMContentLoaded", () => {
  initializeHomeEventListeners();
});

function initializeHomeEventListeners() {
  const roomCards = document.querySelectorAll(".room-card");
  roomCards.forEach((card) => {
    card.addEventListener("click", async function () {
      const roomId = this.dataset.roomId;
      await loadRoomDetails(roomId);
    });
  });
  const roomSquares = document.querySelectorAll(".room-square");
  roomSquares.forEach((square) => {
    square.addEventListener("click", async function () {
      const roomId = this.dataset.roomId;
      await loadRoomDetails(roomId);
    });
  });
  initializeRoomViewToggle();
}

function initializeRoomViewToggle() {
  const cardViewBtn = document.getElementById("cardViewBtn");
  const gridViewBtn = document.getElementById("gridViewBtn");
  const roomCards = document.querySelectorAll(".room-card");
  const roomSquares = document.querySelectorAll(".room-square");

  function setActiveButton(button) {
    const buttons = document.querySelectorAll(".view-toggle-btn");
    buttons.forEach((btn) => btn.classList.remove("border-b-4"));
    button.classList.add("border-b-4");
  }

  cardViewBtn.addEventListener("click", () => {
    roomCards.forEach((card) => card.classList.remove("hidden"));
    roomSquares.forEach((square) => square.classList.add("hidden"));
    setActiveButton(cardViewBtn);
  });

  gridViewBtn.addEventListener("click", () => {
    roomCards.forEach((card) => card.classList.add("hidden"));
    roomSquares.forEach((square) => square.classList.remove("hidden"));
    setActiveButton(gridViewBtn);
  });

  // Set default active view
  setActiveButton(cardViewBtn);
}

async function loadRoomDetails(roomId) {
  try {
    const response = await fetch(
      `../../../backend/server/roomDetails.php?roomID=${roomId}`
    );
    const data = await response.json();
    if (data.error) {
      throw new Error(data.error);
    }

    // Fetch room details HTML template
    const templateResponse = await fetch("../components/roomDetails.php");
    if (!templateResponse.ok) {
      throw new Error("Failed to load the room details template");
    }

    const template = await templateResponse.text();

    let roomData = data[0];

    // Replace placeholders in the HTML template
    const filledTemplate = template
      .replace(/{{roomID}}/g, roomData.roomID)
      .replace(/{{type}}/g, roomData.type)
      .replace(/{{capacity}}/g, roomData.capacity)
      .replace(
        /{{image}}/g,
        `https://placehold.co/300x200?text=Image+For+${roomData.roomID}`
      );

    // Update the page content with room details
    const mainContent = document.getElementById("main-content");
    mainContent.innerHTML = filledTemplate;
    const homeBtn = document.getElementById("backToHomeBtn");
    if (homeBtn) {
      homeBtn.addEventListener("click", navigateToHomePage);
    }
  } catch (error) {
    console.error("Error loading room details:", error);
    // alert("Failed to load room details.");
  }
}
async function navigateToHomePage() {
  await loadContent("home.php");
  initializeHomeEventListeners();
}