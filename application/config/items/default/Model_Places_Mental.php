<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()->set_decay_factor(0.02)
        ->add('gp_hideout', 1)
        ->add('gp_bedroom', 1)
        ->add('gp_meds', 1)
        ->add('gp_druglab', 1)
        ;