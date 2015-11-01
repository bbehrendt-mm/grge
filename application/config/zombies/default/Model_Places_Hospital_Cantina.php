<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies::factory()
    ->set_strength(25, 2)->set_chance(0.3)->set_range(5,25)

    ->add(Model_Combat_Zombies_Starver::cls(), 3)
    ->add(Model_Combat_Zombies_Lurker::cls(), 3)
    ->add(Model_Combat_Zombies_Fatass::cls(), 2)
    ->add(Model_Combat_Zombies_Runner::cls(), 2);