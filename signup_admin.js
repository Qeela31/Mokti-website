document.getElementById("signupForm").addEventListener("submit", function (event) {
    // Prevent form submission
    event.preventDefault();

    // Get form input values
    const username = document.getElementById("username").value.trim();
    const email = document.getElementById("email").value.trim();
    const phone = document.getElementById("phone").value.trim();
    const password = document.getElementById("password").value.trim();

    // Validation flags
    let isValid = true;
    let errorMessage = "";

    // Validate username: must only contain alphabets
    const usernameRegex = /^[A-Za-z]+$/;
    if (!usernameRegex.test(username)) {
        isValid = false;
        errorMessage += "Username must contain only alphabets (uppercase, lowercase, or mix).\n";
    }

    // Validate email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        isValid = false;
        errorMessage += "Please enter a valid email address.\n";
    }

    // Validate phone (optional, but numeric if provided)
    if (phone && !/^\d+$/.test(phone)) {
        isValid = false;
        errorMessage += "Phone number must contain only digits.\n";
    }

    // Validate password: 6-8 characters, at least one uppercase, one numeric, one special character, no spaces
    const passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+])[A-Za-z\d!@#$%^&*()_+]{6,8}$/;
    if (!passwordRegex.test(password)) {
        isValid = false;
        errorMessage += "Password must be 6-8 characters long, include at least one uppercase letter, one numeric digit, one special character, and contain no spaces.\n";
    }

    // Check if all validations passed
    if (!isValid) {
        alert(errorMessage); // Show error messages
    } else {
        alert("Sign-up successful!");
        event.target.submit(); // Submit the form if valid
    }
});
