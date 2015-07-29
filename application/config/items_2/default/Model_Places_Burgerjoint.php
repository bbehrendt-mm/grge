<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Itemfactory::factory()->set_decay_factor(0.15)
        ->add('gp_fastfood', 4)
        ->add('gp_kitchenutils', 1)
        ->add('gp_kitchenfood', 1)
        ->add('gp_water', 1)
        ;