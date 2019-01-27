<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Item extends Model_Log_Message {

    public const MLTI_OTHER = 0;
    public const MLTI_DIGUP = 1;
    public const MLTI_EAGLE = 2;
    public const MLTI_DEATH = 3;
    public const MLTI_ZOMBIFY = 4;
    public const MLTI_GHULKILL = 5;
    public const MLTI_SOUL = 6;
    public const MLTI_VENDING = 7;
    public const MLTI_RAVEN = 8;
    public const MLTI_BOX = 9;
    public const MLTI_DEATH_ENEMY = 10;
    public const MLTI_DEATH_PET = 11;

    protected static $type = Model_Log_Message::MLM_ITEM_LOG;

    /**
     * @param mixed                                                 $type
     * @param Model_Items_Abstract_Item|Model_Items_Abstract_Item[] $item
     * @param int|null|string                                       $uin
     *
     * @throws Exception
     */
    public function __construct($type, $item, $uin = null) {
        if (!$uin) $uin =  Globals::PrimaryPlayerF()->id();

        if (!is_array($item))
            $item = [$item];

        $ticks = Globals::hasCurrentGame() ? Globals::CurrentGameF()->duration() : -1;

        $tmp = [];
        foreach ($item as $single)
            $tmp[] = new Model_Struct_Item($single,$ticks);

        $time = Globals::CurrentGameF()->now();
        parent::__construct([
            'primary' => $uin,
            'class' => $type,
            'content' => [$time =>[$uin => $tmp]]
        ], $uin);
    }

    protected function postprocess($data) {
        if ($data['primary'] === -1)
            $data['primary'] = __('Niemand');
        else {
            $primary =  Globals::CurrentGameF()->get_player($data['primary']);
            $data['primary'] = $primary ? $primary->name() : __($data['primary']);
        }

        foreach ($data['content'] as $tc => &$sub)
            foreach ($sub as $uin => &$lists) {
                foreach ($lists as &$item)
                    /** @var Model_Struct_Item $item */
                    $item = [
                        'name' => __($item->getName()),
                        'icon' => $item->getIcon(),
                        'count' => $item->getCount(),
                        'gt' => ($item->getVariant() !== null && $item->getVariant() >= 0) ? Tool_Scripts::get_daytime($item->getVariant())->getTimestamp() : null,
                    ];
                unset($item);

                if ($uin === -1) {
                    $name = __('Niemand');
                    $pl = null;
                } else {
                    $pl = Globals::CurrentGameF()->get_player($uin);
                    $name = $pl ? $pl->name() : __($uin);
                }

                $lists = [
                    'player' => $name,
                    'self' => $pl ? ($uin === Globals::PrimaryPlayerF()->id()) : false,
                    'items' => $lists
                ];

            }
        unset($sub,$lists);

        return $data;
    }

    /**
     * @param Model_Log_Types_Item $merger
     *
     * @return bool
     */
    public function merge($merger): bool {
        /** @noinspection NotOptimalIfConditionsInspection */
        if (is_a($merger, static::class, true) && $merger->data['class'] === $this->data['class']
            && !in_array(
                $this->data['class'],
                [static::MLTI_DEATH, static::MLTI_DEATH_ENEMY, static::MLTI_DEATH_PET,
                 static::MLTI_GHULKILL, static::MLTI_ZOMBIFY], true
            )
        )
            foreach ($merger->data['content'] as $tc => $d)
                if (!isset($this->data['content'][$tc])) $this->data['content'][$tc] = $d;
                else foreach ($d as $uin => $items)
                    if (!isset($this->data['content'][$tc][$uin])) $this->data['content'][$tc][$uin] = $items;
                    else $this->data['content'][$tc][$uin] = array_merge($this->data['content'][$tc][$uin], $items);
        else return false;
        return true;
    }
}