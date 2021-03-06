<?php
require('verifco.php');
require('functions.php');

$retour = "";

if(connected()) {
    if(isset($_POST) && !empty($_POST)) {
        if(isset($_POST['html']) && isset($_POST['css']) && isset($_POST['js']) && isset($_POST['json'])) {
            $array = array('html', 'css', 'js');
            
            // try decoding json
            try {
                $json = json_decode($_POST['json'], true);
            } catch(Exception $e) {
                http_response_code(400);
                echo $e;
                die();
            }
            
            if(isset($_POST['id']) && !empty($_POST['id']) && file_exists('bits/' . $id)) {
                // the folder exists
                $id = htmlspecialchars($_POST['id']);
                $retour = "done";
            } else {
                // else create one
                do {
                    $id = generateRandomString(6);
                } while(file_exists('bits/' . $id));
                
                mkdir('bits/' . $id, 0777, true);
                
                $retour = $id;
            }
            
            // fill html, css and js
            foreach($array as $ext) {
                $myfile = fopen('bits/' . $id . '/' . $id.'.'.$ext, "w");
                fwrite($myfile, $_POST[$ext]);
                fclose($myfile);
            }
            
            // prepare styles and scripts
            $scripts = explode(PHP_EOL, $json['externaljs']);
            $styles  = explode(PHP_EOL, $json['externalcss']);
        
            $scriptsrender = "";
            foreach($scripts as $s) {
                $scriptsrender .= '<script src="' . $s . '"></script>' . PHP_EOL;
            }
            
            $stylesrender = "";
            foreach($styles as $s) {
                $stylesrender .= '<link rel="stylesheet" href="' . $s . '">' . PHP_EOL;
            }
            
            //create debug.html
            $renderfile = fopen('bits/' . $id . '/debug.html', "w");
            fwrite($renderfile, '<!DOCTYPE html>
<html>
    <head>
        <title>' . $json['title'] .  '</title>
        <meta charset="utf-8">
        ' . $scriptsrender . '
        ' . $stylesrender . '
        ' . $json['stuffhead'] . '
        <script src="' . $id . '.js"></script>
        <link rel="stylesheet" href="' . $id . '.css">
    </head>
    <body>
        ' . $_POST['html'] . '
    </body>
</html>');
            
            // fill json
            $jsonfile = fopen('bits/' . $id . '/' . $id.'.json', "w");
            fwrite($jsonfile, json_encode($json));
            fclose($jsonfile);
        } else {
            http_response_code(400);
            $retour = "Invalid request : html, css, js and json needed in POST";
        }
    } else {
        http_response_code(400);
        $retour = "Invalid request : html, css, js and json needed in POST";
    }
} else {
    http_response_code(403);
    $retour = 'Not connected';
}

echo $retour;

?>