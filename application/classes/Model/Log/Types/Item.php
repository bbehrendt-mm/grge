<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Item extends Model implements Interface_Message {

    const MLTI_OTHER = 0;
    const MLTI_DIGUP = 1;
    const MLTI_EAGLE = 2;
    const MLTI_DEATH = 3;
    const MLTI_ZOMBIFY = 4;
    const MLTI_GHULKILL = 5;
    const MLTI_SOUL = 6;
    const MLTI_VENDING = 7;

    private static $item_trunc_length = 15;

    private $type = Model_Log_Types_Item::MLTI_OTHER;
    /**
     * @var Model_Struct_Item[][]
     */
    private $items = array();
    private $uins = array();
    private $timecodes;

    public function __construct($type, $item, $uin = null) {
        global $game, $player;
        $this->uins[] = $uin ? $uin : $player->user_id();

        if (!is_array($item))
            $item = array($item);

        $tmp = array();
        foreach ($item as $single)
            $tmp[] = new Model_Struct_Item($single);

        $this->items[] = $tmp;
        $this->timecodes[] = $game->now();
        $this->type = $type;
    }

    protected function get_icon_strings($id = -1) {
        $selected = array();
        if ($id >= 0)
            $selected = $this->items[$id];
        else array_walk_recursive($this->items, function($a) use (&$selected) { $selected[] = $a; });
        $selected = array_reverse($selected);

        $return = array();
        if (count($selected) <= 5)
            array_walk($selected, function($a) use (&$return) {
                /** @var Model_Struct_Item $a */
                $return[] = "<span class=\"value\"><img src=\"{$a->getIcon()}\" alt=\"?\" /> " . __($a->getName()) . (($a->getCount() !== null) ? " ({$a->getCount()})" : "") . "</span>";
            });
        else array_walk($selected, function($a) use (&$return) {
                /** @var Model_Struct_Item $a */
                $return[] = "<span class=\"value\" title=\"" . __($a->getName()) . (($a->getCount() !== null) ? " ({$a->getCount()})" : "") . "\"><img src=\"{$a->getIcon()}\" alt=\"?\"></img></span>";
            });

        return $return;
    }

    protected function finalize_item_string($id = -1, $trunc = true) {
        $items = $this->get_icon_strings($id);
        if ($trunc)
            return implode(', ', array_slice($items, 0, static::$item_trunc_length)) . ((count($items) > static::$item_trunc_length) ? ', <span>[ … ]</span>' : '');
        else return implode(', ', $items);
    }

    protected function distinct_usernames() {

        $temp = array();
        array_walk($this->uins, function($a) use (&$temp) {
            global $game;
            if (!isset($temp[$a]))
                $temp[$a] = $game->get_player($a)->name();
        });
        return $temp;
    }

    public function render_title()
    {
        global $game;
        if (is_string($this->type))
            return __(':itemdef erhalten!', array(':itemdef' => $this->finalize_item_string()));
        switch ($this->type) {
            case static::MLTI_DIGUP:
                return __(':itemdef gefunden!', array(':itemdef' => $this->finalize_item_string()));
            case static::MLTI_EAGLE:
                return __(':itemdef entdeckt!', array(':itemdef' => $this->finalize_item_string()));
            case static::MLTI_DEATH:
                return __(':name ist von uns gegangen...', array(':name' => $game->get_player($this->uins[0])->name()));
            case static::MLTI_ZOMBIFY:
                return __(':name ist gestorben und hat sich in einen Zombie verwandelt!', array(':name' => $game->get_player($this->uins[0])->name()));
            case static::MLTI_GHULKILL:
                return __(':name hat nun endlich seinen ewigen Frieden gefunden...', array(':name' => $game->get_player($this->uins[0])->name()));
            case static::MLTI_SOUL:
                return __(':itemdef angelockt!', array(':itemdef' => $this->finalize_item_string()));
            case static::MLTI_VENDING:
                return __(':itemdef erworben!', array(':itemdef' => $this->finalize_item_string()));
            default:
                return __(':itemdef aufgetaucht!', array(':itemdef' => $this->finalize_item_string()));
        }
    }

    /**
     * Renders a body, or returns null
     * @return string|NULL Body as string or null if no body applies
     */
    public function render_body()
    {
        global $player;
        $data = $this->distinct_usernames();
        $fstp = count($data) == 1 && isset($data[$player->id()]);

        $return = "";
        if (is_string($this->type))
            $return = __($this->type);
        else switch ($this->type) {
            case static::MLTI_DIGUP:
                $return .= $fstp ? __('Geduld zahlt sich aus. Es war nicht leicht, aber du hast etwas nützliches finden können.') : __('Geduld zahlt sich aus. Es war nicht leicht, aber ihr habt etwas nützliches finden können.');
                break;
            case static::MLTI_EAGLE:
                $return .= $fstp ? __('Nicht schlecht! Vielen wäre das verborgen geblieben, aber du hast es auf Anhieb entdeckt.') : __('Nicht schlecht! Anderen wäre das verborgen geblieben, aber ihr habt es auf Anhieb entdeckt.');
                break;
            case static::MLTI_DEATH:
                $return .= $fstp ? __('Du hast soeben deinen letzten Atemzug getan und deiner Gemeinschaft das wenige, was du hattest, hinterlassen. Das wars dann wohl...') : __('Heute ist ein trauriger Tag für eure kleine Gemeinschaft, denn sie ist soeben wieder etwas geschrumpft. Nur einige sterbliche Überreste sind noch zurück geblieben...');
                break;
            case static::MLTI_ZOMBIFY:
                $return .= $fstp ? __('Du hast dich soeben in einen Zombie verwandelt!') : __('Heute ist ein trauriger Tag für eure kleine Gemeinschaft, denn sie ist soeben wieder etwas geschrumpft. Die Zombiehorden hingegen haben Zuwachs zu verzeichnen...');
                break;
            case static::MLTI_GHULKILL:
                $return .= $fstp ? __('Deine Freunde haben dir endlich den ewigen Frieden geschenkt.') : __('Es ist immer schwer, jemandem den man gekannt hat den Gnadenstoß zu geben. Nur einige sterbliche Überreste sind noch zurück geblieben...');
                break;
            case static::MLTI_SOUL:
                $return .= $fstp ? __('Na sowas. Es scheint, als würde sich da etwas zu dir hingezogen fühlen!') : __('Na sowas. Es scheint, als würde sich da etwas zu euch hingezogen fühlen!');
                break;
            case static::MLTI_VENDING:
                $return .= $fstp ? __('Du konntest den Versuchungen des Kapitalismus anscheinend nicht widerstehen und hast etwas gekauft.') : __('Ihr konntet den Versuchungen des Kapitalismus anscheinend nicht widerstehen und habt etwas gekauft.');
                break;
        }

        if ($return != "") $return = "<b>$return</b><br />";

        if (count($data) == 1)
            $return .= $this->finalize_item_string(-1, false) . '<br />';
        else foreach ($data as $id => $name) {
            $return .= "<b>$name</b><ul>";
            for ($i = 0; $i < count($this->uins); $i++)
                if ($this->uins[$i] == $id)
                    $return .= '<li><i>' . date(__('G:i:s \U\h\r \a\m d.m.Y (T)'),$this->timecodes[$i]) . ':</i>' . $this->finalize_item_string($i, false) . '</li>';
            $return .= "</ul><br />";
        }

        $return .= __('Die meisten neuen Gegenstände werden auf den Boden gelegt. Manche Gegenstände, wie beispielsweise Batterien, werden aber unter Umständen automatisch aufgesammelt und entweder deinem Rucksack oder deinem Munitionsgürtel hinzugefügt.') . '<br />';


        return $return;
    }

    /**
     * Returns the message timecode
     * @return int
     */
    public function timecode()
    {
        return null;
    }

    /**
     * @param Interface_Message|Model_Log_Types_Item $new
     * @return bool
     */
    public function merge($new) {
        if (is_a($new, get_called_class(), true) && $new->type == $this->type) {
            for ($i = 0; $i < count($new->uins); $i++) {
                $this->uins[] = $new->uins[$i];
                $this->items[] = $new->items[$i];
                $this->timecodes[] = $new->timecodes[$i];
            }
            return true;
        } else return false;
    }
}
