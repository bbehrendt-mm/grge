<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()->set_decay_factor(0.08)
        ->add('gp_druglab', 4)
        ->add('gp_meds', 4)
        ->add('gp_alcohol', 1)
        ->add('gp_diy', 1)
        ->add('gp_ammo', 3)
        ->add('gp_weapons', 2)
        ->add(Model_Items_Chem::cls(), 2)
        ->add(Model_Items_Morphine::cls(), 1)
        ;