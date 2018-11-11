<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()->set_decay_factor(0.15)
        ->add('gp_hideout', 5)
        ->add(Model_Items_Wine::cls(), 1)
        ;