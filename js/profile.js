// Function to show different sections
function showSection(sectionId) {
    // Hide all sections
    document.querySelectorAll('.content-section').forEach(section => {
        section.style.display = 'none';
    });
    
    // Show the selected section
    document.getElementById(sectionId).style.display = 'block';
    
    // Update active menu item
    document.querySelectorAll('.menu-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Find the clicked menu item and add active class
    //  event.target.classList.add('active');

    document.querySelector(`.menu-item[onclick="showSection('${sectionId}')"]`).classList.add('active');
    if (sectionId === 'logout') {
        document.getElementById('logoutOverlay').style.display = 'flex';
    }

}

// Initialize the page - show personal info by default
document.addEventListener('DOMContentLoaded', function() {
    // Show personal info section by default
    document.getElementById('personal-info').style.display = 'block';
    
    // Set the first menu item as active
    document.querySelector('.menu-item').classList.add('active');
  
     // Initialize message timers
     initializeMessageTimers();

     setupLogout();


     // Attach logout event listener
    //  document.getElementById("my5").addEventListener("click", logout);
});

function cancelEdit(){
    document.getElementById('editProfileForm').style.display = 'none';  
}

// Function to make success messages disappear after a few seconds
function initializeMessageTimers() {
    const messages = document.querySelectorAll('.alert');
    messages.forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            setTimeout(() => {
                message.style.display = 'none';
            }, 500);
        }, 5000);
    });
}

if (confirmLogout) {
    confirmLogout.addEventListener("click", function() {
        // Change profile picture to blank
        const profilePic = document.getElementById("pro"); // Using the ID from your HTML
        if (profilePic) {
            profilePic.src = "images/blankprofile.jpg";
        }
        
        // Hide username and email
        const userName = document.getElementById("ad"); // Using the ID from your HTML
        const userEmail = document.getElementById("ae"); // Using the ID from your HTML
        
        if (userName) {
            userName.classList.add("hidden");
        }
        
        if (userEmail) {
            userEmail.classList.add("hidden");
        }
        
        // Hide the logout overlay
        document.getElementById('logoutOverlay').style.display = 'none';
        
        // Show a logout success message
        alert("You have been logged out successfully!");
        
        // Optional: Redirect to login page after a short delay
        setTimeout(function() {
             window.location.href = "singup.php"; // Change to your home/login page
            // showSection('singup.php');

        });
    });
}
    
    // Handle cancel logout
    if (cancelLogout) {
        cancelLogout.addEventListener("click", function() {
            // Close the popup without logging out
            document.getElementById('logoutOverlay').style.display = 'none';
            // Return to previous active section
            showSection('personal-info');
        });
    }



document.addEventListener("DOMContentLoaded", function() {
    // Get references to elements
    const logoutBtn = document.getElementById("logoutBtn");
    const logoutOverlay = document.getElementById("logoutOverlay");
    const confirmLogout = document.getElementById("confirmLogout");
    const cancelLogout = document.getElementById("cancelLogout");
    const profileImg = document.getElementById("profileImg");
    const username = document.getElementById("username");
    const userEmail = document.getElementById("userEmail");

    // Show logout popup when logout button is clicked
    logoutBtn.addEventListener("click", function() {
        logoutOverlay.style.display = "flex";
    });

    // Handle confirm logout
    confirmLogout.addEventListener("click", function() {
        // Change profile picture to blank
        profileImg.src = "images/blankprofile.jpg";
        
        // Hide username and email
        username.classList.add("hidden");
        userEmail.classList.add("hidden");
        
        // Close the popup
        logoutOverlay.style.display = "none";
        
        
        // For demo purposes, alert that logout was successful
        alert("You have been logged out successfully!");
    });

    // Handle cancel logout
    cancelLogout.addEventListener("click", function() {
        // Close the popup without logging out
        logoutOverlay.style.display = "none";
    });
});

