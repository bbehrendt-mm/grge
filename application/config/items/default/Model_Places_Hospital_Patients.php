<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()->set_decay_factor(0.3)
        ->add('gp_hideout', 2)
        ->add('gp_bedroom', 6)
        ->add('gp_meds', 4)
        ->add('Model_Items_Morphine', 1)
        ;