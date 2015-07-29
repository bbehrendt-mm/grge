<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Itemfactory::factory()
        ->add('Model_Items_Basefood'		, 3)
        ->add('gp_alcohol'					, 1)
        ->add('Model_Items_Coffee'		    , 1)
        ->add('Model_Items_Lunchbag'	    , 1)
        ->add('Model_Items_Generic_Water1'  , 1)
        ->add('Model_Items_Dalad'           , 1)
        ->add('Model_Items_Generic_Spice'   , 1)
        ;