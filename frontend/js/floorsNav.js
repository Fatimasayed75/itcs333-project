function initializeFloorNavigation() {
  let currentFloor = 0; // Start at floor 0
  const totalFloors = 2; // 0 1 2
  // remove hidden from #grid-rooms
  document.getElementById("grid-rooms").classList.remove("hidden");
  const roomSquares = document.querySelectorAll("#grid-rooms .dep .room-square");
  const roomCards = document.querySelectorAll(".room-card"); // Add room cards here for consistency

  // Function to update the current floor and filter rooms
  function updateFloor() {
    // Update the floor display
    const floorDisplay =
      currentFloor === 0 ? "Ground Floor" : `Floor ${currentFloor}`;
    document.getElementById("currentFloor").textContent = floorDisplay;

    // Show/hide rooms based on the current floor
    roomSquares.forEach((card) => {
      const roomFloor = card.getAttribute("data-room-floor");
      if (parseInt(roomFloor) == currentFloor) {
        card.style.display = "block"; // Show room card
      } else {
        card.style.display = "none"; // Hide room card
      }
    });

    // Reinitialize the room search with the updated floor value
    initializeRoomSearch(roomCards, roomSquares, currentFloor);
  }

  // Event listener for the previous floor button
  document
    .getElementById("prevFloorBtn")
    .addEventListener("click", function () {
      if (currentFloor > 0) {
        currentFloor--; // Go to the previous floor
        updateFloor(); // Update the displayed rooms
      }
    });

  // Event listener for the next floor button
  document
    .getElementById("nextFloorBtn")
    .addEventListener("click", function () {
      if (currentFloor < totalFloors) {
        currentFloor++; // Go to the next floor
        updateFloor(); // Update the displayed rooms
      }
    });

  // Initialize the floor display when the page loads
  updateFloor();
}
