<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()->set_decay_factor(0.1)
        ->add('gp_useless', 2)
        ->add('gp_alcohol', 2)
        ->add('gp_hideout', 1)
        ->add('gp_diy', 1)
        ->add('gp_gardening', 1)
        ;