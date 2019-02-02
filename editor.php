<?php
require('functions.php');
session_start();

$html = "<!-- this is a comment -->";
$css = "/* this is a comment */";
$js = "// this is a comment";

$stuffheadhidden = "";
$externalcss = "";
$externaljs = "";

$title = "Untitled bit";
$value = "Untitled";
$class = "";

$debug = false;

if(!empty($_GET)) {
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
            
            $debug = $id;
            
            try {
                $json = json_decode($json, true);
                
                if($json['title'] != "Untitled") {
                    $title = $json['title'];
                    $value = $title;
                }
                
                if(isset($json['stuffhead'])) {
                    $stuffheadhidden = $json['stuffhead'];
                }
                
                if(isset($json['externalcss'])) {
                    $externalcss = $json['externalcss'];
                }
                
                if(isset($json['externaljs'])) {
                    $externaljs = $json['externaljs'];
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
        
        <!-- Ace.js -->
        <script src="https://cloud9ide.github.io/emmet-core/emmet.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.2/ace.js" type="text/javascript" charset="utf-8"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.2/ext-language_tools.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.2/ext-emmet.js" type="text/javascript" charset="utf-8"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/push.js/1.0.9/push.min.js"></script>
        
        <!-- Vex.js -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/vex-js/4.1.0/css/vex.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/vex-js/4.1.0/js/vex.combined.min.js"></script>
        <script>vex.defaultOptions.className = 'vex-theme-plain'</script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/vex-js/4.1.0/css/vex-theme-plain.css">
        
        <!-- Personal js -->
        <script src="editor.js"></script>
        <script src="ajax.js"></script>
    </head>
    <body>
        <pre id="stuffheadhidden" class="none"><?= $stuffheadhidden ?></pre>
        <pre id="externalcsshidden" class="none"><?= $externalcss ?></pre>
        <pre id="externaljshidden" class="none"><?= $externaljs ?></pre>
        <header class="middle">
            <a href="/" id="logo">
                <img src="" alt="">
            </a><h2 class="top">
                <i class="fas fa-edit"></i><input id="title" type="text" value="<?= $value; ?>"><br>
                by TheRolf
            </h2><span class="taille"></span>
            <div id="buttons" class="middle">
                <div class="button" id="settings">
                    <i class="fas fa-gear"></i>
                </div><?php if($debug) { ?><a href="bits/<?= $debug ?>/debug.html" target="_blank" class="button">
                    <i class="fas fa-external-link-alt"></i>
                </a><?php } ?><div class="button" id="overlay">
                    <i class="far fa-window-maximize"></i>
                </div>
                <div class="button" id="save">
                    <i class="fas fa-save"></i>
                </div><a class="button" id="home" href="index.php">
                    <i class="fas fa-home"></i>
                </a><span class="taille"></span>
            </div>
        </header>
        <div id="editors" class="top <?= $class ?>">
            <div class="editor">
                <h4>HTML</h4>
                <div id="html" class="aceeditor"><?= htmlspecialchars($html) ?></div>
            </div><div class="editor">
                <h4>CSS</h4>
                <div id="css" class="aceeditor"><?= htmlspecialchars($css) ?></div>
            </div><div class="editor">
                <h4>JS</h4>
                <div id="js" class="aceeditor"><?= htmlspecialchars($js) ?></div>
            </div>
        </div>
        <iframe src="/" frameborder="0" id="preview"></iframe>
    </body>
</html>