<?php
class Model_Javascript {

    private $lines = array();
    private $arguments = array();

    /**
     * @return Model_Javascript
     */
    public static function factory() {
        return new Model_Javascript();
    }

    /**
     * @param string $line
     * @return Model_Javascript
     */
    public function custom($line) {
        $this->lines[] = $line;
        return $this;
    }

    /**
     * @return Model_Javascript
     */
    public function close_qtip() {
        return $this->custom("jQuery('.qtip').hide();");
    }

    /**
     * @param string $arg_name
     * @param string $description
     * @return Model_Javascript
     */
    public function add_argument($arg_name, $description) {
        $this->arguments[$arg_name] = $description;
        return $this;
    }

    /**
     * @param string $name
     * @param bool $close
     * @return Model_Javascript
     */
    public function versa($name, $close = false) {
        return $this->custom("game.gui.morph.presets('versa', " . ($close ? 'false' : 'true') . ", '{$name}');");
    }

    /**
     * @param number $id
     * @param string $action
     * @param null|mixed[] $additional
     * @return Model_Javascript
     */
    public function use_item($id, $action, $additional = null) {
        if ($additional === null)
            return $this->custom("game.xmlhttp.command('item/use', {action: '{$action}', item: {$id}});");
        else {
            $args = array();
            foreach ($additional as $name => $value)
                if (strpos($value, '$') === 0) {
                    $var = substr($value, 1);
                    $this->add_argument($var, 'Argument required: ' . $var);
                    $args[] = "{$name}: " . $var;
                } else $args[] = "{$name}: " . json_encode($value);
            $args = implode(', ', $args);
            return $this->custom("game.xmlhttp.command('item/use', {action: '{$action}', item: {$id}, " . htmlspecialchars($args) . "});");
        }
    }

    /**
     * @return string
     */
    public function compile() {
        $tmp = array();
        foreach ($this->arguments as $var => $desc)
            $tmp[] = "var {$var}; if (({$var} = prompt('{$desc}', '')) == null) return;";
        return implode('', $tmp) . implode('', $this->lines);
    }

}