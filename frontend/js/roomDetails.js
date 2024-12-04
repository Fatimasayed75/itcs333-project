async function loadRoomDetails(roomId) {
  try {
    const response = await fetch(
      `../../../backend/server/roomDetails.php?roomID=${roomId}`
    );
    const data = await response.json();
    if (data.error) {
      throw new Error(data.error);
    }

    // Fetch room details HTML template
    const templateResponse = await fetch("../components/roomDetails.php");
    if (!templateResponse.ok) {
      throw new Error("Failed to load the room details template");
    }

    const template = await templateResponse.text();

    let roomData = data[0];
    let bookings = roomData["roomBookings"];
    console.log(bookings);

    // Replace placeholders in the HTML template
    const filledTemplate = template
      .replace(/{{roomID}}/g, roomData.roomID)
      .replace(/{{type}}/g, roomData.type)
      .replace(/{{capacity}}/g, roomData.capacity)
      .replace(/{{floor}}/g, roomData.floor)
      .replace(/{{department}}/g, roomData.department)
      .replace(
        /{{isAvailable}}/g,
        roomData.isAvailable ? "Available" : "Not Available"
      )
      .replace(
        /{{image}}/g,
        `https://placehold.co/300x200?text=Image+For+${roomData.roomID}`
      );

    // Update the page content with room details
    const mainContent = document.getElementById("main-content");
    mainContent.innerHTML = filledTemplate;

    // Call the function to create the chart right after DOM manipulation
    createRoomAvailabilityChart(bookings); // Pass bookings data to chart creation function

    const homeBtn = document.getElementById("backToHomeBtn");
    if (homeBtn) {
      homeBtn.addEventListener("click", navigateToHomePage);
    }

    // Reinitialize any event listeners if needed
    const bookRoomBtn = document.getElementById("bookRoomBtn");
    if (bookRoomBtn) {
      bookRoomBtn.addEventListener("click", () => bookRoom(roomId));
    }
  } catch (error) {
    console.error("Error loading room details:", error);
    document.getElementById("main-content").innerHTML =
      "Failed to load room details.";
    // alert("Failed to load room details.");
  }
}

async function loadRoomDetails(roomId) {
  try {
    const response = await fetch(`../../../backend/server/roomDetails.php?roomID=${roomId}`);
    const data = await response.json();
    if (data.error) {
      throw new Error(data.error);
    }

    // Fetch room details HTML template
    const templateResponse = await fetch("../components/roomDetails.php");
    if (!templateResponse.ok) {
      throw new Error("Failed to load the room details template");
    }

    const template = await templateResponse.text();

    let roomData = data[0];
    let bookings = roomData["roomBookings"];
    console.log(bookings);

    // Replace placeholders in the HTML template
    const filledTemplate = template
      .replace(/{{roomID}}/g, roomData.roomID)
      .replace(/{{type}}/g, roomData.type)
      .replace(/{{capacity}}/g, roomData.capacity)
      .replace(/{{floor}}/g, roomData.floor)
      .replace(/{{department}}/g, roomData.department)
      .replace(
        /{{isAvailable}}/g,
        roomData.isAvailable ? "Available" : "Not Available"
      )
      .replace(
        /{{image}}/g,
        `https://placehold.co/300x200?text=Image+For+${roomData.roomID}`
      );

    // Update the page content with room details
    const mainContent = document.getElementById("main-content");
    mainContent.innerHTML = filledTemplate;

    // Call the function to create the chart right after DOM manipulation
    createRoomAvailabilityChart(bookings); // Pass bookings data to chart creation function

    const homeBtn = document.getElementById("backToHomeBtn");
    if (homeBtn) {
      homeBtn.addEventListener("click", navigateToHomePage);
    }

    // Reinitialize any event listeners if needed
    const bookRoomBtn = document.getElementById("bookRoomBtn");
    if (bookRoomBtn) {
      bookRoomBtn.addEventListener("click", () => bookRoom(roomId));
    }
  } catch (error) {
    console.error("Error loading room details:", error);
    document.getElementById("main-content").innerHTML =
      "Failed to load room details.";
    // alert("Failed to load room details.");
  }
}

function createRoomAvailabilityChart(bookings) {
  const ctx = document.getElementById('roomAvailability');

  if (ctx) {
    // Generate dates from today to the next 7 days
    const dates = [];
    for (let i = 0; i < 7; i++) {
      const date = new Date();
      date.setDate(date.getDate() + i);
      dates.push(date.toLocaleDateString()); // Store dates in 'MM/DD/YYYY' format
    }

    // Convert bookings into drawable rectangles
    const rectangles = bookings.map(booking => {
      const startTime = new Date(booking.startTime);
      const endTime = new Date(booking.endTime);

      const bookingDate = startTime.toLocaleDateString();
      const dateIndex = dates.indexOf(bookingDate);

      if (dateIndex !== -1) {
        return {
          x: startTime.getHours() + startTime.getMinutes() / 60, // Start time in hours
          x2: endTime.getHours() + endTime.getMinutes() / 60, // End time in hours
          y: dateIndex, // Corresponding date index for the y-axis
          startTime: startTime, // Store the actual start time for the tooltip
          endTime: endTime, // Store the actual end time for the tooltip
          backgroundColor: 'rgba(255, 165, 0, 0.7)', // Orange color with transparency
          borderColor: 'rgba(255, 165, 0, 1)', // Solid orange border
          borderWidth: 1
        };
      }
      return null;
    }).filter(rect => rect !== null); // Remove null entries (invalid bookings)

    // Create the chart using Chart.js
    new Chart(ctx, {
      type: 'scatter', // Base type; we'll draw custom rectangles
      data: {
        datasets: [
          {
            label: 'Booked Slot',
            data: [], // Empty dataset to avoid rendering points
            backgroundColor: 'rgba(255, 165, 0, 0.7)',
            borderColor: 'rgba(255, 165, 0, 1)',
            borderWidth: 1
          }
        ]
      },
      options: {
        
        responsive: true,
        maintainAspectRatio: true,
        scales: {
          x: {
            type: 'linear',
            position: 'top',
            min: 8, // Start at 8 AM
            max: 18, // End at 6 PM
            title: {
              display: true,
              text: 'Time (Hours)'
            },
            ticks: {
              stepSize: 1,
              callback: function(value) {
                const hour = Math.floor(value);
                const minutes = value % 1 === 0.5 ? '30' : '00';
                return `${hour}:${minutes}`; // Format time labels
              }
            }
          },
          y: {
            type: 'category',
            labels: dates,
            title: {
              display: true,
              text: 'Date'
            },
            ticks: {
              callback: (value, index) => dates[index] // Map index to date
            },
            grid: {
              display: false
            },
            max: 6 // Limiting the max value to 6 ensures the chart stays within the 7 days
          }
        },
        plugins: {
          tooltip: {
            enabled: true,
            mode: 'index',
            intersect: false,
            callbacks: {
              title: function(context) {
                const date = dates[context[0].raw.y];
                return `Date: ${date}`;
              },
              label: function(context) {
                const { raw } = context;
                console.log("Context", context);
                const startTime = raw.startTime.getHours() + ':' + raw.startTime.getMinutes().toString().padStart(2, '0');
                const endTime = raw.endTime.getHours() + ':' + raw.endTime.getMinutes().toString().padStart(2, '0');
                return `Time: ${startTime} - ${endTime}`;
              }
            }
          }
        }
      },
      plugins: [
        {
          id: 'drawRectangles',
          beforeDatasetsDraw(chart) {
            const ctx = chart.ctx;
            const xAxis = chart.scales.x;
            const yAxis = chart.scales.y;

            rectangles.forEach(rect => {
              const x1 = xAxis.getPixelForValue(rect.x);
              const x2 = xAxis.getPixelForValue(rect.x2);
              const y = yAxis.getPixelForValue(rect.y) - 10; // Center the rectangle
              const height = 20; // Rectangle height

              // Draw rectangle
              ctx.fillStyle = rect.backgroundColor;
              ctx.fillRect(x1, y, x2 - x1, height);

              // Draw border
              if (rect.borderColor) {
                ctx.strokeStyle = rect.borderColor;
                ctx.lineWidth = rect.borderWidth;
                ctx.strokeRect(x1, y, x2 - x1, height);
              }
            });
          }
        }
      ]
    });
  } else {
    console.error('Canvas element not found!');
  }
}




async function navigateToHomePage() {
  await loadContent("home.php");
  initializeHomeEventListeners();
}
