<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()->set_decay_factor(0.2)
        ->add('gp_kitchenfood', 2)
        ->add('gp_water', 2)
        ->add('gp_kitchenutils', 1)
        ->add('gp_alcohol', 7)
        ->add(Model_Items_Generic_Table::cls(), 1)
        ->add(Model_Items_Heatjacket3::cls(), 1)
        ->add(Model_Items_Heatjacket4::cls(), 1)
        ;