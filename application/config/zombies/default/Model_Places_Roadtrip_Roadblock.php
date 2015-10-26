<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies2::factory()
    ->set_strength(4, 1)->set_chance(0.05)

    ->add(Model_Combat_Zombies_Shambler::cls(), 1)
    ->add(Model_Combat_Zombies_Runner::cls(), 1)
    ->add(Model_Combat_Zombies_Fatass::cls(), 1);