<?php
include_once 'functions.php';

class Admin {

    private $pdo;
    private $errorMessages = [];
    private $errorState = 0;


    function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function cleanInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    public function editUserInfo(string $umail, string $upassold, string $upassnew, int $uid, int $role, string $ufname, string $ulname, int $status) {
        // Clean and validate first name
        $cleanedFname = $this->cleanInput($ufname);
        if (empty($cleanedFname) || !preg_match("/^[a-zA-Z\s]+$/", $cleanedFname)) {
            array_push($this->errorMessages, "Förnamn får inte vara tomt och får endast innehålla bokstäver! ");
            return $this->errorMessages;
            //return "Förnamn får inte vara tomt och får endast innehålla bokstäver!";
        }
    
        // Clean and validate last name
        $cleanedLname = $this->cleanInput($ulname);
        if (empty($cleanedLname) || !preg_match("/^[a-zA-Z\s]+$/", $cleanedLname)) {
            array_push($this->errorMessages, "Efternamn får inte vara tomt och får endast innehålla bokstäver! ");
            return $this->errorMessages;
            //return "Efternamn får inte vara tomt och får endast innehålla bokstäver!";
        }
    
        // Get password and current email of the user
        $stmt_getUserDetails = $this->pdo->prepare('SELECT u_password, u_email FROM table_users WHERE u_id = :uid');
        $stmt_getUserDetails->bindParam(':uid', $uid, PDO::PARAM_INT);
        $stmt_getUserDetails->execute();
        $userDetails = $stmt_getUserDetails->fetch();
        
        // If user edits their own data (legacy)
        if (isset($_POST['edit-user-submit'])) {
            // Check if entered password is correct
            if (!password_verify($upassold, $userDetails['u_password'])) {
                array_push($this->errorMessages, "Lösenordet är inte giltigt ");
                return $this->errorMessages;    
                //return "The password is invalid";
            }
        }
    
        // Update fields
        $hashedPassword = password_hash($upassnew, PASSWORD_DEFAULT);
        
        // Update password if new password field isn't empty
        if (!empty($upassnew)) {
            $updatePassword = "u_password = :upassnew, ";
        } else {
            $updatePassword = "";
        }
        // Only set u_email if it has changed
        $updateEmail = $umail !== $userDetails['u_email'] ? ", u_email = :umail" : "";
    
        // Update in the database 
        $stmt_editUserInfo = $this->pdo->prepare("
            UPDATE table_users
            SET $updatePassword u_role_fk = :role, u_status = :status, u_fname = :ufname, u_lname = :ulname 
            $updateEmail
            WHERE u_id = :uid
        ");
        
        // Bind parameters
        if (!empty($upassnew)) {
            $stmt_editUserInfo->bindParam(':upassnew', $hashedPassword, PDO::PARAM_STR);
        }

        if ($updateEmail) {
            $stmt_editUserInfo->bindParam(':umail', $umail, PDO::PARAM_STR);
        }
        
        $stmt_editUserInfo->bindParam(':role', $role, PDO::PARAM_INT);
        $stmt_editUserInfo->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt_editUserInfo->bindParam(':ufname', $cleanedFname, PDO::PARAM_STR); // Use cleaned name
        $stmt_editUserInfo->bindParam(':ulname', $cleanedLname, PDO::PARAM_STR); // Use cleaned name
        $stmt_editUserInfo->bindParam(':uid', $uid, PDO::PARAM_INT);
        
        // Execute the statement
        if ($stmt_editUserInfo->execute() && $uid == $_SESSION['user_id']) {
            $_SESSION['user_email'] = $umail; // Update session email if changed
        }

        if ($this->errorState == 1) {
            return $this->errorMessages;
        } else {
            return 1;    
        }
    }
    
    

    public function searchUsers(string $input, int $includeInactive) {
        $input = cleanInput($input);

        // Replace all whitespace characters with % wildcards
        $input = preg_replace('/\s+/', '%', $input);

        $inputJoker = "%".$input."%";

        // Start building the query
        $searchQuery = 'SELECT * FROM table_users WHERE (u_name LIKE :uname OR u_email LIKE :email OR u_fname LIKE :fname OR u_lname LIKE :lname OR CONCAT(u_fname, u_lname) LIKE :fullname)';

         // Conditionally add status filter
        if (!$includeInactive) {
            $searchQuery .= ' AND u_status = 1';
        }

        // Add ORDER BY clause to sort by u_fname, then u_lname
        $searchQuery .= ' ORDER BY u_fname ASC, u_lname ASC';

        $stmt_searchUsers = $this->pdo->prepare($searchQuery);
        $stmt_searchUsers->bindParam(':uname', $inputJoker, PDO::PARAM_STR);
        $stmt_searchUsers->bindParam(':email', $inputJoker, PDO::PARAM_STR);
        $stmt_searchUsers->bindParam(':fname', $inputJoker, PDO::PARAM_STR);
        $stmt_searchUsers->bindParam(':lname', $inputJoker, PDO::PARAM_STR);
        $stmt_searchUsers->bindParam(':fullname', $inputJoker, PDO::PARAM_STR);
        $stmt_searchUsers->execute();
        $usersList = $stmt_searchUsers->fetchAll();
        
        return $usersList;
    }

    public function populateUserField(array $usersArray) {
        foreach ($usersArray as $user) {
            echo "
            <tr " . ($user['u_status'] === 0 ? "class='table-danger'" : "") . " onclick=\"window.location.href='admin-account.php?uid={$user['u_id']}';\" style=\"cursor: pointer;\">
                <td>{$user['u_fname']} {$user['u_lname']}</td>
                <td>{$user['u_name']}</td>
                <td>{$user['u_email']}</td>
            </tr>";
        }
    }

    public function getUserInfo(int $uid) {
        $stmt_selectUserData = $this->pdo->prepare('SELECT * FROM table_users WHERE u_id = :uid');
        $stmt_selectUserData->bindParam(':uid', $uid, PDO::PARAM_INT);
        $stmt_selectUserData->execute();
        $userInfo = $stmt_selectUserData->fetch();
        return $userInfo;
    }

}

?>