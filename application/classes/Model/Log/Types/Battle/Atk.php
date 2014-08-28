<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Battle_Atk extends Model implements Interface_Message {
	
	private $human_attacker;
	private $atk_name, $atk_count;
	private $def_name, $def_count;
	private $weapon;
    private $protection = null;
    private $cover = array();
    private $prot_value = 0;
	private $damage, $kills;
	private $note;
    private $spa;
    private $spd;

    /**
     * Creates an attack summary to use in battles
     * @param Model_Battle_Combatant $atk
     * @param Model_Battle_Combatant $def
     * @param Model_Battle_Weapon $weapon
     * @param int $damage
     * @param int $kills
     * @param null $note
     * @param Model_Items_Abstract_Armor $protection
     * @param array $cover
     * @param int $prot_value
     */
	public function __construct($atk, $def, $weapon, $damage, $kills, $note = null, $protection = null, $cover = array(), $prot_value = 0) {
		$this->human_attacker = !$atk->is_zombie();
		$this->atk_name = $atk->name();
		$this->atk_count = $atk->count();
        $this->spa = $atk->special();
		$this->def_name = $def->name();
		$this->def_count = $def->count();
        $this->spd = $def->special();
		
		$this->weapon = is_string($weapon) ? $weapon : get_class($weapon);
		$this->damage = $damage;
		$this->kills = $kills;
		$this->note = $note;

        $this->protection = ($protection !== null) ? get_class($protection) : null;
        $this->cover = $cover;
        $this->prot_value = $prot_value;
	}
	
	public function render_title() {
		return NULL;
	}
	
	public function is_human_attacker() {
		return $this->human_attacker;
	}
	
	public function get_name() {
		return ($this->human_attacker) ? $this->atk_name : $this->def_name;
	}
	
	public function get_damage() {
		return $this->damage;
	}
	
	public function get_kills() {
		return $this->kills;
	}
	
	public function get_energy() {
		$w = $this->weapon;
		return $w::$energy_cost;
	}
	
	public function get_ammo() {
		$w = $this->weapon;
		$r = Array();
		foreach ($w::ammo() as $a => $c)
			if ($a === 'self') $r[$w::static_icon()] = 1;
			elseif ($a === 'custom') $r[$w::custom_ammo_icon()] = 1;
			else $r[$a::static_icon()] = $c;
			
		return $r;
	}
	
	public function render_body() {
		$w = $this->weapon;
		$def_abs_count = $this->def_count + $this->kills;
		$dmg = round($this->damage);
		
		$tmp = $this->human_attacker ? "<img alt=\"?\" src=\"/application/assets/icons/citizen.gif\"></img> <b>{$this->atk_name}</b> " . __('attackiert') . " <img alt=\"?\" src=\"/application/assets/icons/zombie.gif\"></img> <b>" . ($this->spd ? __($this->def_name) : ($def_abs_count . " " . __($this->def_name))) . "</b>" : "<img alt=\"?\" src=\"/application/assets/icons/zombie.gif\"></img> <b>" . ($this->spa ? __($this->atk_name) : ($this->atk_count . " " . __($this->atk_name))) . "</b> " . (!$this->spa ? __('stürzen sich auf') : __('stürzt sich auf')) . " <img alt=\"?\" src=\"/application/assets/icons/citizen.gif\"></img> <b>{$this->def_name}</b>";
		$tmp .= "<table><tr>";
		$tmp .= "<td style=\"width: 24px;\"><img src=\"{$w::static_icon()}\" alt=\"?\" title=\"<b>" . __('Eingesetzte Waffe') . "</b> <img src='{$w::static_icon()}' alt='?' /> " . __($w::static_name()) . "\"></img></td>";
		$tmp .= "<td style=\"width: 128px;\">";
			foreach ($w::ammo() as $a => $c)
				if ($a === 'self') $tmp .= "<img src=\"{$w::static_icon()}\" alt=\"?\" title=\"<b>" . __('Waffe zerstört!') . "</b>" . __('Diese Waffe hat ihren Einsatz nicht überlebt...') . "\"></img>";
				elseif ($a === 'custom') $tmp .= "<img src=\"{$w::custom_ammo_icon()}\" alt=\"?\" title=\"<b>" . __('Verbrauchte Ressourcen') . "</b>" . __('Durch den Einsatz der Waffe wurden einige Ressourcen verbraucht.') . "\"></img>";
				else for ($i = 0; $i < $c; $i++) $tmp .= "<img src=\"{$a::static_icon()}\" alt=\"?\" title=\"<b>" . __('Verbrauchter Gegenstand') . "</b>" . __('Durch den Einsatz dieser Waffe wurde ein Gegenstand verbraucht: ') . "<img src='{$a::static_icon()}' alt='?' /> " . __($a::static_name()) . "\"></img>";
		$tmp .= "</td>";
		$tmp .= ($w::$energy_cost > 0) ?
			"<td style=\"width: 64px;\"><img src=\"/application/assets/icons/status_energy.gif\" alt=\"?\" title=\"<b>" . __('Verbrauchte Energie') . "</b>" . __('Der Einsatz dieser Waffe hat Energie verbraucht.') . "\"></img> {$w::$energy_cost}</td>" :
			"<td style=\"width: 64px;\"></td>";
		$tmp .= "<td style=\"width: 32px;\"><img src=\"/application/assets/icons/damage.gif\" alt=\"?\" title=\"<b>" . __('Schaden') . "</b>" . ($this->human_attacker ? __('Dieser Angriff hat einiges an Schaden bei den Zombies angerichtet!') : __('Die Zombies haben einiges an Schaden angerichtet!')) . "\"></img> {$dmg}</td>";
		
		if ($this->kills > 0)
			$tmp .= $this->human_attacker ?
				"<td style=\"width: 32px;\"><img src=\"/application/assets/icons/killz.gif\" alt=\"?\" title=\"<b>" . __('Vernichtete Zombies') . "</b>" . __('Die Reihen der Zombies wurden etwas gelichtet.') . "\"></img> {$this->kills}</td>" :
				"<td style=\"width: 32px;\"><img src=\"/application/assets/icons/killc.gif\" alt=\"?\" title=\"<b>" . __('Oh nein!') . "</b>" . __('Und wieder hat ein Mensch ein grausames Ende gefunden...') . "\"></img> <b>{$this->def_name}</b></td>";
			else $tmp .= "<td style=\"width: 128px;\"></td>";

        if ($this->prot_value > 0) {
            $cls = $this->protection;
            $p = array();
            foreach ($this->cover as $item)
                $p[] = $item::static_icon();
            if ($this->protection)
                $pp = $cls::static_icon();
            else $pp = null;
            $tmp .= "<td style=\"width: 128px;\">";
            foreach ($p as $path)
                $tmp .= "<img src=\"" . $path  . "\" alt=\"?\" title=\"<b>" . __('Schutz') . "</b>" . __('Dieser Gegenstand hat Schaden absorbiert.') . "\"></img>";
            if ($pp)
                $tmp .= "<img src=\"" . $pp  . "\" alt=\"?\" title=\"<b>" . __('Rüstung') . "</b>" . __('Die Rüstung hat zusätzlichen Schaden abgewendet.') . "\"></img>";
            $tmp .= " {$this->prot_value}</td>";
        }
		if ($this->note) $tmp .= "<td>" . __($this->note) . "</td></tr></table>";
		else $tmp .= "<td></td></tr></table>";
		
		return $tmp;
	}
	
	public function timecode() {
		return NULL;
	}

    /**
     * @param Interface_Message $new
     * @return bool
     */
    public function merge($new) {
        return false;
    }
}