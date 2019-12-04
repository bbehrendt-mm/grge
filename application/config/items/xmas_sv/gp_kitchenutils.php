<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add(Model_Items_Generic_Tube::cls()	    , 1)
        ->add(Model_Items_Generic_Table::cls()	    , 1)
        ->add(Model_Items_Generic_Boiler::cls()	    , 1)
        ->add(Model_Items_Gardenchair2::cls()	    , 2)
        ->add(Model_Items_Gardenchair::cls()		    , 1)
        ->add(Model_Items_Generic_Cloth::cls()	    , 2)
        ->add(Model_Items_Smallbottle::cls()		    , 1)
        ->add(Model_Items_Generic_Mixer::cls()		, 1)
        ->add(Model_Items_Generic_Oven::cls()		, 1)
        ->add(Model_Items_Knife::cls()				, 1)
        ;