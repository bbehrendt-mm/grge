<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Motorhome extends Model_Places_Home {

    protected static $name = 'Klappriger Wohnwagen';
    protected static $description = 'Als die Zombies kamen haben sich die meisten deiner Nachbarn einfach in ihren Häusern verbarrikadiert. Du hingegen bist mit deinem Wohnmobil geflohen, was sich im Nachhinein leider auch als nicht optimal erwiesen hat. Immerhin musst du regelmäßig Benzin für dieses Teil finden und es in Schuss halten, um weiterfahren zu können.';
    protected static $icon = 'motorhome';

    //Base deco value
    protected static $base_deco_value = -30;

    private $progress = 0;
    private $driving = false;
    private static $max_weight = 900;
    private $km = 0;
    private $force_nomap = false;

    private $parts = array(
        'Model_Items_Generic_Motor' => array(1,1),
        'Model_Items_Generic_Belt'  => array(2,2),
        'Model_Items_Generic_Tube'  => array(5,5),
        'Model_Items_Generic_Sum'   => array(20,20),
    );

    public function get_level_progress() {
        return $this->progress;
    }

    public function get_parts() {
        return $this->parts;
    }

    public function get_distance() {
        return $this->km;
    }

    public function get_map_points() {
        return $this->get_distance();
    }

    public function get_level() {
        return $this->progress;
    }

    public function is_driving() {
        return $this->driving;
    }

    public function mapable() {
        return !$this->driving && !$this->force_nomap;
    }

    public function uin($new = null) {
        if ($new !== null)
            Model_Blueprints::fast_apply($this, 'upgrades', ['bedr1','sofa1','manu1','gen1','gen2','ktc1','outside']);

        return parent::uin($new);
    }

    private function mapcontrol($populate) {
        /** @global Model_Game $game */
        global $game;

        foreach ($game->players(false) as $p) {
            /** @var Model_Player $p */
            if ($p->location_class() != $this->uin()) {
                if ($p->alive()) {
                    $p->set_cod('Zurückgelassen');
                    $p->kill();
                }
                $p->location_class($this->uin());
            }
        }

        foreach ($game->locations() as $location)
            if ($location != $this->uin()) {
                $lobj = $game->location($location);
                if ($lobj) $lobj->grind();
                else $game->uin()->remove($location);
            }

        if ($populate) {
            if ($this->progress <= 2)
                $game->reset_maps('roadtrip_easy');
            elseif ($this->progress <= 5)
                $game->reset_maps('roadtrip_medium');
            else $game->reset_maps('roadtrip_hard');
        } else $game->reset_maps('roadtrip_driving');

        $game->map_main()->insert_location($this);
    }

    private function drivecontrol($start, $break = false) {
        /**
         * @global Model_Game $game
         * @global Model_Player $player
         */
        global $player, $game;

        if ($start == $this->driving)
            return;

        if (!$start && !$break) {
            $this->progress++;
            $game->config('zombies.accum', $game->config('zombies.accum') + 0.15);
            $game->config('places.dryout_factor', $game->config('places.dryout_factor') + 0.15);
            $game->config('places.outworld.location_density', $game->config('places.outworld.location_density') + 0.05);
            $game->config('places.outworld.alt_spawn_stranger', false);
        }

        if (!$start)
            foreach (Tool_Scripts::at_location($this->uin()) as $p)
                $p->get_status()->remove('fragile/driver');
        else new Model_Buffs_Driver($player->id());

        $game->delete_lobby();
        $this->impaler = 0;
        if ($this->has_upgrade('fence')) {
            $this->remove_upgrades(['fence','deffence1','deffence2']);
            $this->defense -= 10;
        }

        $this->force_nomap = (!$start && $break);

        if (!$break)
            $this->mapcontrol(!($this->driving = $start));
        else $this->driving = $start;
    }

    private function motor_status() {
        $s = 1;
        foreach ($this->parts as $status)
            $s = min($s, $status[0]/$status[1]);

        return $s;
    }

    public function weight() {
        /** @global Model_Game $game */
        global $game;
        $w = $this->inventory()->weight();
        foreach ($game->players(false) as $p)
            /** @var Model_Player $p */
            $w += $p->inventory()->weight() + ($p->job(1080) ? 25 : 50);

        return $w;
    }

    public function weight_max() {
        return static::$max_weight;
    }

    public function stop_break() {
        /** @global Model_Player $player */
        global $player;
        $player->log()->add('Du fährst deinen Wohnwagen auf den Standstreifen und hälst an. Eine kleine Pause tut gut...');
        $this->drivecontrol(false, true);
    }

    public function stop() {
        /** @global Model_Player $player */
        global $player;
        $player->log()->add('Du suchst einen geeigneten Parkplatz und hälst das Wohnmobil an. Tja, Zeit sich hier mal etwas umzusehen...');
        $this->drivecontrol(false);
    }

    public function repair($addr, $count) {
        /** @global Model_Player $player */
        global $player;
        if ($this->driving) return false;
        if ($player->get_status()->retrieve('fragile')) return false;

        foreach ($this->parts as $part => &$data) {
            if (Tool_System::getClassID($part) == $addr) {
                $num = min($count, $data[1] - $data[0]);

                if ($num <= 0) {
                    $player->log()->add('Eigentlich sieht hier alles gut in Schuss aus... an diesen Teilen brauchst du nichts zu reparieren.');
                    return true;
                }

                if (Tool_Scripts::consume_available_items(array($part => $num), true, true, false)) {
                    $data[0] += $num;
                    $player->log()->add('Sehr gut, die Ersatzteile haben genau gepasst. Du hast den Wohnwagen repariert.');
                } else $player->log()->add('Leider fehlen dir hierfür die Ersatzteile...');

                break;
            }
        }

        return true;
    }

    public function start() {
        /** @global Model_Player $player */
        global $player;

        if ($this->driving)
            return;
        if ($player->get_status()->retrieve('fragile'))
            return;

        if ($player->job(1080)) {
            $player->log()->add('Es hat diverse Vorteile, ein Kind zu sein. Die Tatsache, dass du nicht Autofahren kannst, ist keiner davon.');
            return;
        }

        if ($this->motor_status() <= 0) {
            $player->log()->add('Du drehst den Zündschlüssel und hörst ein Klappern, aber der Motor springt nicht an. Irgend etwas muss da kaputt sein...');
            return;
        }

        if (!$this->force_nomap && $this->weight() > static::$max_weight) {
            $player->log()->add('Du drehst den Zündschlüssel und trittst auf das Gaspedal. Der Motor ächzt, aber du kommst keinen Meter vorran. Anscheinend ist das Wohnmobil überladen...');
            return;
        }

        $player->log()->add('Du drehst den Zündschlüssel und trittst auf das Gaspedal. Mit beeindruckendem Tempo siehst du den Parkplatz im Rückspiegel verschwinden. Hier wirst du wohl nie wieder hinkommen.... gut so!');
        $this->zombie_factory()->accumulation(0);
        $this->drivecontrol(true);
    }

    public function get_speed($raw_kmh = false) {
        if (!$this->driving) return 0;
        $speed = 35 + 75 * sqrt($this->motor_status());
        return $raw_kmh ? $speed : $speed/12;
    }

    public function tick() {
        if (!$this->driving) {
            foreach (Tool_Scripts::at_location($this->uin()) as $p)
                $p->get_status()->remove('fragile/driver');
            return;
        }
        $this->km += $this->get_speed();

        foreach (Tool_Scripts::at_location($this->uin()) as $p)
            if ($p->get_status()->retrieve('fragile/driver')) {

                $kc = 100;
                if (($s = $p->get_status()->get(Model_Status::MS_STAT_SLEEPY) < 20))
                    $kc *= $s/20;
                if (($s = $p->get_status()->get(Model_Status::MS_STAT_DRUNK) > 20))
                    $kc *= (100-$s)/80;

                if (mt_rand(0,100) > $kc) {

                    $this->log()->add(':name hat einen Unfall gebaut! Die Insassen haben Verletzungen davon getragen und der Wohnwagen wurde schwer beschädigt!', array(':name' => $p->name()));
                    foreach (Tool_Scripts::at_location($this->uin()) as $ps) {
                        $ps->set_cod('Autounfall');
                        $ps->get_status()->modify(Model_Status::MS_STAT_HEALTH, -mt_rand(10,80));
                        if ($ps->id() != $p->id())
                            $ps->log()->add('Du hast gerade eben noch friedlich aus dem Fenster geschaut, jetzt liegst du plötzlich in einem Trümmerhaufen aus Blech und Blut. :name, dieser verblödete Idiot, hat anscheinend einen Unfall gebaut.', array(':name' => $p->name()));
                    }
                    $p->get_status()->modify(Model_Status::MS_STAT_HEALTH, -mt_rand(20,50));
                    $p->log()->add('Tja, sowas passiert wenn man in deinem Zustand autofährt. Vielleicht hättest du das jemand anderen tun lassen sollen, zum Beispiel jemandem der nicht das einzige Fahrzeugwrack auf der Straße im Umkreis von 10 Kilometern frontal rammt?');

                    foreach ($this->parts as &$status_value)
                        $status_value[0] = 0;

                    $this->drivecontrol(false);
                    return;
                }

            }

        $status = $this->motor_status();
        $damage = (mt_rand(0,110) > ($status * 100));

        if (!$damage)
            return;

        $c = array(array('chance' => $this->parts['Model_Items_Generic_Sum'][0], 'value' => 'Model_Items_Generic_Sum'));
        if ($status <= .9)  $c[] = array('chance' => $this->parts['Model_Items_Generic_Tube'][0], 'value' => 'Model_Items_Generic_Tube');
        if ($status <= .75) $c[] = array('chance' => $this->parts['Model_Items_Generic_Belt'][0], 'value' => 'Model_Items_Generic_Belt');
        if ($status <= .5)  $c[] = array('chance' => $this->parts['Model_Items_Generic_Motor'][0], 'value' => 'Model_Items_Generic_Motor');

        $this->parts[Tool_Gambling::roulette($c)][0]--;

        if ($this->motor_status() <= 0)
            $this->drivecontrol(false);
    }

    public function pretick() {
        parent::pretick();

        if ($this->is_driving())
            $this->zombie_factory()->accumulation(0);
    }
}	