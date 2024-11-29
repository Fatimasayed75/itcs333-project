document.addEventListener("DOMContentLoaded", function () {
    // Handle reply button click event using event delegation
    document.body.addEventListener('click', function(event) {
        if (event.target && event.target.classList.contains('reply-button')) {
            const commentID = event.target.dataset.commentId;
            const replyContent = document.getElementById(`replyContent-${commentID}`).value;

            // Validate the reply content
            if (!replyContent.trim()) {
                alert('Reply content cannot be empty');
                return;
            }

            // Send AJAX request to save the reply
            fetch('../../../backend/server/reply.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    commentID: commentID,
                    replyContent: replyContent
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Append the new reply to the UI
                    const repliesSection = document.getElementById(`replies-${commentID}`);
                    const newReplyDiv = document.createElement('div');
                    newReplyDiv.classList.add('reply', 'p-4', 'bg-gray-50', 'border-l-4', 'border-gray-400', 'shadow-sm');
                    newReplyDiv.innerHTML = ` 
                        <p class="font-medium text-gray-800"><strong>User:</strong> ${data.replyContent}</p>
                        <p class="text-sm text-gray-500"><small>Posted on: ${data.createdAt}</small></p>
                    `;
                    repliesSection.appendChild(newReplyDiv);

                    // Clear the textarea and hide the reply section
                    document.getElementById(`replyContent-${commentID}`).value = '';
                    document.getElementById(`reply-section-${commentID}`).style.display = 'none';
                    const iconElement = event.target;
                    toggleDetails(commentID, iconElement);
                } else {
                    alert(data.message);  // Show error message if reply wasn't saved
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('There was an error submitting your reply');
            });
        }
    });

    // Fetch existing replies for all comments
    const commentIDs = Array.from(document.querySelectorAll('.comment')).map(comment => comment.id.split('-')[1]);
    
    commentIDs.forEach(commentID => {
        fetchReplies(commentID);
    });

    // Function to fetch replies from the server
    function fetchReplies(commentID) {
        fetch(`../../../backend/server/fetch_replies.php?commentID=${commentID}`)
            .then(response => response.json())
            .then(data => {
                if (data.replies) {
                    const repliesSection = document.getElementById(`replies-${commentID}`);
                    data.replies.forEach(reply => {
                        const replyDiv = document.createElement('div');
                        replyDiv.classList.add('reply', 'p-4', 'bg-gray-50', 'border-l-4', 'border-gray-300', 'shadow-sm');
                        replyDiv.innerHTML = `
                            <p class="font-medium text-gray-800"><strong>User:</strong> ${reply.replyContent}</p>
                            <p class="text-sm text-gray-500"><small>Posted on: ${reply.createdAt}</small></p>
                        `;
                        repliesSection.appendChild(replyDiv);
                    });
                }
            })
            .catch(error => console.error('Error fetching replies:', error));
    }

    // Loop through each comment to check if the last reply was from an admin
    commentIDs.forEach(commentID => {
        const lastReplyIsAdmin = document.getElementById(`reply-section-${commentID}`);

        // Show the reply section only if the last reply is from the admin
        if (lastReplyIsAdmin) {
            lastReplyIsAdmin.style.display = 'block';
        }
    });
});


function toggleDetails(commentID, iconElement) {
    const detailsSection = document.getElementById(`details-${commentID}`);
    // Toggle the visibility of the notification details
    detailsSection.classList.toggle('hidden');
    
    // Toggle the icon (change between down and up arrows)
    if (detailsSection.classList.contains('hidden')) {
        iconElement.classList.remove('fa-chevron-up');
        iconElement.classList.add('fa-chevron-down');
    } else {
        iconElement.classList.remove('fa-chevron-down');
        iconElement.classList.add('fa-chevron-up');
    }
}
