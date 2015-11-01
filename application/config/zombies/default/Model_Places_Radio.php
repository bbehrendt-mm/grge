<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies::factory()
    ->set_strength(15, 2)->set_chance(0.15)->set_range(10,40)

    ->add(Model_Combat_Zombies_Shambler::cls(), 5)
    ->add(Model_Combat_Zombies_Runner::cls(), 3)
    ->add(Model_Combat_Zombies_Fatass::cls(), 3)
    ->add(Model_Combat_Zombies_Starver::cls(), 1);