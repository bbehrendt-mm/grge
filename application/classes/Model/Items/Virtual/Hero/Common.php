<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Hero_Common extends Model_Items_Abstract_Virtual {

    protected $remaining = array(
        'hero_focus' => 1,
        'hero_wof' => 1,
        'hero_sleep' => 1,
        'hero_unaddict' => 1,
        'context_escape' => PHP_INT_MAX,
    );

	protected static $static_info = Array(
			'name' => 'Heldentaten',
	);

    protected function hid() {
        /** @global $player Model_Player */
        global $player;
        $tmp = parent::hid()
            ->add_action('Kraft sammeln', Model_Action::factory()
                    ->buttonskin('hero')
                    ->description('Reduziert jede deiner Statusleisten um 15% und fügt die abgezogenen Punkte deiner Energie hinzu.')
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_HUNGER, $t_h = -floor(0.15 * $player->get_status()->get(Model_Status::MS_STAT_HUNGER)))
                            ->effect(Model_Status::MS_STAT_THIRST, $t_d = -floor(0.15 * $player->get_status()->get(Model_Status::MS_STAT_THIRST)))
                            ->effect(Model_Status::MS_STAT_HEALTH, $t_g = -floor(0.15 * $player->get_status()->get(Model_Status::MS_STAT_HEALTH)))
                            ->effect(Model_Status::MS_STAT_SLEEPY, $t_m = -floor(0.15 * $player->get_status()->get(Model_Status::MS_STAT_SLEEPY)))
                            ->effect(Model_Status::MS_STAT_ENERGY, abs($t_h) + abs($t_d) + abs($t_g) + abs($t_m))
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
                        ->effect(Model_Status::MS_STAT_HUNGER, PHP_INT_MAX)
                    )
                    ->effect(Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_THIRST, PHP_INT_MAX)
                    )
                    ->effect(Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_ENERGY, PHP_INT_MAX)
                    )
                    ->effect(Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_SLEEPY, PHP_INT_MAX)
                    )
                    ->effect(Model_Effect::factory()
                        ->effect(Model_Status::MS_STAT_HEALTH, PHP_INT_MAX)
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
                                if ($p->get_status()->retrieve('drug3')) {
                                    $p->get_status()->remove('drug3');
                                    $p->get_status()->remove('drug2');
                                    $p->log()->add('Herzlichen Glückwunsch - das ist jetzt das :num. mal, dass du deine Sucht nach verschreibungspflichtigen Medikamenten, industriellem Lösungsmittel oder abgelaufenem Hustensaft besiegt hast!', array(':num' => mt_rand(10,99)));
                                } else $p->log()->add('Hmm... nichts passiert. Kann es eventuell sein, dass du gar nicht auf Entzug warst?');
                            })
                    )
                , 'hero_unaddict')
            ;

        // TODO: Allow animals to somehow escape, too!
        if ($player->get_escape_target() && $player->get_escape_target() != $player->location_class())
            $tmp->add_action('Überstürzte Flucht', Model_Action::factory()
                ->buttonskin('context')
                ->description('Hast du dich in einer Ruine verlaufen, dann verwende diese Aktion um aus deiner misslichen Lage zu befreien und zum Eingang zurückzukehren. ACHTUNG: Du wirst während der Flucht die meisten deiner Gegenstände verlieren und dir sehr wahrscheinlich eine Verletzung zuziehen. Wird der Fluchtweg von Zombies blockiert, verlierst du 20 Gesundheit für jeden Zombie - du behälst jedoch mindestens 1 Gesundheitspunkt nach der Flucht. Die Zombies werden durch diese Aktion nicht getötet!')
                ->effect(Model_Effect::factory()
                    ->custom(function($p) {
                        /** @var Model_Player $p */
                        /** @global Model_Game $game */
                        global $game;
                        if (!($did = $p->get_escape_target())) return;
                        if ($did == $p->location_class() || !$p->location()->can_leave($p->id(), true, Interface_Tickable::IT_TYPE_PLAYER) || !$game->location($did)->can_enter($p->id(), Interface_Tickable::IT_TYPE_PLAYER)) {
                            $p->log()->add('Eine Flucht scheint im Moment aussichtslos...');
                            return;
                        }

                        $damage = min($p->get_status()->get(Model_Status::MS_STAT_HEALTH) - 1, $p->location()->zombie_pop() * 20);
                        $injury = mt_rand(0,100) < (50 + $damage);

                        foreach ($p->inventory()->get() as $item)
                            if (!$item->is_essential() && $item->drop()) {
                                $p->inventory()->remove($item->uin());
                                $p->location()->inventory()->add($item);
                            }

                        $p->get_status()->modify(Model_Status::MS_STAT_HEALTH, -$damage);
                        if ($injury) new Model_Buffs_Blood($p->id());

                        $p->location()->leave($p->id(), Interface_Tickable::IT_TYPE_PLAYER);
                        $game->location($did)->enter($p->id(), Interface_Tickable::IT_TYPE_PLAYER);
                        $p->location_class($did);

                        $p->log()->add('Puuh, das war eine ganz schön wilde Flucht... aber jetzt scheinst du erst einmal in Sicherheit zu sein.');
                    })
                )
                , 'context_escape'
            );

        return $tmp;
    }
}	