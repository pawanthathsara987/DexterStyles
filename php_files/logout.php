<?php
        session_start();

        // Unset all session variables
        $_SESSION = array();

        // Destroy the session
        session_destroy();

        // Optional: redirect to login or home page
        header("Location: ./../home.php");
        exit;
?>