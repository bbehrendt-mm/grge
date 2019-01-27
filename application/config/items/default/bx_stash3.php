<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add(Model_Items_Generic_Gunpowder::cls()	, 2)
        ->add(Model_Items_Generic_Tube::cls()		, 2)
        ->add(Model_Items_Generic_Wire::cls()        , 2)
        ->add(Model_Items_Generic_Bed::cls()		    , 2)
        ->add(Model_Items_Generic_Electro2::cls()	    , 1)
        ->add(Model_Items_Generic_Pressure::cls()	, 2)
        ->add(Model_Items_Generic_Sum::cls()		    , 2)
        
        ->add(Model_Items_Chainsaw::cls()            , 3)
        ->add(Model_Items_Machete2::cls()            , 3)
        ->add(Model_Items_Batgun2::cls()             , 3)
        
        ;