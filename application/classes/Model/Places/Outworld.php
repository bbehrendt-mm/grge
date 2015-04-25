<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Outworld extends Model_Places_Abstract_Node {
	
	protected static $name = 'Die Umgebung des Verstecks';
	protected static $description = 'Früher blühte hier das Leben, jetzt findet man hier nur noch Sand und gelegentlich ein paar Zombies, die in kleinen Grüppchen die Ruinen der Zivilisation umstreifen. Unwahrscheinlich, dass du hier etwas nützliches findest. Eventuell findest du aber das ein oder andere Gebäude, das du nach nützlichen Dingen durchsuchen kannst.';

	private $initial_supply = false;
	
	protected $survival_find = true;
    protected $tickets = Array();
	
	private function initial_supply() {
		global $game;
        $this->initial_supply = true;

        if ($game->config('places.outworld.spawn_stranger')) {
            $this->inventory->add(new Model_Items_Body('Leiche eines Schnitzeljägers', 'Sieht so aus als hätte dieser arme Tropf an einer Schnitzeljagt teilgenommen... seine linke Hand hält ein paar unleserliche Schriftstücke fest umklammert, seine rechte einen Text, der mit "Dayan" unterschrieben ist...'));
            $this->inventory->add(new Model_Items_Paracetoid);
            $this->inventory->add(new Model_Items_Paracetin);
            $this->inventory->add(new Model_Items_Machete);
            $this->inventory->add(new Model_Items_Ammobelt);
            $this->inventory->add(new Model_Items_Batgun);
            $this->inventory->add(new Model_Items_Tentkit);

            //Water bottle
            $bottle = new Model_Items_Bottle;
            $bottle->add_water(3, 25);
            $this->inventory->add($bottle);

            $this->log->add(new Model_Log_Types_Text('Verschiedene Gegenstände gefunden', 'Ein hilfreicher Fund', 'Nach nur ein paar Metern findest du ein notdürftig aufgeschlagenes Lager - der Besitzer ist wohl im Schlaf überrascht worden. Naja, wenigstens wird er dann wohl nichts mehr dagegen haben wenn du dich an seiner Ausrüstung bedienst ...'));
        }

        if ($game->config('places.outworld.alt_spawn_stranger')) {
            $this->inventory->add(new Model_Items_Body('Leiche eines Reporters', 'Er hat wohl gehofft, mit der Story über die Zombie-Apokalypse den Pulizer-Preis zu gewinnen. Hoffen wir mal für ihn, dass der auch posthum verliehen wird...'));
            $this->inventory->add(new Model_Items_Paracetoid);
            $this->inventory->add(new Model_Items_Paracetin);
            $this->inventory->add(new Model_Items_Lunchbox());
            $this->inventory->add(new Model_Items_Sportsdrink());
            $this->inventory->add(new Model_Items_Tentkit);

            $this->log->add(new Model_Log_Types_Text('Verschiedene Gegenstände gefunden', 'Ein hilfreicher Fund', 'Nach nur ein paar Metern findest du ein notdürftig aufgeschlagenes Lager - der Besitzer ist wohl im Schlaf überrascht worden. Naja, wenigstens wird er dann wohl nichts mehr dagegen haben wenn du dich an seiner Ausrüstung bedienst ...'));
        }
	}

    public function pretick() {
        /**
         * @global $game Model_Game
         */
        global $game;

        //Check for zombie attack
        if ($this->initial_supply || !$game->config('places.outworld.spawn_stranger'))
            parent::pretick();
    }

	public function tick() {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
        global $game, $player;
					
		if (!$this->initial_supply && ($game->config('places.outworld.spawn_stranger') || $game->config('places.outworld.alt_spawn_stranger')))
		{
			$this->initial_supply();
			return true;	
		}

		return parent::tick();
	}

    protected function create_npcs() {
        global $game;

        $php53bb = $this;
        $ret = parent::create_npcs();

        if (Tool_Events::current($game->next_tick()) == 'halloween' && !$game->setting_mode(2000))
            $ret['halloween'] = Model_Npc::factory()->name('Seelensammler')
                ->add_action('Ansprechen', Model_Action::factory()
                        ->condition(function($p) {
                            /** @var Model_Player $p */
                            return !(bool)$p->buff_retr('soulcatcher');
                        })
                        ->fail_message('... Träger des Zeichens ... begib dich auf deine Reise ... die gequälten Seelen zu befreien.')
                        ->effect(Model_Effect::factory()
                                ->message('... Wanderer ... der du über dieses grausame Land schreitest ... hilf mir, gequälte Seelen zu reinigen und du sollst ... belohnt werden.')
                        )
                )
                ->add_action('Seelenfänger werden', Model_Action::factory()
                        ->condition(function($p) {
                            /** @var Model_Player $p */
                            return !(bool)$p->buff_retr('soulcatcher');
                        })
                        ->fail_message('... du trägst das Zeichen des Seelenfängers ... bereits!')
                        ->effect(Model_Effect::factory()
                                ->buff('Model_Buffs_Soulcatcher')
                                ->message('... so gehe nun hinaus in die Welt ... und verrichte mein Werk ...')
                        )
                )
                ->add_action('Seelen übergeben', Model_Action::factory()
                        ->condition(function($p) {
                            /** @var Model_Player $p */
                            return (Tool_Scripts::count_available_items('Model_Items_Soul', true, false, false, $p) + Tool_Scripts::count_available_items('Model_Items_Soul2', true, false, false, $p) > 0);
                        })
                        ->fail_message('... du hast keine Seelen ... bei dir.')
                        ->effect(Model_Effect::factory()
                                ->custom(function($p) {
                                    /** @global Model_User $user */
                                    global $user;

                                    /** @var Model_Player $p */
                                    $ws = Tool_Scripts::count_available_items('Model_Items_Soul', true, false, false, $p);
                                    $ss = Tool_Scripts::count_available_items('Model_Items_Soul2', true, false, false, $p);

                                    Tool_Scripts::consume_available_items(array('Model_Items_Soul' => $ws, 'Model_Items_Soul2' => $ss), true, false, false, $p);
                                    $points = $ws + 5 * $ss;

                                    $user->award_universal_soulpoints($p->user_id(), $points);
                                    $p->log()->add(new Model_Log_Types_Text(null, null, 'Du hast :total Seelen die Freiheit geschenkt und wirst dafür mit :usp Universal-Seelenpunkten belohnt!', array(':total' => $ws + $ss, ':usp' => $points)));
                                })
                        )
                )
            ;

        if (Tool_Events::current($game->next_tick()) == 'xmas' && !$game->setting_mode(2000))
            $ret['xmas'] = Model_Npc::factory()->name('Der Schaffner')
                ->add_action('Ticket übergeben', Model_Action::factory()

                        ->requirement('Model_Items_Generic_Ticket', 1)
                        ->effect(Model_Effect::factory()
                                ->message('Du schließt für einen Moment deine Augen... als du sie wieder öffnest, stehst du plötzlich auf einem verlassenen Weihnachtsmarkt! In der Mitte des Markts steht eine leere Weihnachtsbaum-Halterung. Wie traurig... du solltest dich vom Geist der Weihnacht erfüllen lassen und dort einen wunderschön geschmückten Weihnachtsbaum aufstellen! Sicherlich wirst du dafür genug Materialien hier finden...')
                                ->custom(function($p) use ($php53bb) {
                                    /** @var Model_Player $p */
                                    global $game;

                                    $tid = time() . '_' . mt_rand();
                                    $mapid = "xmasmap_{$tid}";
                                    $xmas_id = $game->register_map($mapid, 'xmas', 'xmas');
                                    $xmasfair = $game->location($xmas_id);
                                    $xmasfair->register_doorway($this->uin);

                                    $php53bb->leave_map($p->id());
                                    $p->location_class($xmas_id);
                                    $xmasfair->enter_map($p->id());


                                    $game->map($xmas_id)->movement_modifier(0.1);
                                    if (!$p->buff_retr('freeze'))
                                        new Model_Buffs_Freeze($p->id());
                                })
                        )
                )
            ;

        return $ret;
    }
}	