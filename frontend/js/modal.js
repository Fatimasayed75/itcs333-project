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

function closeModal() {
    var modalContainer = document.getElementById('modalContainer');
    var addRoomModal = document.getElementById('addRoomModal');
    var roomListModal = document.getElementById('roomListModal');
    
    if (modalContainer) modalContainer.classList.add('hidden');
    if (addRoomModal) addRoomModal.classList.add('hidden');
    if (roomListModal) roomListModal.classList.add('hidden');
}

function editRoom(roomID) {
    // Implement edit room functionality
    alert('Edit room: ' + roomID);
}

function deleteRoom(roomID) {
    // Implement delete room functionality
    if(confirm('Are you sure you want to delete room ' + roomID + '?')) {
        // Add AJAX call to delete room
        alert('Deleting room: ' + roomID);
    }
}

// Add event listeners after DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    var addRoomForm = document.getElementById('addRoomForm');
    if (addRoomForm) {
        addRoomForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Add AJAX call to submit room data
            alert('Adding new room');
            closeModal();
        });
    }
});
