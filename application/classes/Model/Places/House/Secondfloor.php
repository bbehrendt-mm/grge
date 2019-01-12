<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_House_Secondfloor extends Model_Places_Abstract_Place {
	
	protected static $location_name = 'Erste Etage des verbrannten Hauses';
	protected static $description = 'Hier oben sind die Brandschäden weitaus schlimmer als im Erdgeschoss... wahrscheinlich ist das Feuer hier oben ausgebrochen. Anscheinend bist du der Erste, der sich hier hoch getraut hat - keine Spuren von Zombies oder anderen Plünderern.';
    protected static $outside = false;

	protected static $weight_limit = 60;
	
	public function can_enter($pid = null, $type = Interface_Tickable::IT_TYPE_PLAYER): bool
    {
        if (!$pid) $player = Globals::CurrentPlayerF();
        elseif ($type === Interface_Tickable::IT_TYPE_PLAYER) $player = Globals::CurrentGameF()->get_player($pid);
        else $player = Globals::CurrentGameF()->get_npc($pid);

        if ($player->inventory()->weight() > static::$weight_limit)
		{
			if (!Tool_Scripts::is_npc())
		        $player->log()->add(new Model_Log_Types_String(null, 'Du versuchst, die Treppe in die erste Etage hinaufzusteigen. Das Holz knirscht unter deinen Füßen und du merkst, wie der Boden langsam nachgibt. Sofort springst du zurück - du bist zu schwer beladen, um hier hochzulaufen. Lege ein paar schwere Sachen aus deinem Rucksack ab und versuche es dann erneut.'));
			return false;
		}	
		return parent::can_enter($pid, $type);
	}
	
	//Enter location
	public function enter($pid = null, $type = Interface_Tickable::IT_TYPE_PLAYER): bool {
        if (!$pid) $player = Globals::CurrentPlayerF();
        elseif ($type === Interface_Tickable::IT_TYPE_PLAYER) $player = Globals::CurrentGameF()->get_player($pid);
        else $player = Globals::CurrentGameF()->get_npc($pid);

        $player->inventory()->temporal_limit(static::$weight_limit);
        return parent::enter($pid, $type);
	}

    //Leave location
    public function leave($pid = null, $type = Interface_Tickable::IT_TYPE_PLAYER): bool {
        if (!$pid) $player = Globals::CurrentPlayerF();
        elseif ($type === Interface_Tickable::IT_TYPE_PLAYER) $player = Globals::CurrentGameF()->get_player($pid);
        else $player = Globals::CurrentGameF()->get_npc($pid);

        $player->inventory()->temporal_limit(NULL);
        return parent::leave($pid, $type);
    }
}	