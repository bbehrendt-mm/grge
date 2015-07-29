<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Itemfactory::factory()->set_decay_factor(0.25)
        ->add('Model_Items_Coffin', 3)
        ->add('Model_Items_Coffin2', 1)
        ;