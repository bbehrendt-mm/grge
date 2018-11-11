<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add(Model_Items_Generic_Wood::cls()		, 1)
        ->add(Model_Items_Generic_Metal::cls()		, 1)
        ->add(Model_Items_Money::cls()				, 1)
        ->add(Model_Items_Battery::cls()			    , 1)
        ->add(Model_Items_Generic_Gunpowder::cls()	, 1)

        ->add(Model_Items_Generic_Sum::cls()		    , 3)
        ->add(Model_Items_Generic_Tube::cls()		, 3)
        ->add(Model_Items_Generic_Wire::cls()        , 3)
        ->add(Model_Items_Generic_Bed::cls()		    , 3)
        ->add(Model_Items_Generic_Electro::cls()	    , 3)
        ->add(Model_Items_Generic_Pressure::cls()	, 3)
        ;