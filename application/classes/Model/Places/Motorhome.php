<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Motorhome extends Model_Places_Home {

    protected static $name = 'Klappriger Wohnwagen';
    protected static $description = 'Als die Zombies kamen haben sich die meisten deiner Nachbarn einfach in ihren Häusern verbarrikadiert. Du hingegen bist mit deinem Wohnmobil geflohen, was sich im Nachhinein leider auch als nicht optimal erwiesen hat. Immerhin musst du regelmäßig Benzin für dieses Teil finden und es in Schuss halten, um weiterfahren zu können.';

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
        if ($new !== null) {
            $this->home_extensions("bed", "lv1", true);
            $this->home_extensions("sofa", "lv1", true);
            $this->home_extensions("manu", "base", true);
            $this->home_extensions("solar", "base", true);
            $this->home_extensions("solar", "generator", true);
            $this->home_extensions("kitchen", "base", true);
            $this->set_decay(0, true);
        }
        return parent::uin($new);
    }

    private function mapcontrol($populate) {
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

        $game->reset_maps($populate ? 'roadtrip_easy' : 'roadtrip_driving');
        $game->map_main()->insert_location($this);
    }

    private function drivecontrol($start, $break = false) {
        if ($start == $this->driving)
            return;

        if (!$start && !$break)
            $this->progress++;

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

    public function interaction_break() {
        global $player;
        $player->log()->add('Du fährst deinen Wohnwagen auf den Standstreifen und hälst an. Eine kleine Pause tut gut...');
        $this->drivecontrol(false, true);
    }

    public function interaction_stop() {
        global $player;
        $player->log()->add('Du suchst einen geeigneten Parkplatz und hälst das Wohnmobil an. Tja, Zeit sich hier mal etwas umzusehen...');
        $this->drivecontrol(false);
    }

    public function interaction_repair($arg) {
        global $player;
        if ($this->driving) return false;
        if (!isset($arg['action']) || !$arg['action'] || !isset($arg['num']) || $arg['num'] <= 0) return false;

        foreach ($this->parts as $part => &$data) {
            if (md5($part) == $arg['action']) {
                $num = min($arg['num'], $data[1] - $data[0]);

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

    public function interaction_start() {
        global $player;

        if ($this->driving)
            return;

        if ($this->motor_status() <= 0) {
            $player->log()->add('Du drehst den Zündschlüssel und hörst ein Klappern, aber der Motor springt nicht an. Irgend etwas muss da kaputt sein...');
            return;
        }

        if ($this->weight() > static::$max_weight) {
            $player->log()->add('Du drehst den Zündschlüssel und trittst auf das Gaspedal. Der Motor ächzt, aber du kommst keinen Meter vorran. Anscheinend ist das Wohnmobil überladen...');
            return;
        }

        $player->log()->add('Du drehst den Zündschlüssel und trittst auf das Gaspedal. Mit beeindruckendem Tempo siehst du den Parkplatz im Rückspiegel verschwinden. Hier wirst du wohl nie wieder hinkommen.... gut so!');
        $this->zombie_factory()->reset_zombie_population();
        $this->drivecontrol(true);
    }

    public function get_speed($raw_kmh = false) {
        if (!$this->driving) return 0;
        $speed = 35 + 75 * sqrt($this->motor_status());
        return $raw_kmh ? $speed : $speed/12;
    }

    public function tick() {
        if (!$this->driving)
            return;

        $status = $this->motor_status();
        $damage = (mt_rand(0,110) > ($status * 100));

        $this->km += $this->get_speed();

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
            $this->zombie_factory()->reset_zombie_population();
    }
}	