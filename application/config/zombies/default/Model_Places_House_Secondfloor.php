<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies::factory()->set_range(5,10)
    ->set_strength(5, 1)->set_chance(0.05)

    ->add(Model_Combat_Zombies_Shambler::cls(), 1);