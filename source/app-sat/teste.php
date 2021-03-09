<?php
/*
    $output="09998_30_900013819_log.txt";
    echo $output;
    $caminho_absoluto="/var/www/html/source/app-sat/api/bash/temp/".$output;
    if(isset($caminho_absoluto) && file_exists($caminho_absoluto)){
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename="'.$output.'"');
        header('Content-Type: application/octet-stream');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($caminho_absoluto));
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Expires: 0');

        readfile($caminho_absoluto);
    }
*/
echo "<script>window.open('https://www.w3schools.com');</script>"
?>