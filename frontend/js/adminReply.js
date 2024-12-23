document.addEventListener("DOMContentLoaded", function () {
    // Handle reply button click event using event delegation
    document.body.addEventListener('click', function (event) {
        if (event.target && event.target.classList.contains('admin-reply-button')) {
            const commentID = event.target.dataset.commentId;
            const replyContent = document.getElementById(`adminReplyContent-${commentID}`).value;

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
                    replyContent: replyContent,
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Check if replies section exists
                        let repliesSection = document.getElementById(`replies-${commentID}`);
                        if (!repliesSection) {
                            // Create a new replies section if it doesn't exist
                            repliesSection = document.createElement('div');
                            repliesSection.id = `replies-${commentID}`;
                            repliesSection.classList.add('replies-container', 'space-y-4', 'mt-4');
                        }

                        // Create the new reply element
                        const newReplyDiv = document.createElement('div');
                        newReplyDiv.classList.add('reply', 'p-3', 'bg-[#FDF6F6]', 'border-l-4', 'border-[#D885A3]', 'shadow-sm');
                        newReplyDiv.innerHTML = `
                            <p class="text-sm text-gray-800"><strong>You:</strong> ${data.replyContent}</p> 
                            <p class="text-xs text-gray-500">Posted on: ${data.createdAt}</p>
                        `;
                        //${data.fullName}

                        // Append the new reply to the replies section
                        repliesSection.appendChild(newReplyDiv);

                        // Now we adjust the order, placing the replies section before the reply input section
                        const replyInputSection = document.getElementById(`admin-reply-section-${commentID}`);
                        if (replyInputSection && repliesSection) {
                            replyInputSection.parentNode.insertBefore(repliesSection, replyInputSection);
                        }

                        // Clear the textarea
                        document.getElementById(`adminReplyContent-${commentID}`).value = '';
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
                        replyDiv.classList.add('reply', 'p-3', 'bg-gray-50', 'border-l-4', 'border-gray-300', 'shadow-sm', 'rounded-md');
                        replyDiv.innerHTML = `
                            <p class="font-medium text-gray-800"><strong>Admin :</strong> ${reply.replyContent}</p>
                            <p class="text-sm text-gray-500"><small>Posted on: ${reply.createdAt}</small></p>
                        `;
                        repliesSection.appendChild(replyDiv);
                    });
                }
            })
            .catch(error => console.error('Error fetching replies:', error));
    }
});
