<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Tentkit extends Model_Places_Abstract_Hideout {

    protected static $name = 'InstaZELT™';
    protected static $description = 'Das InstaZELT™ ist die perfekte mobile Unterkunft für Campingtrips, mehrtägige Open-Air-Konzerte und iPhone-Releases. Leider stellen die meisten Käufer eines InstaZELT™s relativ schnell fest, dass sich dieses Zelt zwar kinderleicht aufbauen, danach aber nicht mehr abbauen lässt. Manche würde das als einen Designfehler bezeichnen... ';
    protected static $icon = 'itent';
    protected static $upgradable = false;

    protected $cursed;

    //Base deco value
    protected static $base_deco_value = 0;

    //Base defense
    protected $defense = 5;

    //Base: 15% per day
    protected static $decay_rate = 0.30;

    //Exp: 8% per day
    protected static $decay_exp = 0;

    public function __construct($cursed = false) {
        $this->cursed = $cursed;
        parent::__construct();
    }

    public function uin($new = null) {
        if ($new !== null)
            $this->setup_new_room($this->room(),
                                  ['bedroom'],
                                  ['bedr1']);

        return parent::uin($new);
    }

    public function tick($type = Interface_Tickable::IT_TYPE_PLAYER) {
        if ($this->cursed) {
            $this->set_decay(1,true);
            foreach (Tool_Scripts::at_location($this->uin(), true, true) as $p)
                /** @var Model_Player $p */
                if (!Tool_Scripts::is_npc($p)) {
                    $p->log()->add('Gerade hast du es dir bequem gemacht, da hörst du hinter dir plötzlich die Zeltplane reißen. Noch bevor du dich umdrehen kannst spürst du einen stechenden Schmerz im Rücken - herzlichen Glückwunsch, du bist tot.');
                    $p->achievements()->achieve(Model_Achievement::MA_SLASHER_KILLER);
                    $p->get_status()->set_cause_of_death("Serienkiller-Opfer");
                    $p->get_status()->retrieve('heartbeat')->unbuff();
                } else $p->kill();
        }
    }

    public function can_enter($pid = null, $type = Interface_Tickable::IT_TYPE_PLAYER) {
        $is_player = true;
        if (!$pid) $player = Globals::CurrentPlayer();
        elseif ($type == Interface_Tickable::IT_TYPE_PLAYER) $player = Globals::CurrentGame()->get_player($pid);
        else {
            $is_player = false;
            $player = Globals::CurrentGame()->get_npc($pid);
        }

        if (Tool_Scripts::at_location($this->uin(), $is_player, !$is_player)) {
            if ($type == Interface_Tickable::IT_TYPE_PLAYER)
                $player->log()->add('Leider passt nur eine einzige Person in ein InstaZELT™... und dieses ist schon voll.');
            return false;
        } else return true;
    }
}	