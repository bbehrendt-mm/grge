<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Scene {

    const MCS_EV_NEW_CHALLENGER = 1;           // [ID, Group, Name, Unique, Avatar, Type, [x, y], [Health, Max Health, Count], [Ini, Dmg, Res, Acc], [Norm. Sprite, Death Sprite]]
    const MCS_EV_NEXT = 2;                     // [ID]
    const MCS_EV_ATTACK = 3;                   // [Atk-ID, Def-ID, [Ammo-Icons ...], [Wpn-Name, Wpn-Icon, Wpn-Anim], damage]
    const MCS_EV_DAMAGE = 4;                   // [ID, Damage, Kills, Death]
    const MCS_EV_INJURY = 5;                   // [ID, [Inj-Name, Inj-Icon]]
    const MCS_EV_MOVE = 6;                     // [ID, [x, y], distance, [to-id, to-distance]]
    const MCS_EV_SWITCH = 7;                   // [ID, [Wpn-Name, Wpn-Icon]]
    const MCS_EV_DBG_AI = 8;                   // [ID, [P_ATK, P_SWC, P_MOV]]
    const MCS_EV_BREAK = 9;                    // [ID, [Wpn-Name, Wpn-Icon]]

    private $log_data = [];

    public function export() {
        return $this->log_data;
    }

    public function summarize() {
        $tmp = [];
        $groups = [];

        foreach ($this->log_data as $entry) {

            $type = $entry[0];
            $entry = array_slice($entry, 1);

            switch ($type) {
                case static::MCS_EV_NEW_CHALLENGER:
                    list($id, $group, $name, $unique, /* $avatar */, $atype, list($x, $y), list($health, $max, $count), list($ini, $dmg, $res, $acc), list($sprite, $sprite_death)) = $entry;

                    if (!isset($tmp[$group])) $tmp[$group] = [];
                    $groups[$id] = $group;
                    $tmp[$group][$id] = [
                        'name' => $name,
                        'unique' => $unique,
                        'count' => $count,
                        'icon' => $sprite,

                        'death' => 0,
                        'dmg_dealt' => 0,
                        'dmg_taken' => 0,
                        'injuries' => [],

                        'used_ammo' => [],
                        'damaged_items' => []
                    ];

                    break;

                case static::MCS_EV_ATTACK:
                    list($atk, $def, $ammo, list($name, $icon, $animation), $damage) = $entry;

                    $tmp[$groups[$atk]][$atk]['dmg_dealt'] += $damage;
                    foreach ($ammo as $a)
                        if (!isset($tmp[$groups[$atk]][$atk]['used_ammo'][$a]))
                            $tmp[$groups[$atk]][$atk]['used_ammo'][$a] = 1;
                        else $tmp[$groups[$atk]][$atk]['used_ammo'][$a]++;

                    break;

                case static::MCS_EV_DAMAGE:
                    list($id, $damage, $kills, $death) = $entry;

                    $tmp[$groups[$id]][$id]['dmg_taken'] += $damage;
                    $tmp[$groups[$id]][$id]['death'] += $kills;
                    break;

                case static::MCS_EV_INJURY:
                    list($id, list($name, $icon)) = $entry;

                    if (!isset($tmp[$groups[$id]][$id]['injuries'][$icon]))
                        $tmp[$groups[$id]][$id]['injuries'][$icon] = [1, $name];
                    else $tmp[$groups[$id]][$id]['injuries'][$icon][0]++;
                    break;

                case static::MCS_EV_BREAK:
                    list($id, list($wpn_name, $wpn_icon)) = $entry;

                    if (!isset($tmp[$groups[$id]][$id]['damaged_items'][$wpn_icon]))
                        $tmp[$groups[$id]][$id]['damaged_items'][$wpn_icon] = [1, $wpn_name];
                    else $tmp[$groups[$id]][$id]['damaged_items'][$wpn_icon][0]++;
                    break;
            }

        }

        return $tmp;
    }

    public static function vitalize($log_data = []) {
        return array_map(
            function($e) {
                 return static::translate_entry($e);
            },
            array_values(array_filter($log_data,
                function($e) {
                    switch ($e[0]) {
                        case static::MCS_EV_DBG_AI: return Kohana::$environment != Kohana::DEVELOPMENT;
                        default: return true;
                    }
                }
        )));
    }

    private static function translate_entry($entry) {
        $type = $entry[0];

        switch ($type) {
            case static::MCS_EV_NEW_CHALLENGER:
                $entry[3] = $entry[4] ? $entry[3] : __($entry[3]);
                break;
            case static::MCS_EV_ATTACK:
                $entry[4][0] = __($entry[4][0]);
                break;
            case static::MCS_EV_INJURY: case static::MCS_EV_SWITCH: case static::MCS_EV_BREAK:
                $entry[2][0] = __($entry[2][0]);
                break;

            default: break;
        }

        return $entry;
    }

    public function __toString() {
        return implode("\r\n",array_map(function($v) {return $this->entry_to_string($v);}, $this->log_data));
    }

    private function entry_to_string($entry) {
        $type = $entry[0];
        $entry = array_slice($entry, 1);

        switch ($type) {
            case static::MCS_EV_NEW_CHALLENGER:
                list($id, $group, $name, /* $unique */, /* $avatar */, $atype, list($x, $y), list($health, $max, $count), list($ini, $dmg, $res, $acc), list($sprite, $sprite_death)) = $entry;
                switch ($atype) {
                    case Model_Combat_Actor::MCA_TYPE_PLAYER:
                        $tmp = "Player $name"; break;
                    case Model_Combat_Actor::MCA_TYPE_NPC:
                        $tmp = "NPC $name"; break;
                    case Model_Combat_Actor::MCA_TYPE_PET:
                        $tmp = "Pet $name"; break;
                    case Model_Combat_Actor::MCA_TYPE_ZOMBIE:
                        $tmp = "$count Zombies ($name)"; break;
                    default: $tmp = "Unknown $count x $name";
                }
                return "Combatant $id ($tmp, member of fraction $group) entered the ring at $x / $y; Health $health / $max; INI $ini DMG $dmg RES $res ACC $acc";


            case static::MCS_EV_NEXT:
                list($id) = $entry;
                return "--- Combatant $id is now acting! ---";

            case static::MCS_EV_ATTACK:
                list($atk, $def, $ammo, list($name, $icon, $animation), $damage) = $entry;
                return "Combatant $atk attacks Combatant $def using $name." . ($ammo ? " " . implode(', ', $ammo) . " has been consumed as ammo." : '');

            case static::MCS_EV_DAMAGE:
                list($id, $damage, $kills, $death) = $entry;
                return "Combatant $id takes " . round($damage, 2) . " damage, $kills die" . ($death ? " and the group is obliterated." : '.');

            case static::MCS_EV_INJURY:
                list($id, list($name, $icon)) = $entry;
                return "Combatant $id has been injured: $name!";

            case static::MCS_EV_MOVE:
                list ($id, list($x, $y), $distance, list($to_id, $dist)) = $entry;
                return "Combatant $id moves " . round($distance, 2) . " fields to " . round($x, 2) . " / " . round($y, 2) . ($to_id >= 0 ? (", towards Combatant $to_id, remaining distance is " . round($dist, 2) . ".") : ".");

            case static::MCS_EV_DBG_AI:
                list($id, list($atk, $swc, $mov)) = $entry;
                return "Combatant $id is thinking: PR:ATK $atk / PR:SWC $swc / PR:MOV $mov.";

            case static::MCS_EV_SWITCH:
                list($id, list($wpn_name, $wpn_icon)) = $entry;
                return "Combatant $id switches weapon to $wpn_name.";

            case static::MCS_EV_BREAK:
                list($id, list($wpn_name, $wpn_icon)) = $entry;
                return "Combatant $id's' weapon $wpn_name broke!";

            default: return "UNKNOWN SCENE INSTRUCTION ($type)!!! Data is " . json_encode($entry);
        }
    }

    /**
     * @param Model_Combat_Actor $combatant
     */
    public function add_combatant($combatant) {
        $this->log_data[] = [
            static::MCS_EV_NEW_CHALLENGER,

            $combatant->id(),
            $combatant->group(),
            $combatant->name(),
            $combatant->unique(),
            $combatant->get_avatar(),
            $combatant->get_type(),
            $combatant->position(),
            $combatant->strength(),
            $combatant->stats(),
            [
                $combatant->customSprite(false),
                $combatant->customSprite(true)
            ]
        ];
    }

    /**
     * @param Model_Combat_Actor $combatant
     */
    public function next_combatant($combatant) {
        $this->log_data[] = [
            static::MCS_EV_NEXT,

            $combatant->id(),
        ];
    }

    /**
     * @param Model_Combat_Actor $atk
     * @param Model_Combat_Actor $def
     * @param Model_Combat_Weapon $weapon
     * @param number $damage
     */
    public function attack($atk, $def, $weapon, $damage) {
        $this->log_data[] = [
            static::MCS_EV_ATTACK,

            $atk->id(),
            $def->id(),
            $weapon->get_ammo_icons(),
            [
                $weapon->name(),
                $weapon->icon(),
                $weapon->get_animation(),
            ],
            $damage
        ];
    }

    /**
     * @param Model_Combat_Actor $combatant
     * @param number $amount
     * @param number $deaths
     * @param bool $kill
     */
    public function damage($combatant, $amount, $deaths, $kill) {
        $this->log_data[] = [
            static::MCS_EV_DAMAGE,

            $combatant->id(),
            $amount,
            $deaths,
            (bool)$kill
        ];
    }

    /**
     * @param Model_Combat_Actor $combatant
     * @param string $name
     * @param string $icon
     */
    public function injury($combatant, $name, $icon) {
        $this->log_data[] = [
            static::MCS_EV_INJURY,

            $combatant->id(),
            [
                $name,
                $icon
            ]

        ];
    }

    /**
     * @param Model_Combat_Actor $combatant
     * @param int[] $pos
     * @param $distance
     * @param null|Model_Combat_Actor $to
     */
    public function move($combatant, $pos, $distance, $to = null) {
        $this->log_data[] = [
            static::MCS_EV_MOVE,

            $combatant->id(),
            $pos,
            $distance,
            $to ? [
                $to->id(),
                $to->distance_from($combatant)
            ] : [
                -1,
                -1
            ],
        ];
    }


    /**
     * @param Model_Combat_Actor $combatant
     * @param Model_Combat_Weapon $new_weapon
     */
    public function switch_weapon($combatant, $new_weapon) {
        $this->log_data[] = [
            static::MCS_EV_SWITCH,

            $combatant->id(),
            [
                $new_weapon->name(),
                $new_weapon->icon(),
            ]

        ];
    }

    /**
     * @param Model_Combat_Actor $combatant
     * @param Model_Combat_Weapon $weapon
     */
    public function break_weapon($combatant, $weapon) {
        $this->log_data[] = [
            static::MCS_EV_BREAK,

            $combatant->id(),
            [
                $weapon->name(),
                $weapon->icon(),
            ]

        ];
    }

    /**
     * @param Model_Combat_Actor $combatant
     * @param Model_Items_Abstract_Armor $armor
     */
    public function break_armor($combatant, $armor) {
        $this->log_data[] = [
            static::MCS_EV_BREAK,

            $combatant->id(),
            [
                $armor->name(),
                $armor->icon(),
            ]

        ];
    }

    /**
     * @param Model_Combat_Actor $combatant
     * @param array|null $ai_atk
     * @param array|null $ai_swc
     * @param array|null $ai_mov
     */
    public function dbg_battle_ai($combatant, $ai_atk, $ai_swc, $ai_mov) {
        $this->log_data[] = [
            static::MCS_EV_DBG_AI,

            $combatant->id(),
            [
                $ai_atk ? $ai_atk[0] : '-',
                $ai_swc ? $ai_swc[0] : '-',
                $ai_mov ? $ai_mov[0] : '-',
            ]

        ];
    }
}