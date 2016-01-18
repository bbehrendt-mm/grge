<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Item extends Model_Log_Message {

    const MLTI_OTHER = 0;
    const MLTI_DIGUP = 1;
    const MLTI_EAGLE = 2;
    const MLTI_DEATH = 3;
    const MLTI_ZOMBIFY = 4;
    const MLTI_GHULKILL = 5;
    const MLTI_SOUL = 6;
    const MLTI_VENDING = 7;
    const MLTI_RAVEN = 8;

    protected static $type = Model_Log_Message::MLM_ITEM_LOG;

    /**
     * @param mixed $type
     * @param Model_Items_Abstract_Item|Model_Items_Abstract_Item[] $item
     * @param int|null|string $uin
     */
    public function __construct($type, $item, $uin = null) {
        /**
         * @global Model_Game $game
         * @global Model_Player $player
         */
        global $game, $player;

        if (!$uin) $uin =  $player->id();

        if (!is_array($item))
            $item = [$item];

        $tmp = [];
        foreach ($item as $single)
            $tmp[] = new Model_Struct_Item($single);

        $time = $game->now();

        $usr = $game->get_player($uin);

        parent::__construct([
            'primary' => $usr ? $usr->name() : $uin,
            'class' => $type,
            'content' => [$time =>[$uin => $tmp]]
        ], $uin);
    }

    protected function postprocess($data) {
        /**
         * @global Model_Game $game
         * @global Model_Player $player
         */
        global $game, $player;

        foreach ($data['content'] as $tc => &$sub)
            foreach ($sub as $uin => &$lists) {
                foreach ($lists as &$item)
                    /** @var Model_Struct_Item $item */
                    $item = [
                        'name' => __($item->getName()),
                        'icon' => $item->getIcon(),
                        'count' => $item->getCount()
                    ];
                $lists = [
                    'player' => !is_numeric($uin) ? __($uin) : $game->get_player($uin)->name(),
                    'self' => !is_numeric($uin) ? false : ($uin == $player->id()),
                    'items' => $lists
                ];
            }

        return $data;
    }

    /**
     * @param Model_Log_Types_Item $new
     * @return bool
     */
    public function merge($new) {
        if (is_a($new, get_called_class(), true) && $new->data['class'] == $this->data['class'] && !in_array($this->data['class'], [static::MLTI_DEATH,static::MLTI_GHULKILL,static::MLTI_ZOMBIFY]))
            foreach ($new->data['content'] as $tc => $d)
                if (!isset($this->data['content'][$tc])) $this->data['content'][$tc] = $d;
                else foreach ($d as $uin => $items)
                    if (!isset($this->data['content'][$tc][$uin])) $this->data['content'][$tc][$uin] = $items;
                    else $this->data['content'][$tc][$uin] = array_merge($this->data['content'][$tc][$uin], $items);
        else return false;
        return true;
    }
}