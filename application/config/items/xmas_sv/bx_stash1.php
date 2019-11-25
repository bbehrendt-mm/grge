<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add(Model_Items_Generic_Wood::cls()	     , 3)
        ->add(Model_Items_Generic_Metal::cls()	     , 3)
        ->add(Model_Items_Generic_Crwood::cls()	     , 2)
        ->add(Model_Items_Generic_Crmetal::cls()		     , 2)

        ->add(Model_Items_Generic_Tube::cls()		     , 1)
        ->add(Model_Items_Money::cls()				     , 1)
        ->add(Model_Items_Battery::cls()			     , 1)
        ->add(Model_Items_Generic_Wire::cls()        , 1)
        ->add(Model_Items_Generic_Spraycan::cls()    , 1)
        ;