<?php
include_once 'functions.php';

class Utility {

    private $pdo;
    private $errorMessages = [];
    private $errorState = 0;


    function __construct($pdo) {
        $this->pdo = $pdo;
    }

    

    
}

?>