// Call this function after re-rendering the room cards
document.addEventListener("DOMContentLoaded", () => {
  initializeHomeEventListeners();
  initFilter();
});

function initializeHomeEventListeners() {
  
  if(IsAdmin()){
    initalizeListenersForAddRoom();
  }
  // Initialize listeners to view details for notifications (this selects multiple buttons with the same ID)
  document.querySelectorAll(".view-details-noti").forEach((button) => {
    button.addEventListener("click", async function (e) {
      await loadContent("notifiations.php");

      const notificationsNavLinkSidebar = document
        .querySelector(".sidebar .nav-link a#notifiations-tab")
        ?.closest(".nav-link");
      const notificationsNavLinkTopNav = document
        .querySelector(".top-nav .nav-link a#notifiations-tab")
        ?.closest(".nav-link");

      // Set notifications tab as active
      setActiveLink(notificationsNavLinkSidebar);
      setActiveLink(notificationsNavLinkTopNav);
    });
  });

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
  initializeRoomSearch(roomCards, roomSquares);
  initFilter();

  document.getElementById("filterIcon").classList.remove("hidden");
  // Initialize countdown on page load
  if(!IsGuest()){
    startCountdown();
  }

  // Initialize listeners to view details for booking history
  document
    .getElementById("view-details-history")
    ?.addEventListener("click", async function (e) {
      await loadContent("bookings.php");

      const bookingsNavLinkSidebar = document
        .querySelector(".sidebar .nav-link a#bookings-tab")
        ?.closest(".nav-link");
      setActiveLink(bookingsNavLinkSidebar);
    });
}

function initializeRoomViewToggle() {
  const cardViewBtn = document.getElementById("cardViewBtn");
  const gridViewBtn = document.getElementById("gridViewBtn");
  const roomCards = document.querySelectorAll(".room-card");
  const roomSquares = document.querySelectorAll(".room-square");
  const floorNav = document.querySelector(
    ".flex.justify-center.space-x-8.mb-4"
  ); // Floor navigation section

  function setActiveButton(button) {
    const buttons = document.querySelectorAll(".view-toggle-btn");
    buttons.forEach((btn) => btn.classList.remove("border-b-4"));
    button.classList.add("border-b-4");
  }

  // Card view event
  cardViewBtn.addEventListener("click", () => {
    // Show room cards, hide room squares
    roomCards.forEach((card) => card.classList.remove("hidden"));
    roomSquares.forEach((square) => {
      square.style.display = "none";
    });

    setActiveButton(cardViewBtn);

    // Hide floor navigation in card view
    if (floorNav) {
      floorNav.classList.add("hidden");
    }
    document.getElementById("grid-rooms").classList.add("hidden");
    document.getElementById("filterDiv").classList.remove("hidden");
    initFilter();
    clearFilters();
  });

  // Grid view event
  gridViewBtn.addEventListener("click", () => {
    // Show room squares, hide room cards
    roomCards.forEach((card) => card.classList.add("hidden"));
    roomSquares.forEach((square) => square.classList.remove("hidden"));
    setActiveButton(gridViewBtn);
    // Show floor navigation in grid view
    if (floorNav) {
      floorNav.classList.remove("hidden");
    }
    document.getElementById("filterDiv").classList.add("hidden");
    clearFilters();
    initializeFloorNavigation();
  });

  // Set default active view and ensure room cards are visible initially
  setActiveButton(cardViewBtn);
  roomCards.forEach((card) => card.classList.remove("hidden"));
  roomSquares.forEach((square) => square.classList.add("hidden"));
}

// async function bookRoom(roomId) {
//   try {
//     const response = await fetch(`../../../backend/server/bookRoom.php`, {
//       method: "POST",
//       headers: {
//         "Content-Type": "application/json",
//       },
//       body: JSON.stringify({ roomID: roomId }),
//     });

//     if (!response.ok) {
//       throw new Error("Network response was not ok");
//     }

//     const data = await response.json();
//     if (data.success) {
//       alert("Room booked successfully!");
//     } else {
//       alert("Failed to book the room.");
//     }
//   } catch (error) {
//     console.error("Error booking room:", error);
//     alert("Failed to book the room. Please try again later.");
//   }
// }
