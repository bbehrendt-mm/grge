<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add(Model_Items_Body::cls() , 5)
        ->add(Model_Items_Body2::cls(), 1)
        ->add(Model_Items_Body3::cls(), 2)
        ;
