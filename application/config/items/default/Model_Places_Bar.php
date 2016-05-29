<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()->set_decay_factor(0.2)
        ->add('gp_kitchenfood', 1)
        ->add('gp_water', 1)
        ->add('gp_kitchenutils', 1)
        ->add('gp_alcohol', 5)
        ->add('Model_Items_Generic_Table', 1)
        ;