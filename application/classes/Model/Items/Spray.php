<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Spray extends Model_Items_Abstract_Item implements Interface_Static {

    protected static $static_info = Array(
        'name' => 'Spray',
        'icon' => 'spray/spray0',
        'description' => 'Dieses Spray verbessert nicht nur deinen Körpergeruch um fast 12 Prozent, es verleiht dir außerdem verschiedene zusätzliche Boni.',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_DRUG,
    );

    protected static $instances_info = [
        [
            'name'        => 'Kampfspray (M²K)',
            'icon'        => 'spray/spray1',
            'description' => 'Dieses Spray erhöht für eine Stunde den Schaden, den du im Kampf anrichtest - allerdings auf Kosten deiner Gesundheit. Du kannst den Effekt auf verstärken, indem du mehrere Kampfsprays einsetzt...',
        ],
        [
            'name'        => 'Berserker-Spray (M²Be)',
            'icon'        => 'spray/spray2',
            'description' => 'Dieses Spray verwandelt dich in einen Berserker - du wirst im Kampf massiven Schaden anrichten, kannst allerdings nur mit bloßen Händen kämpfen.',
        ],
    ];

    protected static $weight = 2;

    protected function hid(): Model_Hid {
        $f = function() {
            switch ($this->type) {
                case 0: return 'apl1';
                case 1: return 'apl2';

                default: return 'apl0';
            }
        };

        return parent::hid()
            ->add_action('Applizieren', Model_Action::factory()
                ->effect(Model_Effect::factory()
                    ->consume($this)
                    ->spawn(Model_Items_Generic_Spraycan::cls(), 1)
                    ,'apl0')
                ->effect(Model_Effect::factory()
                    ->consume($this)
                    ->spawn(Model_Items_Generic_Spraycan::cls(), 1)
                    ->buff(Model_Buffs_Spray1::cls(), false,12)
                    ,'apl1')
                ->effect(Model_Effect::factory()
                    ->consume($this)
                    ->spawn(Model_Items_Generic_Spraycan::cls(), 1)
                    ->buff(Model_Buffs_Spray2::cls(), false,12)
                    ,'apl2')
                ->export($f)
                ->decider($f)
            );
    }
}