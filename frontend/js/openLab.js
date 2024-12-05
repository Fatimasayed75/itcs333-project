document.addEventListener("DOMContentLoaded", function () {
    // Handle approve/reject button click event using event delegation
    document.body.addEventListener('click', function (event) {
        if (event.target && (event.target.classList.contains('approve-booking') || event.target.classList.contains('reject-booking'))) {
            const button = event.target;
            const bookingID = button.dataset.bookingId;
            const status = button.dataset.status;
            const roomID = document.getElementById('roomID-' + bookingID).value;
            const bookingTime = document.getElementById('bookingTime-' + bookingID).value;
            const startTime = document.getElementById('startTime-' + bookingID).value;
            const endTime = document.getElementById('endTime-' + bookingID).value;
            const userID = document.getElementById('userID-' + bookingID).value;

            // Send AJAX request to update booking status
            fetch('../../../backend/server/openLab.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    bookingID: bookingID,
                    status: status,
                    roomID: roomID,
                    bookingTime: bookingTime,
                    startTime: startTime,
                    endTime: endTime,
                    userID: userID,
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    
                    const bookingElement = document.getElementById('pending-booking-' + bookingID);
                    const actionText = bookingElement.querySelector('.booking-actions');

                    const statusColor = status === 'approved' ? '#D885A3' : '#B0B0B0';

                    actionText.innerHTML = `<p style="color: ${statusColor};">Booking ${status.charAt(0).toUpperCase() + status.slice(1)}</p>`;

                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    });
});
