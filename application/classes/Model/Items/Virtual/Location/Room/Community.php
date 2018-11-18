<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Room_Community extends Model_Items_Abstract_Virtual {


    protected function hid(): Model_Hid {
        $tmp = parent::hid();

        if ($this->roomF()->has_content('sofa1'))
            parent::hid()->add_action('In der Sitzecke entspannen', Model_Action::factory()
                    ->buttonskin('hideout')
                    ->condition(function($p) {
                        /** @var Model_Player $p */
                        if ($p->get_status()->retrieve('fragile')) return 'fragile';
                        return true;
                    })
                    ->fail_message('Du bist im Moment beschäftigt.', 'fragile')
                    ->show_as(Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_ENERGY, '++')
                            ->effect(Model_Status::MS_STAT_SLEEPY, '--')
                    )
                    ->effect(Model_Effect::factory()
                            ->message('Du setzt dich in den Sitz fallen und versuchst, all die schlimmen Ereignisse heute abzuschütteln.')
                            ->custom(function($p) {
                                /** @var Model_Player $p */
                                if		($this->roomF()->has_content('sofa2'))	new Model_Buffs_Couch($p->id(), 2);
                                elseif	($this->roomF()->has_content('sofa1'))	new Model_Buffs_Couch($p->id(), 1);
                            })
                    )
            );


        return $tmp;
    }
}	