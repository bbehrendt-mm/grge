<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()->set_decay_factor(0.3)
        ->add('gp_diy', 45)
        ->add('gp_hideout', 18)
        ->add(Model_Items_Generic_Caravan::cls(), 1)
        ->add(Model_Items_Generic_Sum::cls(), 8)
        ;