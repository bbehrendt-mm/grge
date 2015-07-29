<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Itemfactory::factory()->set_decay_factor(0.2)
        ->add('gp_kitchenutils', 2)
        ->add('gp_kitchenfood', 2)
        ->add('gp_water', 1)
        ;