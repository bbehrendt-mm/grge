<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log extends Model {
		
	const ML_Flag_Alert = 1;
	const ML_Flag_NoLog = 2;
	const ML_Flag_Important = 4;
	const ML_Flag_Tickbased = 8;
	
	private $logstore = Array();
	
	public function __sleep() {
			
		foreach ($this->logstore as $id => $data)
			if ($data->notification && $data->read) unset($this->logstore[$id]);
		
		$i = 0;
		while (count($this->logstore) > 55)
		{
			unset($this->logstore[$i]);
			$i++;
		}
		
		$this->logstore = array_values($this->logstore);

		return array('logstore');
	}
	
	public function add($text, $timestamp, $flags = 0) {
		//Create log entry and fill it with data
		$entry = new stdClass;
		$entry->text = $text;
		$entry->timestamp = $timestamp;
		$entry->flag = $flags;
		
		//Append log entry to log
		$this->logstore[] = $entry;
	}	
	
	public function get($limit = 50) {
		$ret = array();
		$i = 0;
		
		//Collect entries from log and return them
		while ($i<min(50, count($this->logstore))) $ret[] =& $this->logstore[count($this->logstore)-(++$i)];
		return $ret;
	} 
	
	public function plain($headline, $body, $effects = NULL, $class = 'default', $is_notification = false, $timestamp = NULL) {
		global $game, $player;
		
		$ticked = ($timestamp === NULL);
		if ($timestamp === NULL) $timestamp = $game->now();
		elseif ($timestamp === true) $timestamp = time();
		
		//Create log entry and fill it with data
		$entry = new stdClass;
		$entry->head = $headline;
		$entry->body = $body;
		$entry->timestamp = $timestamp;
		$entry->notification = $is_notification;
		$entry->flag = 0;
		$entry->class = $class;
		$entry->read = false;
		
		//Append log entry to log
		$this->logstore[] = $entry;
	}
	
	public function found_item(&$item, $taken = true, $location = NULL) {
		global $game, $player;
			
		$msg_skel = '<b>%1$s</b>%2$s<br /><br />%3$s<br />%4$s';
		$btext = 'Du hast einen Gegenstand entdeckt!';
		$ltext = 'Ort: %1$s';
		$ibody = 'Nach einer erschöpfenden Suche entdeckst du ein/eine/einen %1$s am Boden.';
		$tbody = $taken ? 'Du steckst es/ihn/sie ein und führst deine Suche fort.' : 'Du legst es/ihn/sie auf den Boden und führst deine Suche fort.';
			
		$idb = "<span class=\"value\"><img src=\"{$item->icon()}\" alt=\"?\"></img>{$item->name()}</span>";		
		$lmg = ($location === NULL) ? $player->location()->name() : $location->name();
			
		$headline = sprintf('%1$s gefunden!', $idb);
		$body = sprintf($msg_skel, $btext, sprintf($ltext, $lmg), sprintf($ibody, $idb), $tbody);
		
		$this->plain($headline, $body, NULL, 'item');		
	}	
	
	public function found_building(&$location) {
		global $game, $player;
			
		$msg_skel = '<b>%1$s</b><br />%2$s';
		$btext = 'Du hast eine Ruine entdeckt!';
		$ibody = 'Du siehst einen Schatten am Horizon. Als du näher kommst erkennst du, dass es sich um ein/eine/einen %1$s handelt. Hurra!';
			
		$idb = "<span class=\"value\">{$location->name()}</span>";		
			
		$headline = sprintf('%1$s entdeckt!', $idb);
		$body = sprintf($msg_skel, $btext, sprintf($ibody, $idb));
		
		$this->plain($headline, $body, NULL, 'location');		
	}	

	public function ambient($head, $innerhead, $body, $notify = false) {
		$msg_skel = '<b>%1$s</b><br />%2$s';
		
		$body = sprintf($msg_skel, $innerhead, $body);		
		
		$this->plain($head, $body, NULL, 'ambient');
		if ($notify) $this->notify($body);
	}
	
	public function ambient_nano($body, $notify = false) {		
		$this->plain($body, NULL, NULL, 'ambient');
		if ($notify) $this->notify($body);
	}

	public function notify($text, $effects = NULL, $class = 'default') {
		$this->plain('', $text, $effects, $class, true, true);
	}	

	public function battle($blog) {
		global $game, $player;
			
		if ($blog->generic->distance > 75) $dtext = 'am Horizont';
		elseif ($blog->generic->distance > 50)	$dtext = 'weit entfernt';
		elseif ($blog->generic->distance > 30)	$dtext = 'in einiger Entfernung';
		elseif ($blog->generic->distance > 20)	$dtext = 'in der Nähe';
		elseif ($blog->generic->distance > 10)	$dtext = 'kurz vor dir';
		elseif ($blog->generic->distance >  0)	$dtext = 'direkt vor dir';
		else									$dtext = 'überraschend und ohne Vorwarnung direkt vor dir';
							
		$headline = sprintf('Zombieangriff! ' . (($blog->generic->zombies == 1) ? '%1$d Zombie taucht' : '%1$d Zombies tauchen') . ' %2$s auf!', $blog->generic->zombies, $dtext);
		
		if ($blog->generic->escape) return ($game->config('zombies.cowardly')) ? $this->plain($headline, '<b>' . (($blog->generic->zombies == 1) ? 'Der Zombie ist' : 'Die Zombies sind') . ' entkommen</b>So schnell hast du ' . (($blog->generic->zombies == 1) ? 'einen Zombie' : 'Zombies') . ' noch nie laufen sehen. ' . (($blog->generic->zombies == 1) ? 'So ein Feigling' : 'Feiglinge') . '...', NULL, 'zombie') : $this->plain($headline, '<b>Du bist entkommen</b>Zum Glück konntest du verschwinden, bevor dich ' . (($blog->generic->zombies == 1) ? 'der Zombie bemerkt hat' : 'die Zombies bemerkt haben') . '.', NULL, 'zombie');
		
		$distance = array();
		if (!empty($blog->distance))
		{
		$distance[] = '<b>' . (($blog->generic->zombies == 1) ? 'Der Zombie stürmt' : 'Die Zombies stürmen') . ' auf dich zu!</b>';
		foreach ($blog->distance->entries as $entry )
			$distance[] = "<span class=\"value\"><img src=\"{$entry->item->icon()}\" alt=\"?\"></img>{$entry->item->name()}</span>: $entry->description";
		} else $distance[] = '<b>Du hast keine Chance, aus der Distanz anzugreifen!</b>';
		
		if (empty($blog->melee)) return $this->plain($headline, sprintf('<b>Lasst das Zombieschießen beginnen!</b>%1$s', implode('<br />', $distance)), NULL, 'zombie');
		
		$melee[] = sprintf('<b>%1$i Zombies haben dich erreicht!</b>', $blog->generic->melee);
		
		foreach ($blog->melee->entries as $entry )
			$melee[] = "<span class=\"value\"><img src=\"{$entry->item->icon()}\" alt=\"?\"></img>{$entry->item->name()}</span>: $entry->description";
		
		if (!empty($blog->fistfight)) 
			$melee[] = sprintf('<b>Du hast keine einsatzbereiten Waffen mehr!</b> Mit bloßen Händen vernichtest du <value><img src="/application/assets/icons/zombie.gif"></img>%1$d</value>!</b>', $blog->fistfight->kills);
		
		if ($blog->generic->alive)	
			return $this->plain($headline, sprintf('<b>Lasst die Schlacht beginnen!</b>%1$s<br /><br />%2$s<br /><br />Du konntest ' . (($blog->generic->zombies == 1) ? 'den Zombie' : 'alle Zombies') . ' besiegen. Glück gehabt ...', implode('<br />', $distance), implode('<br />', $melee)), NULL, 'zombie');
		else return $this->plain($headline, sprintf('<b>Lasst die Schlacht beginnen!</b>%1$s<br /><br />%2$s<br /><br />' . (($blog->generic->zombies == 1) ? 'Der Zombie wirft' : 'Die Zombies werfen') . ' dich zu Boden und ' . (($blog->generic->zombies == 1) ? 'fällt' : 'fallen') . ' über dich her ... hier kommst du nicht mehr lebend raus!', implode('<br />', $distance), implode('<br />', $melee)), NULL, 'zombie');
	}
}	