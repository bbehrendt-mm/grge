<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Greenhouse extends Model_Items_Abstract_Virtual {

    protected $remaining = array(
        'water_plant' => PHP_INT_MAX
    );

    protected function hid() {
        return parent::hid()->add_action('Seltsames Gewächs gießen', Model_Action::factory()
                ->description('Achtung: Diese Aktion wird sämtliches Wasser auf dem Boden verbrauchen!')
                ->show_as(Model_Effect::factory()
                    ->ambiguous_effect()
                )
                ->effect(Model_Effect::factory()
                    ->custom(function($p) {
                        /** @var Model_Player $p */
                            if (!($water = $p->location()->inventory()->get('Model_Items_Abstract_Liquid'))) {
                                $p->location()->log()->add(new Model_Log_Types_Text(null, null, 'Du benötigst Wasser zum Gießen... Fülle das Wasser, das du verwenden willst, aus deiner Flasche auf den Boden.'));
                                return;
                            }

                            $tox = 0;
                            foreach ($water as $item) {
                                $tox += $item->toxicity();
                                $item->consume();
                            }

                            $tox = $tox / count($water);
                            if (count($water) == 1) $p->location()->log()->add(new Model_Log_Types_Text(null, null, 'Du gießt das Wasser auf den Boden um das Gewäch herum. Überraschenderweise passiert nichts spannendes, doch als du dich gerade umdrehen und wieder gehen willst lässt dir das Gewächs eine Frucht vor die Füße fallen... Ob das seine Art war, Danke zu sagen?'));
                            else $p->location()->log()->add(new Model_Log_Types_Text(null, null, 'Du gießt das Wasser auf den Boden um das Gewäch herum. Überraschenderweise passiert nichts spannendes, doch als du dich gerade umdrehen und wieder gehen willst lässt dir das Gewächs :num Früchte vor die Füße fallen... Ob das seine Art war, Danke zu sagen?', array(':num' => count($water))));

                            for ($i = 0; $i < count($water); $i++)
                                if		($tox <= 1)		$p->location()->inventory()->add(new Model_Items_Vedge);
                                elseif	($tox <= 8)		$p->location()->inventory()->add(new Model_Items_Vedge2);
                                elseif	($tox <= 16)	$p->location()->inventory()->add(new Model_Items_Vedge3);
                                elseif	($tox <= 32)	$p->location()->inventory()->add(new Model_Items_Vedge4);
                                else					$p->location()->inventory()->add(new Model_Items_Vedge5);
                        })
                )
            , 'water_plant');
    }
}	