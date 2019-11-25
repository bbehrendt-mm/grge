<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add(Model_Items_Basefood::cls()		, 3)
        ->add('gp_alcohol'					, 1)
        ->add(Model_Items_Coffee::cls()		    , 1)
        ->add(Model_Items_Lunchbag::cls()	    , 1)
        ->add(Model_Items_Generic_Water1::cls()  , 1)
        ->add(Model_Items_Dalad::cls()           , 1)
        ->add(Model_Items_Generic_Spice::cls()   , 1)
        ;