<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies2::factory()
    ->set_strength(10, 3)->set_chance(0.1)

    ->add(Model_Combat_Zombies_Shambler::cls(), 5)
    ->add(Model_Combat_Zombies_Runner::cls(), 1);