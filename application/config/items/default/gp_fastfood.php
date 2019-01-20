<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add(Model_Items_Softdrink::cls(), 3)
        ->add(Model_Items_Fastfood::cls() , 5)
        ->add(Model_Items_Nutrient::cls() , 1)
        ->add(Model_Items_Lunchbag::cls() , 1)
        ->add(Model_Items_Money::cls()	 , 1)
        ->add(Model_Items_Bigbottle::cls(), 1)
        ;