<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add('Model_Items_Chem'			, 2)
        ->add('Model_Items_Powderpack'		, 1)
        ->add('Model_Items_Nutrient'		, 1)
        ->add('Model_Items_Pill'			, 2)
        ->add('Model_Items_Bandage'			, 1)
        ->add('gp_alcohol'						, 1)
        ->add('Model_Items_Generic_Micropur', 1)
        ->add('Model_Items_Generic_Oven'	, 1)
        ->add('Model_Items_Money'			, 2)
        ->add('Model_Items_Generic_Table'	, 1)
        ->add('Model_Items_Meds'            , 1)
        ->add('Model_Items_Dildo'           , 1)
        ->add('Model_Items_Generic_Spice'   , 1)
        ->add('Model_Items_Shield'	        , 1)
        ->add('Model_Items_Helmet'          , 2)
        ->add('Model_Items_Jacket'          , 1)
        ->add('Model_Items_Shield3'         , 1)

        ->add(Model_Items_Virtual_Invoke_Animal::cls()        , 1)
        ;