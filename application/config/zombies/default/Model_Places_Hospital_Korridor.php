<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies::factory()
    ->set_strength(20, 2)->set_chance(0.1)->set_range(5,10)

    ->add(Model_Combat_Zombies_Nurse::cls(), 1)
    ->add(Model_Combat_Zombies_Lurker::cls(), 1);