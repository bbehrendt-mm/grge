<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add('Model_Items_Gardenchair'	    ,3)
        ->add('Model_Items_Generic_Water2'  ,1)
        ->add('Model_Items_Generic_Wood'	,2)
        ->add('Model_Items_Generic_Tube'	,2)
        ->add('Model_Items_Generic_Micropur',1)
        ->add('Model_Items_Shield'	        ,1)
        ->add('Model_Items_Helmet'          ,1)
        ->add('Model_Items_Generic_Bike'    ,1)
        ->add('Model_Items_Generic_Belt'    ,1)

        ->add(Model_Items_Virtual_Invoke_Animal::cls()        , 1)
        ;