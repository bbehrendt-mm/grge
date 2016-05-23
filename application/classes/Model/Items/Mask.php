<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Mask extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Schamanenmaske',
			'icon' => 'shamask',
			'description' => 'Diese Schamanenmaske wurde seit Generationen in deiner Familie vererbt. Man sagt, ihr wohnen mystische Kräfte inne... hey, ist das da auf der Rückseite ein Made-In-China-Schriftzug?',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
	);

	protected static $weight = 0;
	protected static $essential = true;

    protected $level = 1;

	public $nextuse = 0;

    public function __construct($set_level) {
        parent::__construct();
        $this->level = $set_level;
    }

    protected function hid() {
        $hid = parent::hid();

        $hid->add_action('Sprühregen', Model_Action::factory()
                ->description('Erzeugt einen spontanen Sprühregen, der belagernde Zombies tötet.')
                ->deny_for(Interface_Plentity::IC_NPC_ANIMAL)
                ->condition(function($p) {
                    /** @global Model_Game $game */
                    global $game;

                    /** @var Model_Player $p */

                    if ($this->nextuse > $game->duration()) return 'time';
                    if ($p->location()->zombie_pop() <= 0) return 'zombies';
                    return true;
                })
                ->fail_message('Du kannst die Maske maximal einmal am Tag anwenden.', 'time')
                ->fail_message('Hier sind keine Zombies...', 'zombies')
                ->effect(
                    Model_Effect::factory()
                        ->message('Du setzt die Maske auf und fühlst, wie dich mystische Kräfte durchströhmen. Die Maske hat deinen Wunsch erhört!')
                        ->custom(function($p) {
                            /** @var Model_Player $p */
                            $kills = ceil($p->location()->zombie_pop()/($this->level >= 2 ? 2 : 4));
                            $p->location()->zombie_factory()->accumulation($p->location()->zombie_pop() - $kills);
                            $p->achievements()->achieve(Model_Achievement::MA_KILLED_ZOMBIES, $kills);

                            /** @global Model_Game $game */
                            global $game;
                            $this->nextuse = $game->duration() + 288;
                        })
                )
            );

        if ($this->level >= 3)
            $hid->add_action('Platzregen', Model_Action::factory()
                ->description('Verursacht heftige Regenfälle, die einiges an Wasser auf dem Boden zurücklassen.')
                ->deny_for(Interface_Plentity::IC_NPC_ANIMAL)
                ->condition(function($p) {
                    /** @global Model_Game $game */
                    global $game;

                    /** @var Model_Player $p */

                    if ($this->nextuse > $game->duration()) return 'time';
                    return true;
                })
                ->fail_message('Du kannst die Maske maximal einmal am Tag anwenden.', 'time')
                ->effect(
                    Model_Effect::factory()
                        ->message('Du setzt die Maske auf und fühlst, wie dich mystische Kräfte durchströhmen. Die Maske hat deinen Wunsch erhört!')
                        ->custom(function($p) {
                            /** @var Model_Player $p */
                            $splats = mt_rand(1, $this->level >= 4 ? 4 : 2);
                            for ($i = 0; $i < $splats; $i++)
                                $p->location()->inventory()->add(new Model_Items_Generic_Water0());

                            /** @global Model_Game $game */
                            global $game;
                            $this->nextuse = $game->duration() + 288;
                        })
                )
            );

        if ($this->level >= 5)
            $hid->add_action('Heilungsritual', Model_Action::factory()
                ->description('Jeder Spieler und jeder NPC in der aktuellen Zone regeneriert Gesundheit.')
                ->deny_for(Interface_Plentity::IC_NPC_ANIMAL)
                ->condition(function($p) {
                    /** @global Model_Game $game */
                    global $game;

                    /** @var Model_Player $p */

                    if ($this->nextuse > $game->duration()) return 'time';
                    return true;
                })
                ->fail_message('Du kannst die Maske maximal einmal am Tag anwenden.', 'time')
                ->effect(
                    Model_Effect::factory()
                        ->message('Du setzt die Maske auf und fühlst, wie dich mystische Kräfte durchströhmen. Die Maske hat deinen Wunsch erhört!')
                        ->custom(function($p) {
                            /** @var Model_Player $p */
                            foreach (Tool_Scripts::at_location($p->location_class(), true, true) as $pl)
                                $pl->get_status()->modify([Model_Status::MS_STAT_HEALTH, mt_rand(5, $this->level >= 6 ? 50 : 25)], Model_Status::MS_EFFECT_UNSCALE);

                            /** @global Model_Game $game */
                            global $game;
                            $this->nextuse = $game->duration() + 288;
                        })
                )
            );

        if ($this->level >= 7)
            $hid->add_action('Sandsturm', Model_Action::factory()
                ->description('Die Zone wird von einem heftigen Sandsturm getroffen, der Spieler und NPCs Schaden zufügt, belagernde Zombies tötet und die Fundchancen der Zone teilweise regeneriert.')
                ->deny_for(Interface_Plentity::IC_NPC_ANIMAL)
                ->condition(function($p) {
                    /** @global Model_Game $game */
                    global $game;

                    /** @var Model_Player $p */

                    if ($this->nextuse > $game->duration()) return 'time';
                    return true;
                })
                ->fail_message('Du kannst die Maske maximal einmal am Tag anwenden.', 'time')
                ->effect(
                    Model_Effect::factory()
                        ->message('Du setzt die Maske auf und fühlst, wie dich mystische Kräfte durchströhmen. Die Maske hat deinen Wunsch erhört!')
                        ->custom(function($p) {
                            /** @var Model_Player $p */
                            foreach (Tool_Scripts::at_location($p->location_class(), true, true) as $pl)
                                $pl->get_status()->modify([Model_Status::MS_STAT_HEALTH, mt_rand(-15, -2)], Model_Status::MS_EFFECT_UNSCALE);

                            $kills = ceil($p->location()->zombie_pop()/1.5);
                            $p->location()->zombie_factory()->accumulation($p->location()->zombie_pop() - $kills);
                            $p->achievements()->achieve(Model_Achievement::MA_KILLED_ZOMBIES, $kills);

                            $p->location()->hero_replensish(0.5);

                            /** @global Model_Game $game */
                            global $game;
                            $this->nextuse = $game->duration() + 288;
                        })
                )
            );

        return $hid;
    }
	
	public function drop_dead() {
		return null;
	}

    public function drop($p = null, $silent = false) {
        return false;
    }
}	