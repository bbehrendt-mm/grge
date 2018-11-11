<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add(Model_Items_Xmas_Wine::cls()  , 2)
        ->add(Model_Items_Xmas_Beer::cls()  , 1)
        ->add(Model_Items_Xmas_Drink::cls() , 1)
        ->add(Model_Items_Xmas_Coffee::cls(), 1)
        ;