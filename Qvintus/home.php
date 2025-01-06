<?php
include_once 'includes/header.php';

if (isset($_POST['user-login'])) {
    echo "Debug: Username - " . $_POST['uname'] . "<br>";
    echo "Debug: Password - " . $_POST['upass'] . "<br>";

    $errorMessages = $user->login($_POST['uname'], $_POST['upass']);
}
?>



<?php
include_once 'includes/footer.php';
?>