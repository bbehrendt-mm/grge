<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add(Model_Items_Generic_Plasticbag::cls()  , 4)
        ->add(Model_Items_Oldrifle::cls()            , 1)
        ->add(Model_Items_Handgun::cls()			    , 1)
        ->add(Model_Items_Watergun::cls()		    , 2)
        ->add(Model_Items_Knife::cls()			    , 1)
        ->add(Model_Items_Chainsaw::cls()            , 1)
        ->add(Model_Items_Vest::cls()                , 1)
        ->add(Model_Items_Bat::cls()                 , 1)
        ->add(Model_Items_Flashlight::cls()          , 1)
        ;