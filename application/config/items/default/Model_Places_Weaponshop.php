<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Itemfactory::factory()->set_decay_factor(0.3)
        ->add('gp_ammo', 3)
        ->add('gp_weapons', 1)
        ;