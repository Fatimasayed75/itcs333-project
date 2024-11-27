document.addEventListener('DOMContentLoaded', () => {
    const bookingCards = document.querySelectorAll('.previous-booking-card');
    bookingCards.forEach(card => {
        const bookingID = card.getAttribute('data-booking-id');
        checkFeedbackStatus(bookingID); // Check feedback status for this booking
    });
});

function checkFeedbackStatus(bookingID) {
    fetch(`../../../backend/server/checkFeedback.php?bookingID=${bookingID}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const feedbackButton = document.getElementById(`feedback-btn-${bookingID}`);
                const feedbackStatus = document.getElementById(`feedback-status-${bookingID}`);

                if (data.feedbackExists) {
                    // Hide the feedback button and show the submitted status
                    if (feedbackButton) feedbackButton.classList.add('hidden');
                    if (feedbackStatus) feedbackStatus.classList.remove('hidden');
                }
            } else {
                console.error(data.message || 'Failed to check feedback status.');
            }
        })
        .catch(error => console.error('Error checking feedback status:', error));
}


// Function to show modal for feedback
function showModal(bookingID, roomID, bookingTime, startTime, endTime) {
    const modal = document.getElementById('comment-modal');
    const commentInput = document.getElementById('comment-input-modal');
    const errorMessage = document.getElementById('error-message');

    errorMessage.classList.add('hidden');
    modal.setAttribute('data-booking-id', bookingID);
    modal.setAttribute('data-room-id', roomID);
    modal.setAttribute('data-booking-time', bookingTime);  // Add booking time to modal
    modal.setAttribute('data-start-time', startTime);      // Add start time to modal
    modal.setAttribute('data-end-time', endTime);          // Add end time to modal

    if (modal) {
        modal.classList.remove('hidden');
    }
}


// Function to hide modal
function hideModal() {
    const modal = document.getElementById('comment-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

function saveComment() {
    const modal = document.getElementById('comment-modal');
    const commentInput = document.getElementById('comment-input-modal');
    const errorMessage = document.getElementById('error-message');
    const bookingID = modal.getAttribute('data-booking-id');
    const roomID = modal.getAttribute('data-room-id');

    const commentContent = commentInput.value.trim();

    if (!commentContent) {
        errorMessage.classList.remove('hidden');
        return;
    }

    errorMessage.classList.add('hidden');

    const data = {
        bookingID: bookingID,
        roomID: roomID,
        comment: commentContent,
        bookingTime: modal.getAttribute('data-booking-time'),
        startTime: modal.getAttribute('data-start-time'),
        endTime: modal.getAttribute('data-end-time')
    };

    // Make a POST request to save the comment
    fetch('../../../backend/server/addComment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(json => {
        if (json.success) {
            hideModal();

            // Immediately hide the feedback button
            const feedbackButton = document.getElementById(`feedback-btn-${bookingID}`);
            if (feedbackButton) {
                feedbackButton.style.display = 'none'; // Hide the feedback button directly
            }

            // Dynamically create and insert the "✔ Feedback Submitted" message
            const bookingCard = document.getElementById(`previous-booking-card-${bookingID}`);
            if (bookingCard) {
                // Create the feedback submitted status element
                const feedbackStatus = document.createElement('span');
                feedbackStatus.className = 'text-emerald-600 text-xs font-semibold flex items-center justify-center';
                feedbackStatus.textContent = '✔ Feedback Submitted';
                
                // Find the container (like the one in HTML) where the button exists, and append to it
                const buttonContainer = bookingCard.querySelector('.flex');
                
                if (buttonContainer) {
                    // Append the feedback status next to the Rebook button
                    buttonContainer.appendChild(feedbackStatus);
                }
            }
            
        } else {
            alert(json.message || 'Failed to save the comment.');
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        alert('An error occurred while saving the comment.');
    });
}


