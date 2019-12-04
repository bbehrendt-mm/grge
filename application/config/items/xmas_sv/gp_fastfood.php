<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add(Model_Items_Softdrink::cls(), 8)
        ->add(Model_Items_Beer::cls(), 4)
        ->add(Model_Items_Fastfood::cls() , 10)
        ->add(Model_Items_Lunchbag::cls() , 2)
        ->add(Model_Items_Money::cls()	 , 1)
        ->add(Model_Items_Bigbottle::cls(), 2)
        ;