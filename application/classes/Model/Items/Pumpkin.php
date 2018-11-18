<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Pumpkin extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Essbarer Kürbis',
			'icon' => 'pumpkinedb',
			'description' => 'Ein essbarer Kürbis! Dieses riesige Teil stillt deinen Hunger garantiert - allerdings musst du die harte Schale erstmal aufbekommen...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);	

	protected static $weight = 15;

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Essen', Model_Action::factory()
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HUNGER, 100)
                        ->effect(Model_Status::MS_STAT_ENERGY, -30)
                        ->consume($this)
                        ->message('Du schlägst deine Zähne in das Kürbisfleisch - so muss es sich anfühlen, ein Zombie zu sein!')
                )
            );
    }

	public function mixchem($chemval): bool
    {
        $this->consume();
        switch ($chemval)
        {
            case 1:
                Tool_Scripts::chem_reaction(
                    'Der Kürbis saugt die Chemikalie auf und verliert etwas an Farbe... jetzt kannst du ihn nur noch als Zierkürbis verwenden.',
                    $chemval,$this,new Model_Items_Generic_Pumpkin);
                return false;
            default:
                Tool_Scripts::chem_reaction(
                    'Der Kürbis saugt vor deinen Augen die Chemikalie auf... und beginnt, in Zeitraffer zu faulen!',
                    $chemval,$this, new Model_Items_Pumpkin2);
                return true;
        }
	}
}	