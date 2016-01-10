<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Alcohol extends Model_Items_Abstract_Item {
	
	protected static $alcohol = 10;
    protected static $energy = 10;
    protected static $thirst = 20;
    protected static $additional_effects = [];
	protected static $cat = Model_Items_Abstract_Item::MIAI_CAT_FOOD;

    protected function hid() {
        /**
         * @global Model_Player $player
         */
        global $player;
        $a = static::$alcohol * (Tool_Scripts::get_timeofday() == "evening" ? 0.75 : 1);
        return parent::hid()
            ->add_action('Trinken', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_DRUNK, $a)
                            ->effect(Model_Status::MS_STAT_ENERGY, static::$energy)
                            ->effect(Model_Status::MS_STAT_THIRST, static::$thirst)
                            ->effect(static::$additional_effects)
                            ->consume($this)
                            ->spawn('Model_Items_Smallbottle')
                            ->message('Das tut gut ... nach einem ordentlichen Drink sieht die Welt gleich weniger apokalyptisch aus!')
                    ,'s1')
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_DRUNK, -12)
                            ->effect(Model_Status::MS_STAT_ENERGY, 5)
                            ->effect(Model_Status::MS_STAT_THIRST, -20)
                            ->effect(Model_Status::MS_STAT_HUNGER, -40)
                            ->achieve(Model_Achievement::MA_ALCOHOLIC)
                            ->consume($this)
                            ->spawn('Model_Items_Smallbottle')
                            ->message('Die Welt um dich herum dreht sich bereits mit bedenklicher Geschwindigkeit, aber einer geht sicher noch rein! ... denkst du, kurz bevor sich dir der Magen umdreht und seinen Inhalt zu Tage fördert.')
                    ,'s2')
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_DRUNK, $a)
                            ->effect(Model_Status::MS_STAT_ENERGY, static::$energy)
                            ->effect(Model_Status::MS_STAT_HEALTH, -static::$alcohol)
                            ->effect(Model_Status::MS_STAT_THIRST, static::$thirst)
                            ->effect(static::$additional_effects)
                            ->consume($this)
                            ->spawn('Model_Items_Smallbottle')
                            ->message('Eigentlich kann man ja mit dem Trinken nie früh genug anfangen. Nachdem du die Flasche ausgetrunken hast, stellst du diese Aussage jedoch spontan in Frage - immerhin dreht sich die Welt um dich herum, und dir ist speiübel.')
                        ,'s3')
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_DRUNK, $a)
                            ->effect(Model_Status::MS_STAT_ENERGY, static::$energy)
                            ->effect(Model_Status::MS_STAT_THIRST, static::$thirst)
                            ->effect(static::$additional_effects)
                            ->buff('Model_Buffs_Drunk')
                            ->consume($this)
                            ->spawn('Model_Items_Smallbottle')
                            ->achieve(Model_Achievement::MA_ALCOHOLIC)
                            ->message('Das tut gut ... nach einem ordentlichen Drink sieht die Welt gleich weniger apokalyptisch aus! Aber warum kommt der Boden plötzlich auf dich zugeflogen?')
                        ,'s4')
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_DRUNK, $a)
                            ->effect(Model_Status::MS_STAT_ENERGY, static::$energy)
                            ->effect(Model_Status::MS_STAT_HEALTH, -static::$alcohol)
                            ->effect(Model_Status::MS_STAT_THIRST, static::$thirst)
                            ->effect(static::$additional_effects)
                            ->buff('Model_Buffs_Drunk')
                            ->consume($this)
                            ->spawn('Model_Items_Smallbottle')
                            ->achieve(Model_Achievement::MA_ALCOHOLIC)
                            ->message('Eigentlich kann man ja mit dem Trinken nie früh genug anfangen. Nachdem du die Flasche ausgetrunken hast, stellst du diese Aussage jedoch spontan in Frage - allerdings nur für einen Augenblick, denn du verlierst kurz darauf das Bewusstsein.')
                        ,'s5')
                    ->effect(
                        Model_Effect::factory()
                            ->consume($this)
                            ->spawn('Model_Items_Generic_Waterb', 1)
                            ->spawn('Model_Items_Smallbottle')
                            ->achieve(Model_Achievement::MA_HALLOWEEN_15)
                            ->message('Kaum ist der erste Tropfen deine Kehle hinunter gelaufen, merkst du das etwas nicht stimmt. Diese Flasche war mit BLUT gefüllt!!')
                        ,'horror')
                    ->decider(function($p) use ($a) {
                        /** @global Model_Game $game */
                        global $game;

                        /** @var Model_Player $p */
                        if (Tool_Events::current($game->next_tick()) == 'halloween' && Tool_Gambling::random(0.08))
                            return 'horror';

                        $ca = $p->get_status()->simulate(Model_Status::MS_STAT_DRUNK, $a, Model_Status::MS_EFFECT_ITEM, false);
                        $as_child = ($p->type() == Interface_Plentity::IC_NPC_ANIMAL || (!Tool_Scripts::is_npc($p) && $p->job(1080)));
                        if ($ca > 100) return 's2';
                        if ($ca > 90) return $as_child ? 's5' : 's4';
                        return $as_child ? 's3' : 's1';
                    })
                    ->export('s1')
            );
    }

	public function alc_content() {
		return static::$alcohol;
	}
}	