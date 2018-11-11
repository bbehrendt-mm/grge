<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add(Model_Items_Fastfood::cls(), 2)
        ->add(Model_Items_Basefood::cls(), 2)
        ->add(Model_Items_Lunchbag::cls(), 1)
        ;