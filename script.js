// Function to edit profile (show edit options)
function editProfile() {
    document.getElementById('editProfileForm').style.display = 'flex'; 
    // function saveFunction(){
    //     document.getElementById('editProfileForm').style.display = 'flex';
    // }
    
    
}
function saveFunction(event) {
    event.preventDefault(); // Prevent form submission (page reload)
}


function cancelEdit(){
          document.getElementById('editProfileForm').style.display = 'none';  
    
}

// Function to show card details
function displayCardDetails() {
    const cardDetails = document.getElementById('card-details');
    cardDetails.style.display = 'flex';
}



// Function to show settings (brightness, battery saver, etc.)
function showSettings() {
    // Create the settings modal with the display options
    let settingsModal = `
        <div class="settings-modal" align="right">
            <h2>Settings</h2><br><br>
            
            <div class="settings-option">
                <label>Version: 1.0.0</label>
            </div>
            
            <div class="settings-option">
                <label>Change Theme:</label>
                <button onclick="changeTheme('light')">Light</button>
                <button onclick="changeTheme('dark')">Dark</button>
            </div>
            <div class="settings-option" onclick="clearCache()">Clear Cache</div>
            <div class="settings-option" onclick="viewPrivacyPolicy()">Privacy Policy</div>
            <div class="settings-option" onclick="toggleNotifications()">Notification Settings</div>
            <div class="settings-option" onclick="toggleNetworkAcceleration()">Network Acceleration</div>
           <button onclick="closeSettings()">Close</button>
        </div>
    `;

    // Insert the modal into the page
    document.body.insertAdjacentHTML('beforeend', settingsModal);
}

function closeSettings() {
    // Close the settings modal
    let modal = document.querySelector('.settings-modal');
    modal.remove();
}

function changeTheme(theme) {
    // Apply light or dark theme
    if (theme === 'dark') {
        document.body.classList.add('dark-theme');
        document.body.classList.remove('light-theme');
    } else {
        document.body.classList.add('light-theme');
        document.body.classList.remove('dark-theme');
    }
}

function changeBrightness(brightness) {
    // Change the brightness of the page
    if (brightness === 'dark') {
        document.body.style.filter = 'brightness(50%)';
    } else {
        document.body.style.filter = 'brightness(100%)';
    }
}

function clearCache() {
    // Simulate cache clear action
    alert('Cache has been cleared!');
}

function viewPrivacyPolicy() {
    // Simulate viewing privacy policy
    alert('Viewing Privacy Policy...');
}

function toggleNotifications() {
    // Simulate toggling notification settings
    alert('Toggled Notification Settings');
}

function toggleNetworkAcceleration() {
    // Simulate toggling network acceleration
    alert('Toggled Network Acceleration');
}



// Function to handle logout confirmation
document.addEventListener("DOMContentLoaded", function () {
    function logout() {
        // Get user profile elements
        let profileImg = document.querySelector(".profile-sidebar img");
        let userName = document.querySelector(".profile-sidebar h5");
        let userEmail = document.querySelector(".profile-sidebar p");

        // Create the logout popup
        let popup = document.createElement("div");
        popup.id = "logoutPopup";
        popup.innerHTML = `
            <img src="${profileImg.src}" alt="Profile Picture" style="width:80px; height:80px; border-radius:50%; margin-bottom:10px;">
            <h5>${userName.innerText}</h5>
            <p>Are you sure you want to log out?</p>
            <button id="confirmLogout">Confirm Logout</button>
            <button id="cancelLogout">Cancel</button>
        `;

        // Append popup to the body
        document.body.appendChild(popup);

        // Handle Confirm Logout button
        document.getElementById("confirmLogout").addEventListener("click", function () {
            profileImg.src = "C:\xampp\htdocs\dexter\images\blankprofile.jpg";  // Remove profile picture
            userName.classList.add("hidden"); // Hide username
            userEmail.classList.add("hidden"); // Hide email
            document.body.removeChild(popup); // Remove popup
        });

        // Handle Cancel Logout button
        document.getElementById("cancelLogout").addEventListener("click", function () {
            document.body.removeChild(popup); // Close popup
        });
    }

    // Attach the logout function to the logout button
    document.getElementById("my5").addEventListener("click", logout);
});