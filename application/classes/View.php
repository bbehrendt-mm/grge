<?php defined('SYSPATH') OR die('No direct script access.');

class View extends Kohana_View {

    public function render($file = null) {
        $buffer = parent::render($file);

        global $compression;
        $compression[0] += strlen($buffer);

        if (Kohana::$config->load('server.io.performance.output_compression')) {

            $pattern = '/(\/\/ ## JS COMPRESS BEGIN ## \/\/.*?\/\/ ## JS COMPRESS END ## \/\/)/s';
            while (preg_match($pattern, $buffer, $script)) {
                $buffer = preg_replace($pattern, Minifier::minify($script[0]), $buffer, 1);
            }
            $buffer = preg_replace(['/\>[^\S ]+/s','/[^\S ]+\</s','/(\s)+/s'], ['>','<','\\1'], $buffer);
        }

        $compression[1] += strlen($buffer);;
        return $buffer;
    }

}
