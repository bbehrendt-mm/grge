<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add(Model_Items_Stick::cls()               , 10)
        ->add(Model_Items_Generic_Xmasneedles::cls() , 5)
        ->add(Model_Items_Generic_Cd::cls()          , 18)
        ->add(Model_Items_Generic_Led::cls()         , 10)
        ->add(Model_Items_Generic_Bauble::cls()      , 19)
        ->add(Model_Items_Generic_Ducttape::cls()    , 5)
        ->add(Model_Items_Generic_Wire::cls()        , 3)
        ->add(Model_Items_Generic_Electro::cls()     , 1)

        ->add(Model_Items_Generic_Spice::cls()       , 3)
        ->add(Model_Items_Generic_Bobblehead::cls()  , 3)
        ->add(Model_Items_Generic_Cloth::cls()       , 3)
        ->add(Model_Items_Xmas_Rubbing::cls()        , 3)
        ->add(Model_Items_Xmas_Paraspirin::cls()     , 6)
        ;