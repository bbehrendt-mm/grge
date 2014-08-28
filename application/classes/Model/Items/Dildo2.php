<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Dildo2 extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Modifizierter Massagestab',
			'icon' => 'dildo2',
			'description' => 'Dieser Massagestab wurde anscheinend etwas modifiziert. Um den Massageeffekt zu erhöhen. Am Rücken! Denn an anderen Körperstellen kann man dieses Teil nicht benutzen!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
	);

	protected static $weight = 1;

    protected function hid() {
        global $player;
        if ($player->job(1080))
            return parent::hid();
        return parent::hid()
            ->add_action('Benutzen', Model_Action::factory()
                    ->requirement('Model_Items_Generic_Supercharger', 1)
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
                            ->buff('Model_Buffs_Exited', false, 48)
                            ->effect(Model_Player::MP_STAT_ENERGY, 70)
                            ->effect(Model_Player::MP_STAT_SLEEPY, 70)
                        , null, true)
                    ->decider(function() {
                        return (mt_rand(0,10) <= 3) ? 1 : 0;
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