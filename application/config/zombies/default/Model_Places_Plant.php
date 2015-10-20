<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies2::factory()
    ->set_strength(10, 10)->set_chance(0.6)

    ->add(Model_Combat_Zombies_Mutant::cls(), 1);