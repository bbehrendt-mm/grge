<?php defined('SYSPATH') OR die('No direct access allowed.');

class Tool_Scripts
{

    /**
     * Counts available items
     * @param string $classname Restrict items to a specific class and its descendants
     * @param bool $active_player Include active players inventory
     * @param bool $active_location Include active locations inventory
     * @param bool $other_players Include inventory of other players at the active location
     * @param null|Model_Player $perspective
     * @return number
     */
    public static function count_available_items($classname = null, $active_player = true, $active_location = true, $other_players = false, $perspective = null)
    {
        /**
         * @var $belt Model_Items_Ammobelt
         */

        //Add ammobelt items
        $d = 0;
        if (Tool_System::instance_of($classname, 'Model_Items_Abstract_Ammo'))
            foreach (Tool_Scripts::available_items('Model_Items_Ammobelt', $active_player, $active_location, $other_players) as $belt)
                $d += $belt->has($classname);
        else return count(Tool_Scripts::available_items($classname, $active_player, $active_location, $other_players, $perspective));

        return $d;
    }

    /**
     * @param mixed $matrix
     * @param bool $active_player
     * @param bool $active_location
     * @param bool $other_players
     * @param null|Model_Player $perspective
     * @return bool
     */
    public static function consume_available_items($matrix, $active_player, $active_location, $other_players, $perspective = null, $grind = false) {
        /**
         * @var $belt Model_Items_Ammobelt
         * @var $item Model_Items_Abstract_Item
         */

        foreach ($matrix as $classname => $count)
            if (static::count_available_items($classname, $active_player, $active_location, $other_players, $perspective) < $count)
                return false;

        foreach ($matrix as $classname => $count) {

            if (Tool_System::instance_of($classname, 'Model_Items_Abstract_Ammo')) {
                foreach (Tool_Scripts::available_items('Model_Items_Ammobelt', $active_player, $active_location, $other_players) as $belt)
                    if ($belt->get($classname, $count)) break;
                    else {
                        $count -= $belt->has($classname);
                        $belt->get($classname, $belt->has($classname));
                    }
            } else {
                foreach (static::available_items($classname, $active_player, $active_location, $other_players, $perspective) as $item) {
                    if ($grind)
                        $item->grind();
                    else $item->consume();
                    if (--$count <= 0) break;
                }
            }
        }

        return true;
    }

    /**
     * Returns a list of available items
     * @param string $classname Restrict items to a specific class and its descendants
     * @param bool $active_player Include active players inventory
     * @param bool $active_location Include active locations inventory
     * @param bool $other_players Include inventory of other players at the active location
     * @param null|Model_Player $perspective
     * @return Model_Items_Abstract_Item[]
     */
    public static function available_items($classname = null, $active_player = true, $active_location = true, $other_players = false, $perspective = null)
    {
        /**
         * @global $player Model_Player
         */
        if ($perspective)
            $player = $perspective;
        else global $player;

        $proto = Array();
        if ($active_player)
            $proto = array_merge($proto, $player->inventory()->get($classname));

        if ($active_location)
            $proto = array_merge($proto, $player->location()->inventory()->get($classname));

        if ($other_players)
            foreach (Tool_Scripts::at_location() as $s_player)
                if ($s_player->uin() != $player->uin())
                    $proto = array_merge($proto, $s_player->inventory()->get($classname));

        return $proto;
    }

    /**
     * Returns a list of players, who currently stay at the location specified by $lid
     * @param number $lid Location; default is the active players location
     * @return Model_Player[]
     */
    public static function at_location($lid = null)
    {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
        global $player, $game;

        if ($lid === null) $lid = $player->location_class();

        $ret = Array();
        foreach ($game->players(true) as $s_player)
            if ($s_player->alive() && $s_player->location_class() == $lid)
                $ret[] = $s_player;

        return $ret;
    }

    /**
     * Returns a list of players, who currently stay at the location specified by $lid and can be used as comrades
     * @param number $lid Location; default is the active players location
     * @return Model_Player[]
     */
    public static function comrades($lid = null)
    {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
        global $player;

        if ($lid === null) $lid = $player->location_class();

        $ret = Array();
        foreach (static::at_location($lid) as $p)
            if (static::check_comrade($p->id()))
                $ret[] = $p;

        return $ret;
    }

    /**
     * Returns true if the player specified by $pid is a comrade
     * @param number $pid Player
     * @return Model_Player|boolean
     */
    public static function check_comrade($pid)
    {
        /**
         * @global $player Model_Player
         * @global $game Model_Game
         */
        global $player, $game;

        //Check if PID is valid
        if (!$pid || $pid == $player->id() || !($r = $game->get_player($pid))) return false;
        $lid = $player->location_class();

        //Check if player is in companion mode
        if (!$r->companion())
            return false;

        //Check if both players share the same location
        foreach ($game->players(true) as $s_player)
            if ($s_player->id() == $pid)
                return ($s_player->location_class() == $lid) ? $r : false;

        //Fallback case
        return false;
    }

    /**
     * Places a newly spawned item in the current locations inventory or tries to add it to the finders inventory
     * @param Model_Items_Abstract_Item|Model_Items_Abstract_Item[] $item The item (can also be an array of items)
     * @param boolean $log True if you want a log message to be created
     * @param Model_Places_Abstract_Place $use_location The location; if not set, the current location is used
     * @return boolean
     */
    public static function place_new_item($item, $log = true, $use_location = null)
    {
        /**
         * @global $player Model_Player
         */
        global $player;

        $location = $use_location ? $use_location : $player->location();

        //Fix single element arrays
        if (is_array($item) && count($item) == 1)
            $item = $item[0];

        //Recursive call for multiple items
        if (is_array($item)) {
            foreach ($item as $single) static::place_new_item($single, false, $location);
            if ($log) $location->log()->add(new Model_Log_Types_Item(Model_Log_Types_Item::MLTI_DIGUP, $item));
            return true;
        }

        if ($log) $location->log()->add(new Model_Log_Types_Item(Model_Log_Types_Item::MLTI_DIGUP, $item));
        $try_to_take = Tool_System::instance_of($item, 'Interface_Autotaker');

        if ($try_to_take) {
            $player->inventory()->add($item);
            if (!$item->take(true))
                $player->inventory()->remove($item->uin());
            else return true;
        }

        if ($location->inventory()->add($item) === false) return true;

        return false;
    }

    /**
     * Starts a simple zombie battle with a number of shamblers
     * @param number $num_zmb Number of attacking shamblers
     * @param number $distance Distance of the zombie group
     * @param string $headline String to use as headline for the battle log message; default is "Ein Kampf!"
     * @param boolean $limit_to_player Set true, if you want only the active player to become involved in battle; default is false
     * @param boolean $escapeable Set true if the battle can be escaped from; default is false
     */
    public static function simple_battle($num_zmb, $distance, $headline = "Ein Kampf!", $limit_to_player = false, $escapeable = false)
    {
        /**
         * @global $player Model_Player
         */
        global $player;
        $battle_log = Tool_Scripts::battle(new Model_Battle_Shambler($num_zmb, $distance), $limit_to_player ? $player : Tool_Scripts::at_location(), $escapeable, $battle, $zc);
        $player->location()->log()->add(new Model_Log_Types_Battle($headline, $battle_log));
    }

    /**
     * Returns the primary hideout object
     * @param null|Model_Game $game
     * @return Model_Places_Home
     */
    public static function home($game = null) {
        /**
         * @global $game Model_Game
         */
        if ($game === null) global $game;
        return $game->location(-2);
    }

    /**
     * Returns items from all hideouts
     * @param null|string $type Item type, or null for all items
     * @return Model_Items_Abstract_Item[]
     */
    public static function get_home_items($type = null) {
        /**
         * @global $game Model_Game
         */
        global $game;

        $ret = array();
        foreach ($game->maps() as $map) foreach ($map->get_locations('Model_Places_Abstract_Hideout') as $id)
            foreach ($game->location($id)->inventory()->get($type) as $item)
                $ret[] = $item;
        return $ret;
    }

    /**
     * Returns the type of location specified by $lid
     * @param $lid int Location ID
     * @return bool|int false, if the location id is invalid; 0 for a standart location; 1 for a location node, 2 for a hideout
     */
    public static function location_type($lid) {
        /**
         * @global $game Model_Game
         */
        global $game;

        if (!($location = $game->location($lid)))
            return false;

        if (Tool_System::instance_of($location, 'Model_Places_Abstract_Hideout'))
            return 2;
        elseif (Tool_System::instance_of($location, 'Model_Places_Abstract_Node'))
            return 1;
        else return 0;
    }

    /**
     * @param Model_Player $p
     * @return Model_Places_Abstract_Hideout|null
     */
    public static function current_location_hideout($p = null) {
        /** @global $player Model_Player */
        if ($p === null)
            global $player;
        else $player = $p;

        if (static::location_type($player->location_class()) == 2)
            return $player->location();
        else return null;
    }

    /**
     * Starts a standart battle
     * @param Model_Battle_Combatant[]|Model_Battle_Combatant $zombies
     * @param Model_Player[]|Model_Player $players
     * @param bool $escapeable
     * @param Model_Battle_Battle $battle_obj Will contain a reference to the batle object after calling this function.
     * @param number $count Will contain the initial zombie count.
     * @param number $achievement
     * @return array|bool
     */
    public static function battle($zombies, $players, $escapeable, &$battle_obj, &$count, $achievement = null) {
        if (!$zombies || !$players)
            return false;

        if (!is_array($zombies)) $zombies = array($zombies);
        if (!is_array($players)) $players = array($players);

        $battle_obj = new Model_Battle_Battle($escapeable);
        foreach ($zombies as $zombie) $battle_obj->spawn_combatant($zombie);

        $count = $battle_obj->get_zombie_count();

        $passives = array();
        $actives = array();
        foreach ($players as $s_player)
            /** @var Model_Player $s_player */
            if (!$s_player->buff_retr('passout')) {
                $battle_obj->spawn_combatant($s_player->create_contestant());
                $actives[]= $s_player;
            } else $passives[] = $s_player;

        $ret = $battle_obj->fight();
        if ($battle_obj->get_zombie_count() > 0)
            foreach ($passives as $s_player) {
                /** @var Model_Player $s_player */
                $s_player->set_cod('Im Schlaf zerfetzt');
                $s_player->kill();
            }
        else if ($achievement) foreach ($actives as $s_player)
            if ($s_player->alive()) $s_player->achievements()->achieve($achievement, 1);

        return $ret;
    }

    /**
     * Returns a DateTime object containing the in-universe time and date
     * @return DateTime
     */
    public static function get_daytime() {
        global $game;
        $ticks = $game->duration() + $game->getDaytimeOffset();
        $days = floor($ticks/288);
        $d = new DateTime();
        $d->setDate(1998,6,1);
        $d->setTime(floor(($ticks%288)/12), 5 * ($ticks%12), 0);
        $d->add(new DateInterval("P{$days}D"));

        return $d;
    }

    /**
     * Returns the in-universe time of day
     * @return string ("night", "morning", "day", "evening")
     */
    public static function get_timeofday() {
        switch (static::get_daytime()->format('G')) {
            case 22:case 23:case 0:case 1:case 2:case 3:case 4:case 5:
            return "night"; break;
            case 6:case 7:case 8:case 9:
            return "morning"; break;
            case 10:case 11:case 12:case 13:case 14:case 15:case 16:case 17:
            return "day"; break;
            case 18:case 19:case 20:case 21:
            return "evening"; break;
            default: return "";
        }
    }

    public static function get_map_description($mapid) {
        if ($mapid === null) $mapid = 'main';
        $c = Kohana::$config->load("balancing.names");
        return (isset($c[$mapid])) ? $c[$mapid] : 'Unbekannte Karte';
    }

    public static function calculate_find_chances($pid = null) {
        global $game;
        if ($pid === null)
            global $player;
        else $player = $game->get_player($pid);

        $c = 1;

        //Flashlight Effect
        if (static::get_timeofday() != 'night' && !$player->location()->is_outside() && ($fb = $player->buff_retr('flashlight')) && $fb->active())
            $c *= 1.2;

        //Night Malus
        if (static::get_timeofday() == 'night' && isset($fb) && $fb && !$fb->active())
            $c *= 0.25;

        //Fatigue Malus
        if ($player->stats_get(Model_Player::MP_STAT_SLEEPY) < 50)
            $c *= ($player->stats_get(Model_Player::MP_STAT_SLEEPY)/50);

        //Fatigue Bonus
        if ($player->stats_get(Model_Player::MP_STAT_SLEEPY) > 90)
            $c *= ($player->stats_get(Model_Player::MP_STAT_SLEEPY)/90);

        //Drunk Malus
        $c *= (1 - ($player->stats_get(Model_Player::MP_STAT_DRUNK)/100));

        //Survivalist Boni
        if ($player->job(1060)) {
            if ($player->job(1060, 5, false)) $c *= 1.15;
            elseif ($player->job(1060, 2, false)) $c *= 1.05;
        }

        //Child Bonus
        if ($player->job(1080)) $c *= 1.5;

        return $c;
    }

    /**
     * @param string|null $message Message
     * @param number $cv Chem Value
     * @param Model_Items_Abstract_Item $item Target item
     * @param Model_Items_Abstract_Item|Model_Items_Abstract_Item[] $results Resulting items
     * @param number|null $p Player ID
     */
    public static function chem_reaction($message, $cv, $item, $results = array(), $p = null) {
        global $game;

        if ($p === null)
            $p = $game->get_player()->id();

        if ($message)
            $game->get_player($p)->log()->add($message);

        $game->get_player($p)->location()->log()->add(new Model_Log_Types_Chem($cv, $item, $results, $p));
        static::place_new_item($results, false, $game->get_player($p)->location());
    }

    /**
     * @param null|Model_Player $player
     * @return Model_Items_Abstract_Transport|null
     */
    public static function get_active_transport($player = null) {
        if ($player === null)
            global $player;

        $selected = null;
        $items = $player->inventory()->get('Model_Items_Abstract_Transport');
        foreach ($items as $item) {
            /** @var Model_Items_Abstract_Transport $item */
            /** @var Model_Items_Abstract_Transport $selected */
            if ($item->active() && ($selected === null || $item->speedup() > $selected->speedup()))
                $selected = $item;
        }

        return $selected;
    }
}
