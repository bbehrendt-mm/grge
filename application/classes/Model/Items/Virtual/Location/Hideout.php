<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Hideout extends Model_Items_Abstract_Virtual {

    protected $remaining = array(
        'hideout_builder' => PHP_INT_MAX,
        'hideout_sleep' => PHP_INT_MAX,
        'hideout_workshop' => PHP_INT_MAX,
        'hideout_power' => PHP_INT_MAX,
        'hideout_kitchen' => PHP_INT_MAX,
        'hideout_defense' => PHP_INT_MAX,
        'hideout_couch' => PHP_INT_MAX,
    );

    public function __construct($upgradable = true) {
        if (!$upgradable)
            $this->remaining['hideout_builder'] = 0;
    }

    protected function hid() {
        /** @global Model_Player $player */
        global $player;

        /** @var Model_Places_Abstract_Hideout $location */
        $location = $player->location();
        /** @noinspection PhpUndefinedMethodInspection */
        $location_driving = Tool_System::instance_of($location, 'Model_Places_Motorhome') && $location->is_driving();

        $tmp = parent::hid();

        if (!$location_driving)
            $tmp->add_action('Versteck ausbauen ...', Model_Action::factory()
                ->buttonskin('hideout')
                ->description('Dein Versteck sieht etwas langweilig aus... du solltest es mit einigen nützlichen Erweiterungen etwas aufpeppen!')
                ->javascript(Model_Javascript::factory()
                        ->close_qtip()
                        ->versa('builder'))
            , 'hideout_builder');

        if (!$location->has_upgrade("hideout"))
            return $tmp;

        if ($location->has_upgrade("sofa1"))
            $tmp->add_action('In der Sitzecke entspannen', Model_Action::factory()
                    ->buttonskin('hideout')
                    ->condition(function($p) {
                        /** @var Model_Player $p */
                        if ($p->buff_retr('fragile')) return 'fragile';
                        return true;
                    })
                    ->fail_message('Du bist im Moment beschäftigt.', 'fragile')
                    ->show_as(Model_Effect::factory()
                            ->effect(Model_Player::MP_STAT_ENERGY, '++')
                            ->effect(Model_Player::MP_STAT_SLEEPY, '--')
                    )
                    ->effect(Model_Effect::factory()
                            ->message('Du setzt dich in den Sitz fallen und versuchst, all die schlimmen Ereignisse heute abzuschütteln.')
                            ->custom(function($p) {
                                /** @var Model_Player $p */
                                /** @var Model_Places_Abstract_Hideout $l */
                                $l = $p->location();

                                if		($l->has_upgrade("sofa2"))	new Model_Buffs_Couch($p->id(), 2);
                                elseif	($l->has_upgrade("sofa1"))	new Model_Buffs_Couch($p->id(), 1);
                            })
                    )
                , 'hideout_couch');

        $tmp->add_action($location->has_upgrade("bedr1") ? 'Ins Bett gehen' : 'Auf dem Boden schlafen', Model_Action::factory()
            ->buttonskin('hideout')
            ->condition(function($p) {
                /** @var Model_Player $p */
                if ($p->buff_retr('fragile')) return 'fragile';
                if ($p->buff_retr('wow')) return 'wow';
                if ($p->stats_get(Model_Player::MP_STAT_THIRST) < 20) return 'thirst';
                if ($p->stats_get(Model_Player::MP_STAT_HUNGER) < 20) return 'hunger';
                if ($p->stats_get(Model_Player::MP_STAT_SLEEPY) > 85) return 'sleepy';
                if ($p->location()->home_extensions("hideout", "cursed")) return 'cursed';
                return true;
            })
            ->fail_message('Du bist im Moment beschäftigt.', 'fragile')
            ->fail_message('Dafür bist du im Moment zu aufgeregt.', 'wow')
            ->fail_message('Du wälzt dich hin und her, aber dein furchtbarer Durst hindert sich am Einschlafen...', 'thirst')
            ->fail_message('Du wälzt dich hin und her, aber dein furchtbarer Hunger hindert sich am Einschlafen...', 'hunger')
            ->fail_message('Du wälzt dich hin und her, aber kannst einfach nicht einschlafen... Vielleicht bist du ja gar nicht müde.', 'sleepy')
            ->fail_message('Du legst dich auf das Bett und versuchst zu schlafen. Allerdings kannst du dich einfach nicht dazu durchringen, in diesem fürchterlichen Raum die Augen zu schließen. Als du dann auch noch jemanden (oder etwas?) in der Ferne durch die Gänge schleichen hörst, springst du wieder auf. Sieht nicht so aus, als könntest du hier schlafen...', 'cursed')
            ->show_as(Model_Effect::factory()
                ->effect(Model_Player::MP_STAT_ENERGY, '++')
                ->effect(Model_Player::MP_STAT_SLEEPY, '++')
                ->effect(Model_Player::MP_STAT_HEALTH, $location->has_upgrade("bedr1") ? '++' : 0)
            )
            ->effect(Model_Effect::factory()
                ->message('Es war ein langer Tag, und du bist froh wenigstens für ein paar Stunden alles um dich herum vergessen zu können ...')
                ->custom(function($p) {
                        /** @var Model_Player $p */
                        /** @var Model_Places_Abstract_Hideout $l */
                        $l = $p->location();
                        $d = $l->has_upgrade("bedrlights") ? 2 : 5;

                        if		($l->has_upgrade("bedr3"))	new Model_Buffs_Presleep($p->id(), 3, $d);
                        elseif	($l->has_upgrade("bedr2"))	new Model_Buffs_Presleep($p->id(), 2, $d);
                        elseif	($l->has_upgrade("bedr1"))	new Model_Buffs_Presleep($p->id(), 1, $d);
                        else										new Model_Buffs_Presleep($p->id(), 0, 6);
                    })
            )
        , 'hideout_sleep');

        if ($location->has_upgrade("manu1"))
            $tmp->add_action('Werkbank ...', Model_Action::factory()
                ->buttonskin('hideout')
                ->description('Nicht jedes Items lässt sich einfach so finden - manche musst du auch auf einer Werkbank wie dieser herstellen.')
                ->javascript(Model_Javascript::factory()
                    ->close_qtip()
                    ->versa('workshop'))
            , 'hideout_workshop');
        if ($location->has_upgrade("gen1"))
            $tmp->add_action('Notstrom-Aggregat ...', Model_Action::factory()
                ->buttonskin('hideout')
                ->description('Bist du es nicht leid, immer bei Kerzenlicht fernzusehen? Dann beweg mal deinen faulen Hintern und erzeug etwas Strom!')
                ->javascript(Model_Javascript::factory()
                    ->close_qtip()
                    ->versa('power'))
            , 'hideout_power');
        if ($location->home_extensions("kitchen", "base"))
            $tmp->add_action('Küche ...', Model_Action::factory()
                ->buttonskin('hideout')
                ->description('Mit dieser Küche ist das leibliche Wohl gesichtert. Vorrausgesetzt natürlich, du findest Zutaten.')
                ->javascript(Model_Javascript::factory()
                    ->close_qtip()
                    ->versa('kitchen'))
            , 'hideout_kitchen');
        if ($location->any_def() && !$location_driving)
            $tmp->add_action('Verteidigung ...', Model_Action::factory()
                ->buttonskin('hideout')
                ->description('Zombies oder Zeugen Jehovas stehen an deiner Tür? Nicht mehr lange...')
                ->javascript(Model_Javascript::factory()
                    ->close_qtip()
                    ->versa('defense'))
            , 'hideout_defense');

        return $tmp;
    }
}	