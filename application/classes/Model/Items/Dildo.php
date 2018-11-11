<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Dildo extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Massagestab',
			'icon' => 'dildo',
			'description' => 'Es ist ein Massagestab. NUR ein Massagestab. Für deinen verspannten Rücken! Und der ist auch nur deshalb so klebrig, weil du immer so schwitzige Hände bekommst, wenn du ihn benutzt!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
            'deco' => 1,
	);

	protected static $weight = 1;

    protected function hid() {
        if (Tool_Scripts::is_npc() || (!Globals::shadowPlayerExists() && Globals::PrimaryPlayerF()->job(1080)))
            return parent::hid();
        return parent::hid()
            ->add_action('Benutzen', Model_Action::factory()
                ->allow_remote(false)
                ->deny_for(Interface_Plentity::IC_NPC_ANIMAL)
                ->requirement(Model_Items_Battery::cls(), 1)
                ->condition(function($p) {
                    /** @var Model_Player $p */
                    if (count(Tool_Scripts::at_location($p->location_class(), true, true)) != 1) return 'peek';
                    elseif ($p->get_status()->retrieve('wow')) return 'wow';
                    else return true;
                })
                ->fail_message('Bist du verrückt? Das kannst du doch nicht machen, wenn alle zugucken... Such dir ein ruhigeres Plätzchen.', 'peek')
                ->fail_message('Dafür bist du im Moment zu aufgeregt.', 'wow')
                ->show_as(
                    Model_Effect::factory()
                        ->achieve(Model_Achievement::MA_MASOCHIST)
                        ->buff('Model_Buffs_Exited', false, 9)
                        ->effect(Model_Status::MS_STAT_ENERGY, 10)
                        ->effect(Model_Status::MS_STAT_SLEEPY, 10)
                    , null, false)
                ->decider(function() {
                    return Tool_Gambling::random(0.1) ? 1 : 0;
                })
                //Success
                ->effect(
                    Model_Effect::factory()
                        ->achieve(Model_Achievement::MA_MASOCHIST)
                        ->buff('Model_Buffs_Exited', false, 9)
                        ->effect(Model_Status::MS_STAT_ENERGY, 10)
                        ->effect(Model_Status::MS_STAT_SLEEPY, 10)
                        ->message('Naja, wenn die Welt schonmal untergegangen ist, dann kann man ruhig mal etwas experimentieren. Eigentlich wars sogar ganz angenehm...')
                )
                //Failure
                ->effect(
                    Model_Effect::factory()
                        ->buff('Model_Buffs_Blood', false)
                        ->achieve(Model_Achievement::MA_MASOCHIST)
                        ->buff('Model_Buffs_Exited', false, 9)
                        ->effect(Model_Status::MS_STAT_ENERGY, 10)
                        ->effect(Model_Status::MS_STAT_SLEEPY, 100)
                        ->message('AAAARGH! GOTT VERDAMMT! Eine falsche Handbewegung, schon leckst du wie ein Weinfass mit Einschussloch!')
                )
            );
    }
}	