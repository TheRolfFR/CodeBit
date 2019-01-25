<?php
    session_start();

    function connected() {
        if(isset($_SESSION['connected']) and !empty($_SESSION['connected'])) {
            return true;
        }
        return false;
    }
?>