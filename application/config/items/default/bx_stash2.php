<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add('Model_Items_Generic_Wood'		, 1)
        ->add('Model_Items_Generic_Metal'		, 1)
        ->add('Model_Items_Money'				, 1)
        ->add('Model_Items_Battery'			    , 1)
        ->add('Model_Items_Generic_Gunpowder'	, 1)

        ->add('Model_Items_Generic_Sum'		    , 3)
        ->add('Model_Items_Generic_Tube'		, 3)
        ->add('Model_Items_Generic_Wire'        , 3)
        ->add('Model_Items_Generic_Bed'		    , 3)
        ->add('Model_Items_Generic_Electro'	    , 3)
        ->add('Model_Items_Generic_Pressure'	, 3)
        ;