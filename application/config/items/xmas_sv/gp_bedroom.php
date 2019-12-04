<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add(Model_Items_Matches::cls(), 1)
        ->add(Model_Items_Generic_Bed::cls()	 , 2)
        ->add(Model_Items_Battery::cls()		 , 1)
        ->add(Model_Items_Generic_Lamp::cls()    , 1)
        ->add(Model_Items_Generic_Cloth::cls()   , 3)
        ->add(Model_Items_Generic_Teddy::cls()   , 1)
        ->add(Model_Items_Dildo::cls()           , 1)
        ->add('gp_literature'               , 1)
        ->add(Model_Items_Jacket::cls()          , 1)
        ->add(Model_Items_Heatjacket1::cls()     , 1)
        ->add(Model_Items_Heatjacket2::cls()     , 1)
        ;