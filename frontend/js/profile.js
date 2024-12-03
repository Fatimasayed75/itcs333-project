function editProfile() {
    console.log('Starting to fetch profile template...');
    fetch('frontend/templates/components/profile.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            console.log('Profile template fetched successfully.');
            return response.text();
        })
        .then(data => {
            console.log('Profile template loaded, updating DOM...');
            document.getElementById('editProfileContent').innerHTML = data;
            document.getElementById('editProfileModal').classList.remove('hidden');
            console.log('Profile modal displayed.');
        })
        .catch(error => {
            console.error('Error loading edit profile:', error);
        });
}

function closeModal() {
    console.log('Closing modal...');
    document.getElementById('editProfileModal').classList.add('hidden');
}

document.getElementById('editProfileForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const formData = new FormData(this);

    fetch('editProfile.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Profile updated successfully');
            // Optionally, you can close the modal or update the UI here
        } else {
            console.error('Error updating profile:', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});