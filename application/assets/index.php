<?php
function kill($str = null) {
    header("HTTP/1.0 404 Not Found");
    if ($str !== null)
        echo $str;
    exit;
}

function typeWrangler(&$file, &$ext) {
    preg_match('/(.*\.)(\w*?)$/',$file, $ext);

    if (count($ext) < 3) return false;
    $base = $ext[1];
    $ext = $ext[2];



    if (!in_array($ext, ['css','js','jpg','bmp','gif','png','ico','webp','eot','svg','ttf','woff']))
        return false;

    if ($ext != 'css')
        $base = preg_replace('/^css\//','',$base);

    if (in_array($ext, ['jpg','bmp','gif','png','ico']) && file_exists($base . $ext) && file_exists($base . 'webp'))
        $ext = 'webp';
    elseif (in_array($ext, ['jpg','bmp','gif','png','ico','webp']) && !file_exists($base . $ext))
        foreach (['webp','png','ico','gif','jpg','bmp'] as $new_ext)
            if (file_exists($base . $new_ext)) {
                $ext = $new_ext;
                break;
            }

    $file = $base . $ext;
    return file_exists($base . $ext);
};

$f = str_replace($_SERVER['SCRIPT_NAME']."/", '', $_SERVER['PHP_SELF']);
$f = str_replace('..','.',$f);

if (!typeWrangler($f,$ext))
    kill("Not found: $f");


switch ($ext) {
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