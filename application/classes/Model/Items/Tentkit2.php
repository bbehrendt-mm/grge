<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Tentkit2 extends Model_Items_Tentkit  {

	protected static $static_info = Array(
			'name' => 'DELUXE InstaZELT™-Kit',
			'icon' => 'tentkit2',
			'description' => 'Warum solltest du ein gewöhnliches InstaZELT™ aufbauen, wenn du auch ein InstaZELT™ Deluxe bekommen kannst? Dieses verbesserte InstaZELT™ bietet mehr Platz, ist gemütlicher eingerichtet und kommt mit doppelt so vielen Warnhinweisen in der Bedienungsanleitung wie ein gewöhnliches InstaZELT™!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
	);

	protected static $weight = 7;

    protected function hid() {
        return parent::hid()
            ->add_action('InstaZELT™ aufstellen', Model_Action::factory()
                ->allow_remote(false)
                ->deny_for(Interface_Plentity::IC_NPC_ANIMAL)
                ->condition(function($p) {
                    /** @var Model_Player $p */
                    /** @global Model_Game $game */
                    global $game;
                    return (Tool_Scripts::current_location_hideout($p) == null && $game->map($p->location_class())->get_map_type() == Model_Map_Abstract::MMA_TYPE_OVERVIEW && !Tool_System::instance_of($p->location(), 'Model_Places_Abstract_Xmas'));
                })
                ->fail_message('Du kannst an dieser Stelle kein InstaZELT™ aufstellen.')
                ->effect(
                    Model_Effect::factory()
                        ->custom(function($p) {
                            /** @var Model_Player $p */
                            /** @global Model_Game $game */
                            global $game;
                            $cursed = Tool_System::instance_of($p->location(), ['Model_Places_Mental','Model_Places_House_Hobby']);

                            $id = $game->map($p->location_class())->implant_location(new Model_Places_Tentkit2($cursed),0,0,true,null,false,$p->location_class(),true,true,null);
                            $p->location()->leave($p->id(), Interface_Tickable::IT_TYPE_PLAYER);
                            $p->location_class($id);
                        })
                        ->consume($this)
                        ->message('Einfach diesen Nippel durch die Lasche ziehen .... PUFF, mit einem Schlag stehst du in einem InstaZELT™!')
                )
            );
    }
}	