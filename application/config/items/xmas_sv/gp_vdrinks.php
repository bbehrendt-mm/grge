<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add('gp_alcohol'				    , 1)
        ->add(Model_Items_Coffee::cls()	    , 1)
        ->add(Model_Items_Softdrink::cls()   , 4)
        ;