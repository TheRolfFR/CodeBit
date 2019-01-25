<?php
function redirect($location, $extension) {
    if(!empty($location)) {
        switch ($location) {
            case 'this':
                $redirect = '<script>window.location.search=';
                $redirect .= (isset($extension)) ? $extension : '""';
                $redirect .= '</script>';
                echo $redirect;
                break;
            case 'editor':
                header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . 'editor.php');
                break;
        }
        exit();
    }
    return;
}

function generateRandomString($length = 10) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
?>