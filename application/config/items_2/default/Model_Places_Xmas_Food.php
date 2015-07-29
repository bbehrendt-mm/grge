<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Itemfactory::factory()->set_decay_factor(0.4)
        ->add('gp_xmas',  1)
        ->add('gp_xmas_food', 5)
        ->add('gp_xmas_drink', 1)
        ;