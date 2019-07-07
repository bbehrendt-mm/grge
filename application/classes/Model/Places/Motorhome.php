<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Motorhome extends Model_Places_Home {

    protected static $location_name = 'Klappriger Wohnwagen';
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

    public function get_level_progress(): int
    {
        return $this->progress;
    }

    public function get_parts(): array
    {
        return $this->parts;
    }

    public function get_distance(): int
    {
        return $this->km;
    }

    public function get_map_points(): int
    {
        return $this->get_distance();
    }

    public function get_level(): int
    {
        return $this->progress;
    }

    public function is_driving(): bool
    {
        return $this->driving;
    }

    public function mapable(): bool
    {
        return !$this->driving && !$this->force_nomap;
    }

    public function setup_primary_rooms(): Model_Room
    {
        $room = $this->create_new_room(5,['inside','primary']);

        $room->name('Wohnmobil', true);
        $room->name_is_fixed(true);

        $room->upgrade('Wohnmobil',false,['common','common_hideout']);
        $room->inventory()->add(new Model_Items_Virtual_Location_Room_Generic(
            'Verteidigen...', null, 'fighter'
        ));

        Model_Blueprints::fast_apply($this, 'rooms', ['motorhome'], $room);
        Model_Blueprints::fast_apply($this, 'upgrades', ['hideout','bedr1','sofa1','gen1','gen2','ktc2'], $room);

        return $room;
    }

    public function setup_additional_rooms(): void
    {
        $this->create_new_room(25,['outside'])->set_default_state();
        $this->create_new_room(25,['outside'])->set_default_state();
        $this->create_new_room(25,['outside'])->set_default_state();
    }

    private function mapcontrol($populate): void
    {
        foreach (Globals::CurrentGameF()->players(false) as $p) {
            /** @var Model_Player $p */
            if ($p->location_class() !== $this->uin()) {
                if ($p->get_status()->alive()) {
                    $p->get_status()->set_cause_of_death('Zurückgelassen');
                    $p->kill();
                }
                $p->location_class($this->uin());
            }
        }

        foreach (Globals::CurrentGameF()->locations() as $location)
            if ($location !== $this->uin()) {
                $lobj = Globals::CurrentGameF()->location($location);
                if ($lobj) $lobj->grind();
                else Globals::CurrentGameF()->uin()->remove($location);
            }

        if ($populate) {
            if ($this->progress <= 2)
                Globals::CurrentGameF()->reset_maps('roadtrip_easy');
            elseif ($this->progress <= 5)
                Globals::CurrentGameF()->reset_maps('roadtrip_medium');
            else Globals::CurrentGameF()->reset_maps('roadtrip_hard');
        } else Globals::CurrentGameF()->reset_maps('roadtrip_driving');

        Globals::CurrentGameF()->map_main()->insert_location($this);
    }

    private function drivecontrol($start, $break = false): void
    {
        if ($start === $this->driving)
            return;

        if (!$start && !$break) {
            $this->progress++;
            Globals::CurrentGameF()->config('zombies.accum', Globals::CurrentGameF()->config('zombies.accum') + 0.15);
            Globals::CurrentGameF()->config('places.dryout_factor', Globals::CurrentGameF()->config('places.dryout_factor') + 0.1);
            Globals::CurrentGameF()->config('places.outworld.location_density', Globals::CurrentGameF()->config('places.outworld.location_density') + 0.05);
            Globals::CurrentGameF()->config('places.outworld.alt_spawn_stranger', false);
        }

        if (!$start) {
            $this->roomF()->enabled(true);
            foreach (Tool_Scripts::at_location($this->uin()) as $p)
                $p->get_status()->remove('fragile/driver');
        } else {
            $this->roomF()->enabled(false);
            new Model_Buffs_Driver(Globals::CurrentPlayerF()->id());
        }

        Globals::CurrentGameF()->delete_lobby();
        foreach ($this->rooms as $room)
            if (!$room->has_tag('primary') && !$room->has_tag('addcaravan')) {
                $room->clear();
                $room->remove_tag('inside');
                $room->add_tag('outside');
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
        $w = $this->inventory()->weight();
        foreach (Globals::CurrentGameF()->players(false) as $p)
            /** @var Model_Player $p */
            $w += $p->inventory()->weight() + ($p->job_child() ? 25 : 50);

        return $w;
    }

    public function weight_max(): int
    {
        return static::$max_weight;
    }

    public function stop_break(): void
    {
        Globals::PrimaryPlayerF()->log()->add('Du fährst deinen Wohnwagen auf den Standstreifen und hälst an. Eine kleine Pause tut gut...');
        $this->drivecontrol(false, true);
    }

    public function stop(): void
    {
        Globals::PrimaryPlayerF()->log()->add('Du suchst einen geeigneten Parkplatz und hälst das Wohnmobil an. Tja, Zeit sich hier mal etwas umzusehen...');
        $this->drivecontrol(false);
    }

    public function repair($addr, $count): bool
    {
        if ($this->driving) return false;
        if (Globals::PrimaryPlayerF()->get_status()->retrieve('fragile')) return false;

        foreach ($this->parts as $part => &$data) {
            if (Tool_System::getClassID($part) === $addr) {
                $num = min($count, $data[1] - $data[0]);

                if ($num <= 0) {
                    Globals::PrimaryPlayerF()->log()->add('Eigentlich sieht hier alles gut in Schuss aus... an diesen Teilen brauchst du nichts zu reparieren.');
                    return true;
                }

                if (Tool_Scripts::consume_items([Struct_ItemEntry::make($part, $num)], Struct_ScriptItemSource::default())) {
                    $data[0] += $num;
                    Globals::PrimaryPlayerF()->log()->add('Sehr gut, die Ersatzteile haben genau gepasst. Du hast den Wohnwagen repariert.');
                } else Globals::PrimaryPlayerF()->log()->add('Leider fehlen dir hierfür die Ersatzteile...');

                break;
            }
        }

        return true;
    }

    public function start(): void
    {
        if ($this->driving) return;
        if (Globals::PrimaryPlayerF()->get_status()->retrieve('fragile')) return;

        // Ignore Wunderkind
        if (Globals::PrimaryPlayerF()->job(1080)) {
            Globals::PrimaryPlayerF()->log()->add('Es hat diverse Vorteile, ein Kind zu sein. Die Tatsache, dass du nicht Autofahren kannst, ist keiner davon.');
            return;
        }

        if ($this->motor_status() <= 0) {
            Globals::PrimaryPlayerF()->log()->add('Du drehst den Zündschlüssel und hörst ein Klappern, aber der Motor springt nicht an. Irgend etwas muss da kaputt sein...');
            return;
        }

        if (!$this->force_nomap && $this->weight() > static::$max_weight) {
            Globals::PrimaryPlayerF()->log()->add('Du drehst den Zündschlüssel und trittst auf das Gaspedal. Der Motor ächzt, aber du kommst keinen Meter vorran. Anscheinend ist das Wohnmobil überladen...');
            return;
        }

        Globals::PrimaryPlayerF()->log()->add('Du drehst den Zündschlüssel und trittst auf das Gaspedal. Mit beeindruckendem Tempo siehst du den Parkplatz im Rückspiegel verschwinden. Hier wirst du wohl nie wieder hinkommen.... gut so!');
        $this->zombie_factory()->accumulation(0);
        $this->drivecontrol(true);
    }

    public function get_speed($raw_kmh = false) {
        if (!$this->driving) return 0;
        $speed = 35 + 75 * sqrt($this->motor_status());
        return $raw_kmh ? $speed : $speed/12;
    }

    public function tick($type = Interface_Tickable::IT_TYPE_PLAYER): bool
    {
        if (!$this->driving) {
            foreach (Tool_Scripts::at_location($this->uin()) as $p)
                $p->get_status()->remove('fragile/driver');
            return true;
        }
        $this->km += $this->get_speed();

        foreach (Tool_Scripts::at_location($this->uin()) as $p)
            if ($p->get_status()->retrieve('fragile/driver')) {

                $kc = 100;
                if ( ($s = $p->get_status()->get(Model_Status::MS_STAT_SLEEPY)) < 20)
                    $kc *= $s/20;
                if ( ($s = $p->get_status()->get(Model_Status::MS_STAT_DRUNK)) > 20)
                    $kc *= (100-$s)/80;

                if (random_int(0,100) > $kc) {

                    $this->log()->add(':name hat einen Unfall gebaut! Die Insassen haben Verletzungen davon getragen und der Wohnwagen wurde schwer beschädigt!', array(':name' => $p->name()));
                    foreach (Tool_Scripts::at_location($this->uin(), true, true) as $ps) {
                        $ps->get_status()->set_cause_of_death('Autounfall');
                        $ps->get_status()->modify(Model_Status::MS_STAT_HEALTH, -random_int(10,80));
                        if (!Tool_Scripts::is_npc($ps) && $ps->id() !== $p->id())
                            $ps->log()->add('Du hast gerade eben noch friedlich aus dem Fenster geschaut, jetzt liegst du plötzlich in einem Trümmerhaufen aus Blech und Blut. :name, dieser verblödete Idiot, hat anscheinend einen Unfall gebaut.', array(':name' => $p->name()));
                    }

                    $p->get_status()->modify(Model_Status::MS_STAT_HEALTH, -random_int(20,50));
                    $p->log()->add('Tja, sowas passiert wenn man in deinem Zustand autofährt. Vielleicht hättest du das jemand anderen tun lassen sollen, zum Beispiel jemandem der nicht das einzige Fahrzeugwrack auf der Straße im Umkreis von 10 Kilometern frontal rammt?');

                    foreach ($this->parts as &$status_value)
                        $status_value[0] = 0;
                    unset($status_value);

                    $this->drivecontrol(false);
                    return true;
                }

            }

        $status = $this->motor_status();
        $damage = (random_int(0,110) > ($status * 100));

        if (!$damage)
            return true;

        $c = array(array('chance' => $this->parts['Model_Items_Generic_Sum'][0], 'value' => 'Model_Items_Generic_Sum'));
        if ($status <= .9)  $c[] = array('chance' => $this->parts['Model_Items_Generic_Tube'][0], 'value' => 'Model_Items_Generic_Tube');
        if ($status <= .75) $c[] = array('chance' => $this->parts['Model_Items_Generic_Belt'][0], 'value' => 'Model_Items_Generic_Belt');
        if ($status <= .5)  $c[] = array('chance' => $this->parts['Model_Items_Generic_Motor'][0], 'value' => 'Model_Items_Generic_Motor');

        $this->parts[Tool_Gambling::roulette($c)][0]--;

        if ($this->motor_status() <= 0)
            $this->drivecontrol(false);

        return true;
    }

    public function pretick(): void
    {
        parent::pretick();

        if ($this->is_driving())
            $this->zombie_factory()->accumulation(0);
    }

    public function is_upgradable(): bool
    {
        return !$this->is_driving() && parent::is_upgradable();
    }

    public function add_permanent_room() {
        $this->create_new_room(20,['inside','addcaravan'])->set_default_state();
    }
}	