<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Itemfactory::factory()->set_decay_factor(0.46)
        ->add('gp_swater', 5)
        ->add('gp_diy', 1)
        ->add('gp_druglab', 1)
        ->add('gp_alcohol', 1)
        ;