<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies2::factory()
    ->set_strength(30, 4)->set_chance(0.3)

    ->add(Model_Combat_Zombies_Shambler::cls(), 5)
    ->add(Model_Combat_Zombies_Fatass::cls(), 4)
    ->add(Model_Combat_Zombies_Runner::cls(), 2);