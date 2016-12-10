<?php defined('SYSPATH') OR die('No direct access allowed.');

class Tool_Scripts
{

    /**
     * @param number $t
     * @param Model_Player|null $p
     */
    public static function rebuild_primary_equipment($t, $p = null) {
        /** @global Model_Game $game */
        global $game;

        if (!$p)
            foreach ($game->players() as $player)
                static::rebuild_primary_equipment($t, $player);
        else {
            $items = $p->get_equipment($t);
            if (!count($items) || !$items[0]->allows_primary()) return;

            if (!$p->get_equipment($t, true))
                $items[0]->equip_primary($p);
        }
    }

    /**
     * Counts available items
     * @param string $classname Restrict items to a specific class and its descendants
     * @param bool $active_player Include active players inventory
     * @param bool $active_location Include active locations inventory
     * @param bool $other_players Include inventory of other players at the active location
     * @param null|Interface_Plentity $perspective
     * @param null|callable $decider
     * @return number
     */
    public static function count_available_items($classname = null, $active_player = true, $active_location = true, $other_players = false, $perspective = null, $decider = null)
    {
        /**
         * @var $belt Model_Items_Ammobelt
         */

        //Add ammobelt items
        $d = 0;
        if (Tool_System::instance_of($classname, 'Model_Items_Abstract_Ammo'))
            foreach (Tool_Scripts::available_items('Model_Items_Ammobelt', $active_player, $active_location, $other_players, $perspective) as $belt)
                $d += $belt->has($classname);
        // Add stackable items
        elseif (Tool_System::instance_of($classname, 'Model_Items_Abstract_Stackable'))
            foreach (Tool_Scripts::available_items($classname, $active_player, $active_location, $other_players, $perspective, $decider) as $instance)
                $d += $instance->count();
        else return count(Tool_Scripts::available_items($classname, $active_player, $active_location, $other_players, $perspective, $decider));

        return $d;
    }

    /**
     * @param mixed $matrix
     * @param bool $active_player
     * @param bool $active_location
     * @param bool $other_players
     * @param null|Interface_Plentity $perspective
     * @param bool $grind
     * @param null|callable|callable[] $decider
     * @return bool
     */
    public static function has_available_items($matrix, $active_player, $active_location, $other_players, $perspective = null, $grind = false, $decider = null) {
        /**
         * @param string $cls
         * @return callable|null
         */
        $get_decider = function($cls) use ($decider) {
            if (!is_array($decider))
                return $decider;
            else return isset($decider[$cls]) ? $decider[$cls] : null;
        };

        foreach ($matrix as $classname => $count) {
            if (static::count_available_items($classname, $active_player, $active_location, $other_players, $perspective, $get_decider($classname)) < $count)
                return false;
        }

        return true;
    }

    /**
     * @param mixed $matrix
     * @param bool $active_player
     * @param bool $active_location
     * @param bool $other_players
     * @param null|Interface_Plentity $perspective
     * @param bool $grind
     * @param null|callable|callable[] $decider
     * @return bool
     */
    public static function consume_available_items($matrix, $active_player, $active_location, $other_players, $perspective = null, $grind = false, $decider = null) {
        /**
         * @param string $cls
         * @return callable|null
         */
        $get_decider = function($cls) use ($decider) {
            if (!is_array($decider))
                return $decider;
            else return isset($decider[$cls]) ? $decider[$cls] : null;
        };

        foreach ($matrix as $classname => $count) {
            if (static::count_available_items($classname, $active_player, $active_location, $other_players, $perspective, $get_decider($classname)) < $count)
                return false;
        }

        /**
         * @var $belt Model_Items_Ammobelt
         * @var $item Model_Items_Abstract_Item
         */
        foreach ($matrix as $classname => $count) {
            if (Tool_System::instance_of($classname, 'Model_Items_Abstract_Ammo')) {
                foreach (Tool_Scripts::available_items('Model_Items_Ammobelt', $active_player, $active_location, $other_players, $perspective) as $belt)
                    if ($belt->get($classname, $count)) break;
                    else {
                        $count -= $belt->has($classname);
                        $belt->get($classname, $belt->has($classname));
                    }
            } elseif (Tool_System::instance_of($classname, 'Model_Items_Abstract_Stackable')) {
                foreach (Tool_Scripts::available_items($classname, $active_player, $active_location, $other_players, $perspective, $get_decider($classname)) as $instance)
                    if ($instance->count() > $count) {
                        for ($i = 0; $i < $count; $i++) $instance->consume();
                        break;
                    } elseif ($instance->count() == $count) {
                        $instance->grind();
                        break;
                    } else {
                        $count -= $instance->count();
                        $instance->grind();
                    }
            } else {
                foreach (static::available_items($classname, $active_player, $active_location, $other_players, $perspective, $get_decider($classname)) as $item) {
                    if ($grind)
                        $item->grind();
                    else $item->consume();
                    if (--$count <= 0) break;
                }
            }
        }

        /**
         * @global $player Model_Player
         */
        if (is_object($perspective))
            $player = $perspective;
        else global $player;

        if ($active_player) $player->inventory()->reset_weight();
        if ($active_location) $player->location()->inventory()->reset_weight();
        if ($other_players)
            foreach (Tool_Scripts::at_location() as $s_player)
                if ($s_player->uin() != $player->uin())
                    $s_player->inventory()->reset_weight();

        return true;
    }

    public static function getBrainCoinLikelinessLevel($lid = null) {
        /**
         * @global Model_Game $game
         * @global Model_Player $player
         */
        global $game, $player;

        // Get Radar data
        list($radar_min, $radar_max, $radar_prop, $radar_increase) = $lid ? $game->location($lid)->zombie_factory()->get_radar_data() : $player->location()->zombie_factory()->get_radar_data();

        // Check if we're at a hideout with active defenses
        $hideout = Tool_Scripts::current_location_hideout();
        $protected_hideout = $hideout && $hideout->get_defense() > 0;

        // Calculate approx. number of ticks between each blockade increase and random attack; set random attack value to zero if we're at a hideout
        if ($protected_hideout)
            $radar_prop = 0;
        else $radar_prop = ($radar_prop > 0) ? ceil(pow($radar_prop,-1)) : 0;
        $radar_increase = ($radar_increase > 0) ? ceil(pow($radar_increase,-1)) : 0;

        // Calculate danger level
        $danger = ($radar_prop > 0) ? floor($radar_max/4) : 0;              // Base value: Max attack group size
        if (!$protected_hideout && $radar_prop <= 1.5 && $radar_prop > 0)     $danger += 2;    // Increase by 2 if we have a very high attack probability
        elseif (!$protected_hideout && $radar_prop <= 3 && $radar_prop > 0)   $danger += 1;    // Increase by 1 if we have a high attack probability
        elseif ($radar_prop <= 15  || $radar_prop == 0)  $danger -= 1;                           // Decrease by 1 if we have a very low attack probability
        if ($radar_increase != 0 && $radar_increase <= 3)   $danger += 1;   // Increase by 1 if we have a very high blocking speed
        $danger = min(5,max(($radar_prop > 0) ? 1 : 0,$danger));            // Confine danger to 0-5 range

        switch ($danger) {
            case 0: return 0;
            case 1: return 0.003;
            case 2: return 0.01;
            case 3: return 0.04;
            case 4: return 0.08;
            case 5: return 0.15;
            default: return 0;
        }
    }

    /**
     * Returns a list of available items
     * @param string $classname Restrict items to a specific class and its descendants
     * @param bool $active_player Include active players inventory
     * @param bool $active_location Include active locations inventory
     * @param bool $other_players Include inventory of other players at the active location
     * @param null|Interface_Plentity $perspective
     * @param null|callable $decider
     * @return Model_Items_Abstract_Item[]
     */
    public static function available_items($classname = null, $active_player = true, $active_location = true, $other_players = false, $perspective = null, $decider = null)
    {
        /**
         * @global $player Model_Player
         */
        if (is_object($perspective))
            $player = $perspective;
        else global $player;

        if (Tool_Scripts::is_npc($player))
            $other_players = false;

        $proto = [];
        if (!$player) return [];
        if ($active_player)
            $proto = array_merge($proto, $player->inventory()->get($classname));

        if ($active_location && $player->location())
            $proto = array_merge($proto, $player->location()->inventory()->get($classname));

        if ($other_players)
            foreach (Tool_Scripts::at_location($player->location_class(), true, true) as $s_player)
                if ($s_player->uin() != $player->uin())
                    $proto = array_merge($proto, $s_player->inventory()->get($classname));

        return ($decider && is_callable($decider)) ? array_values(array_filter($proto, $decider)) : $proto;
    }
    /**
     * Returns the first available item from a list
     * @param string $classname Restrict items to a specific class and its descendants
     * @param bool $active_player Include active players inventory
     * @param bool $active_location Include active locations inventory
     * @param bool $other_players Include inventory of other players at the active location
     * @param null|Interface_Plentity $perspective
     * @return Model_Items_Abstract_Item
     */

    public static function first_available_item($classname, $active_player = true, $active_location = true, $other_players = false, $perspective = null)
    {
        $l = static::available_items($classname, $active_player, $active_location, $other_players, $perspective);
        if (count($l) > 0) return $l[0];
        else return null;
    }


    /**
     * Returns a list of players, who currently stay at the location specified by $lid
     * @param number $lid Location; default is the active players location
     * @param bool $include_players Include players
     * @param bool $include_npcs Include NPCs
     * @return Interface_Plentity[]|Model_Player[]
     */
    public static function at_location($lid = null, $include_players = true, $include_npcs = true)
    {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
        global $player, $game;

        if (!$include_players && !$include_npcs) return [];
        if ($lid === null) $lid = $player->location_class();

        $ret = [];
        if ($include_players)
            foreach ($game->players(true) as $s_player)
                if ($s_player->get_status()->alive() && $s_player->location_class() == $lid)
                    $ret[] = $s_player;
        if ($include_npcs)
            foreach ($game->npcs(true) as $s_player)
                if ($s_player->get_status()->alive() && $s_player->location_class() == $lid)
                    $ret[] = $s_player;

        return $ret;
    }

    /**
     * Returns a list of players, who currently stay at the location specified by $lid and can be used as comrades
     * @param number $lid Location; default is the active players location
     * @return Model_Player[]
     */
    public static function comrades($lid = null, $include_players = true, $include_npcs = false)
    {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
        global $player;

        if ($lid === null) $lid = $player->location_class();

        $ret = [];
        foreach (static::at_location($lid, $include_players, $include_npcs) as $p)
            if ($p->id() != $player->id() && static::check_comrade($p))
                $ret[] = $p;

        return $ret;
    }

    /**
     * Returns true if the player specified by $pid is a comrade
     * @param number|Model_Player|Interface_Plentity $pid Player
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
        $r = null;
        if (is_numeric($pid))
            $r = $game->get_player($pid);
        elseif (is_string($pid))
            $r = $game->get_npc($pid);
        elseif (is_object($pid))
            $r = $pid;

        if (!$r) return false;

        //Check if player is in companion mode
        if (!$r->companion())
            return false;

        //Check if both players share the same location
        return ($r->location_class() == $player->location_class()) ? $r : false;
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

            if (Tool_System::instance_of($item, 'Model_Items_Abstract_Ammo')) {
                /** @var $belt Model_Items_Ammobelt */
                $belt = Tool_Scripts::first_available_item(Model_Items_Ammobelt::cls(), true, false, false);
                /** @var Model_Items_Abstract_Ammo $item */
                if ($belt && $item->take()) {
                    $belt->add($item);
                    return true;
                }

            } else {
                $player->inventory()->add($item);
                if (!$item->take(true))
                    $player->inventory()->remove($item->uin());
                else return true;
            }
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
        Tool_Scripts::combat([$limit_to_player ? [$player] : Tool_Scripts::at_location(), [Model_Combat_Zombies_Shambler::factory()->count($num_zmb)]], $escapeable, $distance, $player->location(), $headline);
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
     * @param Interface_Plentity $p
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
     * @param Model_Combat_Actor[][] $combatants
     * @param bool $escapeable
     * @param Model_Places_Abstract_Place null $location
     * @return Model_Combat_Field
     */
    public static function combat($combatants, $escapeable, $distance = 10, $location = null, $title = 'Ein Kampf!', $text = null) {
        /**
         * @global Model_Player $player
         * @global Model_Game $game
         */

        global $game, $player;

        $battle = Model_Combat_Field::factory();

        $non_combatants = [];
        $actual_combatants = [];
        $no_nc = true;

        foreach ($combatants as $fraction => $group) if (count($group) > 0) {
            $actual_combatants[$fraction] = $non_combatants[$fraction] = [];
            /** @var Model_Combat_Actor|Model_NPC_Nano $member */
            foreach ($group as $member) {
                if (Tool_System::instance_of($member, Model_NPC_Nano::cls()) && ($member->get_status()->retrieve('passout') || !$member->is_fighter())) {
                    $non_combatants[$fraction][] = $member;
                    $no_nc = false;
                }
                    
                else $actual_combatants[$fraction][] = $member;
            }
        }

        foreach ($actual_combatants as $fraction => $group)
            $battle->add_combatant($fraction + 1, $group);

        $battle->init_positions($distance)->begin($escapeable && $no_nc);

        foreach ($actual_combatants as $fraction => $group)
            if ($battle->count_group_members($fraction + 1) == 0)
                foreach ($non_combatants[$fraction] as $member) {
                    $member->get_status()->set_cause_of_death('Im Schlaf zerfetzt');
                    $member->kill();
                }

        if ($location === null)
            $location = $player->location();

        $battle->get_scene()->set_atmosphere($location->battle_location_type());

        //Upload to DB
        $vid = Model_Combat_Handler::upload($game->id(), $game->season(), $battle);
        $location->log()->add(new Model_Log_Types_Battle($title, $text, $vid, $battle->get_scene()->summarize()));

        return $battle;
    }

    /**
     * Returns a DateTime object containing the in-universe time and date
     * @param number $t
     * @return DateTime
     */
    public static function get_daytime($t = null) {
        /** @global Model_Game $game */
        global $game;
        if ($t === null) $t = $game->duration();
        $ticks = $t + $game->getDaytimeOffset();
        $days = floor($ticks/288);
        $d = new DateTime();
        $d->setDate(1998,Kohana::$config->load('server.season'),2);
        $d->setTime(floor(($ticks%288)/12), 5 * ($ticks%12), 0);
        $d->add(new DateInterval("P{$days}D"));

        return $d;
    }

    /**
     * Returns the in-universe time of day
     * @param Model_Player|null $p
     * @return string ("night", "morning", "day", "evening")
     */
    public static function get_timeofday($p = null) {
        if ($p && $p->location()->getPerpetualDayTime())
            return $p->location()->getPerpetualDayTime();
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

    public static function calculate_find_chances($pid = null) {
        /**
         * @global Model_Game $game
         * @global Interface_Plentity|Model_Player $player
         */
        global $game;
        if ($pid === null)
            global $player;
        else $player = $game->get_player($pid);

        $c = 1;

        $is_night = in_array(static::get_timeofday(), ['night','snowynight']);
        $is_light = false;

        //Flashlight Effect
        if (!$is_night && !$player->location()->is_outside() && ($fb = $player->get_status()->retrieve('flashlight')) && $fb->active())
            $c *= 1.2;
        elseif (($fb = $player->get_status()->retrieve('flashlight')) && $fb->active())
            $is_light = true;

        // Rudolphs Nose
        if (($rn = $player->get_status()->retrieve('rudolph')) && $rn->active())
            $c *= 1.5;

        //Night Malus
        if ($is_night && !$is_light)
            $c *= 0.25;
        //Fatigue Malus
        if ($player->get_status()->get(Model_Status::MS_STAT_SLEEPY) < 50)
            $c *= ($player->get_status()->get(Model_Status::MS_STAT_SLEEPY)/50);

        //Fatigue Bonus
        if ($player->get_status()->get(Model_Status::MS_STAT_SLEEPY) > 90)
            $c *= ($player->get_status()->get(Model_Status::MS_STAT_SLEEPY)/90);

        //Drunk Malus
        $c *= (1 - ($player->get_status()->get(Model_Status::MS_STAT_DRUNK)/100));

        //Survivalist Boni
        if (!Tool_Scripts::is_npc($player) && $player->job(1060)) {
            if ($player->job(1060, 5, false)) $c *= 1.15;
            elseif ($player->job(1060, 2, false)) $c *= 1.05;
        }

        //Child Bonus
        if (!Tool_Scripts::is_npc($player) && $player->job(1080)) $c *= 1.5;

        //Item Spawnrate Stat
        $c *= $player->get_status()->get(Model_Status::MS_CHAR_ITEM_SPAWNRATE);

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
        /** @global Model_Game $game */
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

    /**
     * @param null|Model_Player|Interface_Plentity $player
     * @return bool
     */
    public static function is_npc($player = null) {
        if ($player === null)
            global $player;

        if (!$player) return false;
        return $player->type() != Interface_Plentity::IC_NPC_NONPC;
    }
}
