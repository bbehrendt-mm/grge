<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Struct_Item extends Model {

    private $name;
    private $icon;
    private $count;

    /**
     * @param Model_Items_Abstract_Item $item
     */
    public function __construct($item) {
        $this->name = $item->name();
        $this->icon = $item->icon();
        $this->count = (Tool_System::instance_of($item, 'Interface_Countable')) ? $item->count() : null;
    }

    /**
     * @return number|null
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


}