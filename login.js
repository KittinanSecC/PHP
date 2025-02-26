const signUpButton = document.getElementById('signUpButton');
const signInButton = document.getElementById('signInButton');
const signInForm = document.getElementById('signIn');
const signUpForm = document.getElementById('signup');

signUpButton.addEventListener('click', function () {
    signInForm.style.display = "none";
    signUpForm.style.display = "block";
})
signInButton.addEventListener('click', function () {
    signInForm.style.display = "block";
    signUpForm.style.display = "none";
})

// Password toggle functionality
document.querySelectorAll('.togglePassword').forEach(icon => {
    icon.addEventListener('click', function () {
        const targetId = this.getAttribute('data-target');
        const passwordField = document.getElementById(targetId);
        if (passwordField.type === "password") {
            passwordField.type = "text";
            this.classList.remove("fa-eye-slash");
            this.classList.add("fa-eye");
        } else {
            passwordField.type = "password";
            this.classList.remove("fa-eye");
            this.classList.add("fa-eye-slash");
        }
    });
});

// Toggle between login and signup forms
document.getElementById('signUpButton').addEventListener('click', function () {
    document.getElementById('signIn').style.display = 'none';
    document.getElementById('signup').style.display = 'block';
});

document.getElementById('signInButton').addEventListener('click', function () {
    document.getElementById('signup').style.display = 'none';
    document.getElementById('signIn').style.display = 'block';
});

// Validate Password Match
function validateSignup() {
    let password = document.getElementById("signupPassword").value;
    let confirmPassword = document.getElementById("confirmPassword").value;
    let errorText = document.getElementById("passwordError");

    if (password !== confirmPassword) {
      errorText.style.display = "block";
      return false; // Prevent form submission
    } else {
      errorText.style.display = "none";
      return true;
    }
  }

  // Toggle between login and signup forms
  document.getElementById('signUpButton').addEventListener('click', function () {
    document.getElementById('signIn').style.display = 'none';
    document.getElementById('signup').style.display = 'block';
  });

  document.getElementById('signInButton').addEventListener('click', function () {
    document.getElementById('signup').style.display = 'none';
    document.getElementById('signIn').style.display = 'block';
  });



  