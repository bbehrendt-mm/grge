<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()->set_decay_factor(0.15)
        ->add('gp_diy', 1)
        ->add('gp_kitchenfood', 1)
        ->add('gp_hideout', 1)
        ->add('gp_petstuff', 2)
        ->add(Model_Items_Virtual_Invoke_Animal::cls()        , 2)
        ->add(Model_Items_Money::cls(), 2)
        ;