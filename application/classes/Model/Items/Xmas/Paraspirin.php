<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Xmas_Paraspirin extends Model_Items_Abstract_Stackable implements Interface_Event {

	protected static $static_info = Array(
			'name' => 'Paraspirin',
			'icon' => 'paraspirine',
			'description' => 'Paraspirin ist ein leichtes Schmerzmittel. In der Postapokalypse ist es eher weniger nützlich, da sich sein Wirkungsbereich nicht auf Bissverletzungen, gebrochene Knochen oder abgerissene Körperteile ausdehnt. Allerdings gibt es Gerüchte, nach der Einnahme würden sich die Effekte von Alkohol verstärken...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_DRUG,
	);

	protected static $autospawn = Array(5,15);
	protected static $weight = 1;

    protected function hid() {
        return parent::hid()
            ->add_action('Schlucken', Model_Action::factory()
                ->allow_auto(false)
                ->effect(
                    Model_Effect::factory()
                        ->buff('Model_Buffs_Paraspirin', false, 24)
                        ->consume($this)
                        ->message('Lecker... mit Zitronengeschmack!')
                )
            );
    }
}	