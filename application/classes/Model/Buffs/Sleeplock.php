<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Sleeplock extends Model_Buffs_Abstract_Fragile {
	
	protected static $name = 'Schlafen';
	protected static $desc = 'Du hast es dir gemütlich gemacht und ruhst dich aus. Daher kannst du im Moment keine Aktion durchführen und diesen Ort nicht verlassen.';
	protected static $visible = false;
	
	protected static $abortable = true;
	
	protected function action_on_complete() {}
	
	public function cancel() {
        if ($buff = $this->assoc_player->get_status()->retrieve('sleep_cozy'))
			$buff->unbuff();

        if ($this->associated_to_player())
            $this->assoc_player->log()->add('Nach einem ausgiebigen Nickerchen fühlst du dich der harschen Welt da draußen wieder gewachsen. Los gehts!');
        parent::cancel();
	}
}