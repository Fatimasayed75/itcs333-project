function initializeRoomSearch(roomCards) {
  const searchInput = document.getElementById("roomSearch");

  if (!searchInput) return; // Exit if search input is not found

  searchInput.addEventListener("input", (e) => {
    const searchTerm = e.target.value.toLowerCase();

    roomCards.forEach((card) => {
      const roomId = card.querySelector("h3").textContent.toLowerCase();
      const roomType = card.dataset.roomType || "";
      const roomCapacity = card.dataset.roomCapacity || "";

      if (
        roomId.includes(searchTerm) ||
        roomType.includes(searchTerm) ||
        roomCapacity.includes(searchTerm)
      ) {
        card.style.display = "";
      } else {
        card.style.display = "none";
      }
    });
  });
}
