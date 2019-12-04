<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()->set_decay_factor(0.5)
        ->add('gp_gardening', 2)
        ->add('gp_diy', 5)
        ;