<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies::factory()->set_range(0,60)
    ->set_strength(10, 10)->set_chance(0.6)

    ->add(Model_Combat_Zombies_Mutant::cls(), 1);