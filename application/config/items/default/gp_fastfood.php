<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Itemfactory::factory()
        ->add('Model_Items_Softdrink', 3)
        ->add('Model_Items_Fastfood' , 5)
        ->add('Model_Items_Nutrient' , 1)
        ->add('Model_Items_Lunchbag' , 1)
        ->add('Model_Items_Money'	 , 1)
        ;