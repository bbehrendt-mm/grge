<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()->set_decay_factor(0.05)
        ->add('gp_swater', 4)
        ->add('gp_water', 2)
        ->add('gp_diy', 1)
        ->add('gp_hideout', 1)
        ->add('Model_Items_Generic_Plasma', 1)
        ;