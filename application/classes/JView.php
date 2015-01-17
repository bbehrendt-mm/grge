<?php defined('SYSPATH') OR die('No direct script access.');

class JView extends Kohana_View {

    private $use_compression = true;

    /**
     * Returns a new View object. If you do not define the "file" parameter,
     * you must call [View::set_filename].
     *
     *     $view = View::factory($file);
     *
     * @param   string  $file   view filename
     * @param   array   $data   array of values
     * @return  JView
     */
    public static function factory($file = NULL, array $data = NULL) {
        return new JView($file, $data);
    }

    public function disable_compression() {
        $this->use_compression = false;
        return $this;
    }

    public function render($file = null) {
        $buffer = parent::render($file);

        if ($this->use_compression && Kohana::$config->load('server.io.performance.output_compression'))
            $buffer = Minifier::minify($buffer);

        return $buffer;
    }
}
