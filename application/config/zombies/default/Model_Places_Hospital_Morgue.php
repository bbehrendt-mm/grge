<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies::factory()
    ->set_strength(5, 3)->set_chance(0.2)

    ->add(Model_Combat_Zombies_Starver::cls(), 5);