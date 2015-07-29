<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Itemfactory::factory()->set_decay_factor(0.3)
        ->add('gp_hideout', 1)
        ->add('gp_bedroom', 3)
        ->add('gp_meds', 2)
        ;