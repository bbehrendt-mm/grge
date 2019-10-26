<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Strangewood_Exit extends Model_Places_Abstract_Place implements Interface_Corridor {
	
	protected static $location_name = 'Parkplatz';
	protected static $description = 'Vor dem Eingang in den Wald findest du einen kleinen Parkplatz. Auf einigen Plätzen stehen verrostete Autos, deren Marken und Hersteller du noch nie gehört hast. Je tiefer du in den Wald blickst, desto weniger Licht siehst du durch die Baumkronen dringen. Bist du sicher, dass du weitergehen willst?';
    protected static $icon = 'exit';
    protected static $outside = true;

    protected $processed_players = [];

    //Enter map
    public function enter_map($pid = null, $type = Interface_Tickable::IT_TYPE_PLAYER): bool
    {
        $b = true;
        if ($b = $this->enter($pid, $type) && !in_array($pid, $this->processed_players)) {
            $p = Globals::CurrentGameF()->get_player($pid);
            if (!Tool_Scripts::is_npc($p)) {

                $map = Globals::CurrentGameF()->map($this->uin());
                /** @var Model_Map_Labyrinth $map */
                if ($map->place_location(new Model_Places_Strangewood_Singleplayer($p),true, 0, null, true))
                    $this->processed_players[] = $pid;
                else throw new Exception("FAIL");
            }
        }

        return $b;
    }
}	