<?php
require('functions.php');
session_start();

$html = "&lt!-- this is a comment --&gt";
$css = "/* this is a comment */";
$js = "// this is a comment";

$title = "Untitled bit";
$value = "Untitled";
$class = "";

if(!empty($_GET)) {
    if(isset($_GET['connect']) && !empty($_GET['connect'])) {
        $_SESSION['connected'] = true;
        redirect('this');
        die();
    }
    if(isset($_GET['disconnect']) && !empty($_GET['disconnect'])) {
        $_SESSION['connected'] = false;
        redirect('this');
        die();
    }
    
    if(isset($_GET['id']) && !empty($_GET['id'])) {
        $id = htmlspecialchars($_GET['id']);
        if(file_exists('bits/'. $id)) {
            $file = file_get_contents('bits/' . $id . '/' . $id . '.html');
            $html = ($file) ? $file : "";
            
            $file = file_get_contents('bits/' . $id . '/' . $id . '.css');
            $css = ($file) ? $file : "";
            
            $file = file_get_contents('bits/' . $id . '/' . $id . '.js');
            $js = ($file) ? $file : "";
            
            $file = file_get_contents('bits/' . $id . '/' . $id . '.json');
            $json = ($file) ? $file : "";
            
            try {
                $json = json_decode($json, true);
                
                if($json['title'] != "Untitled") {
                    $title = $json['title'];
                    $value = $title;
                }
                
                $class = $json['editorconfig'];
            } catch(Exception $e) {
            }
        }
    }
}

?>
<html>
    <head>
        <link rel="stylesheet" href="editor.css">
        <link rel="stylesheet" href="base.css">
        <meta charset="utf-8">
        <title><?= $title ?></title>
        <script src="https://cloud9ide.github.io/emmet-core/emmet.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.2/ace.js" type="text/javascript" charset="utf-8"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.2/ext-language_tools.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.2/ext-emmet.js" type="text/javascript" charset="utf-8"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/push.js/1.0.9/push.min.js"></script>
        <script src="editor.js"></script>
        <script src="ajax.js"></script>
    </head>
    <body>
        <header class="middle">
            <a href="/" id="logo">
                <img src="" alt="">
            </a><h2 class="top">
                <i class="fas fa-edit"></i><input id="title" type="text" value="<?= $value; ?>"><br>
                by TheRolf
            </h2><span class="taille"></span>
            <div id="buttons" class="middle">
                <div id="overlay">
                    <i class="far fa-window-maximize"></i>
                </div>
                <div id="save">
                    <i class="fas fa-save"></i>
                </div><div id="home">
                    <i class="fas fa-home"></i>
                </div><span class="taille"></span>
            </div>
        </header>
        <div id="editors" class="top <?= $class ?>">
            <div class="editor">
                <h4>HTML</h4>
                <div id="html" class="aceeditor"><?= $html ?></div>
            </div><div class="editor">
                <h4>CSS</h4>
                <div id="css" class="aceeditor"><?= $css ?></div>
            </div><div class="editor">
                <h4>JS</h4>
                <div id="js" class="aceeditor"><?= $js ?></div>
            </div>
        </div>
        <iframe src="/" frameborder="0" id="preview"></iframe>
    </body>
</html>