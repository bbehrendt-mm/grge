<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies2::factory()
    ->set_strength(10, 2)->set_chance(0.1)

    ->add(Model_Combat_Zombies_Shambler::cls(), 5)
    ->add(Model_Combat_Zombies_Fatass::cls(), 2)
    ->add(Model_Combat_Zombies_Starver::cls(), 2);