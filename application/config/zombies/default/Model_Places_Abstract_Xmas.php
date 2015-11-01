<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies::factory()
    ->set_strength(5, 1)->set_chance(0.15, 0.2)->set_range(1,50)

    ->add(Model_Combat_Zombies_Shambler::cls(), 1);