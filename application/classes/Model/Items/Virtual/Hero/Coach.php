<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Hero_Coach extends Model_Items_Abstract_Virtual {

    public function __construct($level = 1) {
        $this->remaining = array(
            'hero_job_0' => ceil($level/3),
        );
    }

    protected static $static_info = Array(
        'name' => 'Heldentaten',
    );

    protected function hid() {
        return parent::hid()
            ->add_action('Umstoßen', Model_Action::factory()
                    ->buttonskin('hero hja')
                    ->description('Tötet blockierende Zombies ohne Kampf. Für jeden Zombie werden 2 Gesundheitspunkte abgezogen. Deine Gesundheit kann durch diese Aktion nicht unter 20 fallen. Hast du nicht genug Gesundheit um alle Zombies zu töten, so musst du den Rest in einem normalen Kampf besiegen.')
                    ->effect(
                        Model_Effect::factory()
                            ->custom(function($p) {
                                /** @var Model_Player $p */

                                $pkills = floor(min(0,$p->stats_get(Model_Player::MP_STAT_HEALTH) - 20)/2);
                                $zombies = $p->location()->zombie_pop();
                                $p->location()->zombie_pop(true);
                                $p->achievements()->achieve(Model_Achievement::MA_KILLED_ZOMBIES, min($pkills,$zombies));
                                $p->stats_modify(Model_Player::MP_STAT_HEALTH, min($pkills,$zombies) * -2);

                                if ($pkills >= $zombies)
                                    $p->log()->add('Na, das war ja einfach. Du konntest diese schlappen Zombies einfach umrennen.');
                                else {
                                    $p->log()->add('Fast hätte es geklappt... leider sind ein paar standhafte Zombies übrig geblieben, die dich jetzt ziemlich grimmig anschauen...');
                                    $p->location()->zombie_factory()->accumulate_zombies($zombies - $pkills);
                                    $p->location()->break_out(true);
                                }
                            })
                    )
                , 'hero_job_0');
    }
}	