<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Hero_Common extends Model_Items_Abstract_Virtual {

    protected $remaining = array(
        'hero_focus' => 1,
        'hero_wof' => 1,
        'hero_sleep' => 1,
        'hero_unaddict' => 1,
    );

	protected static $static_info = Array(
			'name' => 'Heldentaten',
	);

    protected function hid() {
        global $player;
        return parent::hid()
            ->add_action('Kraft sammeln', Model_Action::factory()
                    ->buttonskin('hero')
                    ->description('Reduziert jede deiner Statusleisten um 15% und fügt die abgezogenen Punkte deiner Energie hinzu.')
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Player::MP_STAT_HUNGER, $t_h = -floor(0.15 * $player->stats_get(Model_Player::MP_STAT_HUNGER)))
                            ->effect(Model_Player::MP_STAT_THIRST, $t_d = -floor(0.15 * $player->stats_get(Model_Player::MP_STAT_THIRST)))
                            ->effect(Model_Player::MP_STAT_HEALTH, $t_g = -floor(0.15 * $player->stats_get(Model_Player::MP_STAT_HEALTH)))
                            ->effect(Model_Player::MP_STAT_SLEEPY, $t_m = -floor(0.15 * $player->stats_get(Model_Player::MP_STAT_SLEEPY)))
                            ->effect(Model_Player::MP_STAT_ENERGY, abs($t_h) + abs($t_d) + abs($t_g) + abs($t_m))
                            ->message('Du wirst nicht sterben... nicht hier, und auch nicht auf diese Weise! Mit diesem Mantra hast du deine letzten Kräfte mobilisiert und neue Energie gewonnen.')
                    )
            , 'hero_focus')
            ->add_action('Glücksrad', Model_Action::factory()
                    ->buttonskin('hero')
                    ->description('Setzt eine zufällige Statusleiste auf 100')
                    ->show_as(Model_Effect::factory()
                        ->message('Gott würfelt nicht - denn er ist zu beschäftigt damit, am Glücksrad zu drehen. Eine deiner Statusleisten wurde auf 100 gesetzt; hoffentlich bist du mit dem Ergebnis zufrieden...')
                    , null, true)
                    ->effect(Model_Effect::factory()
                        ->effect(Model_Player::MP_STAT_HUNGER, PHP_INT_MAX)
                    )
                    ->effect(Model_Effect::factory()
                        ->effect(Model_Player::MP_STAT_THIRST, PHP_INT_MAX)
                    )
                    ->effect(Model_Effect::factory()
                        ->effect(Model_Player::MP_STAT_ENERGY, PHP_INT_MAX)
                    )
                    ->effect(Model_Effect::factory()
                        ->effect(Model_Player::MP_STAT_SLEEPY, PHP_INT_MAX)
                    )
                    ->effect(Model_Effect::factory()
                        ->effect(Model_Player::MP_STAT_HEALTH, PHP_INT_MAX)
                    )
                , 'hero_wof')
            ->add_action('Winterschlaf', Model_Action::factory()
                    ->buttonskin('hero')
                    ->description('Du fällst an Ort und Stelle in einen erholsamen Schlaf.')
                    ->effect(
                        Model_Effect::factory()
                            ->message('Es war ein langer Tag, und du bist froh wenigstens für ein paar Stunden alles um dich herum vergessen zu können ...')
                            ->custom(function($p) {
                                /** @var Model_Player $p */

                                new Model_Buffs_Sleep($p->id(), 3);
                            })
                    )
                , 'hero_sleep')
            ->add_action('Willenskraft', Model_Action::factory()
                    ->buttonskin('hero')
                    ->description('Beendet Entzugserscheinungen auf der Stelle.')
                    ->effect(
                        Model_Effect::factory()
                            ->custom(function ($p) {
                                /** @var Model_Player $p */
                                if ($p->buff_retr('drug3')) {
                                    $p->buff_remove('drug3');
                                    $p->buff_remove('drug2');
                                    $p->log()->add('Herzlichen Glückwunsch - das ist jetzt das :num. mal, dass du deine Sucht nach verschreibungspflichtigen Medikamenten, industriellem Lösungsmittel oder abgelaufenem Hustensaft besiegt hast!', array(':num' => mt_rand(10,99)));
                                } else $p->log()->add('Hmm... nichts passiert. Kann es eventuell sein, dass du gar nicht auf Entzug warst?');
                            })
                    )
                , 'hero_unaddict')
            ;
    }
}	