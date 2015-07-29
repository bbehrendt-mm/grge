<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Itemfactory::factory()->set_decay_factor(0.4)
        ->add('gp_swater', 3)
        ->add('gp_hideout', 15)
        ->add('Model_Items_Tentkit2', 1)
        ;