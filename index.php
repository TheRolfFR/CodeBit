<?php
    require('functions.php');
    require('verifco.php');
    
    if(isset($_GET) && !empty($_GET)) {
        if(isset($_GET['pass']) && !empty($_GET['pass'])) {
            $hash = password_hash(htmlspecialchars($_GET['pass']), PASSWORD_BCRYPT);
            
            if(!file_exists('password.txt')) {
                $passfile = fopen("password.txt", "w");
                fwrite($passfile, 'pass');
                fclose($passfile);
            }
            $content = file_get_contents('password.txt');
                
            if($content == 'pass') {
                $passfile = fopen("password.txt", "w");
                fwrite($passfile, $hash);
                fclose($passfile);
                
                $_SESSION['connected'] = $hash;
                redirect('this');
            }
            
            if(password_verify(htmlspecialchars($_GET['pass']), $hash)) {
                $_SESSION['connected'] = $hash;
                redirect('this');
            }
        }
        if(isset($_GET['disconnect'])) {
            unset($_SESSION['connected']);
            redirect('this');
        }
    }
    
    $path = 'bits';
    $fandf = scandir($path);
    unset($fandf[array_search('.', $fandf, true)]);
    unset($fandf[array_search('..', $fandf, true)]);
    // var_dump($fandf);
    $retour = "";
    if(count($fandf)) {
        foreach($fandf as $forf) {
            if(is_dir($path . '/' . $forf)) {
                if(file_exists($path . '/' . $forf . '/' . $forf . '.json') && file_exists($path . '/' . $forf . '/' . $forf . '.html') && file_exists($path . '/' . $forf . '/' . $forf . '.css') && file_exists($path . '/' . $forf . '/' . $forf . '.js')) {
                    $json = json_decode(file_get_contents($path . '/' . $forf . '/' . $forf . '.json'), true);
                    $retour .= '<li><a href="editor.php?id=' . $forf . '">' . $json['title'] . '</a></li>';
                }
            }
        }
    } else {
        $retour = '<li><i>No bits created</i>';
        if(connected()) {
            $retour .= '<br><a id="create" href="editor.php">Why not create one ?</a>';
        }
        $retour .= '</li>';
    }
?>

<html>
<head>
    <meta charset="utf-8">
    <title>CodeBit of TheRolf</title>
    <link rel="stylesheet" href="base.css">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/vex-js/4.1.0/css/vex.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vex-js/4.1.0/js/vex.combined.min.js"></script>
    <script>vex.defaultOptions.className = 'vex-theme-plain'</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/vex-js/4.1.0/css/vex-theme-plain.css">
    <script src="index.js"></script>
</head>
<body class="middle">
    <div id="bgDiagonal"><div></div></div>
    <div class="width1020" id="main">
        <div id="title" class="middle">
            <h2>CODEBIT</h2><div>of TheRolf</div><span class="taille"></span>
            <? if(isset($_SESSION) && isset($_SESSION['connected'])) { ?>
                <a href="?disconnect" class="button middle">
                    <i class="fas fa-sign-out-alt"></i><span class="taille"></span>
                </a>
            <? } else { ?>
                <div id="login" class="button middle">
                    <i class="fas fa-sign-in-alt"></i><span class="taille"></span>
                </div>
            <? } ?>
        </div>
        <ul id="bits" class="middle">
            <div><?= $retour ?></div><span class="taille"></span>
        </ul>
    </div>
    <span class="taille"></span>
</body>
</html>