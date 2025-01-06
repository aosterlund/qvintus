<?php
include_once 'functions.php';

class User {
    private $username;
    private $role;
    private $pdo;
    private $errorMessages = [];
    private $errorState = 0;

    function __construct($pdo) {
        $this->role = 2;
        $this->username = "Worker123";
        $this->pdo = $pdo;
    }

    public function cleanInput($data) {
        $data = trim($data);
        return $data; // Removed stripslashes and htmlspecialchars for non-HTML-specific cases.
    }

    public function checkUserRegisterInput(string $uuser, string $umail, string $upass, string $upassrepeat, int $uid = null) {
        $this->errorState = 0;

        // Check if username or email exists
        $stmt_checkUsername = $this->pdo->prepare('SELECT * FROM table_users WHERE u_name = :uuser OR u_email = :email');
        $stmt_checkUsername->bindParam(':uuser', $uuser, PDO::PARAM_STR);
        $stmt_checkUsername->bindParam(':email', $umail, PDO::PARAM_STR);
        $stmt_checkUsername->execute();

        if ($stmt_checkUsername->rowCount() > 0) {
            $this->errorMessages[] = "Användarnamn eller e-postadress är upptagen!";
            $this->errorState = 1;
        }

        // Check if passwords match
        if ($upass !== $upassrepeat) {
            $this->errorMessages[] = "Angivna lösenorden matchar inte!";
            $this->errorState = 1;
        } elseif (strlen($upass) < 8) {
            $this->errorMessages[] = "Angivna lösenordet är för kort!";
            $this->errorState = 1;
        }

        // Validate email format
        if (!filter_var($umail, FILTER_VALIDATE_EMAIL)) {
            $this->errorMessages[] = "E-postadressen är inte i rätt format!";
            $this->errorState = 1;
        }

        return $this->errorState === 1 ? $this->errorMessages : 1;
    }

    public function register(string $uuser, string $umail, string $upass, string $fname, string $lname) {
        $uname = $this->cleanInput($uuser);
        $fname = $this->cleanInput($fname);
        $lname = $this->cleanInput($lname);

        $stmt_insertNewUser = $this->pdo->prepare(
            'INSERT INTO table_users (u_name, u_pass, u_email, u_role_fk, u_status, u_fname, u_lname) 
             VALUES (:user, :upass, :umail, 1, 1, :fname, :lname)'
        );
        $stmt_insertNewUser->bindParam(':user', $uname, PDO::PARAM_STR);
        $stmt_insertNewUser->bindParam(':upass', $upass, PDO::PARAM_STR);  // No hashing now
        $stmt_insertNewUser->bindParam(':umail', $umail, PDO::PARAM_STR);
        $stmt_insertNewUser->bindParam(':fname', $fname, PDO::PARAM_STR);
        $stmt_insertNewUser->bindParam(':lname', $lname, PDO::PARAM_STR);

        if ($stmt_insertNewUser->execute()) {
            return 1;
        } else {
            $this->errorMessages[] = "Lyckades inte registrera användaren! Kontakta support!";
            return $this->errorMessages;
        }
    }

    public function login(string $unamemail, string $upass) {
        $unamemail = $this->cleanInput($unamemail);

        $stmt_checkUsername = $this->pdo->prepare('SELECT * FROM table_users WHERE u_name = :uname OR u_email = :email');
        $stmt_checkUsername->bindParam(':uname', $unamemail, PDO::PARAM_STR);
        $stmt_checkUsername->bindParam(':email', $unamemail, PDO::PARAM_STR);
        $stmt_checkUsername->execute();

        if ($stmt_checkUsername->rowCount() === 0) {
            $this->errorMessages[] = "Användarnamnet eller e-postadressen finns inte!";
            return $this->errorMessages;
        }

        $userData = $stmt_checkUsername->fetch();

        // No password verification, just plain comparison
        if ($upass === $userData['u_pass']) {
            if ((int)$userData['u_status'] === 0) {
                $this->errorMessages[] = "Detta konto har inaktiverats! Kontakta administratören och be om hjälp.";
                return $this->errorMessages;
            }

            $_SESSION['user_id'] = $userData['u_id'];
            $_SESSION['user_name'] = $userData['u_name'];
            $_SESSION['user_email'] = $userData['u_email'];
            $_SESSION['user_role'] = $userData['u_role_fk'];
            session_regenerate_id(true);

            header("Location: books.php");
            exit();
        } else {
            $this->errorMessages[] = "Lösenordet är fel!";
            return $this->errorMessages;
        }
    }

    public function checkLoginStatus() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php");
            exit();
        }
        return true;
    }

    public function checkUserRole(int $requiredValue) {
        $stmt_checkUserRole = $this->pdo->prepare(
            'SELECT r_level FROM table_roles WHERE r_id = :rid'
        );
        $stmt_checkUserRole->bindParam(':rid', $_SESSION['user_role'], PDO::PARAM_INT);
        $stmt_checkUserRole->execute();

        $userRoleData = $stmt_checkUserRole->fetch();

    }

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: index.php");
    }

    public function deleteUser(int $uid) {
        $stmt_deleteUser = $this->pdo->prepare('DELETE FROM table_users WHERE u_id = :uid');
        $stmt_deleteUser->bindParam(':uid', $uid, PDO::PARAM_INT);

        return $stmt_deleteUser->execute()
            ? "Användaren har raderats"
            : "Något gick snett ... Försök igen.";
    }
}
?>
