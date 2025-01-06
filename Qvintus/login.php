<?php
// Start the session
session_start();

// Include header
include_once 'includes/header.php';

// Initialize error messages array
$errorMessages = [];

// Check if the form is submitted
if (isset($_POST['user-login'])) {
    // Get the login credentials from the form
    $username = $_POST['uname'];
    $password = $_POST['upass'];

    // Debugging: Print out the entered username and password
    echo "Username entered: " . htmlspecialchars($username) . "<br>";
    echo "Password entered: " . htmlspecialchars($password) . "<br>";

    // Perform basic validation (You can expand this later)
    if (empty($username) || empty($password)) {
        $errorMessages[] = "Both username and password are required.";
    } else {
        // Example admin credentials for testing purposes
        $adminUsername = 'admin';
        $adminPassword = 'password123'; // Replace with real password hashing in a real app

        // Debugging: Check if the credentials match
        if ($username === $adminUsername && $password === $adminPassword) {
            echo "<p>Credentials match! Logging you in...</p>"; // Debugging

            // Set session variable for admin login
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['username'] = $username; // Store the username for later use
            // Redirect to the admin dashboard
            header("Location: admin.php");
            exit;
        } else {
            echo "<p>Invalid username or password.</p>"; // Debugging
            $errorMessages[] = "Invalid username or password.";
        }
    }
}
?>

<div class="container">
    <?php
    // Show success message if the user is redirected after sign-up
    if (isset($_GET['newuser'])) {
        echo "<div class='alert alert-success text-center' role='alert'>
            You have successfully signed up. Please login using the form below.
        </div>";
    }

    // Display error messages if there are any
    if (isset($errorMessages)) {
        echo "<div class='alert alert-danger text-center' role='alert'>";
        foreach ($errorMessages as $message) {
            echo $message;
        }
        echo "</div>";
    }
    ?>

    <div class="mw-500 mx-auto">
        <h1 class="my-5">Logga in</h1>
        <form action="" method="post">
            <label class="form-label" for="uname">Användarnamn eller e-post</label><br>
            <input class="form-control" type="text" name="uname" id="uname" required><br>

            <label class="form-label" for="upass">Lösenord</label><br>
            <input class="form-control" type="password" name="upass" id="upass" required><br>

            <input class="btn btn-primary py-2 px-4" type="submit" name="user-login" value="Logga in">
        </form>
    </div>
</div>

<?php
// Include footer
include_once 'includes/footer.php';
?>
