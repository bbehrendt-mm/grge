<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Itemfactory::factory()->set_decay_factor(0.3)
        ->add('gp_meds', 3)
        ->add('gp_druglab', 1)
        ->add('Model_Items_Chem', 1)
        ;