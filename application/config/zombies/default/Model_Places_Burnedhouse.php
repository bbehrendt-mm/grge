<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies::factory()
    ->set_strength(10, 4)->set_chance(0.15, 0.6)->set_range(10,20)

    ->add(Model_Combat_Zombies_Shambler::cls(), 1);