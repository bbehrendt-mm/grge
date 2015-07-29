<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Itemfactory::factory()->set_decay_factor(0.2)
        ->add('gp_kitchenutils', 2)
        ->add('gp_kitchenfood', 2)
        ->add('gp_water', 1)
        ->add('gp_hideout', 5)
        ->add('gp_bedroom', 2)
        ->add('gp_diy', 1)
        ->add('gp_fastfood', 1)
        ->add('Model_Items_Generic_Plasma', 1)
        ->add('Model_Items_Generic_Pumpkin', 1)
        ;