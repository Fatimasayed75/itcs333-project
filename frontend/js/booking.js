function confirmBooking(roomID) {
  const startTime = document.getElementById('startTime').value;
  const duration = document.getElementById('duration').value;
  const date = document.getElementById('date').value;

  // Error message container
  const errorMessageContainer = document.getElementById('errorMessage');

  if (!startTime || !duration || !date) {
    // Display the error message
    errorMessageContainer.textContent = 'Please select a start time, duration, and date.';
    errorMessageContainer.classList.remove('hidden'); 
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

  // Clear any previous error messages
  errorMessageContainer.classList.add('hidden');
  errorMessageContainer.textContent = '';

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
        // Show the appropriate modal based on roomID
        if (roomID === 'S40-1002' || roomID === 'S40-2001') {
          const specialModal = document.getElementById('specialSuccessModal');
          specialModal.classList.remove('hidden');

          const closeSpecialModalBtn = document.getElementById('closeSpecialModalBtn');
          closeSpecialModalBtn.addEventListener('click', () => {
            specialModal.classList.add('hidden');
          });
        } else {
          const successModal = document.getElementById('successModal');
          successModal.classList.remove('hidden');

          const closeModalBtn = document.getElementById('closeModalBtn');
          closeModalBtn.addEventListener('click', () => {
            successModal.classList.add('hidden');
          });
        }
      } else {
        // Display the server error message in the UI
        errorMessageContainer.textContent = `${data.message}`;
        errorMessageContainer.classList.remove('hidden');
      }
    })
    .catch(error => {
      console.error('Error making booking:', error);
      errorMessageContainer.textContent = 'An unexpected error occurred. Please try again.';
      errorMessageContainer.classList.remove('hidden');
    });
}
