// Global functions for modal interactions
function openAddRoomModal() {
  var modalContainer = document.getElementById("modalContainer");
  var addRoomModal = document.getElementById("addRoomModal");

  if (modalContainer && addRoomModal) {
    modalContainer.classList.remove("hidden");
    addRoomModal.classList.remove("hidden");
  }
}

function openRoomListModal() {
  var modalContainer = document.getElementById("modalContainer");
  var roomListModal = document.getElementById("roomListModal");

  if (modalContainer && roomListModal) {
    modalContainer.classList.remove("hidden");
    roomListModal.classList.remove("hidden");
  }
}

function openBookListModal() {
  var modalContainer = document.getElementById("modalContainer");
  var bookListModal = document.getElementById("bookListModal");

  if (modalContainer && bookListModal) {
    modalContainer.classList.remove("hidden");
    bookListModal.classList.remove("hidden");
  }
}

function closeModal() {
  var modalContainer = document.getElementById("modalContainer");
  var addRoomModal = document.getElementById("addRoomModal");
  var roomListModal = document.getElementById("roomListModal");
  var bookListModal = document.getElementById("bookListModal");

  if (modalContainer) modalContainer.classList.add("hidden");
  if (addRoomModal) addRoomModal.classList.add("hidden");
  if (roomListModal) roomListModal.classList.add("hidden");
  if (bookListModal) bookListModal.classList.add("hidden");
}

function toggleEditMode(roomID) {
  const row = document.getElementById("room_" + roomID);
  if (!row) return;

  // Toggle view/edit modes
  const viewCells = row.querySelectorAll(".view-mode");
  const editCells = row.querySelectorAll(".edit-mode");

  viewCells.forEach((cell) => cell.classList.toggle("hidden"));
  editCells.forEach((cell) => cell.classList.toggle("hidden"));
}

function toggleBookingEditMode(roomID) {
  const row = document.getElementById("book_" + roomID);
  if (!row) return;

  // Toggle view/edit modes
  const viewCells = row.querySelectorAll(".view-mode");
  const editCells = row.querySelectorAll(".edit-mode");

  viewCells.forEach((cell) => cell.classList.toggle("hidden"));
  editCells.forEach((cell) => cell.classList.toggle("hidden"));
}

function toggleEditBooking(bookingID) {
  const row = document.getElementById("book_" + bookingID);
  if (!row) return;

  // Toggle view/edit modes
  const viewCells = row.querySelectorAll(".view-mode");
  const editCells = row.querySelectorAll(".edit-mode");

  viewCells.forEach((cell) => cell.classList.toggle("hidden"));
  editCells.forEach((cell) => cell.classList.toggle("hidden"));
}

function cancelBookingEdit(bookingID) {
  toggleBookingEditMode(bookingID);
}

function cancelEdit(roomID) {
  toggleEditMode(roomID);
}

function saveRoom(roomID) {
  const row = document.getElementById("room_" + roomID);
  if (!row) return;

  const formData = new FormData();
  formData.append("roomID", roomID);
  formData.append("action", "edit");

  // Get all input values
  const inputs = row.querySelectorAll(".edit-mode input, .edit-mode select");
  inputs.forEach((input) => {
    formData.append(input.name, input.value);
  });

  fetch("../../../backend/server/room.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        alert("Room updated successfully!");
        location.reload();
      } else {
        alert(data.message);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("An error occurred while updating the room");
    });
}

function saveBooking(bookingID) {
  const row = document.getElementById("book_" + bookingID);
  if (!row) return;

  const formData = new FormData();
  formData.append("bookingID", bookingID);
  formData.append("action", "edit");

  // Get all input values
  const inputs = row.querySelectorAll(
    '.edit-mode input, .edit-mode select, input[name="userID"]'
  );
  inputs.forEach((input) => {
    formData.append(input.name, input.value);
  });

  fetch("../../../backend/server/editBooking.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        alert("Booking updated successfully!");
        location.reload();
      } else {
        alert(data.message);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      //   alert("An error occurred while updating the Booking");
    });
}

function deleteRoom(roomID) {
  if (confirm("Are you sure you want to delete room " + roomID + "?")) {
    const formData = new FormData();
    formData.append("roomID", roomID);
    formData.append("action", "delete");

    fetch("../../../backend/server/room.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          alert("Room deleted successfully!");
          location.reload();
        } else {
          alert("Failed to delete room: " + (data.message || "Unknown error"));
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("An error occurred while deleting the room");
      });
  }
}

function deleteBooking(bookingID) {
  if (
    confirm("Are you sure you want to delete this booking " + bookingID + "?")
  ) {
    const formData = new FormData();
    formData.append("bookingID", bookingID);
    formData.append("action", "delete");

    fetch("../../../backend/server/editBooking.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          alert("Booking deleted successfully!");
          location.reload();
        } else {
          alert(
            "Failed to delete booking: " + (data.message || "Unknown error")
          );
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("An error occurred while deleting the booking");
      });
  }
}

// Add event listeners after DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  const addRoomForm = document.getElementById("addRoomForm");

  if (addRoomForm) {
    let isSubmitting = false;
    addRoomForm.addEventListener("submit", function (e) {
      e.preventDefault();

      if (isSubmitting) return; // Prevent multiple submissions
      isSubmitting = true;

      const formData = new FormData(this);
      formData.append("action", "add");

      fetch("../../../backend/server/room.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            alert("Room added successfully!");
            addRoomForm.reset();
            closeModal();
            location.reload();
          } else {
            alert("Error: " + (data.message || "Unknown error occurred"));
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("Failed to add room. Please try again.");
        })
        .finally(() => {
          isSubmitting = false; // Reset the flag regardless of success/failure
        });
    });
  }

  if(IsAdmin()){
    initalizeListenersForAddRoom();
  }

  // To show quantity input when checkbox is selected
});

function updateUserID(selectElement) {
  // Loop through all options to reset their text to include the full name
  for (let i = 0; i < selectElement.options.length; i++) {
    const option = selectElement.options[i];
    if (option.selected) {
      // For the selected option, display the userID only
      option.textContent = option.value; // Show userID only
    } else {
      // For non-selected options, display full name
      option.textContent =
        option.value +
        " - " +
        option.dataset.firstname +
        " " +
        option.dataset.lastname;
    }
  }
}

function initalizeListenersForAddRoom() {
  const roomIDinput = document.getElementById("NewRoomID");
  const roomFloorInput = document.getElementById("NewRoomFloor");
  const roomDepInput = document.getElementById("NewRoomDept");

  document
    .querySelectorAll('input[type="checkbox"]')
    .forEach(function (checkbox) {
      checkbox.addEventListener("change", function () {
        const equipmentID = checkbox.value;
        const quantityInput = document.getElementById("quantity" + equipmentID);

        // Show or hide quantity input based on checkbox state
        if (checkbox.checked) {
          quantityInput.classList.remove("hidden");
        } else {
          quantityInput.classList.add("hidden");
          quantityInput.value = 10; // Reset to default quantity if unchecked
        }
      });
    });

  // Event listener for when roomID input changes
  roomIDinput.addEventListener("input", function () {
    let FullroomID = roomIDinput.value;
    // trim S40 and leading zeros (up to 3 zeros)
    let roomIDWithZeros = FullroomID.replace(/^S40-/, "");
    let roomID = FullroomID.replace(/^S40-0{0,3}/, "");

    let floor = 0;
    if (roomID.length == 4) {
      floor = roomID[0];
    }

    // Check if the roomID matches the expected format
    if (
      FullroomID.startsWith("S40-") &&
      roomIDWithZeros.length > 0 &&
      roomIDWithZeros.length <= 4 &&
      floor >= 0 &&
      floor <= 2
    ) {
      let dep = "";

      // Determine department based on room number
      if (
        (roomID >= 0 && roomID <= 39) ||
        (roomID >= 1000 && roomID <= 1039) ||
        (roomID >= 2000 && roomID <= 2039)
      ) {
        dep = "IS";
      } else if (
        (roomID >= 40 && roomID <= 69) ||
        (roomID >= 1040 && roomID <= 1069) ||
        (roomID >= 2040 && roomID <= 2069)
      ) {
        dep = "CS";
      } else if (
        (roomID >= 70 && roomID <= 89) ||
        (roomID >= 1070 && roomID <= 1089) ||
        (roomID >= 2070 && roomID <= 2099)
      ) {
        dep = "CE";
      }

      // Auto-fill the floor and department fields
      roomFloorInput.value = floor;
      roomDepInput.value = dep;
    } else {
      // If roomID is invalid or doesn't match the format, reset the fields
      roomFloorInput.value = "";
      roomDepInput.value = "";
    }
  });
}
