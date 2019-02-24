<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()->set_decay_factor(0.06)
        ->add('gp_smeds', 5)
        ->add('gp_druglab', 1)
        ->add('gp_body', 1)
        ->add(Model_Items_Chem::cls(), 1)
        ;