// Global functions for modal interactions
function openAddRoomModal() {
    var modalContainer = document.getElementById('modalContainer');
    var addRoomModal = document.getElementById('addRoomModal');
    
    if (modalContainer && addRoomModal) {
        modalContainer.classList.remove('hidden');
        addRoomModal.classList.remove('hidden');
    }
}

function openRoomListModal() {
    var modalContainer = document.getElementById('modalContainer');
    var roomListModal = document.getElementById('roomListModal');
    
    if (modalContainer && roomListModal) {
        modalContainer.classList.remove('hidden');
        roomListModal.classList.remove('hidden');
    }
}

function openBookListModal() {
    var modalContainer = document.getElementById('modalContainer');
    var bookListModal = document.getElementById('bookListModal');
    
    if (modalContainer && bookListModal) {
        modalContainer.classList.remove('hidden');
        bookListModal.classList.remove('hidden');
    }
}

function closeModal() {
    var modalContainer = document.getElementById('modalContainer');
    var addRoomModal = document.getElementById('addRoomModal');
    var roomListModal = document.getElementById('roomListModal');
    var bookListModal = document.getElementById('bookListModal');
    
    if (modalContainer) modalContainer.classList.add('hidden');
    if (addRoomModal) addRoomModal.classList.add('hidden');
    if (roomListModal) roomListModal.classList.add('hidden');
    if (bookListModal) bookListModal.classList.add('hidden');
}

function toggleEditMode(roomID) {
    const row = document.getElementById('room_' + roomID);
    if (!row) return;

    // Toggle view/edit modes
    const viewCells = row.querySelectorAll('.view-mode');
    const editCells = row.querySelectorAll('.edit-mode');

    viewCells.forEach(cell => cell.classList.toggle('hidden'));
    editCells.forEach(cell => cell.classList.toggle('hidden'));
}

function toggleBookingEditMode(roomID) {
    const row = document.getElementById('book_' + roomID);
    if (!row) return;

    // Toggle view/edit modes
    const viewCells = row.querySelectorAll('.view-mode');
    const editCells = row.querySelectorAll('.edit-mode');

    viewCells.forEach(cell => cell.classList.toggle('hidden'));
    editCells.forEach(cell => cell.classList.toggle('hidden'));
}

function toggleEditBooking(bookingID) {
    const row = document.getElementById('book_' + bookingID);
    if (!row) return;

    // Toggle view/edit modes
    const viewCells = row.querySelectorAll('.view-mode');
    const editCells = row.querySelectorAll('.edit-mode');

    viewCells.forEach(cell => cell.classList.toggle('hidden'));
    editCells.forEach(cell => cell.classList.toggle('hidden'));
}

function cancelBookingEdit(bookingID) {
    toggleBookingEditMode(bookingID);
}

function cancelEdit(roomID) {
    toggleEditMode(roomID);
}

function saveRoom(roomID) {
    const row = document.getElementById('room_' + roomID);
    if (!row) return;

    const formData = new FormData();
    formData.append('roomID', roomID);
    formData.append('action', 'edit');

    // Get all input values
    const inputs = row.querySelectorAll('.edit-mode input, .edit-mode select');
    inputs.forEach(input => {
        formData.append(input.name, input.value);
    });

    fetch('../../../backend/server/room.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Room updated successfully!');
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the room');
    });
}

function saveBooking(bookingID) {
    const row = document.getElementById('book_' + bookingID);
    if (!row) return;

    const formData = new FormData();
    formData.append('bookingID', bookingID);
    formData.append('action', 'edit');

    // Get all input values
    const inputs = row.querySelectorAll('.edit-mode input, .edit-mode select, input[name="userID"]');
    inputs.forEach(input => {
        formData.append(input.name, input.value);
    });

    fetch('../../../backend/server/editBooking.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Booking updated successfully!');
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the Booking');
    });
}

function deleteRoom(roomID) {
    if(confirm('Are you sure you want to delete room ' + roomID + '?')) {
        const formData = new FormData();
        formData.append('roomID', roomID);
        formData.append('action', 'delete');

        fetch('../../../backend/server/room.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Room deleted successfully!');
                location.reload();
            } else {
                alert('Failed to delete room: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the room');
        });
    }
}

function deleteBooking(bookingID) {
    if(confirm('Are you sure you want to delete this booking ' + bookingID + '?')) {
        const formData = new FormData();
        formData.append('bookingID', bookingID);
        formData.append('action', 'delete');

        fetch('../../../backend/server/editBooking.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Booking deleted successfully!');
                location.reload();
            } else {
                alert('Failed to delete booking: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the booking');
        });
    }
}

// Add event listeners after DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    const addRoomForm = document.getElementById('addRoomForm');
    if (addRoomForm) {
        let isSubmitting = false;
        addRoomForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (isSubmitting) return; // Prevent multiple submissions
            isSubmitting = true;
            
            const formData = new FormData(this);
            formData.append('action', 'add');
            
            fetch('../../../backend/server/room.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Room added successfully!');
                    addRoomForm.reset();
                    closeModal();
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Unknown error occurred'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to add room. Please try again.');
            })
            .finally(() => {
                isSubmitting = false; // Reset the flag regardless of success/failure
            });
        });
    }
});
