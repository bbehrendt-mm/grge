<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Soulcatcher extends Model_Buffs_Abstract_Buff {
	
	protected static $name = 'Zeichen des Seelenfängers';
	protected static $icon = 'soulcatcher';
	protected static $desc = 'Der mysteriöse Fremde in der Aussenwelt hat dir das Zeichen des Seelenfängers übertragen. Von nun an wirst du an Orten, an denen Schreckliches vorgefallen ist, Seelen finden können. Wenn du sie dem Fremden bringst wird er dich sicher belohnen.';
	protected static $bid = 'soulcatcher';

    public function tick() {
        /** @global Model_Game $game */
        global $game;

        parent::tick();

        if (Tool_Events::current($game->next_tick()) != 'halloween')
            return $this->unbuff();

        if (Tool_System::instance_of($this->assoc_player->location(), 'Model_Places_Mental'))
            $soulchance = 30;
        elseif (Tool_System::instance_of($this->assoc_player->location(), 'Model_Places_Mausoleum')  || Tool_System::instance_of($this->assoc_player->location(), 'Model_Places_Asylumhideout'))
            $soulchance = 10;
        elseif (Tool_System::instance_of($this->assoc_player->location(), 'Model_Places_Abstract_Hideout') || Tool_System::instance_of($this->assoc_player->location(), 'Model_Places_Abstract_Node'))
            $soulchance = 0;
        elseif (Tool_System::instance_of($this->assoc_player->location(), 'Model_Places_Hospital'))
            $soulchance = 8;
        elseif (Tool_System::instance_of($this->assoc_player->location(), 'Model_Places_Druglab'))
            $soulchance = 10;
        elseif (Tool_System::instance_of($this->assoc_player->location(), 'Model_Places_Hospital_Er'))
            $soulchance = 35;
        else $soulchance = 2;

        if (mt_rand(0,100) < $soulchance) {
            if (mt_rand(0,100) < $soulchance) $item = new Model_Items_Soul2(1);
            else $item = new Model_Items_Soul(1);

            $this->assoc_player->location()->inventory()->add($item);
            $this->assoc_player->location()->log()->add(new Model_Log_Types_Item(Model_Log_Types_Item::MLTI_SOUL, $item, $this->assoc_player->id()));
        }

        return true;
    }
}
