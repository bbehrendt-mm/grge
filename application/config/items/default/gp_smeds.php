<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add(Model_Items_Paracetin::cls()		, 2)
        ->add(Model_Items_Paracetoid::cls()		, 2)
        ->add(Model_Items_Foodsupplement::cls()	, 1)
        ->add(Model_Items_Paralaxium::cls()		, 2)
        ->add(Model_Items_Nutrient::cls()		, 1)
        ->add(Model_Items_Pill::cls()			, 1)
        ->add(Model_Items_Bandage::cls()			, 1)
        ->add(Model_Items_Generic_Micropur::cls(), 1)
        ->add(Model_Items_Meds::cls()            , 2)
        ->add(Model_Items_Uniheal::cls()         , 1)
        ->add(Model_Items_Uniheal2::cls()        , 1)
        ->add(Model_Items_Morphine::cls()        , 1)
        ;