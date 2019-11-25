<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies::factory()
    ->set_strength(15, 4)->set_chance(0.1)->set_range(0,10)

    ->add(Model_Combat_Zombies_Shambler::cls(), 3)
    ->add(Model_Combat_Zombies_Runner::cls(), 1);