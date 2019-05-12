<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()

    // ++ STACK -> All blueprints below benefit from daytime and handyman bonus, and give builder achievement
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */
        $b->add_modifier_builder();
    })

    // Caravan Room
    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('caravanplus')
            ->name('Anhänger ankoppeln')
            ->description('Erhöht die Anzahl der permanenten Räume deines Wohnmobils um 1.')
            ->steps(0)
            ->energy(5)
            ->requires_room('motorhome')
            ->material(Model_Items_Generic_Caravan::cls(),1)
            ->material(Model_Items_Generic_Sum::cls(),1)
            ->category('Wohnwagen')
            ->effect(Model_Effect::factory()
                ->custom(function(Model_Player $p) {
                    /** @var Model_Places_Motorhome $l */
                    $l = $p->location();
                    $l->add_permanent_room();
                })
        )
    )

    ->pop_stack()
    ;