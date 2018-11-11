<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add(Model_Items_Xmas_Cookie::cls()   , 2)
        ->add(Model_Items_Xmas_Meat::cls()     , 1)
        ->add(Model_Items_Xmas_Sweets::cls()   , 1)
        ;