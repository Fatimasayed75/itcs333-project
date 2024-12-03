function initializeRoomSearch(roomCards, roomSquares, currentFloor) {
  const searchInput = document.getElementById("roomSearch");

  if (!searchInput) return; // Exit if search input is not found

  searchInput.addEventListener("input", (e) => {
    const searchTerm = e.target.value.toLowerCase();

    // Handle room cards visibility
    roomCards.forEach((card) => {
      const roomId = card.querySelector("h3").textContent.toLowerCase();
      if (roomId.includes(searchTerm)) {
        card.style.display = "";
      } else {
        card.style.display = "none";
      }
    });

    // Handle room squares
    roomSquares.forEach((square) => {
      const roomId = square.getAttribute("data-room-id").toLowerCase();
      const roomFloor = square.getAttribute("data-room-floor").toLowerCase();

      if (searchTerm === "") {
        // Reset the square to default state when search term is empty
        square.classList.remove("shine-border");

        if (roomFloor == currentFloor) {
          square.style.display = "block";
        } else {
          square.style.display = "none";
        }
      } else {
        if (roomId.includes(searchTerm)) {
          // Highlight with red and add shining border effect if it matches the search term
          square.classList.add("shine-border");
        } else {
          square.classList.remove("shine-border");
        }
      }
    });
  });
}
