<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies::factory()->set_range(5,20)
    ->set_strength(15, 2)->set_chance(0.2)

    ->add(Model_Combat_Zombies_Runner::cls(), 3)
    ->add(Model_Combat_Zombies_Shambler::cls(), 1);