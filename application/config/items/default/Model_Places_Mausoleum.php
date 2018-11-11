<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()->set_decay_factor(0.25)
        ->add(Model_Items_Coffin::cls(), 3)
        ->add(Model_Items_Coffin2::cls(), 1)
        ;