<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies::factory()
    ->set_strength(4, 1)->set_chance(0.15)->set_range(0,1)

    ->add(Model_Combat_Zombies_Shambler::cls(), 1);