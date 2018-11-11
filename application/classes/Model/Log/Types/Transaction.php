<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Transaction extends Model_Log_Message {

    const MLTT_UP = 1;
    const MLTT_DOWN = 2;
    const MLTT_USE = 3;

    protected static $type = Model_Log_Message::MLM_TRANSACTION_LOG;

    /**
     * @param mixed                                                 $type
     * @param Model_Items_Abstract_Item|Model_Items_Abstract_Item[] $item
     * @param int|null|string                                       $uin
     * @param null|string                                           $action
     *
     * @throws Exception
     */
    public function __construct($type, $item, $uin = null, $action = null) {
        if (!$uin) $uin =  Globals::PrimaryPlayerF()->id();

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
        foreach ($data['items'] as &$item)
            /** @var Model_Struct_Item $item */
            $item = [
                'name' => __($item->getName()),
                'icon' => $item->getIcon(),
                'count' => $item->getCount()
            ];
        unset($item);

        $data['player'] = Globals::CurrentGameF()->get_player($data['uin'])->name();
        $data['self'] = $data['uin'] == Globals::PrimaryPlayerF()->id();
        $data['action'] = __($data['action']);
        unset($data['uin']);

        return $data;
    }

    /**
     * @param Model_Log_Types_Transaction $new
     * @return bool
     */
    public function merge($new) {
        if (is_a($new, static::class, true) && $new->data['class'] == $this->data['class'] && $new->data['uin'] == $this->data['uin'] && in_array($this->data['class'], [static::MLTT_UP,static::MLTT_DOWN]))
            $this->data['items'] = array_merge($new->data['items'], $this->data['items']);
        else return false;
        return true;
    }
}