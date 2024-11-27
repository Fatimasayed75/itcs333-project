document.addEventListener('DOMContentLoaded', () => {
    // Attach event listeners to all reply forms
    document.querySelectorAll('.reply-form').forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent the default form submission
            
            // Extract data from the form
            const commentID = this.querySelector('input[name="commentID"]').value;
            const replyContent = this.querySelector('textarea[name="replyContent"]').value;

            // Debugging logs to verify data
            console.log('Submitting reply:', { commentID, replyContent });

            // Perform an AJAX request using Fetch API
            console.log("HI");
            fetch('../../../backend/server/reply.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    commentID: commentID,
                    replyContent: replyContent,
                }),
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json(); // Parse the response as JSON
                })
                .then(data => {
                    console.log('Server response:', data); // Debugging the server response

                    if (data.success) {
                        // Update the UI with the new reply
                        const repliesSection = document.querySelector(`#replies-${commentID}`);
                        
                        // Create a new reply element
                        const newReply = document.createElement('div');
                        newReply.classList.add('reply', 'p-4', 'bg-gray-50', 'border-l-4', 'border-gray-300', 'shadow-sm');
                        newReply.innerHTML = `
                            <p class="font-medium text-gray-800"><strong>You:</strong> ${replyContent}</p>
                            <p class="text-sm text-gray-500"><small>Posted just now</small></p>
                        `;
                        
                        // Append the new reply to the replies section
                        repliesSection.appendChild(newReply);
                        
                        // Clear the reply textarea
                        this.querySelector('textarea[name="replyContent"]').value = '';
                    } else {
                        // Show an alert if the reply could not be posted
                        alert('Failed to post reply: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error); // Log any errors to the console
                    alert('An error occurred while posting your reply. Please try again.');
                });
        });
    });
});
