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
        if ($uid === null) $uid = Globals::PrimaryPlayer()->id();

        if (!is_array($results))
            $results = [$results];

        $tmp = [];
        foreach ($results as $single)
            $tmp[] = new Model_Struct_Item($single);

        parent::__construct([
            'name' => Globals::CurrentGame()->get_player($uid)->name(),
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
}
