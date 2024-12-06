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
        // Show the appropriate modal based on roomID
        if (roomID === 'S40-1002' || roomID === 'S40-2001') {
          // Show special success modal
          const specialModal = document.getElementById('specialSuccessModal');
          specialModal.classList.remove('hidden');

          // Attach event listener for close button in the special modal
          const closeSpecialModalBtn = document.getElementById('closeSpecialModalBtn');
          if (closeSpecialModalBtn) {
            closeSpecialModalBtn.addEventListener('click', function () {
              specialModal.classList.add('hidden');
            });
          }
        } else {
          // Show default success modal
          const successModal = document.getElementById('successModal');
          successModal.classList.remove('hidden');

          // Attach event listener for close button in the default modal
          const closeModalBtn = document.getElementById('closeModalBtn');
          if (closeModalBtn) {
            closeModalBtn.addEventListener('click', function () {
              successModal.classList.add('hidden');
            });
          }
        }
      } else {
        alert('Error: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Error making booking:', error);
    });
}
