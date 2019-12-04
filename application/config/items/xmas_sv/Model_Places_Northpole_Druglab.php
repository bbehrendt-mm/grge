<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()->set_decay_factor(0.08)
        ->add('gp_druglab', 4)
        ->add('gp_meds', 4)
        ->add('gp_alcohol', 1)
        ;