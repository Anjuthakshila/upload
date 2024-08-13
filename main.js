
<script>
// Form validation before submission
function validateForm() {
    let firstName = document.getElementById("first_name").value;
    let lastName = document.getElementById("last_name").value;
    let email = document.getElementById("email").value;
    let phoneNumber = document.getElementById("phone_number").value;

    if (firstName == "" || lastName == "" || email == "" || phoneNumber == "") {
        alert("All fields are required!");
        return false;
    }

    // Email format validation
    let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        alert("Please enter a valid email address!");
        return false;
    }

    // Phone number validation
    let phonePattern = /^[0-9]{10}$/;
    if (!phonePattern.test(phoneNumber)) {
        alert("Please enter a valid 10-digit phone number!");
        return false;
    }

    return true;
}

// Confirmation for deletion
function confirmDeletion(firstName, lastName) {
    return confirm("Are you sure you want to delete the record of " + firstName + " " + lastName + "?");
}

// Autofill form with existing data on update
function autofillForm(id, firstName, lastName, email, phoneNumber) {
    document.getElementById("id").value = id;
    document.getElementById("first_name").value = firstName;
    document.getElementById("last_name").value = lastName;
    document.getElementById("email").value = email;
    document.getElementById("phone_number").value = phoneNumber;
    document.getElementById("submitButton").value = "Update";
}
</script>