<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Itemfactory::factory()->set_decay_factor(0.41)
        ->add('gp_hideout', 1)
        ->add('gp_useless', 10)
        ->add('gp_kitchenutils', 1)
        ->add('gp_gardening', 1)
        ->add('gp_diy', 1)
        ;