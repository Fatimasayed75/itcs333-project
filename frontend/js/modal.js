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
    const roomName = document.getElementById(`roomName_${roomID}`).value;
    const roomPrice = document.getElementById(`roomPrice_${roomID}`).value;
    const roomCapacity = document.getElementById(`roomCapacity_${roomID}`).value;

    const formData = new FormData();
    formData.append('roomID', roomID);
    formData.append('name', roomName);
    formData.append('price', roomPrice);
    formData.append('capacity', roomCapacity);

    fetch('/backend/server/editRoom.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Room updated successfully!');
            location.reload();
        } else {
            alert('Failed to update room: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the room');
    });
}

function deleteRoom(roomID) {
    if(confirm('Are you sure you want to delete room ' + roomID + '?')) {
        const formData = new FormData();
        formData.append('roomID', roomID);

        fetch('/backend/server/deleteRoom.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
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

// Add event listeners after DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    var addRoomForm = document.getElementById('addRoomForm');
    if (addRoomForm) {
        addRoomForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(addRoomForm);

            fetch('/backend/server/addRoom.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Room added successfully!');
                    closeModal();
                    location.reload();
                } else {
                    alert('Failed to add room: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding the room');
            });
        });
    }
});
