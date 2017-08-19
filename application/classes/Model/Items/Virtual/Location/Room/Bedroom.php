<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Room_Bedroom extends Model_Items_Abstract_Virtual {

    protected function hid() {
        $tmp = parent::hid();

        $tmp->add_action($this->room()->has_content("bedr1") ? 'Ins Bett gehen' : ($this->room()->has_content("hay1") ? 'Auf dem Heu schlafem' : 'Auf dem Boden schlafen'), Model_Action::factory()
            ->buttonskin('hideout')
            ->condition(function($p) {
                /** @var Model_Player $p */
                if ($p->get_status()->retrieve('fragile')) return 'fragile';
                if ($p->get_status()->retrieve('wow')) return 'wow';
                if ($p->get_status()->get(Model_Status::MS_STAT_THIRST) < 20)      return 'thirst';
                if ($p->get_status()->get(Model_Status::MS_STAT_HUNGER) < 20)      return 'hunger';
                if ($p->get_status()->get(Model_Status::MS_STAT_SLEEPY) > 85)      return 'sleepy';
                if ($this->room()->check_room_satisfaction('bedroom_cursed')) return 'cursed';
                return true;
            })
            ->fail_message('Du bist im Moment beschäftigt.', 'fragile')
            ->fail_message('Dafür bist du im Moment zu aufgeregt.', 'wow')
            ->fail_message('Du wälzt dich hin und her, aber dein furchtbarer Durst hindert sich am Einschlafen...', 'thirst')
            ->fail_message('Du wälzt dich hin und her, aber dein furchtbarer Hunger hindert sich am Einschlafen...', 'hunger')
            ->fail_message('Du wälzt dich hin und her, aber kannst einfach nicht einschlafen... Vielleicht bist du ja gar nicht müde.', 'sleepy')
            ->fail_message('Du legst dich auf das Bett und versuchst zu schlafen. Allerdings kannst du dich einfach nicht dazu durchringen, in diesem fürchterlichen Raum die Augen zu schließen. Als du dann auch noch jemanden (oder etwas?) in der Ferne durch die Gänge schleichen hörst, springst du wieder auf. Sieht nicht so aus, als könntest du hier schlafen...', 'cursed')
            ->show_as(Model_Effect::factory()
                ->effect(Model_Status::MS_STAT_ENERGY, '++')
                ->effect(Model_Status::MS_STAT_SLEEPY, '++')
                ->effect(Model_Status::MS_STAT_HEALTH, $this->room()->has_content("bedr1") ? '++' : 0)
            )
            ->effect(Model_Effect::factory()
                ->message('Es war ein langer Tag, und du bist froh wenigstens für ein paar Stunden alles um dich herum vergessen zu können ...')
                ->custom(function($p) {
                        /** @var Model_Player $p */
                        $d = $this->room()->has_content("bedrlights") ? 2 : 5;

                        if		($this->room()->has_content("bedr3") || $this->room()->has_content("hay3")) new Model_Buffs_Presleep($p->id(), $this->room()->has_content("bedr3") ? 3 : -3, $this->room()->has_content("bedr3") ? $d : ($d + 1));
                        elseif	($this->room()->has_content("bedr2") || $this->room()->has_content("hay2")) new Model_Buffs_Presleep($p->id(), $this->room()->has_content("bedr2") ? 2 : -2, $this->room()->has_content("bedr2") ? $d : ($d + 1));
                        elseif	($this->room()->has_content("bedr1") || $this->room()->has_content("hay1")) new Model_Buffs_Presleep($p->id(), $this->room()->has_content("bedr1") ? 1 : -1, $this->room()->has_content("bedr1") ? $d : ($d + 1));
                        else new Model_Buffs_Presleep($p->id(), 0, 6);
                    })
            )
        );

        return $tmp;
    }
}	