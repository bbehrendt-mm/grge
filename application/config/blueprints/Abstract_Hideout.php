<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('hideout')
            ->name('Versteck')
            ->description('Ermöglicht es dir, diesen Ort als Versteck zu nutzen.')
            ->energy(20)
            ->material('Model_Items_Generic_Wood',2)
            ->effect(
                Model_Effect::factory()
                    ->custom(function($p) {
                        /** @var Model_Player $p */
                        $l = $p->location();
                        /** @var Model_Places_Abstract_Hideout $l */
                        $l->set_decay(0);
                    })
            )
    )
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('bedroom1')
            ->name('Schlafecke')
            ->description('Verbessert Regeneration von Energie und Müdigkeit beim Schlafen. Ermöglicht außerdem die Regeneration von Gesundheit beim Schlafen.')
            ->energy(5)
            ->material('Model_Items_Generic_Bed',1)
            ->requires('hideout')
    )
    ->validate();