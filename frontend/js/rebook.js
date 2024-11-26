document.addEventListener("DOMContentLoaded", () => {
    document.body.addEventListener("click", async function(event) {
        if (event.target && event.target.classList.contains("rebook-btn")) {
            const roomId = event.target.dataset.roomId;
            await rebookRoom(roomId);
        }
    });
});

async function rebookRoom(roomId) {
    try {
        // Send request to roomDetails.php for booking or rebooking
        const response = await fetch(`../../../backend/server/roomDetails.php?roomID=${roomId}&action=rebook`, {
            method: "GET",
        });

        const data = await response.json();

        if (data.error) {
            throw new Error(data.error); 
        }

        // Reload the updated room details after rebooking
        await loadRoomDetails(roomId);

    } catch (error) {
        console.error("Error rebooking room:", error);
        alert("Failed to rebook room. Please try again later.");
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

        // Replace placeholders in the HTML template
        const filledTemplate = template
            .replace(/{{roomID}}/g, roomData.roomID)
            .replace(/{{type}}/g, roomData.type)
            .replace(/{{capacity}}/g, roomData.capacity)
            .replace(
                /{{image}}/g,
                `https://placehold.co/300x200?text=Image+For+${roomData.roomID}`
            );

        // Update the page content with room details
        const mainContent = document.getElementById("main-content");
        mainContent.innerHTML = filledTemplate;

        // Reinitialize any event listeners if needed
        const homeBtn = document.getElementById("backToHomeBtn");
        if (homeBtn) {
            homeBtn.addEventListener("click", navigateToHomePage);
        }
    } catch (error) {
        console.error("Error loading room details:", error);
    }
}

async function navigateToHomePage() {
    await loadContent("home.php");
    initializeHomeEventListeners();
}

