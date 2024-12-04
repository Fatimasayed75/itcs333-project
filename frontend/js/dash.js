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
            initializeBookingChart(bookingChartElement, data.bookingStats);
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


function initializeBookingChart(bookingChartElement, bookingStats) {
    if (bookingChartElement) {
        // Define an array of month names
        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        // Create an array with booking data for each month (initialized to 0)
        const bookingCounts = new Array(12).fill(0);

        // Populate the bookingCounts array with the actual booking data
        bookingStats.forEach(stat => {
            const monthIndex = stat.month - 1;
            bookingCounts[monthIndex] = stat.booking_count;
        });

        // Replace zero values with an empty string to hide them
        const adjustedBookingCounts = bookingCounts.map(count => (count === 0 ? "" : count));

        // If the chart instance already exists, just update it
        if (bookingChartInstance) {
            bookingChartInstance.data.labels = monthNames;
            bookingChartInstance.data.datasets[0].data = adjustedBookingCounts;
            bookingChartInstance.update();
        } else {
            bookingChartInstance = new Chart(bookingChartElement, {
                type: "bar",
                data: {
                    labels: monthNames,
                    datasets: [{
                        label: "Number of Monthly Bookings",
                        data: adjustedBookingCounts,
                        backgroundColor: "rgba(216, 133, 163, 0.4)",
                        borderColor: "#D885A3",
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }
}



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
                        color: '#fff',
                        font: {
                            weight: 'bold',
                            size: 12,
                            family: 'sans-serif',
                        },
                        anchor: 'center',
                        align: 'center'
                    }
                }
            }
        });
    }
}
