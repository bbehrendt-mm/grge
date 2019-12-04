<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add(Model_Items_Nutrient::cls()		, 1)
        ->add(Model_Items_Petfood::cls(), 2)
        ->add(Model_Items_Petfood3::cls(), 3)
        ;