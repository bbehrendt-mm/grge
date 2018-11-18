<?php defined('SYSPATH') OR die('No direct access allowed.');

class Tool_Modes {

    public static function get_job_by_id($jobid): \Kohana_Config_Group
    {
        return Kohana::$config->load("modes.jobs.$jobid.meta.name");
    }

    public static function get_mode_by_id($bid): \Kohana_Config_Group
    {
        return Kohana::$config->load("modes.modes.$bid.meta.name");
    }

    public static function config_get_modes($type = null): array
    {
        $db = Kohana::$config->load('modes.modes');
        $ret = array();
        foreach ($db as $id => $data) {
            if (empty($data['meta']) || ($type !== null && is_string($type) && $data['type'] !== $type) || ($type !== null && is_array($type) && !in_array(
                        $data['type'], $type, true
                    )))
                continue;

            $ret[$id] = $data['meta'];
        }
        return $ret;
    }

}