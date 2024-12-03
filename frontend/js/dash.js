document.addEventListener("DOMContentLoaded", function() {
    // Check if the elements exist
    const bookingCountElement = document.getElementById("bookingCount");
    const mostBookedRoomElement = document.getElementById("mostBookedRoom");
    const bookingChartElement = document.getElementById("bookingChart");
    const departmentChartElement = document.getElementById("departmentChart");

    if (!bookingCountElement || !mostBookedRoomElement || !bookingChartElement || !departmentChartElement) {
        console.error("Error: Elements not found in the DOM.");
        return;
    }

    // Fetch data from the PHP file
    fetch("../../../backend/server/dash.php")
        .then(response => {
            console.log("Fetch Response: ", response);
            if (!response.ok) {
                console.error("Network response was not ok");
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then(data => {
            console.log("Fetched Data: ", data);  // Log the response data
            
            // Check if the data contains the required fields
            if (!data.bookingCount || !data.mostBookedRoom || !data.bookingStats || !data.departmentStats) {
                console.error("Missing expected data:", data);
                return;
            }

            // Update the DOM elements
            bookingCountElement.textContent = data.bookingCount;
            mostBookedRoomElement.textContent = data.mostBookedRoom;

            // Draw charts
            drawBookingChart(data.bookingStats);
            drawDepartmentChart(data.departmentStats);
        })
        .catch(error => {
            console.error("Error fetching data:", error);
        });
});

// Booking Statistics Line Chart
function drawBookingChart(bookingStats) {
    const ctx = document.getElementById("bookingChart").getContext("2d");
    console.log("Booking Stats Data: ", bookingStats); // Check what data is passed

    new Chart(ctx, {
        type: "line",
        data: {
            labels: bookingStats.map(stat => `${stat.month}-${stat.year}`),
            datasets: [{
                label: "Bookings",
                data: bookingStats.map(stat => stat.booking_count),
                borderColor: "rgba(75, 192, 192, 1)",
                backgroundColor: "rgba(75, 192, 192, 0.2)",
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

// Department Stats Pie Chart
function drawDepartmentChart(departmentStats) {
    const ctx = document.getElementById("departmentChart").getContext("2d");
    console.log("Department Stats Data: ", departmentStats); // Check what data is passed

    new Chart(ctx, {
        type: "pie",
        data: {
            labels: departmentStats.map(stat => stat.department),
            datasets: [{
                data: departmentStats.map(stat => stat.booking_count),
                backgroundColor: [
                    "rgba(54, 162, 235, 0.6)",
                    "rgba(255, 99, 132, 0.6)",
                    "rgba(255, 206, 86, 0.6)",
                    "rgba(75, 192, 192, 0.6)"
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}
