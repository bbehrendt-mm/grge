<?php

class Syslogd {

    private static $lines = array();
    private static $level = 0;
    private static $line = 0;
    private static $listening = false;

    public static function quit() {
        if (static::$dumpfile)
            file_put_contents(static::$dumpfile, static::get());
    }

    public static function flush($f) {
        file_put_contents($f, static::get());
    }

    public static function get() {
        if (count(static::$lines) == 0)
            return null;
        else return implode("\n", static::$lines);
    }

    public static function in() {
        static::$level++;
    }

    public static function out() {
        static::$level--;
    }

    public static function sprint($s) {
        if (!isset(static::$lines[static::$line]))
            static::$lines[static::$line] = str_repeat("\t", static::$level);
        static::$lines[static::$line] .= $s;
    }

    public static function sprintln($s) {
        static::sprint($s);
        static::$line++;
    }

    public static function ln() {
        static::$line++;
    }

    public static function listen_start() {
        if (static::$listening) return;
        static::$listening = true;
        ob_start();
    }

    public static function listen_stop() {
        if (!static::$listening) return;
        static::$listening = false;
        $s = ob_get_clean();
        foreach (explode("\n", $s) as $line)
            static::sprintln($line);
    }

    public static function dump($var) {

        if (func_num_args() == 0)
            return;
        elseif (func_num_args() > 1) {
            foreach (func_get_args() as $arg)
                static::dump($arg);
            return;
        }

        if (static::$listening)
            var_dump($var);
        else {
            static::listen_start();
            var_dump($var);
            static::listen_stop();
        }
    }
}