<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Strangewood_Entry extends Model_Places_Abstract_Place {
	
	protected static $location_name = 'Strangewood';
	protected static $description = 'Du befindest dich auf einem kleinen Feldweg, der in einen dicht bewachsenen und nahezu undurchdringlichen Wald führt. Komisch, du hättest schwören konnen dass es gestern hier nur Wüste gab ...';
    protected static $icon = 'swood';

    protected $initialized = false;

    public function enter($pid = null, $type = Interface_Tickable::IT_TYPE_PLAYER): bool
    {
        if (!$this->initialized) {
            $uin = $this->uin();
            $this->initialized = true;
            $slid = Globals::CurrentGameF()->register_map("submap_swood_{$uin}", 'swood', 'halloween');
            Globals::CurrentGameF()->get_initialized_event(Model_Events_Halloween::get_key())->register_event_map_id("submap_swood_{$uin}");
            if ($slid) {
                $this->register_doorway($slid);
                Globals::CurrentGameF()->locationF($slid)->register_doorway($uin);
            }
        }

        return parent::enter($pid, $type);
    }

    public function pretick(): void
    {
        if (!Globals::CurrentGameF()->get_initialized_event(Model_Events_Halloween::get_key()))
            Tool_Scripts::combat([Tool_Scripts::at_location($this->uin()), [Model_Combat_Zombies_Behemoth::factory()->strength(100,100,1)]], false, 15, $this, 'Ein Überraschungsangriff!');
    }
}	