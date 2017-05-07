<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Label extends Model_Items_Abstract_Item implements Interface_Label {

    protected $label;
    protected static $max_label_size = 12;

	public function set_label($new_text) {
        $new = (bool)$this->label;
        $this->label = mb_substr($new_text, 0, static::$max_label_size);

        if ($this->label == '') Globals::PrimaryPlayer()->log()->add('Du hast die Beschriftung auf diesem Gegenstand weggewischt.');
        elseif (!$new) Globals::PrimaryPlayer()->log()->add('Du hast diesen Gegenstand mit einer Beschriftung versehen.');
        else Globals::PrimaryPlayer()->log()->add('Du hast die Beschriftung dieses Gegenstands geändert.');
    }
}	