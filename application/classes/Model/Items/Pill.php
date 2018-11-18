<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Pill extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Eine Pille',
			'icon' => 'pill/white',
			'description' => 'Leider hast du zu dieser Pille weder Packung noch Beipackzettel gefunden. Ein versierter Mediziner könnte sicherlich von der Farbe auf die Wirkung dieser Pille schließen - dir bleibt dafür wohl nur der Selbstversuch übrig.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_DRUG,
	);

    protected function hid(): Model_Hid {
        $child_effects = !Globals::shadowPlayerExists() && Globals::PrimaryPlayerF()->job(1080);

        return parent::hid()
            ->add_action('Runter damit!', Model_Action::factory()
                ->show_as(
                    Model_Effect::factory()
                        ->ambiguous_effect()
                        ->achieve(Model_Achievement::MA_PILL_EATER)
                        ->buff('Model_Buffs_Drug1', false, 48)
                        ->consume($this)
                , null, true)
                ->decider(function() {
                    return (!Globals::CurrentGameF()->config('items.pill.use_default_effect_proc')) ? (($this->type + Globals::CurrentUserF()->uid()) % $this->variants()) : $this->type;
                })
                //Red pill
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_ENERGY, 100)
                        ->effect(Model_Status::MS_STAT_SLEEPY, 100)
                        ->message('Augen zu und durch! Du schluckst die Pille herunter - und eine ungeahnte Energie durchströhmt dich! Du fühlst dich, als könntest du Bäume ausreißen!')
                )
                //Blue pill
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HUNGER, 100)
                        ->effect(Model_Status::MS_STAT_THIRST, 100)
                        ->message('Augen zu und durch! Du schluckst die Pille herunter - und fühlst plötzlich weder Hunger noch Durst! Das Zeug war ja der Hammer!')
                )
                //Green pill
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_ENERGY, $child_effects ? -80 : -35)
                        ->effect(Model_Status::MS_STAT_SLEEPY, $child_effects ? -100 : -80)
                        ->message('Augen zu und durch! Du schluckst die Pille herunter - und wirst plötzlich unglaublich müde.')
                )
                //Orange pill
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HEALTH, $child_effects ? -100 : -35)
                        ->message('Augen zu und durch! Du schluckst die Pille herunter - sofort durchzucken Krämpfe deinen Körper! Was immer das war, du hättest es besser nicht schlucken sollen!')
                )
                //White pill
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HEALTH, $child_effects ? 20 : 15)
                        ->effect(Model_Status::MS_STAT_SLEEPY, $child_effects ? -20 : -5)
                        ->effect(Model_Status::MS_STAT_DRUNK, $child_effects ? 5 : 0)
                        ->message('Augen zu und durch! Du schluckst die Pille herunter - und merkst sofort, wie sich dein Körper entspannt. Das fühlt sich gut an!')
                )
                //Green + Red pill
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HUNGER, -15)
                        ->effect(Model_Status::MS_STAT_THIRST, -25)
                        ->message('Augen zu und durch! Du schluckst die Pille herunter - und übrgibst dich direkt danach!')
                )
                //Black pill
                ->effect(
                    Model_Effect::factory()
                        ->custom(function($p) {
                            /** @var Model_Player $p */
                            foreach ($p->inventory()->get() as $item)
                                if (!$item->is_essential()) $item->grind();
                        })
                        ->message('Augen zu und durch! Du schluckst die Pille herunter. Was danach passiert, weißt du nicht mehr - aber dein ganzer Rucksack ist plötzlich leer! Wär hätte denn ahnen können dass ' . ((!Globals::CurrentGameF()->config('items.pill.use_default_effect_proc')) ? 'diese ' : 'die schwarze') . ' Pille Blackouts verursachen kann ...')
                )
                //Yellow pill
                ->effect(
                    Model_Effect::factory()
                        ->message('Augen zu und durch! Du schluckst die Pille herunter - plötzlich tut dein rechter Backenzahn weh. Wie unangenehm!')
                )
                //Red + Blue pill
                ->effect(
                    Model_Effect::factory()
                        ->buff('Model_Buffs_Heartbeat', true)
                        ->message('Augen zu und durch! ' . ((!Globals::CurrentGameF()->config('items.pill.use_default_effect_proc')) ? 'Eigentlich sieht sie sehr gesund aus, daher' : 'In der Hoffung, dies wäre eine Twinoid-Kapsel,') . ' schluckst du die Pille herunter. Tja, und wenn du das überlebt hättest, hättest du wohl gelernt dass das Aussehen auch täuschen kann.')
                )
                //Rose pill
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HEALTH, (Globals::CurrentGameF()->duration() & 1) ? 25 : -50 )
                        ->effect(Model_Status::MS_STAT_ENERGY, (Globals::CurrentGameF()->duration() & 1) ? 25 : -50 )
                        ->effect(Model_Status::MS_STAT_HUNGER, (Globals::CurrentGameF()->duration() & 1) ? 25 : 0 )
                        ->effect(Model_Status::MS_STAT_THIRST, (Globals::CurrentGameF()->duration() & 1) ? 25 : 0 )
                        ->message((Globals::CurrentGameF()->duration() & 1)
                            ? 'Augen zu und durch! Du schluckst die Pille herunter - wenige Sekunden später spürst du, wie sich eine angenehme Wärme in dir ausbreitet. Welch ein schönes Gefühl ...'
                            : 'Augen zu und durch! Du schluckst die Pille herunter - wenige Sekunden später beginnst du, dich unruhig und unwohl zu fühlen. Schmerzen zucken durch deinen Körper, während du dich auf dem Boden krümmst und hoffst, dass die Wirkung der Pille bald nachlässt.'
                        )
                )
                //Marine pill
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HEALTH, (Globals::CurrentGameF()->duration() & 1) ? -50 : 25 )
                        ->effect(Model_Status::MS_STAT_ENERGY, (Globals::CurrentGameF()->duration() & 1) ? -50 : 25 )
                        ->effect(Model_Status::MS_STAT_HUNGER, (Globals::CurrentGameF()->duration() & 1) ? 0 : 25 )
                        ->effect(Model_Status::MS_STAT_THIRST, (Globals::CurrentGameF()->duration() & 1) ? 0 : 25 )
                        ->message((Globals::CurrentGameF()->duration() & 1)
                                ? 'Augen zu und durch! Du schluckst die Pille herunter - wenige Sekunden später beginnst du, dich unruhig und unwohl zu fühlen. Schmerzen zucken durch deinen Körper, während du dich auf dem Boden krümmst und hoffst, dass die Wirkung der Pille bald nachlässt.'
                                : 'Augen zu und durch! Du schluckst die Pille herunter - wenige Sekunden später spürst du, wie sich eine angenehme Wärme in dir ausbreitet. Welch ein schönes Gefühl ...'
                        )
                )
                //Beige pill
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_DRUNK, 100)
                        ->effect(Model_Status::MS_STAT_ENERGY, 100)
                        ->effect(Model_Status::MS_STAT_HUNGER, 100)
                        ->effect(Model_Status::MS_STAT_THIRST, 100)
                        ->message('Augen zu und durch! Du schluckst die Pille herunter - für einen kurzen Moment fühlst du dich großartig, danach fängt alles um dich herum an sich zu drehen ...')
                )
                //Black + White pill
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_DRUNK, -100)
                        ->effect(Model_Status::MS_STAT_HEALTH, 10)
                        ->message('Augen zu und durch! Du schluckst die Pille herunter - sofort fühlst du, wie die Pille dich von innen reinigt. Sehr angenehm!')
                )
                //Pink pill
                ->effect(
                    Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HUNGER, 100)
                        ->buff('Model_Buffs_Nom', false, 120)
                        ->message('Augen zu und durch! Du schluckst die Pille herunter - und mit einem mal fühlst du dich extrem satt! Anscheinend war das irgend eine Nährstoffpille!')
                )
                //Red + Pink pill
                ->effect(
                    Model_Effect::factory()
                        ->buff('Model_Buffs_Hallucinations', false, $child_effects ? 48 : 24)
                        ->message('Augen zu und durch! Du schluckst die Pille herunter - aber es scheint nichts zu passieren. Moment... war die dreiköpfige Giraffe da hinten schon immer da?')
                )

            );
    }

	protected static $instances_info = Array(
			Array(	'name' => 'Rote Pille',				'icon' => 'pill/red'),
			Array(	'name' => 'Blaue Pille',			'icon' => 'pill/blue'),
			Array(	'name' => 'Grüne Pille',			'icon' => 'pill/green'),
			Array(	'name' => 'Orange Pille',			'icon' => 'pill/orange'),
			Array(	'name' => 'Weiße Pille',			'icon' => 'pill/white'),
			Array(	'name' => 'Grün-Rote Pille',		'icon' => 'pill/greenred'),
			Array(	'name' => 'Schwarze Pille',			'icon' => 'pill/black'),
			Array(	'name' => 'Gelbe Pille',			'icon' => 'pill/yellow'),
			Array(	'name' => 'Rot-Blaue Pille',		'icon' => 'pill/redblue'),
			Array(	'name' => 'Rosa Pille',				'icon' => 'pill/rose'),
			Array(	'name' => 'Türkise Pille',			'icon' => 'pill/turquoise'),
			Array(	'name' => 'Beige Pille',			'icon' => 'pill/beige'),
			Array(	'name' => 'Schwarz-weiße Pille',	'icon' => 'pill/blackwhite'),
            Array(	'name' => 'Pinke Pille',	        'icon' => 'pill/pink'),
            Array(	'name' => 'Pink-Rote Pille',	    'icon' => 'pill/redpurple'),
	);
	
	protected static $weight = 0.1;
	protected static $cat = Model_Items_Abstract_Item::MIAI_CAT_DRUG;
	
	public function mixchem($chemval): bool
    {
        $this->consume();
        switch ($chemval)
        {
            case 1:case 2:
                Tool_Scripts::chem_reaction(
                    'Du wirfst die Pille in die Chemikalie ... sie löst sich sofort und rückstandslos auf. Toll ...',
                    $chemval,$this);

                return false;
            case 3:case 4:case 5:
                Tool_Scripts::chem_reaction(
                    'Du wirfst die Pille in die Chemikalie ... es blubbert ein bisschen, und als du die Pille herausholst stellst du fest, dass sie die Farbe geändert hat!',
                    $chemval,$this, new self
                );

                return true;
            default:
                Tool_Scripts::chem_reaction(
                    'Du wirfst die Pille in die Chemikalie ... es blubbert relativ stark. Als du die Pille herausnehmen möchtest stellst du fest, dass plötzlich eine zweite, identische Pille im Reagenzglas liegt! Welch Wunder der Chemie!',
                    $chemval,$this, [new self($this->type), new self($this->type)]);
                return true;
        }
	}
}	