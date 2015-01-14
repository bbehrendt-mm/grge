<?php defined('SYSPATH') or die('No direct access allowed.');

return Model_Blueprints::factory()

    ->add_blueprints(Model_Blueprint::factory()->id('ktc_grill')->name('Grillstation'), true)

    // ++ STACK -> All blueprints below can be produced indefinitely and require local facilities
    ->push_stack(function(&$b) {/** @var Model_Blueprint $b */$b->steps(0)->requires('ktc_grill');})

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:grill1')
            ->name('Gegrillte Leiche')
            ->message('Mit einem Grill kannst du selbst das ekelhafteste Zeug schmackhaft machen. Herzlichen Glückwunsch, du hast ein paar Steaks erzeugt.')
            ->energy(10)
            ->material(['Model_Items_Body' => 1])
            ->produces(['Model_Items_Steak' => 3])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:grill2')
            ->name('Gegrillter Zombie')
            ->message('Mit einem Grill kannst du selbst das ekelhafteste Zeug schmackhaft machen. Herzlichen Glückwunsch, du hast ein paar Steaks erzeugt.')
            ->energy(10)
            ->material(['Model_Items_Body2' => 1])
            ->produces(['Model_Items_Steak' => 2])
    )

    ->add_blueprints(
        Model_Blueprint::factory()
            ->id('i:grill3')
            ->name('Gegrillter Tierkadaver')
            ->message('Mit einem Grill kannst du selbst das ekelhafteste Zeug schmackhaft machen. Herzlichen Glückwunsch, du hast ein paar Steaks erzeugt.')
            ->energy(5)
            ->material(['Model_Items_Body3' => 1])
            ->produces(['Model_Items_Steak' => 1])
    )

    ->drop_stack();