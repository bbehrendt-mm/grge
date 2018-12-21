<?php

class Model_NPC_Special_Sherri extends Model_NPC_Mouse
{
    protected static $movement_scaling = 0.1;
    protected static $comfort_threshold = 90;

    public function __construct() {
        parent::__construct('Sherri');
    }

    public function entity_description() {
        return 'Sherri mag im Kampf nicht sonderlich nützlich sein - dafür kann sie in die kleinste Ritze kriechen und Gegenstände für dich finden.';
    }
}