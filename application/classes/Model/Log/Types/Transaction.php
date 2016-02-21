<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Transaction extends Model_Log_Message {

    const MLTT_UP = 1;
    const MLTT_DOWN = 2;
    const MLTT_USE = 3;

    protected static $type = Model_Log_Message::MLM_TRANSACTION_LOG;

    /**
     * @param mixed $type
     * @param Model_Items_Abstract_Item|Model_Items_Abstract_Item[] $item
     * @param int|null|string $uin
     * @param null|string $action
     */
    public function __construct($type, $item, $uin = null, $action = null) {
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

        parent::__construct([
            'uin' => $uin,
            'class' => $type,
            'items' => $tmp,
            'action' => $action
        ], $uin);
    }

    protected function postprocess($data) {
        /**
         * @global Model_Game $game
         * @global Model_Player $player
         */
        global $game, $player;

        foreach ($data['items'] as &$item)
            /** @var Model_Struct_Item $item */
            $item = [
                'name' => __($item->getName()),
                'icon' => $item->getIcon(),
                'count' => $item->getCount()
            ];

        $data['player'] = $game->get_player($data['uin'])->name();
        $data['self'] = $data['uin'] == $player->id();
        $data['action'] = __($data['action']);
        unset($data['uin']);

        return $data;
    }

    /**
     * @param Model_Log_Types_Transaction $new
     * @return bool
     */
    public function merge($new) {
        if (is_a($new, get_called_class(), true) && $new->data['class'] == $this->data['class'] && $new->data['uin'] == $this->data['uin'] && in_array($this->data['class'], [static::MLTT_UP,static::MLTT_DOWN]))
            $this->data['items'] = array_merge($new->data['items'], $this->data['items']);
        else return false;
        return true;
    }
}