<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies::factory()
    ->set_strength(5, 3)->set_chance(0.2)->set_range(5,16)

    ->add(Model_Combat_Zombies_Starver::cls(), 5);