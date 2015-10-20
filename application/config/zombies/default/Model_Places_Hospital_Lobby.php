<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies2::factory()
    ->set_strength(15, 2)->set_chance(0.2)

    ->add(Model_Combat_Zombies_Nurse::cls(), 5)
    ->add(Model_Combat_Zombies_Fatass::cls(), 2)
    ->add(Model_Combat_Zombies_Runner::cls(), 2);