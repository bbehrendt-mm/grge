<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Toilet extends Model_Places_Abstract_Place
{

    protected static $name = 'Öffentliche Toiletten';
    protected static $description = 'Diese öffentlichen Toiletten sind im Prinzip das Hilton jedes Penners. Für andere Menschen sind diese Toiletten eher wie Paris Hilton: Ziemlich schmutzig, übler Geruch und die halbe Welt war schonmal drin.';
    protected static $icon = 'toilet';
    protected static $outside = false;

    protected $horror = false;


    public function pretick()
    {
        parent::pretick();

        /** @var Model_Game $game */
        global $game;

        if (Tool_Events::current($game->next_tick()) == 'halloween' && !$this->horror && Tool_Gambling::random(0.2)) {

            $this->horror = true;

            foreach (Tool_Scripts::at_location($this->uin()) as $pl) {
                $pl->achievements()->achieve(Model_Achievement::MA_HALLOWEEN_15);
                new Model_Buffs_Exited($pl->id(), 6);
                $pl->log()->add('Du bist gerade dabei, den Mülleimer zu durchwühlen, da hörst du wie hinter dir eine Toilettenspülung betätigt wird. Das Geräusch scheint aus der einen Kabine zu kommen, die seit deinem ersten Besuch abgesperrt war...');
            }
        }
    }
}