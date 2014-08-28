<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Battle_Enter extends Model implements Interface_Message {
	
	private $name;
	private $count;
	private $distance;
	private $is_zombie;
    private $special;
	
	/**
	 * Creates an entry message for a combatant
	 * @param Model_Battle_Combatant $comb
	 */
	public function __construct($comb) {
		$this->is_zombie = $comb->is_zombie();
		$this->name = $comb->name();
		$this->count = $comb->count();
		$this->distance = $comb->distance();
        $this->special = $comb->special();
	}
	
	public function render_title() {
		return NULL;
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function is_human_attacker() {
		return !$this->is_zombie;
	}
	
	public function render_body() {
		if ($this->is_zombie) {
			if		($this->distance < 5)	$d = "in einer dunklen Ecke";
			elseif	($this->distance < 10)	$d = "in unmittelbarer Nähe";
			elseif	($this->distance < 25)	$d = "in der Umgebung";
			elseif	($this->distance < 50)	$d = "in einiger Entfernung";
			elseif	($this->distance < 75)	$d = "weit entfernt";
			else							$d = "am Horizont";
			$tmp = "<img alt=\"?\" src=\"/application/assets/icons/arrowr_r.gif\"></img><img alt=\"?\" src=\"/application/assets/icons/zombie.gif\"></img> " . ($this->special ? __(':zombies erscheint :distance!', array(':zombies' => "<b>" . __($this->name) . '</b>', ':distance' => __($d))) : __(':zombies tauchen :distance auf.', array(':zombies' => "<b>{$this->count} " . __($this->name) . '</b>', ':distance' => __($d))));
		} else $tmp = "<img alt=\"?\" src=\"/application/assets/icons/arrow_r.gif\"></img><img alt=\"?\" src=\"/application/assets/icons/citizen.gif\"></img> " . __(':name tritt dem Kampfgeschehen bei!', array(':name' => $this->name));
		
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