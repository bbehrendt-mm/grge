<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add(Model_Items_Generic_Water1::cls(), 3)
        ->add(Model_Items_Cwater::cls()	      , 6)
        ->add(Model_Items_Cwater2::cls()	      , 1)
        ;