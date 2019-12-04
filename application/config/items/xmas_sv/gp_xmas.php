<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add(Model_Items_Generic_Xmasneedles::cls() , 1)
        ->add(Model_Items_Generic_Cd::cls()          , 1)
        ->add(Model_Items_Generic_Led::cls()         , 1)
        ->add(Model_Items_Generic_Bauble::cls()      , 1)
        ->add(Model_Items_Generic_Ducttape::cls()    , 1)
        ->add(Model_Items_Generic_Wire::cls()        , 1)
        ->add(Model_Items_Generic_Electro::cls()     , 1)

        ->add(Model_Items_Generic_Spice::cls()       , 1)
        ->add(Model_Items_Generic_Bobblehead::cls()  , 1)
        ->add(Model_Items_Generic_Cloth::cls()       , 1)
        ->add(Model_Items_Xmas_Rubbing::cls()        , 5)
        ->add(Model_Items_Xmas_Paraspirin::cls()     , 5)
        ;