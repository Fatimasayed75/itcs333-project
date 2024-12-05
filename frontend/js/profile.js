document.addEventListener('DOMContentLoaded', function() {
    // Function to open the edit profile modal
    window.openEditProfileModal = function() {
        const modal = document.getElementById('editProfileModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    // Function to close the edit profile modal
    window.closeEditProfileModal = function() {
        const modal = document.getElementById('editProfileModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    // Function to preview the selected image
    window.previewImage = function(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('profilePreview');
                if (preview) {
                    preview.src = e.target.result;
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Handle form submission
    const editProfileForm = document.getElementById('editProfileForm');
    if (editProfileForm) {
        editProfileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            // Show loading state
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = 'Saving...';
            submitButton.disabled = true;

            
            fetch('components/editProfile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message || 'Error updating profile');
                if (data.success) {
                    // Show success message
                    alert('Profile updated successfully!');

                    // Update profile information in real-time
                    document.querySelector('.profile-details .profile-pic img').src = formData.get('profilePic') ? URL.createObjectURL(editProfileForm.querySelector('#profilePic').files[0]) : document.querySelector('.profile-details .profile-pic img').src;
                    document.querySelector('.profile-details .profile-info').innerHTML = `
                        <p class="mb-2"><strong>First Name:</strong> ${formData.get('firstName')}</p>
                        <p class="mb-2"><strong>Last Name:</strong> ${formData.get('lastName')}</p>
                        <p class="mb-2"><strong>Email:</strong> ${formData.get('email')}</p>
                        <p class="mb-2"><strong>Role:</strong> ${document.querySelector('.profile-details .profile-info').querySelector('p:nth-child(4)').textContent.split(': ')[1]}</p>
                    `;
                    closeEditProfileModal();
                    // Reload the page to show updated information
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the profile');
            })
            .finally(() => {
                // Restore button state
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            });
        });
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('editProfileModal');
        if (e.target === modal) {
            closeEditProfileModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeEditProfileModal();
        }
    });
});