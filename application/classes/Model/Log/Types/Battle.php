<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Battle extends Model implements Interface_Message {
	
	private $title;
	private $log;
	private $common = null;
	
	private $var = Array();
	private $trv = Array();
	
	private $timecode;
	
	/**
	 * Creates a simple text message
	 * @param String $title Short message title
	 * @param String $head Message headline; will be used as title if no headline is provided
	 * @param String $body Message body
	 * @param Array $effects Array in format [["icon","value","class"]]
	 */
	public function __construct($title, $log, $variables = Array(), $translateables = Array()) {
		$this->title = $title;
		$this->log = $log;
		
		$this->var = $variables;
		$this->trv = $translateables;
		
		$this->timecode = time();
	}
	
	/**
	 * Translates a text using translateables and variables
	 * @param unknown $s
	 */
	private function translate($s) {
		if ($this->var === true) return $s;
		$tmp = $this->var;
		foreach ($this->trv as $key => $value)
			$tmp[$key] = __($value);
		return __($s, $tmp);
	}
	
	private function create_common_data() {
		$this->common = Array();
		
		foreach ($this->log as $round => $entries) foreach ($entries as $entrie)
		switch (get_class($entrie)) {
			case "Model_Log_Types_Battle_Enter":
				if ($entrie->is_human_attacker()) $this->common[$entrie->get_name()] = Array("death" => false, "damage" => 0, "kills" => 0, "ammo" => Array(), "injuries" => Array(), "energy" => 0);
				break;
			case "Model_Log_Types_Battle_Death":
				if ($entrie->is_human_attacker()) $this->common[$entrie->get_name()]["death"] = true;
				break;		
			case "Model_Log_Types_Battle_Injury":
				$this->common[$entrie->get_name()]["injuries"][] = $entrie->get_icon();
				break;
			case "Model_Log_Types_Battle_Atk":
				if ($entrie->is_human_attacker()) {
					$this->common[$entrie->get_name()]["kills"] += $entrie->get_kills();
					$this->common[$entrie->get_name()]["energy"] += $entrie->get_energy();
					foreach ($entrie->get_ammo() as $icon => $count) 
						if (!isset($this->common[$entrie->get_name()]["ammo"][$icon])) $this->common[$entrie->get_name()]["ammo"][$icon] = $count;
						else $this->common[$entrie->get_name()]["ammo"][$icon] += $count;
				} else $this->common[$entrie->get_name()]["damage"] += $entrie->get_damage();
				break;		
		}
	}
	
	public function render_title() {
		return $this->title ? $this->translate($this->title) : __('Gefecht!');
	}
	
	public function render_body() {
		if ($this->common === null) $this->create_common_data(); 
		
		$tmp = '<ul class="log_battle">';
		$tmp .= '<li>' . __('Zusammenfassung') . '<ul>';
		
		foreach ($this->common as $player => $data) {
			$damage = round($data["damage"]);
			
			$tmp .= "<table><tr>";
			
			$tmp .= "<td style=\"width: 176px;\"><img alt=\"?\" src=\"/application/assets/icons/" . ($data["death"] ? 'killc' : 'citizen') . ".gif\"></img><b>{$player}</b></td>";
			$tmp .= "<td style=\"width: 48px;\" title=\"" . __('Energie, die dieser Spieler im Kampf verbraucht hat.') . "\"><img alt=\"?\" src=\"/application/assets/icons/status_energy.gif\"></img>{$data["energy"]}</td>";
			$tmp .= "<td style=\"width: 48px;\" title=\"" . __('Schaden, den dieser Spieler im Kampf erlitten hat.') . "\"><img alt=\"?\" src=\"/application/assets/icons/damage.gif\"></img>{$damage}</td>";
			$tmp .= "<td style=\"width: 48px;\" title=\"" . __('Anzahl der Zombies dieser Spieler erledigt hat.') . "\"><img alt=\"?\" src=\"/application/assets/icons/killz.gif\"></img>{$data["kills"]}</td>";
			$tmp .= "<td style=\"width: 96px;\">";
			foreach ($data["injuries"] as $icon) $tmp .= "<img alt=\"?\" src=\"{$icon}\" title=\"" . __('Verletzungen die dieser Spieler erlitten hat.') . "\"></img>";
			$tmp .= "</td>";
			$tmp .= "<td>";
			if ($data["ammo"]) foreach ($data["ammo"] as $icon => $count) $tmp .= "<img alt=\"?\" src=\"{$icon}\" title=\"" . __('Gegenstände, die dieser Spieler im Kampf verbraucht hat.') . "\"></img>{$count} ";			
			$tmp .= "</td>";
			
			$tmp .= "</tr></table>";
		}
		
		$tmp .= "</ul></li>";
		foreach ($this->log as $round => $entries) {
			$tmp .= '<li>' . __('Runde') . " {$round}<ul>";
			foreach ($entries as $entrie) {
				$txt = $entrie->render_body();
				$tmp .= "<li>{$txt}</li>";
			}
			$tmp .= "</ul></li>";
		}
		$tmp .= "</ul>";
		
		return ($tmp == '') ? null : $tmp;
	}
	
	public function timecode() {
		return $this->timecode;
	}

    /**
     * @param Interface_Message $new
     * @return bool
     */
    public function merge($new) {
        return false;
    }
}