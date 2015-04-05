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
        global $player;
        if ($player->job(1080))
            return parent::hid();
        return parent::hid()
            ->add_action('Benutzen', Model_Action::factory()
                    ->requirement('Model_Items_Battery', 1)
                    ->condition(function($p) {
                        /** @var Model_Player $p */
                        if (count(Tool_Scripts::at_location($p->location_class())) != 1) return 'peek';
                        elseif ($p->buff_retr('wow')) return "wow";
                        else return true;
                    })
                    ->fail_message('Bist du verrückt? Das kannst du doch nicht machen, wenn alle zugucken... Such dir ein ruhigeres Plätzchen.', 'peek')
                    ->fail_message('Dafür bist du im Moment zu aufgeregt.', 'wow')
                    ->show_as(
                        Model_Effect::factory()
                            ->achieve(Model_Achievement::MA_MASOCHIST)
                            ->buff('Model_Buffs_Exited', false, 9)
                            ->effect(Model_Player::MP_STAT_ENERGY, 10)
                            ->effect(Model_Player::MP_STAT_SLEEPY, 10)
                        , null, true)
                    ->decider(function() {
                        return (mt_rand(0,10) <= 1) ? 1 : 0;
                    })
                    //Success
                    ->effect(
                        Model_Effect::factory()
                            ->message('Naja, wenn die Welt schonmal untergegangen ist, dann kann man ruhig mal etwas experimentieren. Eigentlich wars sogar ganz angenehm...')
                    )
                    //Failure
                    ->effect(
                        Model_Effect::factory()
                            ->buff('Model_Buffs_Blood', false)
                            ->effect(Model_Player::MP_STAT_SLEEPY, 100)
                            ->message('AAAARGH! GOTT VERDAMMT! Eine falsche Handbewegung, schon leckst du wie ein Weinfass mit Einschussloch!')
                    )
            );
    }
}	