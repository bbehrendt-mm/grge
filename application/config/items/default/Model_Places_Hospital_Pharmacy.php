<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()->set_decay_factor(0.09)
        ->add('gp_smeds', 3)
        ->add('gp_druglab', 1)
        ->add('Model_Items_Chem', 1)
        ;