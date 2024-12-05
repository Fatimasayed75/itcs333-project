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

    // Function to update profile display
    function updateProfileDisplay(formData) {
        try {
            console.log('Updating profile display with:', {
                firstName: formData.get('firstName'),
                lastName: formData.get('lastName'),
                hasProfilePic: formData.get('profilePic') ? true : false
            });

            // Update name fields
            const firstNameField = document.querySelector('.profile-info p:nth-child(1)');
            const lastNameField = document.querySelector('.profile-info p:nth-child(2)');
            
            if (firstNameField && formData.get('firstName')) {
                firstNameField.innerHTML = `<strong>First Name:</strong> ${formData.get('firstName')}`;
            } else {
                console.warn('First name field not found or empty');
            }
            
            if (lastNameField && formData.get('lastName')) {
                lastNameField.innerHTML = `<strong>Last Name:</strong> ${formData.get('lastName')}`;
            } else {
                console.warn('Last name field not found or empty');
            }

            // Update profile pictures if a new one was uploaded
            const newProfilePic = formData.get('profilePic');
            if (newProfilePic && newProfilePic.size > 0) {
                console.log('Updating profile picture');
                const profilePics = document.querySelectorAll('img[alt="Profile Picture"]');
                const imageUrl = URL.createObjectURL(newProfilePic);
                profilePics.forEach(pic => {
                    pic.src = imageUrl;
                });
            }
        } catch (error) {
            console.error('Error in updateProfileDisplay:', error);
            throw error;
        }
    }

    // Handle form submission
    const editProfileForm = document.getElementById('editProfileForm');
    if (editProfileForm) {
        editProfileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('../components/editProfile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update UI or show success message
                    alert(data.message);
                    closeEditProfileModal();
                    // Optionally, refresh the page or update profile info
                    location.reload();
                } else {
                    // Show error message
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the profile.');
            });
        });
    } else {
        console.warn('Edit profile form not found');
    }

    // Close modal when clicking outside
    const modal = document.getElementById('editProfileModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditProfileModal();
            }
        });
    } else {
        console.warn('Modal element not found');
    }

    // Prevent modal close when clicking inside
    const modalContent = document.querySelector('.inline-block');
    if (modalContent) {
        modalContent.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    } else {
        console.warn('Modal content element not found');
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeEditProfileModal();
        }
    });
});