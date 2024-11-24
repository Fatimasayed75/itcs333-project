// Show the confirmation box
function showConfirmation(bookingID) {
    document.getElementById(`confirm-box-${bookingID}`).classList.remove('hidden');
}

// Hide the confirmation box
function hideConfirmation(bookingID) {
    document.getElementById(`confirm-box-${bookingID}`).classList.add('hidden');
}

// Cancel booking and update UI without refresh
function cancelBooking(bookingID) {
    fetch('../../../backend/server/cancelBooking.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `bookingID=${bookingID}&confirm=yes`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            hideConfirmation(bookingID);

            const bookingCard = document.querySelector(`#booking-card-${bookingID}`);
            if (bookingCard) {
                bookingCard.remove();
            }

            // Check if there are any bookings left in the upcoming bookings section
            const upcomingBookingsSection = document.querySelector('.upcoming-bookings-section');
            const remainingBookings = upcomingBookingsSection.querySelectorAll('.w-full');
            
            if (remainingBookings.length === 0) {
                // If no bookings left, show "No upcoming bookings" message
                const noBookingsMessage = document.createElement('p');
                noBookingsMessage.classList.add('text-gray-500');
                noBookingsMessage.textContent = 'No upcoming bookings.';
                upcomingBookingsSection.appendChild(noBookingsMessage);
            }
        } else {
            alert(data.message || 'Error canceling booking.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing your request.');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.cancel-booking-btn').forEach(button => {
        button.addEventListener('click', function () {
            const bookingID = this.closest('[id^="booking-card-"]').id.replace('booking-card-', '');

            // Confirm the deletion
            if (!confirm('Are you sure you want to cancel this booking?')) {
                return;
            }

            // Send an AJAX POST request
            fetch('../../../backend/server/cancelBooking.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ bookingID })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the booking card from the UI
                    this.closest('.w-full').remove();

                    // Check if there are no remaining bookings
                    const upcomingBookingsSection = document.querySelector('.upcoming-bookings-section');
                    const remainingBookings = upcomingBookingsSection.querySelectorAll('.w-full');

                    if (remainingBookings.length === 0) {
                        // Show the "No upcoming bookings" message
                        const noBookingsMessage = document.createElement('p');
                        noBookingsMessage.classList.add('text-gray-500');
                        noBookingsMessage.textContent = 'No upcoming bookings.';
                        upcomingBookingsSection.appendChild(noBookingsMessage);
                    }
                } else {
                    alert(data.message || 'Failed to cancel booking.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing your request.');
            });
        });
    });
});
