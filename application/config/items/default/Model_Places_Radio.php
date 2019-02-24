<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()->set_decay_factor(0.09)
        ->add('gp_diy', 2)
        ->add('gp_diy_electric', 3)
        ->add('gp_useless', 1)
        ->add('gp_hideout', 2)
        ->add('gp_kitchenfood', 1)
        ->add('gp_ammo', 1)
        ;