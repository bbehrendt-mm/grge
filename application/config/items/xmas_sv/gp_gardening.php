<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add(Model_Items_Matches::cls(), 2)
        ->add(Model_Items_Gardenchair::cls()	    ,3)
        ->add(Model_Items_Generic_Water2::cls()  ,1)
        ->add(Model_Items_Generic_Wood::cls()	,2)
        ->add(Model_Items_Generic_Tube::cls()	,2)
        ->add(Model_Items_Generic_Micropur::cls(),1)
        ->add(Model_Items_Shield::cls()	        ,1)
        ->add(Model_Items_Helmet::cls()          ,1)
        ->add(Model_Items_Generic_Belt::cls()    ,1)
        ;