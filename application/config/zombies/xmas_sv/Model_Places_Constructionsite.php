<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies::factory()
    ->set_strength(30, 5)->set_chance(0.05)->set_range(30,60)

    ->add(Model_Combat_Zombies_Shambler::cls(), 5)
    ->add(Model_Combat_Zombies_Fatass::cls(), 5);