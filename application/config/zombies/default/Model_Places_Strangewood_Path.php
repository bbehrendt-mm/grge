<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies::factory()->set_range(5,10)
    ->set_strength(20, 10)->set_chance(0)
    ->add(Model_Combat_Zombies_Halloween_Shambler::cls(), 1);