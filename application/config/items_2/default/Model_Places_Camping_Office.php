<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Itemfactory::factory()->set_decay_factor(0.3)
        ->add('gp_diy', 5)
        ->add('gp_hideout', 1)
        ->add('gp_gardening', 3)
        ->add('gp_kitchenutils', 2)
        ;