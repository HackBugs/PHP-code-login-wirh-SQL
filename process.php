<?php
// Database connection parameters
$servername = "localhost";
$username = "root"; // Default username for localhost
$password = ""; // Default password for localhost
$dbname = "contact_form";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Proceed with handling form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

    if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Please fill in all fields correctly.";
        exit;
    }

    // Prepare SQL statement to insert data into the database
    $stmt = $conn->prepare("INSERT INTO contacts (name, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $email);

    // Execute the prepared statement
    if ($stmt->execute()) {
        echo "Hello, " . $name . "! Your email is " . $email . ". Your form has been submitted successfully.";
    } else {
        // Check for specific error message indicating duplicate entry
        if ($stmt->errno == 1062) {
            echo "Error: Duplicate entry. This email address may already exist in our records.";
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    // Close statement
    $stmt->close();
} else {
    echo "Invalid request method.";
}

// Close connection
$conn->close();
?>
