<?php defined('SYSPATH') or die('No direct access allowed.');

    return Model_Factory_Items::factory()
        ->add('Model_Items_Magazine', 4)
        ->add('Model_Items_Book'    , 1)
        ->add('Model_Items_Pamphlet', 1)
        ;