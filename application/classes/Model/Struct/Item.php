<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Struct_Item extends Model {

    private $name;
    private $icon;
    private $count;
    private $variant = null;

    /**
     * @param Model_Items_Abstract_Item $item
     * @param null                      $variant
     */
    public function __construct($item,$variant = null) {
        $this->name = $item->name();
        $this->icon = $item->icon();
        $this->count = Tool_System::instance_of($item, 'Interface_Countable') ? $item->count() : null;
        $this->variant = $variant;
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
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getVariant()
    {
        return $this->variant;
    }


}