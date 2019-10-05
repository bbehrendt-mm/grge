<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Strangewood_Entry extends Model_Places_Abstract_Place {
	
	protected static $location_name = 'Strangewood';
	protected static $description = 'Du befindest dich auf einem kleinen Feldweg, der in einen dicht bewachsenen und nahezu undurchdringlichen Wald führt. Komisch, du hättest schwören konnen dass es gestern hier nur Wüste gab ...';
    protected static $icon = 'swood';

    public function uin($uin = NULL) {
        if ($uin === NULL) return parent::uin();
        else $t = parent::uin($uin);


        $slid = Globals::CurrentGameF()->register_map("submap_swood_{$uin}", 'swood', 'halloween');
        if ($slid) {
            $this->register_doorway($slid);
            Globals::CurrentGameF()->locationF($slid)->register_doorway($uin);
        }


        return $t;
    }
}	