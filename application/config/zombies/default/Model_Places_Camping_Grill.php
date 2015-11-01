<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies::factory()
    ->set_strength(25, 4)->set_chance(0.1)->set_range(1,5)

    ->add(Model_Combat_Zombies_Shambler::cls(), 5)
    ->add(Model_Combat_Zombies_Fatass::cls(), 3)
    ->add(Model_Combat_Zombies_Runner::cls(), 2)
    ->add(Model_Combat_Zombies_Starver::cls(), 3);