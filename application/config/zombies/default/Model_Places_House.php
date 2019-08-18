<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Zombies::factory()
        ->set_strength(10, 2)->set_chance(0.2, 0.4)->set_range(2,10)

        ->add(Model_Combat_Zombies_Shambler::cls(), 5)
        ->add(Model_Combat_Zombies_Behemoth::cls(), 1);