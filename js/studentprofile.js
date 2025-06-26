function updateProfile() {
  window.location.href = "student-editprofile.php";
}

// Example of JavaScript functionality for enhanced user interaction

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('UpdateForm');

  // Handle form submission
  form.addEventListener('submit', (event) => {
      const username = document.getElementById('option1').value.trim();
      const email = document.getElementById('option2').value.trim();
      const dob = document.getElementById('option3').value.trim();

      // Validate the inputs
      if (!username || !email || !dob) {
          alert('Please fill in all fields.');
          event.preventDefault(); // Prevent form submission
          return;
      }

      // Show confirmation dialog
      const isConfirmed = confirm('Are you sure you want to update your profile?');
      if (!isConfirmed) {
          event.preventDefault(); // Prevent form submission
          return;
      }
  });
});


