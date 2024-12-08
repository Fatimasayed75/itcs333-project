let currentWeekOffset = 0;
let roomAvailabilityChart = null;

function createRoomAvailabilityChart(bookings) {
  const canvas = document.getElementById("roomAvailability");
  const weekNavPrev = document.getElementById("prevWeek");
  const weekNavNext = document.getElementById("nextWeek");
  const refresh = document.getElementById("refreshBtn");

  let tooltipDiv = null; // tooltip element

  if (canvas) {
    const getDatesForWeek = (weekOffset) => {
      const startDate = new Date();
      startDate.setDate(startDate.getDate() + weekOffset * 7);
      const dates = [];

      for (let i = 0; i < 7; i++) {
        // Generate 7 days for the current week
        const date = new Date(startDate);
        date.setDate(startDate.getDate() + i);
        dates.push(date.toLocaleDateString());
      }

      return dates;
    };

    // Function to update the chart with the new dates
    const updateChart = () => {
      const dates = getDatesForWeek(currentWeekOffset);

      // Convert bookings into drawable rectangles
      const rectangles = bookings
        .map((booking) => {
          if (booking.status !== "pending" && booking.status !== "rejected") {
            const startTime = new Date(booking.startTime);
            const endTime = new Date(booking.endTime);

            const bookingDate = startTime.toLocaleDateString();
            const dateIndex = dates.indexOf(bookingDate);

            if (dateIndex !== -1) {
              return {
                x: startTime.getHours() + startTime.getMinutes() / 60,
                x2: endTime.getHours() + endTime.getMinutes() / 60,
                y: dateIndex,
                startTime,
                endTime,
              };
            }
          }
          return null;
        })
        .filter(Boolean);

      // Destroy the previous chart (if any) to avoid reuse errors
      if (roomAvailabilityChart) {
        roomAvailabilityChart.destroy();
      }

      const chart = new Chart(canvas, {
        type: "scatter",
        data: { datasets: [] },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          scales: {
            x: {
              type: "linear",
              position: "top",
              min: 8,
              max: 18,
            },
            y: {
              type: "category",
              labels: dates,
              max: 6,
            },
          },
          plugins: { tooltip: { enabled: false } }, // Disable built-in tooltip
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
                const y = yAxis.getPixelForValue(rect.y) - 10;
                const height = 20;

                ctx.fillStyle = "rgba(216,133,163,0.7)";
                ctx.fillRect(x1, y, x2 - x1, height);
                ctx.strokeStyle = "rgba(216,133,163,1)";
                ctx.strokeRect(x1, y, x2 - x1, height);
              });
            },
          },
        ],
      });

      // Custom tooltip
      if (!tooltipDiv) {
        tooltipDiv = document.createElement("div");
        tooltipDiv.style.position = "absolute";
        tooltipDiv.style.backgroundColor = "white";
        tooltipDiv.style.border = "1px solid rgba(0,0,0,0.2)";
        tooltipDiv.style.padding = "8px";
        tooltipDiv.style.boxShadow = "0px 4px 8px rgba(0,0,0,0.2)";
        tooltipDiv.style.borderRadius = "4px";
        tooltipDiv.style.pointerEvents = "none";
        tooltipDiv.style.display = "none";
        document.body.appendChild(tooltipDiv);
      }

      canvas.addEventListener("mousemove", (event) => {
        const canvasRect = canvas.getBoundingClientRect();
        const mouseX = event.clientX - canvasRect.left + 15; // Shift detection 10px to the right
        const mouseY = event.clientY - canvasRect.top - 5;  // Shift detection 5px domward


        const hoveredRect = rectangles.find((rect) => {
          const x1 = chart.scales.x.getPixelForValue(rect.x);
          const x2 = chart.scales.x.getPixelForValue(rect.x2);
          const y = chart.scales.y.getPixelForValue(rect.y) - 10;
          const height = 20;
        
          // Check if mouse is within rectangle boundaries
          return (
            mouseX >= x1 && 
            mouseX <= x2 && 
            mouseY >= y && 
            mouseY <= y + height
          );
        });
        

        if (hoveredRect) {
          // Calculate the duration in hours and minutes
          const durationMs = hoveredRect.endTime - hoveredRect.startTime;
          const durationMinutes = Math.floor(durationMs / (1000 * 60));
          const hours = Math.floor(durationMinutes / 60);
          const minutes = durationMinutes % 60;

           // Format duration
          let durationText = "";
          if (hours > 0) {
            durationText = `${hours}h ${minutes}m`;
          } else {
            durationText = `${minutes}m`;
          }
        
          // tooltip
          tooltipDiv.innerHTML = `
            <div style="font-size: 12px; line-height: 1.4; text-align: left;">
              <strong style="color: #D885A3;">Start:</strong> ${hoveredRect.startTime.toLocaleTimeString()}<br>
              <strong style="color: #D885A3;">End:</strong> ${hoveredRect.endTime.toLocaleTimeString()}<br>

              <strong style="color: #D885A3;">Duration:</strong> ${durationText}
            </div>
          `;
          tooltipDiv.style.left = `${event.clientX + 10}px`;
          tooltipDiv.style.top = `${event.clientY + 10}px`;
          tooltipDiv.style.display = "block";
          tooltipDiv.style.fontSize = "12px";
          tooltipDiv.style.padding = "6px";
          tooltipDiv.style.maxWidth = "150px";
        } else {
          tooltipDiv.style.display = "none";
        }        
        
      });

      canvas.addEventListener("mouseout", () => {
        tooltipDiv.style.display = "none";
      });
    };

    updateChart();

    // Navigation buttons
    weekNavPrev.addEventListener("click", () => {
      currentWeekOffset--;
      updateChart();
    });

    weekNavNext.addEventListener("click", () => {
      currentWeekOffset++;
      updateChart();
    });

    refresh.addEventListener("click", () => {
      currentWeekOffset = 0;
      updateChart();
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
