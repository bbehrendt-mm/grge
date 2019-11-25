<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add(Model_Items_Generic_Bed::cls()	    , 2)
        ->add(Model_Items_Battery::cls()		    , 1)
        ->add(Model_Items_Generic_Lamp::cls()    , 2)
        ->add(Model_Items_Generic_Cloth::cls()   , 2)
        ->add(Model_Items_Generic_Teddy::cls()   , 2)
        ->add(Model_Items_Dildo::cls()           , 1)
        ->add('gp_literature'               , 2)
        ->add(Model_Items_Helmet::cls()          , 1)
        ->add(Model_Items_Jacket::cls()          , 1)

        ->add(Model_Items_Virtual_Invoke_Animal::cls()        , 1)
        ;