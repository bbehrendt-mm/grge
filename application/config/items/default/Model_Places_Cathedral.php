<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Itemfactory::factory()->set_decay_factor(0.15)
        ->add('gp_hideout', 5)
        ->add('Model_Items_Wine', 1)
        ;