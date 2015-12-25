<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Basefood extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Gewöhnliche Nahrung',
			'icon' => 'basefood/generic',
			'description' => '',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);
	
	protected static $instances_info = Array(
			Array(	'name' => 'Angebissener Apfel',
					'icon' => 'basefood/apple',
					'description' => 'Um Himmels Willen, sei bloß vorsichtigt! Wenn man dich mit einem angebissenen Apfel auf der Straße erwischt tauchen Anwälte aus dem Nichts heraus auf und verprügeln dich mit überteuerten Tablet-Computern!'),
			Array(	'name' => 'Brotlaib',
					'icon' => 'basefood/bread',
					'description' => 'Endlich bist du vernünftig dafür ausgerüstet, die Enten im Teich zu füttern. Leider sind mittlerweise sowohl Teich als auch Enten einer zombieverseuchten Ödniss gewichen. Dann musst du diesen Brotlaib wohl selbst essen...'),
			Array(	'name' => 'Offene Konservendose',
					'icon' => 'basefood/can',
					'description' => 'Konservendosen überleben alle möglichen Arten von Weltuntergängen - Asteroideneinschläge, Nukleare Explosionen, Maya-Apokalypsen und sogar Landtagswahlen! Guten Appetit!'),
			Array(	'name' => 'Schokoriegel',
					'icon' => 'basefood/chocbar',
					'description' => 'Erinnerst du dich noch an das kleine, dicke Kind, dass auf dem Schulhof immer alleine in einer Ecke stand und an einem Schokoriegel gelutscht hat? Tja, das fette Kind hat inzwischen einen Hunger auf Menschenfleisch entwickelt und hat daher sicher nichts dagegen, dass du dich an seinen Schokoriegeln bedienst.'),
			Array(	'name' => 'Kekse',
					'icon' => 'basefood/cookies',
					'description' => 'KEKSE!!!!'),
			Array(	'name' => 'Ei',
					'icon' => 'basefood/egg',
					'description' => 'Dies ist ein einfaches, braunes Hühnerei, das ein paar Jahre in der Sonne gelegen hat. Daran solltest du aber besser gar nicht denken, wenn du es isst...'),
			Array(	'name' => 'Blasenkaugummi',
					'icon' => 'basefood/gum',
					'description' => 'Kaugummi ist nicht unbedingt ein vollwertiger Ersatz für eine richtige Mahlzeit, aber immer noch besser als nichts. Außerdem kannst du es aufblasen, damit es größer erscheint.'),
			Array(	'name' => 'Abgestandene Nudelsuppe',
					'icon' => 'basefood/noodles',
					'description' => 'Diese Nudelsuppe stärkt deine Anwehkräfte. Nur leider ist dein Immunsystem allgemein relativ machtlos gegen spitze Zähne und Krallen. Naja, zumindest stillt diese Suppe deinen Hunger...'),
	);
	
	protected static $weight = 1;

    protected function hid() {
    return parent::hid()
        ->add_action('Essen', Model_Action::factory()
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HUNGER, 20)
                        ->consume($this)
                        ->message('Das war lecker. Du fühlst, wie sich dein Hunger langsam in Luft auflöst.')
                )
        );
    }
	
	public function mixchem($chemval) {
		$this->consume();
        switch ($chemval)
        {
            case 4:case 5:case 6:
                Tool_Scripts::chem_reaction(
                    'Du tunkst das Essen in die Chemikalie - und beginnt zu blubbern und löst sich vor deinen Augen auf! So ein Ärger, das wirst du wohl nicht mehr essen können...',
                    $chemval,$this);
                return false;
            case 10:
                Tool_Scripts::chem_reaction(
                    'Du tunkst das Essen in die Chemikalie - und beginnt zu blubbern und löst sich vor deinen Augen in seine Bestandteile auf! Zurück bleibt nur eine große glibbrige Masse Nährschleim. Lecker ...',
                    $chemval,$this, [new Model_Items_Nutrient,new Model_Items_Nutrient]);
                return true;
            default:
                Tool_Scripts::chem_reaction(
                    'Du tunkst das Essen in die Chemikalie - und beginnt zu blubbern und löst sich vor deinen Augen in seine Bestandteile auf! Zurück bleibt nur eine große glibbrige Masse Nährschleim. Lecker ...',
                    $chemval,$this, new Model_Items_Nutrient);
                return true;
        }
	}

}	