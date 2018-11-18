<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Room_Generic extends Model_Items_Abstract_Virtual {

    protected $text = 'Herstellen...';
    protected $text_desc = null;
    protected $custom_popup = 'maker';
    protected $custom_action_id = 'lc_lazy_maker';

    public function __construct($text = 'Herstellen...', $text_desc = null, $custom_popup = 'maker', $custom_action_id = 'lc_lazy_maker'
    ) {
        $this->text = $text;
        $this->text_desc = $text_desc;
        $this->custom_popup = $custom_popup;
        $this->custom_action_id = $custom_action_id;

        parent::__construct(null);
    }

    protected function hid(): Model_Hid {
        $action = Model_Action::factory()
            ->buttonskin('location')
            ->popup($this->custom_popup);

        if ($this->text_desc) $action->description($this->text_desc);

        return parent::hid()->add_action($this->text, $action, $this->custom_action_id);
    }
}	