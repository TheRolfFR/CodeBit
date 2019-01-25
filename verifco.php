<?php
    session_start();

    function connected() {
        if(isset($_SESSION['connected']) and !empty($_SESSION['connected']) && file_get_contents('password.txt') == htmlspecialchars($_SESSION['connected'])) {
            return true;
        }
        return false;
    }
?>