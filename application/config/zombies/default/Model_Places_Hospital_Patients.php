<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies2::factory()
    ->set_strength(25, 6)->set_chance(0.2)

    ->add(Model_Combat_Zombies_Lurker::cls(), 3)
    ->add(Model_Combat_Zombies_Nurse::cls(), 2)
    ->add(Model_Combat_Zombies_Fatass::cls(), 2)
    ->add(Model_Combat_Zombies_Runner::cls(), 2);