<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add(Model_Items_Battery::cls()			    , 3)
        ->add(Model_Items_Ammo::cls()				, 1)
        ->add(Model_Items_Generic_Tube::cls()		, 1)
        ->add(Model_Items_Generic_Gunpowder::cls()	, 2)
        ;