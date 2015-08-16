<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Chem extends Model_Log_Message {

    protected static $type = Model_Log_Message::MLM_CHEM_EXPERIMENT;

    /**
     * @param number $chemvalue
     * @param Model_Items_Abstract_Item $item
     * @param $results
     * @param null $uid
     */
    public function __construct($chemvalue, $item, $results, $uid = null) {
        /**
         * @global Model_Game $game
         * @global Model_Player $player
         */
        global $game, $player;
        if ($uid === null) $uid = $player->id();

        if (!is_array($results))
            $results = [$results];

        $tmp = [];
        foreach ($results as $single)
            $tmp[] = new Model_Struct_Item($single);

        parent::__construct([
            'name' => $game->get_player($uid)->name(),
            'chem' => new Model_Struct_Item(new Model_Items_Chem($chemvalue)),
            'item' => new Model_Struct_Item($item),
            'results' => $tmp,
        ], $uid);
    }

    protected function postprocess($data) {
        /**
         * @var Model_Struct_Item $chem
         * @var Model_Struct_Item $item
         */
        $chem = $data['chem'];
        $item = $data['item'];

        $data['chem'] = [
            'name' => __($chem->getName()),
            'icon' => $chem->getIcon(),
            'count' => $chem->getCount()
        ];
        $data['item'] = [
            'name' => __($item->getName()),
            'icon' => $item->getIcon(),
            'count' => $item->getCount()
        ];

        if ($data['results'])
            foreach ($data['results'] as &$res)
                /** @var Model_Struct_Item $res */
                $res = [
                    'name' => __($res->getName()),
                    'icon' => $res->getIcon(),
                    'count' => $res->getCount()
                ];
        else $data['results'] = false;

        return $data;
    }

    /**
     * Renders a body, or returns null
     * @return string|NULL Body as string or null if no body applies
     */
    /*public function render_body()
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
    }*/
}
