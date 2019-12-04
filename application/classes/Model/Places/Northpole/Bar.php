<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Northpole_Bar extends Model_Places_Bar {
	
	protected static $namelist = Array('Rentier-Tankstelle', 'Bar "zum Rammelnden Rentier"', 'Bar "zum wuchtigen Wichtel"', 'Bar "zum eiternden Elfen"', 'Bar "zum saufenden Santa"');
    protected static $icon = 'np_bar';

    protected static $temperature_engine = 0;

    protected $keg_effect = null;
    protected $keg_remaining = 100;

    private function generate_keg_effect() {
        if ($this->keg_effect === null) {
            $this->keg_remaining = 100;
            $this->keg_effect = Tool_Gambling::select([
                                                        0, // Bedazzle Effect
                                                        1, // Softdrink effect
                                                        2, // Beer effect effect
                                                        3, // Vodka effect effect
                                                        4, // Water effect effect
                                                      ]);
        }

    }

    public function trigger_item_action(?Model_Items_Abstract_Item $item)
    {
        $this->generate_keg_effect();

        //if ($this->keg_remaining < 1) return;

        foreach (Tool_Scripts::at_location($this->uin(), false, true) as $npc)
            if (Tool_System::instance_of($npc, Model_NPC_Event_RudolphBR::cls())) {

                if ($npc->get_status()->retrieve('passout') || $npc->get_status()->retrieve('fragile')) {
                    Globals::CurrentPlayerActualF()->log()->add('Dein Rentier scheint gerade nicht bereit zu sein ...');
                    return;
                }

                switch ($this->keg_effect) {

                    case 0:
                        $this->keg_remaining--;
                        new Model_Buffs_Befuddled($npc, 6);
                        Globals::CurrentPlayerActualF()->log()->add('Nach nur ein paar Schluck rutscht :name vom Zapfhahn und fängt an, wirr über das Kennedy-Attentat zu brabbeln.', [':name' => $npc->name()]);
                        break;

                    case 1:
                        $t = floor(min($this->keg_remaining, 100 - $npc->get_status()->get(Model_Status::MS_STAT_THIRST)));
                        $this->keg_remaining -= $t;
                        new Model_Buffs_Exited($npc, ceil($t/10));
                        $npc->get_status()->modify(Model_Status::MS_STAT_THIRST, $t);
                        Globals::CurrentPlayerActualF()->log()->add(':name hat seinen Durst an dem Fass gestillt. Außer einem leichten Zittern kannst du keine negativen Effekte feststellen ...', [':name' => $npc->name()]);
                        break;

                    case 2:
                        $t = min($this->keg_remaining, 100 - $npc->get_status()->get(Model_Status::MS_STAT_THIRST));
                        $d = min($this->keg_remaining,  90 - $npc->get_status()->get(Model_Status::MS_STAT_DRUNK));
                        $final = floor(max(0, min($d,$t)));
                        $this->keg_remaining -= $final;
                        $npc->get_status()->modify(Model_Status::MS_STAT_DRUNK, $final);
                        $npc->get_status()->modify(Model_Status::MS_STAT_THIRST, $final);
                        Globals::CurrentPlayerActualF()->log()->add(':name hat seinen Durst an dem Fass gestillt. Außer einer torkelnden Gangart und einem Schluckauf kannst du keine negativen Effekte feststellen ...', [':name' => $npc->name()]);
                        break;

                    case 3:
                        $this->keg_remaining-=25;
                        new Model_Buffs_Drunk($npc, 6);
                        $npc->get_status()->set(Model_Status::MS_STAT_DRUNK, 100);
                        Globals::CurrentPlayerActualF()->log()->add('Nach ein paar großen Schlucken rutscht :name vom Zapfhahn, taumelt kurz umher und fällt dann in sich zusammen.', [':name' => $npc->name()]);
                        break;

                    case 4:default:
                        $t = floor(min($this->keg_remaining, 100 - $npc->get_status()->get(Model_Status::MS_STAT_THIRST)));
                        $this->keg_remaining -= $t;
                        $npc->get_status()->modify(Model_Status::MS_STAT_THIRST, $t);
                        Globals::CurrentPlayerActualF()->log()->add(':name hat seinen Durst an dem Fass gestillt. Du kannst keine negativen Effekte feststellen ...', [':name' => $npc->name()]);
                        break;
                }

                if ($this->keg_remaining > 60) Globals::CurrentPlayerActualF()->log()->add('Das Fass scheint noch ziemlich voll zu sein.');
                elseif ($this->keg_remaining > 40) Globals::CurrentPlayerActualF()->log()->add('Das Fass scheint noch etwa halbvoll zu sein.');
                elseif ($this->keg_remaining > 10) Globals::CurrentPlayerActualF()->log()->add('Das Fass scheint langsam zur Neige zu gehen.');
                elseif ($this->keg_remaining >= 1) Globals::CurrentPlayerActualF()->log()->add('Das Fass scheint recht leer zu sein.');
                else $item->grind();

                return;

            }

        Globals::CurrentPlayerActualF()->log()->add('... und wie willst du das machen, wenn dein Rentier nicht hier ist?');
    }

    public function uin($uin = NULL) {
        if ($uin === NULL) return parent::uin();
        else $t = parent::uin($uin);

        $trigger = new Model_Items_Trigger('Unbeschriftetes Fass','keg','Hier liegt ein unbeschriftetes Fass... sicherlich wäre es keine gute Idee, wenn du daraus trinken würdest; könnte ja sonstwas drin sein. Aber jemand anderes wäre vielleicht verzweifelt genug, es zu versuchen ...','Rudolph betanken',Model_Items_Abstract_Item::MIAI_CAT_FOOD, 300, null, 30);
        $this->inventory()->add($trigger);

        return $t;
    }
}	