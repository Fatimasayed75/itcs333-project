let bookingChartInstance; // Declare globally

document.addEventListener("DOMContentLoaded", () => {
    initializeDashboard(); // Initialize the dashboard only once when the page is loaded
});

function initializeDashboard() {
    const bookingCountElement = document.getElementById("bookingCount");
    const mostBookedRoomElement = document.getElementById("mostBookedRoom");
    const bookingChartElement = document.getElementById("bookingChart");
    const departmentChartElement = document.getElementById("departmentChart");

    if (!bookingCountElement || !mostBookedRoomElement || !bookingChartElement || !departmentChartElement) {
        // console.error("Dashboard elements not found!");
        return;
    }

    fetchDashboardData().then(data => {
        if (data) {
            // Populate dashboard with fetched data
            initializeBookingCount(bookingCountElement, data.bookingCount);
            initializeMostBookedRoom(mostBookedRoomElement, data.mostBookedRoom);
            // initializeBookingChart(bookingChartElement, data.bookingStats);
            initializeDepartmentChart(departmentChartElement, data.departmentStats);
        } else {
            console.error("No data available to update the dashboard");
        }
    }).catch(error => {
        console.error("Error during dashboard initialization: ", error);
    });
}

async function fetchDashboardData() {
    try {
        const response = await fetch('../../../backend/server/dash.php');
        if (!response.ok) {
            throw new Error("Failed to fetch dashboard data");
        }
        const data = await response.json();
        console.log('Fetched dashboard data:', data); // Debugging log

        if (data.error) {
            throw new Error(data.error);
        }

        return data;
    } catch (error) {
        console.error("Error fetching dashboard data: ", error);
        return null;
    }
}

function initializeBookingCount(bookingCountElement, bookingCountData) {
    bookingCountElement.textContent = bookingCountData; // Populate element
}

function initializeMostBookedRoom(mostBookedRoomElement, mostBookedRoomData) {
    mostBookedRoomElement.textContent = mostBookedRoomData; // Populate element
}

// function initializeBookingChart(bookingChartElement, bookingStats) {
//     if (bookingChartElement) {
//         // If the chart instance already exists, just update it
//         if (bookingChartInstance) {
//             bookingChartInstance.data.labels = bookingStats.map(stat => `${stat.month}/${stat.year}`);
//             bookingChartInstance.data.datasets[0].data = bookingStats.map(stat => stat.booking_count);
//             bookingChartInstance.update(); // Update the chart with new data
//         } else {
//             // If no chart exists, create a new one
//             bookingChartInstance = new Chart(bookingChartElement, {
//                 type: "line",
//                 data: {
//                     labels: bookingStats.map(stat => `${stat.month}/${stat.year}`),
//                     datasets: [{
//                         label: "Monthly Bookings",
//                         data: bookingStats.map(stat => stat.booking_count),
//                         borderColor: "rgba(75,192,192,1)",
//                         borderWidth: 1
//                     }]
//                 },
//                 options: {
//                     responsive: true,
//                     maintainAspectRatio: false
//                 }
//             });
//         }
//     }
// }


// Register chartjs-plugin-datalabels with Chart.js
// Register the chartjs-plugin-datalabels with Chart.js
Chart.register(ChartDataLabels);

function initializeDepartmentChart(departmentChartElement, departmentStats) {
    if (departmentChartElement) {
        const departments = departmentStats.map(stat => stat.department);
        const bookingCounts = departmentStats.map(stat => stat.booking_count);
        const totalBookings = bookingCounts.reduce((acc, count) => acc + count, 0); // Calculate the total bookings

        // Create the pie chart
        new Chart(departmentChartElement, {
            type: "pie",
            data: {
                labels: departments,
                datasets: [{
                    data: bookingCounts,
                    backgroundColor: ["#FCA5A5", "#FDE047", "#93C5FD", "#93C5FD"]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                const percentage = ((tooltipItem.raw / totalBookings) * 100).toFixed(2);
                                return `${tooltipItem.label}: ${percentage}%`;
                            }
                        }
                    },
                    // Display percentages on chart slices using datalabels
                    datalabels: {
                        formatter: function(value, context) {
                            const percentage = ((value / totalBookings) * 100).toFixed(2);
                            return `${percentage}%`; // Display percentage on the slice
                        },
                        color: '#fff', // Label text color (white)
                        font: {
                            weight: 'bold', // Make the font bold
                            size: 12, // Font size
                            family: 'sans-serif', // Font family
                        },
                        anchor: 'center', // Position in the center of the slice
                        align: 'center'  // Align to the center of the slice
                    }
                }
            }
        });
    }
}
