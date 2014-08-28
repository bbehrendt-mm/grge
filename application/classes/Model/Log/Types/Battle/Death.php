<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Battle_Death extends Model implements Interface_Message {
	
	private $name;
	private $is_zombie;
    private $special;
	
	/**
	 * Creates a death message for a combatant
	 * @param Model_Battle_Combatant $comb
	 */
	public function __construct($comb) {
		$this->is_zombie = $comb->is_zombie();
		$this->name = $comb->name();
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
		if ($this->is_zombie)
			$tmp = "<img src=\"/application/assets/icons/killz.gif\" alt=\"?\"></img> " . __($this->special ? ':zombies wurde besiegt!' : 'Die Meute :zombies wurde zerschlagen!', array(':zombies' => "<b>" . __($this->name) . "</b>"));
		else $tmp = "<img src=\"/application/assets/icons/killc.gif\" alt=\"?\"></img> " . __(':name hat es hinter sich...', array(':name' => $this->name));
		
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