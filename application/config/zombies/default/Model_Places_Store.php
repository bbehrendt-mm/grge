<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies::factory()
    ->set_strength(20, 10)->set_chance(0.1)

    ->add(Model_Combat_Zombies_Shambler::cls(), 1);