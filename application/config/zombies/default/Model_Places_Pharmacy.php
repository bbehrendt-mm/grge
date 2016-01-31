<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies::factory()->set_range(5,15)
    ->set_strength(10, 3)->set_chance(0.05, 0.25)

    ->add(Model_Combat_Zombies_Shambler::cls(), 5)
    ->add(Model_Combat_Zombies_Runner::cls(), 1);