<?php defined('SYSPATH') OR die('No direct access allowed.');

class Tool_Npc {

    /**
     * @param           $p
     * @param           $own
     * @param           $location
     * @param array|int $satisfy
     * @param array|int $forbid
     * @param array|int $avoid
     * @param bool      $auto
     *
     * @return array
     * @throws Exception
     */
    public static function get_satisfactory_item($p, $own, $location, $satisfy = [], $forbid = [], $avoid = [], $auto = true): array
    {
        if (!$satisfy || !($own || $location)) return null;

        if (!is_array($satisfy))
            $satisfy = [$satisfy => [0, false]];
        if (!is_array($avoid))
            $avoid = [$avoid => [false, false]];
        if (!is_array($forbid))
            $forbid = [$forbid => [false, 0]];

        $ilist = Tool_Scripts::get_items(null,
            Struct_ScriptItemSource::default()
                ->take_from_player($own)
                ->take_from_location($location)
                ->take_from_others(false)
                ->use_perspective($p)
        );

        $fc = function($val, $ar) {
            [$min, $max] = $ar;
            if ($min === false) $min = -PHP_INT_MAX;
            if ($max === false) $max = PHP_INT_MAX;

            return $val >= $min && $val <= $max;
        };

        $tmp = null;
        foreach ($ilist as $item) {

            foreach ($item->simple_effects($p, $auto) as $action => $effects) {
                $avs = 0;
                $sss = 0;
                foreach ($satisfy as $stat => $arr)
                    if (!isset($effects[$stat]) || !$fc($effects[$stat], $arr))
                        continue 2;
                    else $sss += abs($effects[$stat]);

                foreach ($forbid as $stat => $arr)
                    if (isset($effects[$stat]) && $fc($effects[$stat], $arr))
                        continue 2;

                if (!$item->test_interaction($action, $p))
                    continue;

                foreach ($avoid as $stat => $arr)
                    if (isset($effects[$stat]) && $fc($effects[$stat], $arr))
                        $avs++;

                if ($tmp === null || ($sss > $tmp[2] && $avs <= $tmp[3]))
                    $tmp = [$item, $action, $sss, $avs];
            }
        }

        return $tmp ? [$tmp[0],$tmp[1]] : null;
    }

}