<?php
function kill($str = null) {
    header("HTTP/1.0 404 Not Found");
    if ($str !== null)
        echo $str;
    exit;
}

$f = str_replace($_SERVER['SCRIPT_NAME']."/", '', $_SERVER['PHP_SELF']);
$f = str_replace('..','.',$f);

preg_match('/\.(\w*?)$/',$f, $ext);

if (!isset($ext[1]) || !in_array($ext[1], ['css','js','jpg','bmp','gif','png','ico','webp','eot','svg','ttf','woff']))
    kill();

if ($ext[1] != 'css')
    $f = preg_replace('/^css\//','',$f);

if (file_exists($f)) {

    switch ($ext[1]) {
        case 'css':
            header('Content-Type: text/css');
            break;
        case 'js':
            header('Content-Type: application/javascript');
            break;
        case 'jpg':
            header('Content-Type: image/jpeg');
            break;
        case 'bmp':
            header('Content-Type: image/bmp');
            break;
        case 'gif':
            header('Content-Type: image/gif');
            break;
        case 'png':
            header('Content-Type: image/png');
            break;
        case 'ico':
            header('Content-Type: image/x-icon');
            break;
        case 'webp':
            header('Content-Type: image/webp');
            break;
        case 'eot':
            header('Content-Type: application/vnd.ms-fontobject');
            break;
        case 'svg':
            header('Content-Type: image/svg+xml');
            break;
        case 'ttf':
            header('Content-Type: application/octet-stream');
            break;
        case 'woff':
            header('Content-Type: application/font-woff');
            break;
    }

    header('Cache-Control: public, max-age=120');
    header('Content-Length: ' . filesize($f));
    readfile($f);
    exit;
} else kill("404 Not Found: $f");