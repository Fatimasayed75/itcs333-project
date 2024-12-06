// Function to confirm booking and show modal
function confirmBooking(roomID) {
  const startTime = document.getElementById('startTime').value;
  const duration = document.getElementById('duration').value;
  const date = document.getElementById('date').value;

  if (!startTime || !duration || !date) {
    alert('Please select a start time, duration, and date.');
    return;
  }

  // Format start time to HH:MM:SS
  let formattedStartTime = startTime.padStart(5, '0');
  formattedStartTime = formattedStartTime.replace('.', ':') + ':00'; // Convert '9.00' -> '09:00:00'

  // Combine date and formatted start time
  const fullStartTime = `${date} ${formattedStartTime}`;

  // Prepare data to send to the server
  const bookingData = {
    roomID: roomID,
    startTime: fullStartTime,
    duration: duration,
  };

  // Make a POST request to the server
  fetch('../../../backend/server/booking.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(bookingData),
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Show success modal
        const successModal = document.getElementById('successModal');
        successModal.classList.remove('hidden');

        // Attach event listener for close button inside the modal
        const closeModalBtn = document.getElementById('closeModalBtn');
        if (closeModalBtn) {
          closeModalBtn.addEventListener('click', function () {
            successModal.classList.add('hidden'); // Hide the modal when the close button is clicked
          });
        }
      } else {
        alert('Error: ' + data.message); 
      }
    })
    .catch(error => {
      console.error('Error making booking:', error);
    });
}
