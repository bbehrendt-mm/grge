<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()->set_decay_factor(0.12)
        ->add('gp_hideout', 2)
        ->add('gp_bedroom', 2)
        ->add('gp_smeds', 2)
        ->add(Model_Items_Morphine::cls(), 1)
        ;