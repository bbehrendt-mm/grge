<?php defined('SYSPATH') or die('No direct script access.');

interface Interface_Plentity {

    /**
     * @return Model_Status
     */
    public function get_status();

    public function location_class();

    /** @return Model_Places_Abstract_Place */
    public function location();

    /** @return Model_Inventory */
    public function inventory();

    public function kill();
}