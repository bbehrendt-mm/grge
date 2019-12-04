<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add(Model_Items_Generic_Crwood::cls() , 1)
        ->add(Model_Items_Generic_Crmetal::cls(), 2)
        ;