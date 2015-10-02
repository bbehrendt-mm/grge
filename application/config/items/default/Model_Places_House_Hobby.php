<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()->set_decay_factor(0.05)
        ->add('gp_diy', 4)
        ->add('gp_hideout', 4)
        ->add('gp_alcohol', 6)
        ->add('gp_xmas', 1)
        ->add('Model_Items_Bandage', 2)
        ;