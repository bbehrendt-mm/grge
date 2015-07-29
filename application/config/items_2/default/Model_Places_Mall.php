<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Itemfactory::factory()->set_decay_factor(0.05)
        ->add('gp_hideout', 1)
        ->add('gp_diy', 1)
        ->add('gp_kitchenfood', 1)
        ->add('gp_kitchenutils', 1)
        ->add('gp_bedroom', 1)
        ->add('gp_meds', 1)
        ->add('gp_alcohol', 1)
        ->add('gp_ammo', 1)
        ->add('gp_weapons', 1)
        ;