<?php defined('SYSPATH') OR die('No direct access allowed.');

class Tool_Gamemodes {

    private static $usp_cache = array('job' => array(), 'mode' => array());

    private static function get_sp_mode($mode): int
    {
        $accum = 0;
        foreach (explode(',', $mode) as $imode) {
            if (!isset(static::$usp_cache['mode'][$imode]))
                static::$usp_cache['mode'][$imode] = (int)Globals::CurrentUserF()->soulpoints(null, null, $imode);
            $accum += static::$usp_cache['mode'][$imode];
        }

        return $accum;
    }

    private static function get_sp_job($job): int
    {
        $accum = 0;
        foreach (explode(',', $job) as $ijob) {
            if (!isset(static::$usp_cache['job'][$ijob]))
                static::$usp_cache['job'][$ijob] = (int)Globals::CurrentUserF()->soulpoints(null, $ijob, null);
            $accum += static::$usp_cache['job'][$ijob];
        }
        return $accum;
    }

    /**
     * @param $mode
     *
     * @return array|object
     * @throws Kohana_Exception
     */
    public static function compile_startup_mode($mode) {
        $ret = (array)Kohana::$config->load('modes.modes.' . $mode . '.setup');
        if (!$ret) return array();
        if (!$ret['inherit']) return $ret;

        $inherit = array();
        foreach ($ret['inherit'] as $from) $inherit = array_replace_recursive($inherit, static::compile_startup_mode($from));
        return array_replace_recursive($inherit, $ret);
    }

    public static function compile_startup_job($job): callable
    {
        $ret = (array)Kohana::$config->load('modes.jobs.' . $job . '.setup');
        if (!$ret) return function($mode, $level) {};
        if (!isset($ret['f'])) $ret['f'] = function($mode, $level) {};
        if (!$ret['inherit']) return $ret['f'];

        $func = $ret['f'] ?? function ($mode, $level) {
            };
        $inherit = array();
        foreach ($ret['inherit'] as $from) $inherit[] = static::compile_startup_job($from);
        return function($mode, $level) use ($inherit, $func) {
            foreach ($inherit as $f) $f($mode, $level);
            $func($mode, $level);
        };
    }

    private static function compile_requirements(&$rqdb): bool
    {
        $ret = true;

        if (Kohana::$config->load('build.version.stage') < 3 || Tool_Events::is_october_midness())
            return true;

        foreach ($rqdb['mode'] as $key => &$requirement) {
            if (($current = static::get_sp_mode($key)) < $requirement)
                $ret = false;
            $requirement = array($current, $requirement);
        }
        unset($requirement);
        foreach ($rqdb['job'] as $key => &$requirement2) {
            if (($current = static::get_sp_job($key)) < $requirement2)
                $ret = false;
            $requirement2 = array($current, $requirement2);
        }
        unset($requirement2);
        foreach ($rqdb['ext'] as &$requirement3) {
            if (!($tmp = $requirement3()))
                $ret = false;
            $requirement3 = $tmp;
        }
        unset($requirement3);

        return $ret;
    }

    public static function get_singleplayer_modes(): array
    {
        $ret = (array)Kohana::$config->load('modes');

        $r = [];

        //Modes
        foreach ($ret['modes'] as $id => $mode)
            if ($mode['type'] === 'single')
                $r[] = $id;

        return $r;
    }

    public static function get_multiplayer_modes(): array
    {
        $ret = (array)Kohana::$config->load('modes');

        $r = [];

        //Modes
        foreach ($ret['modes'] as $id => $mode)
            if ($mode['type'] === 'multi_auto' || $mode['type']
                === 'multi_custom' || $mode['type'] === 'special_multi_auto')
                $r[] = $id;

        return $r;
    }

    public static function is_special_mode($mode, $type = null): bool
    {
        if ($type === null) {
            $ret = (array)Kohana::$config->load('modes');
            if (!isset($ret['modes'][$mode])) return false;
            $type = $ret['modes'][$mode]['type'];
        }

        return strpos($type, 'special') === 0;
    }

    public static function compile_mode_database($short = false, $custom_mode_callback = null): array
    {
        $ret = (array)Kohana::$config->load('modes');
        $joblist = [];

        if (!$custom_mode_callback || !is_callable($custom_mode_callback))
            $custom_mode_callback = function($a,$b) {return true;};

        unset($ret['modes']['default']);

        //Modes
        foreach ($ret['modes'] as $mid => &$mode) {
            if (!$custom_mode_callback($mid, $mode)) {
                unset($ret['modes'][$mid]);
                continue;
            }


            $mode['locked'] = !static::compile_requirements($mode['requirements']);
            foreach ($mode['jobs'] as $jid)
                $joblist[$jid] = true;

            if ($short) {
                unset($mode['setup']);
            }
        }
        unset($mode);

        if ($short)
            foreach ($ret['modes'] as $mid => $m)
                if ($m['type'] === 'none')
                    unset($ret['modes'][$mid]);

        //Jobs
        foreach ($ret['jobs'] as $jid => &$job) {
            if (!isset($joblist[$jid]) || !$joblist[$jid]) {
                unset($ret['jobs'][$jid]);
                continue;
            }

            if ($short)
                unset($job['setup']);

            $job['level'] = 0;
            $job['locked'] = !static::compile_requirements($job['requirements']);
            $job['points'] = static::get_sp_job($jid);
            if (Kohana::$config->load('build.version.stage') < 3 || Tool_Events::is_october_midness()) {
                $job['level'] = count($job['levels']) + 1;
                $job['next_level'] = null;
            } elseif (!$job['locked']) {
                $c = static::get_sp_job($jid);
                while ($job['level'] < count($job['levels']))
                    if ($job['levels'][$job['level']] <= $c)
                        $job['level']++;
                    else break;
                $job['level']++;
                $job['next_level'] = $job['levels'][$job['level'] - 1] ?? null;
            }
        }

        return $ret;
    }

    public static function get_job_by_id($jobid): ?string
    {
        return Kohana::$config->load("modes.jobs.$jobid.meta.name");
    }

    public static function get_board_by_id($bid): ?string
    {
        return Kohana::$config->load("modes.modes.$bid.meta.name");
    }

    /**
     * @return Model_Store_Interface[]
     * @throws ReflectionException
     */
    public static function get_store_classes(): array
    {
        $accum = [];
        foreach (scandir(APPPATH . 'classes/Model/Store/', SCANDIR_SORT_ASCENDING) as $filename) {
            if (substr($filename,-4) !== '.php') continue;
            $filename = 'Model_Store_' . substr($filename,0,-4);
            if (!class_exists($filename)) continue;

            $reflection = new ReflectionClass($filename);
            if ($reflection->isAbstract()) continue;

            /** @var Model_Store_Interface $filename */
            $accum[] = $filename;
        }
        return $accum;
    }
}