<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Itemfactory::factory()->set_decay_factor(0.2)
        ->add('gp_diy', 1)
        ->add('gp_kitchenfood', 2)
        ->add('gp_kitchenutils', 1)
        ->add('gp_swater', 3)
        ->add('gp_fastfood', 1)
        ->add('gp_ammo', 2)
        ->add('gp_weapons', 1)
        ->add('Model_Items_Pumpkin', 1)
        ;