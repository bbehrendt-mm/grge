<?php defined('SYSPATH') OR die('No direct script access.');

class View extends Kohana_View {

    public function render($file = null) {
        // Invoke normal rendering function to get output
        $buffer = parent::render($file);

        // Compression data
        global $compression;
        $compression[0] += strlen($buffer);

        // Check if compression is turned on
        if (Kohana::$config->load('server.io.performance.output_compression')) {

            // Find and compress javascript
            $pattern = '/(\/\/ ## JS COMPRESS BEGIN ## \/\/(.*?)\/\/ ## JS COMPRESS END ## \/\/)/su';

            while (preg_match($pattern, $buffer, $script))
                $buffer = preg_replace($pattern, Minifier::minify($script[2]), $buffer, 1);

            // Compress HTML
            $buffer = preg_replace(['/\>[^\S ]+/su','/[^\S ]+\</su','/(\s)+/su'], ['>','<','\\1'], $buffer);
        }

        $compression[1] += strlen($buffer);
        return $buffer;
    }

}
