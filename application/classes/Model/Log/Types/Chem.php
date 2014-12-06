<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Chem extends Model implements Interface_Message {

    private static $item_trunc_length = 15;

    private $type = Model_Log_Types_Item::MLTI_OTHER;
    /**
     * @var Model_Struct_Item[][]
     */
    private $chem, $item;
    private $results = array();
    private $uid;

    private $timecode;


    public function __construct($chemvalue, $item, $results, $uid = null) {
        global $game, $player;
        $this->uid = $uid ? $uid : $player->user_id();

        if (!is_array($results))
            $results = array($results);

        $this->chem = new Model_Struct_Item(new Model_Items_Chem($chemvalue));
        $this->item = new Model_Struct_Item($item);

        foreach ($results as $single)
            $this->results[] = new Model_Struct_Item($single);

        $this->timecode = $game->now();
    }

    public function render_title()
    {
        return null;
    }

    /**
     * Renders a body, or returns null
     * @return string|NULL Body as string or null if no body applies
     */
    public function render_body()
    {
        global $game, $player;

        if (empty($this->results))
            $s = ($player->id() != $this->uid)
                ? ':name hat erfolglos :item mit :chem kombiniert...'
                : 'Du hast erfolglos :item mit :chem kombiniert...';
        else $s = ($player->id() != $this->uid)
            ? ':name hat :item mit :chem kombiniert, und dabei :list erhalten.'
            : 'Du hast :item mit :chem kombiniert, und dabei :list erhalten.';

        return __($s, array(':name' => $game->get_player($this->uid)->name(), ':chem' => $this->render_item($this->chem), ':item' => $this->render_item($this->item), ':list' => $this->render_item($this->results)));
    }

    /**
     * @param Model_Struct_Item|Model_Struct_Item[] $item
     * @return string
     */
    private function render_item($item) {
        if (is_array($item))
            return implode(', ', array_map(function($f) {return $this->render_item($f);}, $item));
        else return "<span class=\"value\"><img src=\"{$item->getIcon()}\" alt=\"?\">" . __($item->getName()) . "</span>";
    }

    /**
     * Returns the message timecode
     * @return int
     */
    public function timecode() {
        return $this->timecode;
    }

    /**
     * @param Interface_Message $new
     * @return bool
     */
    public function merge($new)
    {
        return false;
    }
}
