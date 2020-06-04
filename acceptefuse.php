<?php
    if (strripos($_POST['class'], 'success')){
        echo 'success';
    };
    if ($_POST['class']== 'danger'){
        $text = <<<EOD
{$_POST['text']}
EOD;
    echo '{"class":"danger","text":"'.$text.'"}';
    };
?>