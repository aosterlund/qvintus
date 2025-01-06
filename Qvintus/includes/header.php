<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/class.user.php';
require_once 'includes/class.admin.php';
require_once 'includes/class.book.php';
require_once 'includes/class.utility.php';
$user = new User($pdo);


if(isset($_GET['logout'])) {
	$user->logout();
}

$adminMenuLinks = array(
    array(
        "title" => "Administratör",
        "url" => "admin.php"
	),
);
?>

<!DOCTYPE html>
<html>
<head>
	<title>Qvintus</title>
	<link rel="stylesheet" href="css/style.css">
	<!--<script defer src="js/script.js"></script>-->
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="UTF-8">
	<link rel="icon" href="assets/favicon.ico" type="image/ico">
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.4/dist/umd/popper.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body>
<div class="wrapper d-flex flex-column min-vh-100">
<header class="container-fluid bg-dark mb-5 px-0">
	<nav class="navbar navbar-expand-lg bg-body-tertiary navbar-dark bg-dark px-2 ps-lg-4" data-bs-theme="dark">
	<div class="container-fluid px-2 px-sm-4">
		<a class="navbar-brand" href="index.php">Qvintus</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse justify-content-end" id="navbarNav">
		<ul class="navbar-nav">
			<li class="nav-item">
			<a class="nav-link" href="index.php">Hem</a>
			</li>
			<li class="nav-item">
			<a class="nav-link" href="contact.php">Kontakt</a>
			</li>
			<?php
			// Only show "Worker Login" if the user is not logged in
			if (!isset($_SESSION['user_id'])) {
				echo '<li class="nav-item">
						<a class="nav-link" href="login.php">Admin Login</a>
					</li>';
			}

			// Check if user is logged in and has the admin role
			if (isset($_SESSION['user_id'])) {
				if ($user->checkUserRole(200)) {
					foreach ($adminMenuLinks as $menuItem) {
						echo "<li class='nav-item'>
						<a class='nav-link' href='{$menuItem['url']}'>{$menuItem['title']}</a>
						</li>";
					}
				}
				echo "
				<li class='nav-item'>
					<a class='nav-link' href='?logout=1.php'>Logga ut</a>
				</li>";
			}
			?>
		</ul>
		</div>
	</div>
	</nav>

</header>