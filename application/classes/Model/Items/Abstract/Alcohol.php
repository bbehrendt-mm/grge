<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Abstract_Alcohol extends Model_Items_Abstract_Item {
	
	protected static $alcohol = 10;
	protected static $cat = Model_Items_Abstract_Item::MIAI_CAT_FOOD;

    protected function hid() {
        global $player;
        $a = max(static::$alcohol * (Tool_Scripts::get_timeofday() == "evening" ? 0.75 : 1) * ($player->job(1080) ? 2.5 : 1), $player->job(1080) ? 20 : 0);
        return parent::hid()
            ->add_action('Trinken', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Player::MP_STAT_DRUNK, $a)
                            ->effect(Model_Player::MP_STAT_ENERGY, 10)
                            ->effect(Model_Player::MP_STAT_THIRST, 20)
                            ->consume($this)
                            ->spawn('Model_Items_Smallbottle')
                            ->message('Das tut gut ... nach einem ordentlichen Drink sieht die Welt gleich weniger apokalyptisch aus!')
                    ,'s1')
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Player::MP_STAT_DRUNK, -12)
                            ->effect(Model_Player::MP_STAT_ENERGY, 5)
                            ->effect(Model_Player::MP_STAT_THIRST, -20)
                            ->effect(Model_Player::MP_STAT_HUNGER, -40)
                            ->achieve(Model_Achievement::MA_ALCOHOLIC)
                            ->consume($this)
                            ->spawn('Model_Items_Smallbottle')
                            ->message('Die Welt um dich herum dreht sich bereits mit bedenklicher Geschwindigkeit, aber einer geht sicher noch rein! ... denkst du, kurz bevor sich dir der Magen umdreht und seinen Inhalt zu Tage fördert.')
                    ,'s2')
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Player::MP_STAT_DRUNK, $a)
                            ->effect(Model_Player::MP_STAT_ENERGY, 15)
                            ->effect(Model_Player::MP_STAT_HEALTH, -static::$alcohol)
                            ->effect(Model_Player::MP_STAT_THIRST, 20)
                            ->consume($this)
                            ->spawn('Model_Items_Smallbottle')
                            ->message('Eigentlich kann man ja mit dem Trinken nie früh genug anfangen. Nachdem du die Flasche ausgetrunken hast, stellst du diese Aussage jedoch spontan in Frage - immerhin dreht sich die Welt um dich herum, und dir ist speiübel.')
                        ,'s3')
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Player::MP_STAT_DRUNK, $a)
                            ->effect(Model_Player::MP_STAT_ENERGY, 10)
                            ->effect(Model_Player::MP_STAT_THIRST, 20)
                            ->buff('Model_Buffs_Drunk')
                            ->consume($this)
                            ->spawn('Model_Items_Smallbottle')
                            ->achieve(Model_Achievement::MA_ALCOHOLIC)
                            ->message('Das tut gut ... nach einem ordentlichen Drink sieht die Welt gleich weniger apokalyptisch aus! Aber warum kommt der Boden plötzlich auf dich zugeflogen?')
                        ,'s4')
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Player::MP_STAT_DRUNK, $a)
                            ->effect(Model_Player::MP_STAT_ENERGY, 15)
                            ->effect(Model_Player::MP_STAT_HEALTH, -static::$alcohol)
                            ->effect(Model_Player::MP_STAT_THIRST, 20)
                            ->buff('Model_Buffs_Drunk')
                            ->consume($this)
                            ->spawn('Model_Items_Smallbottle')
                            ->achieve(Model_Achievement::MA_ALCOHOLIC)
                            ->message('Eigentlich kann man ja mit dem Trinken nie früh genug anfangen. Nachdem du die Flasche ausgetrunken hast, stellst du diese Aussage jedoch spontan in Frage - allerdings nur für einen Augenblick, denn du verlierst kurz darauf das Bewusstsein.')
                        ,'s5')
                    ->decider(function($p) use ($a) {
                        /** @var Model_Player $p */
                        $ca = $p->stats_get(Model_Player::MP_STAT_DRUNK) + $a;
                        if ($ca > 100) return 's2';
                        if ($ca > 90) return ($p->job(1080)) ? 's5' : 's4';
                        return ($p->job(1080)) ? 's3' : 's1';
                    })
                    ->export('s1')
            );
    }

	public function alc_content() {
		return static::$alcohol;
	}
}	