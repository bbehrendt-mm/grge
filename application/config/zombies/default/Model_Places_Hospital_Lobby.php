<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Factory_Zombies::factory()->set_range(10,30)
    ->set_strength(15, 2)->set_chance(0.2)

    ->add(Model_Combat_Zombies_Nurse::cls(), 5)
    ->add(Model_Combat_Zombies_Fatass::cls(), 2)
    ->add(Model_Combat_Zombies_Gusher::cls(), 1)
    ->add(Model_Combat_Zombies_Runner::cls(), 2);