<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Hero_Worthless extends Model_Items_Abstract_Virtual {

    public function __construct($level = 20) {
        parent::__construct();
        $this->remaining = array(
            'be_worthless_0' => ceil($level/3),
            'be_worthless_1' => ceil($level/10),
        );
    }

    protected static $static_info = Array(
        'name' => 'Heldentaten',
    );

    protected function hid(): Model_Hid {
        $rnd_1 = array(
            'schwulen',
            'lesbischen',
            'liberalen',
            'muslimischen',
            'schwarzen',
            'kriminellen'
        );
        $rnd_2 = array(
            'Migranten',
            'SJWs',
            'Memes',
            'Hippies',
            'Demokraten',
            'Transsexuellen',
            'Schwarzen',
            'Kriminellen'
        );
        return parent::hid()
            ->add_action('Als Idiot outen', Model_Action::factory()
                ->buttonskin('hero hja')
                ->description('Belustige deine Mitspieler mit einem unglaublich herablassenden Vortrag über ein politisches Thema deiner Wahl, der am Ende nur offenbart, dass du nicht einmal in Ansätzen verstehst, wie Dinge funktionieren. Kostet 15 Energie. Jeder andere Spieler in der Nähe regeneriert 25 Energie und Müdigkeit und erhält den Buff "Aufgeregt" für 10 Minuten. Hat keinen Effekt auf Kinder und Spieler, die gerade beschäftigt sind.')
                ->effect(
                    Model_Effect::factory()
                        ->custom(function($p) {
                            /** @var Model_Player $p */
                            foreach (Tool_Scripts::at_location($p->location_class(), true, false) as $ps) if ($ps->id() !== $p->id()) {
                                // Ignore Wunderkind
                                if ($ps->job(1080))
                                    $ps->log()->add('Diese Erwachsenen werden auch immer wunderlicher... Seit einer Stunde hält :p wild gestikulierend einen Vortrag über irgend etwas. Ob er von einem Skorpion gestochen wurde...?', array(':p' => $p->name()));
                                elseif (!$ps->get_status()->retrieve('fragile')) {
                                    $ps->get_status()->modify(Model_Status::MS_STAT_ENERGY, 25, Model_Status::MS_STAT_SLEEPY, 25);
                                    new Model_Buffs_Exited($ps->id(), 2);
                                    $ps->log()->add('Bessere Unterhaltung gibt es nirgens! :p hat gerade allen erklärt, wie sich Buddhisten, Reptilienmenschen und Chinesen zusammengetan haben, um Donald Trump mithilfe genmanipulierter lesbischer Zombies des Amtes zu entheben.', array(':p' => $p->name()));
                                }
                            }
                        })
                        ->effect(Model_Status::MS_STAT_ENERGY, -15)
                        ->message('Diese Zombieapokalypse ist einzig und allein die Schuld von :subject1 :subject2. Du bist der allererste Mensch, der das herausgefunden hat und sich traut, es zu sagen - also solltest du das auch dringend immer und immer wieder tun!', array(), array(':subject1' => Tool_Gambling::select($rnd_1), ':subject2' => Tool_Gambling::select($rnd_2)))
                )
            , 'be_worthless_0')

            ->add_action('Content stehlen', Model_Action::factory()
                ->buttonskin('hero hja')
                ->description('Dank deiner unglaublichen Originalität bist du in der Lage, die Heldentaten eines anderen anwesenden Spielers zu kopieren.')
                ->condition(function(Model_Player $p) {
                    $b = 0;
                    foreach (Tool_Scripts::at_location($p->location_class(), true, true) as $ps)
                        if ($ps->id() !== $p->id())
                            $b++;
                    return $b > 0;
                })
                ->fail_message('Du bist nicht kreativ genug, um etwas zu tun, was du nicht von jemand anderen kopiert hast ...')
                ->effect(
                    Model_Effect::factory()
                        ->custom(function(Model_Player $p) {
                            $items = [];

                            /** @var Model_Player $p */
                            foreach (Tool_Scripts::at_location($p->location_class(), true, false) as $ps) if ($ps->id() !== $p->id()) {
                                switch ($ps->job()) {
                                    case 1080: case 1081:   // Child
                                        $items[] = new Model_Items_Virtual_Hero_Child(1);
                                        break;
                                    case 10020:case 10021:  // Football Player
                                        $items[] = new Model_Items_Virtual_Hero_Coach(1);
                                        break;
                                    case 10030:case 10031:  // Med Student
                                        $items[] = new Model_Items_Virtual_Hero_Student(1);
                                        break;
                                    case 10040:  // WRA
                                        $items[] = new Model_Items_Virtual_Hero_Woman(1);
                                        break;
                                    default: break;
                                }
                            }

                            if (count($ps->inventory()->get(Model_Items_Virtual_Hero_ShopEagleEye::cls())) > 0)
                                $items[] = new Model_Items_Virtual_Hero_ShopEagleEye();
                            if (count($ps->inventory()->get(Model_Items_Virtual_Hero_ShopArchitective::cls())) > 0)
                                $items[] = new Model_Items_Virtual_Hero_ShopArchitective();

                            $item = (count($items) > 0) ? Tool_Gambling::select($items) : null;

                            /** @var $item Model_Items_Abstract_Virtual */
                            if ($item) {
                                $item->set_remaining_actions(1);
                                $p->inventory()->add($item);
                                $p->log()->add('Hurra! Du hast es geschafft, jemandes Fähigkeiten zu kopieren und als deine eigenen auszugeben!');
                            } else $p->log()->add('Leider gab es hier für dich nichts zu stehlen ...');

                        })
                )
                , 'be_worthless_1');
    }
}	