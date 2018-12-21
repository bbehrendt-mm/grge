<?php

class Model_NPC_Event_Scarecrow extends Model_NPC_Humanoid
{
    protected static $buffs_heartbeat_name = 'Model_Buffs_Event_Strawheart';
    protected static $buffs_metabolism_name = 'Model_Buffs_Event_Strawmetabolism';
    protected static $buffs_place_various = false;

    protected static $handle_death = false;

    public function __construct($name = null) {
        parent::__construct('Grausame Vogelscheuche');
    }

    public function entity_action() {
        return 'Ist grausam';
    }

    public function entity_description() {
        return 'Wer hat denn dieses gruselige Ding hier reingestellt? Die leeren Augen dieser Vogelscheuche sehen aus, als würden sie dich ständig anstarren... wie furchtbar!';
    }

    public function is_fighter() {
        return false;
    }

    public function kill() {
        if ($this->location()) $this->location()->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_LEAVE, $this->id(), true));
        parent::kill();
    }

    public function hid(): Model_Hid
    {
        return parent::hid()
            ->add_action('Gehirn einsetzen', Model_Action::factory()
                ->grind_requirements(false)
                ->requirement(Model_Items_Brainbox::cls(), 1)
                ->condition(function($p) {
                    /** @var Model_Player $p */
                    if ($p->get_status()->retrieve('wow')) return false;
                    else return true;
                })
                ->show_as(Model_Effect::factory()
                    ->buff('Model_Buffs_Exited', false, 3)
                    ->ambiguous_effect()
                )
                ->fail_message('Dafür bist du im Moment zu aufgeregt.')
                ->effect(Model_Effect::factory()
                    ->buff('Model_Buffs_Exited', false, 3)
                    ->spawn(Model_Items_Soul2::cls(), 1)
                    ->custom(function() {
                        $this->kill();
                    })
                    ->message('Der Vogelscheuche ein Gehirn einzusetzen hat sie nicht wirklich weniger gruselig werden lassen... vor allem, weil sie sich vor deinen Augen aufgelöst und eine Seele freigesetzt hat!')
                )
            )
            ;
    }
}