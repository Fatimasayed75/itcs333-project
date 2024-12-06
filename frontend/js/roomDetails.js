let currentWeekOffset = 0;
let roomAvailabilityChart = null;

function createRoomAvailabilityChart(bookings) {
  const ctx = document.getElementById("roomAvailability");
  const weekNavPrev = document.getElementById("prevWeek"); // Previous week button
  const weekNavNext = document.getElementById("nextWeek"); // Next week button
  const refresh = document.getElementById("refreshBtn"); // Current week button

  if (ctx) {
    // Function to generate dates for the given week offset (7 days)
    const getDatesForWeek = (weekOffset) => {
      const startDate = new Date();
      startDate.setDate(startDate.getDate() + weekOffset * 7); // Adjust start date based on week offset
      const dates = [];

      for (let i = 0; i < 7; i++) {
        // Generate 7 days for the current week
        const date = new Date(startDate);
        date.setDate(startDate.getDate() + i);
        dates.push(date.toLocaleDateString()); // Store dates in 'MM/DD/YYYY' format
      }

      return dates;
    };

    // Function to update the chart with the new dates
    const updateChart = () => {
      const dates = getDatesForWeek(currentWeekOffset);

      // Convert bookings into drawable rectangles
      const rectangles = bookings
        .map((booking) => {
          // don't show pending and rejected bookings
          if (booking["status"] != "pending" && booking["status"] != "rejected") {
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
                backgroundColor: "rgba(216,133,163,0.7)", // Orange color with transparency
                borderColor: "rgba(216,133,163,1)", // Solid orange border
                borderWidth: 1,
              };
            }
          }
          return null;
        })
        .filter((rect) => rect !== null); // Remove null entries (invalid bookings)

      // Destroy the previous chart (if any) to avoid reuse errors
      if (roomAvailabilityChart) {
        roomAvailabilityChart.destroy();
      }

      // Create or update the chart using Chart.js
      roomAvailabilityChart = new Chart(ctx, {
        type: "scatter", // Base type; we'll draw custom rectangles
        data: {
          datasets: [
            {
              label: "Booked Slot",
              data: [], // Empty dataset to avoid rendering points
              backgroundColor: "rgba(216,133,163,0.7)",
              borderColor: "rgba(216,133,163,1)",
              borderWidth: 1,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          scales: {
            x: {
              type: "linear",
              position: "top",
              min: 8, // Start at 8 AM
              max: 18, // End at 6 PM
              title: {
                display: true,
                text: "Time (Hours)",
              },
              ticks: {
                stepSize: 1,
                callback: function (value) {
                  const hour = Math.floor(value);
                  const minutes = value % 1 === 0.5 ? "30" : "00";
                  return `${hour}:${minutes}`; // Format time labels
                },
              },
            },
            y: {
              type: "category",
              labels: dates,
              title: {
                display: true,
                text: "Date",
              },
              ticks: {
                callback: (value, index) => dates[index], // Map index to date
              },
              grid: {
                display: false,
              },
              max: 6, // Limit to 7 days (index 0 to 6)
            },
          },
          plugins: {
            tooltip: {
              enabled: true,
              mode: "index",
              intersect: false,
              callbacks: {
                title: function (context) {
                  const date = dates[context[0].raw.y];
                  return `Date: ${date}`;
                },
                label: function (context) {
                  const { raw } = context;
                  const startTime =
                    raw.startTime.getHours() +
                    ":" +
                    raw.startTime.getMinutes().toString().padStart(2, "0");
                  const endTime =
                    raw.endTime.getHours() +
                    ":" +
                    raw.endTime.getMinutes().toString().padStart(2, "0");
                  return `Time: ${startTime} - ${endTime}`;
                },
              },
            },
          },
        },
        plugins: [
          {
            id: "drawRectangles",
            beforeDatasetsDraw(chart) {
              const ctx = chart.ctx;
              const xAxis = chart.scales.x;
              const yAxis = chart.scales.y;

              rectangles.forEach((rect) => {
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
            },
          },
        ],
      });
    };

    // Initialize chart with the current week
    updateChart();

    // Event listeners for navigation buttons
    weekNavPrev.addEventListener("click", () => {
      currentWeekOffset--; // Go back one week
      updateWeekOffset();
      updateChart(); // Update the chart with the new week
    });

    weekNavNext.addEventListener("click", () => {
      currentWeekOffset++; // Go forward one week
      updateWeekOffset();
      updateChart(); // Update the chart with the new week
    });

    // Event listener for the "Current Week" button
    refresh.addEventListener("click", () => {
      currentWeekOffset = 0; // Reset to current week
      updateChart();
      updateWeekOffset();
    });
  } else {
    console.error("Canvas element not found!");
  }
}

function updateWeekOffset() {
  const weekOffsetDisplay = document.getElementById("weekOffset");

  if (currentWeekOffset == 0) {
    weekOffsetDisplay.textContent = "Current Week";
  } else if (currentWeekOffset > 0) {
    weekOffsetDisplay.textContent = `${currentWeekOffset} Week After`;
  } else {
    weekOffsetDisplay.textContent = `${Math.abs(
      currentWeekOffset
    )} Week Before`;
  }
}

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

    if (roomData.floor == 0) {
      roomData.floor = "Ground Floor";
    } else if (roomData.floor == 1) {
      roomData.floor = "First Floor";
    } else if (roomData.floor == 2) {
      roomData.floor = "Second Floor";
    }
    // Replace placeholders in the HTML template
    const filledTemplate = template
      .replace(/{{roomID}}/g, roomData.roomID)
      .replace(/{{type}}/g, roomData.type)
      .replace(/{{capacity}}/g, roomData.capacity)
      .replace(/{{floor}}/g, roomData.floor)
      .replace(/{{department}}/g, roomData.department)
      .replace(
        /{{isAvailable}}/g,
        roomData.isAvailable
          ? "Available for booking"
          : "Not Available for Booking"
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
    if (!roomData.isAvailable) {
      document.getElementById("bookingForm").classList.add("hidden");
    }
  } catch (error) {
    console.error("Error loading room details:", error);
    document.getElementById("main-content").innerHTML =
      "Failed to load room details.";
    // alert("Failed to load room details.");
  }
}

async function navigateToHomePage() {
  await loadContent("home.php");
  initializeHomeEventListeners();
}

// dynamically load available times based on selected date
function loadAvailableTimes(roomID) {
  const date = document.getElementById("date").value;
  const startTimeSelect = document.getElementById("startTime");

  // Clear previous options
  startTimeSelect.innerHTML = "";

  if (date) {
    fetch(
      `../../../backend/server/roomAvailableTime.php?roomID=${roomID}&date=${date}`
    )
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok");
        }

        // Log the raw response text before parsing it
        return response.text(); // Use .text() to see the raw response
      })
      .then((rawText) => {
        console.log("Raw Response:", rawText);

        // Now try parsing the raw response text as JSON
        try {
          const data = JSON.parse(rawText); // Explicitly parse the response as JSON

          if (data.error) {
            console.error(data.error);
          } else {
            // Handle the available times if they are available
            if (data && data.length > 0) {
              data.forEach((time) => {
                const option = document.createElement("option");
                option.value = time;
                option.textContent = time;
                startTimeSelect.appendChild(option);
              });
            } else {
              const option = document.createElement("option");
              option.value = "";
              option.textContent = "No available times";
              startTimeSelect.appendChild(option);
            }
          }
        } catch (err) {
          console.error("Error parsing JSON:", err);
        }
      })
      .catch((error) => {
        console.error("Error fetching available times:", error);
      });
  }
}
