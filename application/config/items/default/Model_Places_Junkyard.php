<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()->set_decay_factor(0.02)
        ->add('gp_useless', 24)
        ->add('gp_ammo', 8)
        ->add('gp_gardening', 8)
        ->add('gp_hideout', 4)
        ->add('gp_diy', 6)
        ->add('Model_Items_Generic_Plasma', 0.5)
        ;