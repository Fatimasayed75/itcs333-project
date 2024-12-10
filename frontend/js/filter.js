function initFilter() {
  document.getElementById("filterIcon").addEventListener("click", function () {
    // Show the modal
    document.getElementById("filterBox").classList.remove("hidden");

    console.log("Filter icon clicked");
    // Dynamically load filter content into the modal
    fetch("../../templates/components/filter-modal.php")
      .then((response) => response.text())
      .then((data) => {
        document.querySelector("#filterBox .bg-white").innerHTML = data;
      })
      .catch((error) => console.error("Error loading filter content:", error));
  });

  document.getElementById("clearFilterIcon").addEventListener("click", clearFilters);
}

// Function to hide the filter box
function hideFilterBox() {
  document.getElementById("filterBox").classList.add("hidden");
}

function applyFilters() {
  const available = document.getElementById("available").value;
  const type = document.getElementById("type").value;
  const capacity = document.getElementById("capacity").value;
  const cardRooms = document.querySelectorAll(".room-card");

  console.log("Applying filters: ", document.getElementById("capacity"));
  console.log("capacity: ", capacity);
  console.log("type: ", type);
  console.log("available: ", available);

  let found = false;
  cardRooms.forEach((card) => {
    const roomAvailable =
      card.getAttribute("data-room-available") == 1 ? "yes" : "no";
    const roomType = card.getAttribute("data-room-type");
    const roomCapacity = card.getAttribute("data-room-capacity");

    const roomCapacityInt = parseInt(roomCapacity, 10);
    const capacityInt = parseInt(capacity, 10);

    if (
      (available == "any" || roomAvailable == available) &&
      (type == "any" || roomType == type) &&
      (capacity == "any" || roomCapacityInt >= capacityInt)
    ) {
      card.style.display = "block";
      found = true;
    } else {
      card.style.display = "none";
    }
  });

  // If no rooms are found, display the "No results" message
  const noResultsMessage = document.getElementById("noResultsMessage");
  if (!found) {
    noResultsMessage.style.display = "block"; // Show the message
  } else {
    noResultsMessage.style.display = "none"; // Hide the message if rooms are found
  }

  // hide the filter modal after applying filters
  hideFilterBox();
  document.getElementById("clearFilterIcon").classList.remove("hidden");
}

function hideFilterBox() {
  // Your logic to close the filter box/modal
  const filterBox = document.getElementById("filterBox");
  filterBox.classList.add("hidden");
}

function clearFilters() {
  const availableFilter = document.getElementById("available");
  const typeFilter = document.getElementById("type");
  const capacityFilter = document.getElementById("capacity");

  if (availableFilter) availableFilter.value = "";
  if (typeFilter) typeFilter.value = "";
  if (capacityFilter) capacityFilter.value = "";

  const cardRooms = document.querySelectorAll(".room-card");
  cardRooms.forEach((card) => {
    card.style.display = "";
  });
  document.getElementById("clearFilterIcon").classList.add("hidden");
  document.getElementById("noResultsMessage").style.display = "none";
  hideFilterBox();
  // clear search input
  document.getElementById("roomSearch").value = "";
}
