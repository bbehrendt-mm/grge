<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies::factory()
    ->set_strength(15, 1)->set_chance(0.1)->set_range(2,10)

    ->add(Model_Combat_Zombies_Shambler::cls(), 1);