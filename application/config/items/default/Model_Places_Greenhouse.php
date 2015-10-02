<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()->set_decay_factor(0.13)
        ->add('gp_water', 5)
        ->add('gp_swater', 2)
        ->add('gp_diy', 2)
        ->add('gp_gardening', 3)
        ->add('Model_Items_Pumpkin', 1)
        ;