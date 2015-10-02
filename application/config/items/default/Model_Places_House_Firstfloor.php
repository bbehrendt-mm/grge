<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()->set_decay_factor(0.2)
        ->add('gp_kitchenutils', 2)
        ->add('gp_kitchenfood', 2)
        ->add('gp_water', 1)
        ;