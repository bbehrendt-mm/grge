<?php

class Syslogd {

    private static $lines = array();
    private static $level = 0;
    private static $line = 0;
    private static $listening = false;

    public static function flush($f): void {
        file_put_contents($f, static::get());
    }

    public static function get(): ?string {
        if (count(static::$lines) === 0)
            return null;
        else return implode("\n", static::$lines);
    }

    public static function in(): void {
        static::$level++;
    }

    public static function out(): void {
        static::$level--;
    }

    public static function sprint($s): void {
        if (!isset(static::$lines[static::$line]))
            static::$lines[static::$line] = str_repeat("\t", static::$level);
        static::$lines[static::$line] .= $s;
    }

    public static function sprintln($s): void {
        static::sprint($s);
        static::$line++;
    }

    public static function ln(): void {
        static::$line++;
    }

    public static function listen_start(): void {
        if (static::$listening) return;
        static::$listening = true;
        ob_start();
    }

    public static function listen_stop(): void {
        if (!static::$listening) return;
        static::$listening = false;
        $s = ob_get_clean();
        foreach (explode("\n", $s) as $line)
            static::sprintln($line);
    }

    public static function dump($var): void {

        if (func_num_args() === 0)
            return;
        elseif (func_num_args() > 1) {
            foreach (func_get_args() as $arg)
                static::dump($arg);
            return;
        }

        if (static::$listening)
            /** @noinspection ForgottenDebugOutputInspection */
            var_dump($var);
        else {
            static::listen_start();
            /** @noinspection ForgottenDebugOutputInspection */
            var_dump($var);
            static::listen_stop();
        }
    }
}