<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add(Model_Items_Generic_Wire::cls()       , 1)
        ->add(Model_Items_Generic_Electro::cls()	    , 3)
        ->add(Model_Items_Generic_Electro2::cls()	    , 1)
        ->add(Model_Items_Generic_Pressure::cls()	, 2)
        ->add(Model_Items_Phone::cls()              , 1)
        ->add(Model_Items_Flashlight::cls()         , 1)
        ;