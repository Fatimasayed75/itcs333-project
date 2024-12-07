// Function to start countdown timers
function startCountdown() {
  const timer = document.querySelector(".countdown-timer");

  const endTime = parseInt(timer.getAttribute("data-end-time"), 10) * 1000; // Convert to milliseconds

  function updateTimer() {
    const now = new Date().getTime();
    const distance = endTime - now;

    if (distance <= 0) {
      timer.innerText = "Time Expired";
      return;
    }

    const hours = Math.floor(
      (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
    );
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    timer.innerText = `${hours}h ${minutes}m ${seconds}s`;
  }

  updateTimer(); // Initial call to set timer immediately
  setInterval(updateTimer, 1000); // Update every second
}
