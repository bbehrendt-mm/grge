<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies::factory()
    ->set_strength(3, 1)->set_chance(0.05)->set_range(20,60)

    ->add(Model_Combat_Zombies_Shambler::cls(), 1);