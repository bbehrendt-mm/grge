<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Tentkit extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'InstaZELT™-Kit',
			'icon' => 'tentkit',
			'description' => 'Mit dem InstaZELT™-Kit kannst du dir in sekundenschnelle und an jedem beliebigen Ort ein Zelt aufschlagen, dass dir Gelegenheit zum Ausruhen gibt und dich vor Zombies schützt.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
	);

	protected static $weight = 5;

    protected function hid() {
        return parent::hid()
            ->add_action('InstaZELT™ aufstellen', Model_Action::factory()
                ->deny_for(Interface_Plentity::IC_NPC_ANIMAL)
                ->allow_remote(false)
                ->condition(function($p) {
                    /** @var Model_Player $p */
                    return (Tool_Scripts::current_location_hideout($p) == null && Globals::CurrentGameF()->map($p->location_class())->get_map_type() == Model_Map_Abstract::MMA_TYPE_OVERVIEW && !Tool_System::instance_of($p->location(), 'Model_Places_Abstract_Xmas'));
                })
                ->fail_message('Du kannst an dieser Stelle kein InstaZELT™ aufstellen.')
                ->effect(
                    Model_Effect::factory()
                        ->custom(function($p) {
                            /** @var Model_Player $p */
                            $cursed = Tool_System::instance_of($p->location(), ['Model_Places_Mental','Model_Places_House_Hobby']);

                            $id = Globals::CurrentGameF()->map($p->location_class())->implant_location(new Model_Places_Tentkit($cursed),0,0,true,null,false,$p->location_class(),true,true,null);
                            $p->location()->leave($p->id(), Interface_Tickable::IT_TYPE_PLAYER);
                            $p->location_class($id);
                        })
                        ->consume($this)
                        ->message('Einfach diesen Nippel durch die Lasche ziehen .... PUFF, mit einem Schlag stehst du in einem InstaZELT™!')
                )
            );
    }
}	