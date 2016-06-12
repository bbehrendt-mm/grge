<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies::factory()
    ->set_strength(12, 3)->set_chance(0.1)->set_range(5,20)

    ->add(Model_Combat_Zombies_Shambler::cls(), 5)
    ->add(Model_Combat_Zombies_Fatass::cls(), 1)
    ->add(Model_Combat_Zombies_Gusher::cls(), 1)
    ->add(Model_Combat_Zombies_Runner::cls(), 1);