<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Maker extends Model_Items_Abstract_Virtual {

    protected static $text = "Herstellen...";
    protected static $text_desc = null;
    protected static $custom_popup = "maker";
    protected static $custom_action_id = "lc_lazy_maker";

    protected function hid() {
        $action = Model_Action::factory()
            ->buttonskin('location')
            ->popup(static::$custom_popup);

        if (static::$text_desc) $action->description(static::$text_desc);

        return parent::hid()->add_action(static::$text, $action, static::$custom_action_id);
    }
}	