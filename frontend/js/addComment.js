// Show the modal for adding comment
function showModal(bookingID, roomID) {
    const modal = document.getElementById('comment-modal');
    const commentInput = document.getElementById('comment-input-modal');
    const errorMessage = document.getElementById('error-message');

    // Reset error message visibility
    errorMessage.classList.add('hidden');

    // Set the bookingID and roomID in the modal context for later use
    modal.setAttribute('data-booking-id', bookingID);
    modal.setAttribute('data-room-id', roomID);

    if (modal) {
        modal.classList.remove('hidden');
    }
}

// Hide the modal
function hideModal() {
    const modal = document.getElementById('comment-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Save the comment from the modal
function saveComment() {
    const modal = document.getElementById('comment-modal');
    const commentInput = document.getElementById('comment-input-modal');
    const errorMessage = document.getElementById('error-message');
    const bookingID = modal.getAttribute('data-booking-id');
    const roomID = modal.getAttribute('data-room-id');

    const commentContent = commentInput.value.trim();

    // Check if the comment content is empty
    if (!commentContent) {
        errorMessage.classList.remove('hidden');
        return;
    }

    // Hide error message if the comment is valid
    errorMessage.classList.add('hidden');

    const data = {
        bookingID: bookingID,
        roomID: roomID,
        comment: commentContent
    };

    // Send the comment data to the server
    fetch('../../../backend/server/addComment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.text())
    .then(text => {
        try {
            const json = JSON.parse(text);
            if (json.success) {
                hideModal();
            } else {
                alert(json.message || 'Failed to save the comment.');
            }
        } catch (err) {
            console.error('Invalid JSON response:', text);
            alert('An unexpected error occurred.');
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        alert('An error occurred while saving the comment.');
    });
}
