<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Mental extends Model_Places_Abstract_Place {
	
	protected static $name = 'Verlassene Irrenanstalt';
	protected static $description = 'Vor langer Zeit war dieses Gebäude mal ein normales Krankenhaus. Irgendwann wurde es zu einer "Heilanstalt für Geisteskranke" umfunktioniert. Gerüchte besagen, dass niemand, der dort eingeliefert wurde, jemals wieder herausgekommen ist. Natürlich ist das Gebäude längst verlassen, es gibt also überhaupt keinen Grund vor irgendwas dort drin Angst zu haben. Obwohl es so scheint als würden selbst die Zombies dieses Gebäude meiden ...';
    protected static $icon = 'mental';
    protected static $outside = false;
    protected $has_patient = false;

	private $mentalstate = 0;
	
	public function pretick() {
		global $game;

        if (mt_rand(0,10) > 3) return true;
		if (count(Tool_Scripts::at_location($this->uin())) <= 0) return true;

		switch ($this->mentalstate)
		{
			case 0:
                $this->log->add(new Model_Log_Types_Text('Erforschung der Irrenanstalt', 'Merkwürdige Ereignisse...', 'Dir läuft ein Schauer über den Rücken, als du durch die schlecht beleuchteten Gänge schleichst ...'));
			    break;
			case 1:
                $this->log->add(new Model_Log_Types_Text('Erforschung der Irrenanstalt', 'Merkwürdige Ereignisse...', 'Du siehst Blutspuren an der Wand. Die müssen entstanden sein, als die Anstalt von Zombies überrannt wurde. Allerdings sieht das Blut überraschend frisch aus ...'));
                if (Tool_Events::current($game->next_tick()) == 'halloween' && !$this->has_patient) {
                    $this->has_patient = true;
                    $this->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER, 'Verstörter Patient'));
                }
                break;
			case 2:
                $this->log->add(new Model_Log_Types_Text('Erforschung der Irrenanstalt', 'Merkwürdige Ereignisse...', 'Du hörst ein knackendes Geräusch hinter dir, und spürst einen Luftzug. Du machst dich bereit, auf Zombies zu treffen. Aber nichts geschieht ...'));
			    break;
			case 3:
                $this->log->add(new Model_Log_Types_Text('Erforschung der Irrenanstalt', 'Erschreckende Ereignisse...', 'Hinter einem Tresen liegen ein paar aufgestapelte Leichen. Sie scheinen ausschließlich Verletzungen am Hals zu haben, der Rest ist unversehrt. Untypisch für Zombies...'));
                $r_bodies = mt_rand(2, 5);
                for ($i = 0; $i < $r_bodies; $i++) $this->inventory->add(new Model_Items_Body);
                break;
			case 4:
                $this->log->add(new Model_Log_Types_Text('Erforschung der Irrenanstalt', 'Erschreckende Ereignisse...', 'Du findest einen Spiegel auf dem Boden, auf dem ein blutverschmierter Teddybär liegt. Das Blut scheint frisch zu sein ... Was ist nur in dieser Anstalt geschehen? Du verspürst den immer stärker werdenden Drang, dieses Gebäude zu verlassen und nie wieder zurückzukehren...'));
			    $this->inventory->add(new Model_Items_Generic_Cursed);
			    break;
			case 5:
                $this->log->add(new Model_Log_Types_Text('Erforschung der Irrenanstalt', 'Erschreckende Ereignisse...', 'Du fühlst erneut einen Luftzug, dann spürst du wie sich etwas von hinten nähert. Noch während du dich umdrehst siehst du etwas aufblitzen, dann fühlst du etwas Kaltes an deinem Hals. Dann wird alles um dich herum schwarz. Herzlichen Glückwunsch, du bist tot.'));
			
                $s_player = Tool_Scripts::at_location($this->uin());
                $s_player = $s_player[mt_rand(0, count($s_player) - 1)];

                $s_player->achievements()->achieve(Model_Achievement::MA_SLASHER_KILLER);

                $s_player->set_cod("Serienkiller-Opfer");
                    $s_player->buff_retr('heartbeat')->unbuff();
                $this->mentalstate = 4;
			    break;
            default:
                $this->mentalstate = 0;
		}
		
		$this->mentalstate++;
		return true;
	}

    protected function create_npcs() {
        global $game;
        $ret = parent::create_npcs();

        if (Tool_Events::current($game->next_tick()) == 'halloween' && $this->has_patient)
            $ret['halloween'] = Model_Npc::factory()->name('Verstörter Patient')
                ->add_action('Ansehen', Model_Action::factory()
                        ->effect(Model_Effect::factory()
                                ->message('Er sieht wie ein Patient dieser Einrichtung aus. Sein Hemd ist voller Blut, und er umklammert irgend etwas mit beiden Händen während er mit leerem Blick die Gänge der Anstalt schleicht. Auf Zurufe reagiert er nicht... anscheinend nimmt er dich nicht einmal wahr. Was er da wohl dabei hat... du könntest versuchen, es ihm wegzunehmen. Immerhin sieht er nicht sehr wehrhaft aus.')
                        )
                )
                ->add_action('Bestehlen', Model_Action::factory()
                        ->show_as(Model_Effect::factory()
                                ->ambiguous_effect()
                        )
                        ->effect(Model_Effect::factory()
                                ->custom(function($p) {
                                    /** @var Model_Player $p */
                                    $this->log()->add(new Model_Log_Types_Battle('Als du versuchst nach ihm zu greifen, beginnt der Patient markerschütternd zu schreien und greift an!', Tool_Scripts::battle([new Model_Battle_Patient(1,10)], Tool_Scripts::at_location($this->uin()), false, $battle, $count)));

                                    /** @var Model_Battle_Battle $battle */
                                    if ($battle->get_zombie_count() == 0) {
                                        $items = array();
                                        $b = new Model_Items_Body('Verstörter Patient', 'Der Patient trägt ein Identifikationsarmband, auf dem sich ein Barcode sowie ein Name befindet. Du wirst wohl nie erfahren, wer das war oder was mit ihm in der Irrenanstalt geschehen ist. Wobei... vermutlich willst du das auch lieber gar nicht wissen.');
                                        $b->give_name(Model_User::random_names(1)[0]);
                                        $items[] = $b;
                                        $items[] = new Model_Items_Hacksaw();
                                        $num = mt_rand(5,20);
                                        for ($i = 0; $i < $num; $i++)
                                            $items[] = new Model_Items_Fleshfood();

                                        Tool_Scripts::place_new_item($items, 'Der Patient hat sich mächtig gewehrt, aber letztendlich bist du doch an seine Gegenstände gekommen.');
                                        $this->has_patient = false;
                                    }
                                })
                        )
                )
                ->add_action('Teddy geben', Model_Action::factory()
                        ->requirement("Model_Items_Generic_Teddy", 1)
                        ->effect(Model_Effect::factory()
                                ->custom(function($p) {
                                    /** @var Model_Player $p */
                                    $p->log()->add('Du hälst ihm deinen Teddy hin. Er sieht ihn mit glasigen Augen an, und greift nach ein paar Sekunden zu. Irgendetwas scheint ihn enttäuscht zu haben, denn er schleicht mit hängenden Schultern davon.');

                                    $items = array();
                                    $items[] = new Model_Items_Hacksaw();
                                    $num = mt_rand(5,20);
                                    for ($i = 0; $i < $num; $i++)
                                        $items[] = new Model_Items_Fleshfood();

                                    $this->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_LEAVE, 'Verstörter Patient'));
                                    Tool_Scripts::place_new_item($items, 'Der Patient hat seine Gegenstände fallen gelassen, als du ihm den Teddy gegeben hast.');
                                    $this->has_patient = false;
                                })
                        )
                )
                ->add_action('Anderen Teddy geben', Model_Action::factory()
                        ->requirement("Model_Items_Generic_Cursed", 1)
                        ->effect(Model_Effect::factory()
                                ->custom(function($p) {
                                    global $game;

                                    /** @var Model_Player $p */
                                    $p->log()->add('Du hälst ihm deinen Teddy hin. Eine Träne läuft ihm aus dem Auge, dann greift er zu und drückt den Teddy fest an sich. Eine Weile verharrt er regungslos, dann zeigt er mit dem Finger auf einen dunklen Gang, der dir bisher verborgen geblieben ist. Als du dich wieder zu ihm umdrehst, ist er verschwunden...');

                                    $items = array();
                                    $items[] = new Model_Items_Hacksaw();
                                    $num = mt_rand(5,20);
                                    for ($i = 0; $i < $num; $i++)
                                        $items[] = new Model_Items_Fleshfood();

                                    $this->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_LEAVE, 'Verstörter Patient'));
                                    Tool_Scripts::place_new_item($items, 'Der Patient hat seine Gegenstände fallen gelassen, als du ihm den Teddy gegeben hast.');
                                    $this->has_patient = false;

                                    $slid = $game->register_map("submap_ashide_{$this->uin()}", 'ashide');
                                    if ($slid) {
                                        $this->register_doorway($slid);
                                        $game->location($slid)->register_doorway($this->uin());
                                    }
                                })
                        )
                )
            ;
        return $ret;
    }


}	