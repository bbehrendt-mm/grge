<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Strangewood_Singleplayer extends Model_Places_Strangewood_Final {

    protected function name_by_profession($p) : string {
        switch ($p) {

            //<editor-fold desc="Single Player Professions">
            case    0:case 1011:case 1012:case 1013:case 2030:case 3010: // Citizen
                return "Verfallener Bungalow";
            case  100:case 1020:case 1021:case 2020:                     // Soldier & Killer
                return "Verfallene Kaserne";
            case  200:case 1030:case 1031:case 1080:case 1081:           // Pathfinder and Child
                return "Verfallenes Pfadfinderlager";
            case  300:case 1040:case 1041:                               // Priest
                return "Verfallene Kirche";
            case  400:case 1050:case 1051:case 3020:                     // Rich git & Painter
                return "Verfallene Lodge";
            case 1060:case 1061:                                         // Survivalist
                return "Eingestürzte Höhle";
            case 1070:case 1071:case 2010:case 4010:                     // Muscleman & Berserker & Gladiator
                return "Überwucherte Lichtung";
            case 3030:                                                   // Tech
                return "Verfallener Bunker";
            //</editor-fold>

            //<editor-fold desc="Multi Player Professions">
            case 10010: case 10011:                                      // Politician & Presitent
                return "Überwucherter Helikopter-Landeplatz";
            case 10020: case 10021:                                      // Football Coach & Player
                return "Überwuchertes Football-Feld";
            case 10030: case 10031:                                      // Med Student & Surgeon
                return "Verfallenes Labor";
            case 10040:                                                  // WRA
                return "Verfallenes Büro";
            case 10041:                                                  // Nerd
                return "Überwucherter Keller";
            case 12010: case 12020: case 12030: case 12040: case 12050:  // Townships Professions
            case 12060: case 12070: case 12080:
                return "Ruinen einer Stadt";
            //</editor-fold>

            default: return "Die Geheime Ruine";
        }
    }

    protected function desc_by_profession($p) : string {
        switch ($p) {
            //<editor-fold desc="Single Player Professions">
            case    0:case 1011:case 1012:case 1013:case 2030:case 3010: // Citizen
                return "Du erinnerst dich an diesen Ort... Als die Zombie-Apokalypse began, hast du hier mit deiner Familie deinen Sommerurlaub verbracht. Ihr habt Türen und Fenster blockiert und auf Hilfe gewartet, die jedoch nie kam. Irgendwann wurden Nahrung und Wasser knapp, also hast du heimlich die Eingangstür aufgeschlossen und dich im Badezimmer versteckt...";

            case  100:case 1020:case 1021:                               // Soldier
                return "Du erinnerst dich an diesen Ort... Als du noch jung warst, hast du hier mit deinen Freunden die Grundausbildung absolviert. Euer Dienst war meist langweilig, weswegen ihr die Zeit damit verbracht habt, einen der anderen Soldaten mit grausamen Streichen zu quälen. Als du eines Abends von einem Kontrollgang im Wald zurück kamst, findest du deine Kameraden brutal abgeschlachtet in der Schlafstube vor. Von dem Soldaten, den ihr schikaniert habt, fehlte jede Spur ...";
            case 2020:                                                   // Killer
                return "Du erinnerst dich an diesen Ort... Als du noch jung warst, hast du hier deine Grundausbildung absolviert. Eine Gruppe anderer Soldaten hat keine Gelegenheit ausgelassen, dich mit grausamen Streichen zu quälen. Als du eines Abends Wachdienst an der Waffenkammer schiebst, fasstest du einen Entschluss. Bis an die Zähne bewaffnest hast du die Schlafstube der anderen Soldaten betreten und jeden einzelnen von ihnen brutal abgeschlachtet. Danach hast du der Kaserne und einem bisherigen Leben für immer den Rücken gekehrt...";

            case  200:case 1030:case 1031:case 1080:case 1081:           // Pathfinder
                return "Du erinnerst dich an diesen Ort... Vor vielen Jahren warst du mit ein paar Freunden in diesem Lager. An einem warmen Sommertag habt ihr mit dem Rest der Gruppe sowie einem Betreuer einen Ausflug zum See gemacht. Du hast dem Betreuer mit deinen Freunden einen Streich gespielt, und während er dadurch abgelenkt war, ist eines der anderen Kinder im See ertrunken ...";

            case  300:case 1040:case 1041:                               // Priest
                return "Du erinnerst dich an diesen Ort... Als die Zombie-Apokalypse begann, hast du in dieser Kirche als Priester gearbeitet. Als die Menschen auf der Suche nach Zuflucht zur Kirche kamen, hast du unter dem Vorwand, Zeichen der Zombie-Infektion erkennen zu können, nur deine Gemeindemitglieder in die Kirche gelassen. Da du - entgegen deiner Behauptung - jedoch keine Ahnung hattest, wie man Infizierte erkennen kann, kam es bald zu einem Zombie-Ausbruch innerhalb der Kirche. Glücklicherweise konntest du dich im Glockenturm verstecken, während der Rest deiner Gemeinde von den Zombies abgeschlachtet wurde ...";

            case  400:case 1050:case 1051:                               // Rich git
                return "Du erinnerst dich an diesen Ort... Diese Lodge war Teil eines weitläufigen Erholungszentrums aus mehreren Lodges und Bungalows mitten im Wald, welches dir gehörte. Als die Zombie-Apokalypse ausbrach, hast du dich gerade mit dem Manager der Anlage in dieser Lodge getroffen. Der Manager wollte die Sicherheitskräfte anweisen, die Gäste im Park zu evakuieren - du hast jedoch darauf bestanden, sämtliche Bemühungen nur darauf konzentriert werden sollen, dich sicher zurück zu deinem Hubschrauber zu bringen ...";
            case 3020:                                                   // Painter
                return "Du erinnerst dich an diesen Ort... Diese Lodge war Teil eines weitläufigen Erholungszentrums aus mehreren Lodges und Bungalows mitten im Wald. Als die Zombie-Apokalypse began, warst du auf Einladung des Managers zu Gast in der Lodge, um zusammen mit zwei Kollegen einen Lageplan der Anlage zu zeichnen. Du erinnerst dich, dass du einen Helikopter abheben gehört hast - kurz darauf wurdet ihr von einer Gruppe von Zombies attackiert. Du bist in einen nahe gelegenen Bungalow geflüchtet, hast die Tür hinter dir verschlossen - und deine Ohren zugehalten, um nicht die Schreie deiner Kollegen hören zu müssen, den du ausgesperrt hattest.";

            case 1060:                                                  // Survivalist
                return "Du erinnerst dich an diesen Ort... Als die Zombie-Apokalypse begann, hast du dich zusammen mit zwei Kollegen in einem nahe gelegenen Ferienpark aufgehalten, um eine Karte des Parks anzufertigen. Als ihr von einer Gruppe Zombies angegriffen wurdet, hat sich einer deiner Kollegen in einem Bungalow verbarrikadiert. Du und der andere Kollege konntet euch in diese Höhle retten - allerdings warst du dir nicht sicher, ob dein Kollege bei dem Angriff möglicherweise infiziert wurde. Als dein Kollege sich etwas von dir entfernt hatte, hast du einen Stützpfeiler zerstört um einen Einsturz zu provozieren und deinen Kollegen in den Tiefen der Höhle einzusperren.";
            case 1061:                                                  // Wolfman
                return "Du erinnerst dich an diesen Ort... Als die Zombie-Apokalypse begann, hast du dich zusammen mit zwei Kollegen in einem nahe gelegenen Ferienpark aufgehalten, um eine Karte des Parks anzufertigen. Als ihr von einer Gruppe Zombies angegriffen wurdet, hat sich einer deiner Kollegen in einem Bungalow verbarrikadiert. Du und der andere Kollege konntet euch in diese Höhle retten - allerdings warst du dir nicht sicher, ob dein Kollege bei dem Angriff möglicherweise infiziert wurde. Also hast du dir heimlich alle Vorräte geschnappt und bist tiefer in die Höhle gelaufen - nur, um wenige Minuten später durch einen Einsturz in der Höhle eingesperrt zu werden.";

            case 1070:case 1071:case 2010:case 4010:                    // Muscleman & Berserker
                return "Du erinnerst dich an diesen Ort... Auf dieser Lichtung hast du als Kind öfters mit deinen Schulfreunden für den Sportunterricht trainiert. Einer deiner Schulkameraden hat dich in jeder Disziplin überflügelt, was dich unglaublich gewurmt hat - also hast du eines Tages ein rötliches Fläschchen aus dem Chemieraum der Schule gestohlen und in seine Wasserflasche geschüttet. Zum Glück für dich ist der Notarzt von einem Herzinfarkt ausgegangen und hat keine Autopsie angeordnet...";

            case 3030:                                                  // Tech
                return "Du erinnerst dich an diesen Ort... deine Firma wurde beauftragt, in diesem ehemaligen Bunker einen Server-Raum für einen internationalen Konzern einzurichten. Als die Zombie-Apokalypse begann, hast du dich mit deinen Kollegen in diesem Bunker versteckt. Eure Vorräte waren begrenzt - also hast du deine Kollegen unter einem Vorwand in den Server-Raum gelockt, die Argon-Brandschutzanlage ausgelöst und deine Kollegen ersticken lassen ...";
            //</editor-fold>

            //<editor-fold desc="Multi Player Professions">
            case 10010: case 10011:                                      // Politician & Presitent
                return "Du erinnerst dich an diesen Ort... Als die ersten Berichte über eine Angriffe von Untoten aufkamen, befandest du dich gerade in deinem monatlichen Golf-Urlaub. Selbstverständlich hast du sofort einen Helikopter rufen lassen, der dich in Sicherheit bringt. Und wenn deine persönliche Assistenten willens gewäsen wäre, dir ein paar intime Gefälligkeiten zu erweisen, hättest du sie vielleicht sogar mitgenommen.";
            case 10020:                                                  // Football Coach
                return "Du erinnerst dich an diesen Ort... Vor Jahren hat deine Mannschaft hier gespielt. Einer deiner Spieler hatte seit einer ganzen Weile ein massives Aggressionsproblem, was dich allerdings nie groß gekümmert hat - immerhin war er der einzig wirklich talentierte Spieler, den du hattest. Zumindest, bis er nach einer herben Niederlage mitten auf dem Platz zwei Spieler aus der gegnerischen Mannschaft angegriffen und totgeschlagen hat...";
            case 10021:                                                  // Football Player
                return "Du erinnerst dich an diesen Ort... zumindest dunkel. Dein Gedächnis ist nach all den Schlägen auf den Kopf in deiner Karriere nicht mehr das allerbeste. Du hast hier dein letztes Spiel gespielt, bevor du für eine lange Zeit umziehen musstest... An die Details kannst du dich jedoch nicht mehr erinnern.";
            case 10030: case 10031:                                      // Med Student & Surgeon
                return "Du erinnerst dich an diesen Ort... du hast hier zusammen mit anderen Medizinern Medikamententests beaufsichtigt. Leider kam später heraus, dass dieses Labor - genau wie seine zwielichtigen Kunden - nie eine Lizenz für solche Tests besessen haben und die meisten Probanden auch nur bedingt freiwillig teilgenommen haben.";
            case 10040:                                                  // WRA
                return "Du erinnerst dich an diesen Ort... früher hast du hier gearbeitet! Deine Aufgabe war es, die Einhaltung rechtlicher und ethischer Vorgaben bei medizinischen Forschungsinstituten zu überwachen und entsprechende Kontrollen zu koordinieren. Zumindest in der Theorie - in der Praxis hast du einfach alles unterschrieben, was man dir vorgelegt hat. Immerhin warst du viel zu beschäftigt, Dinge zu finden, die dich als Frau diskriminieren und gegen die man protestieren kann.";
            case 10041:                                                  // Nerd
                return "Du erinnerst dich an diesen Ort... hier stand früher ein mehrstöckiges Wohnhaus, in dessen Keller du ein Apartment gemietet hattest. Die meiste Zeit hast du darin vor dem PC zugebracht und dir Verschwörungstheorien ausgedacht. Bevölkerungsaustausch, jüdische Weltverschwörung, ChemTrails - das übliche eben. Als die Zombie-Apokalypse began, hast du eine Liste mit Namen und Adressen der \"Schuldigen\" veröffentlicht - die nur ganz zufällig nur aus Leuten bestand, die du persönlich nicht leiden konntest. Von den meisten davon hat man dann auch nie wieder gehört ...";
            case 12010: case 12020: case 12030: case 12040: case 12050:  // Townships Professions
            case 12060: case 12070: case 12080:
                return "Du erinnerst dich an diesen Ort... nach der Zombie-Apokalypse hast du mit 39 anderen Menschen an diesem Ort gewohnt. Allerdings haben die sich stur geweigert, dich zum Bürgermeister zu machen. Also hast du eines Tages alle Vorräte aus der Bank gestohlen, den Schließmechanismus des Stadttors sabotiert und dich in die Wüste abgesetzt.";
            //</editor-fold>

            default: return "Du hast keinerlei Erinnerungen an diesen Ort ...";
        }
    }

    protected function out_by_profession($p) : bool {
        switch ($p) {
            case 1070:case 1071:case 2010:case 10020:case 10021:case 12010: case 12020: case 12030: case 12040:
            case 12050:case 12060: case 12070: case 12080:
                return true;

            default: return false;
        }
    }

    public function trigger_item_action(?Model_Items_Abstract_Item $item) {
        if ($item === null || $this->unlocked) {
            Globals::CurrentPlayerActualF()->log()->add('Nichts passiert ...');
            return;
        }

        $p = Globals::CurrentPlayerActualF()->job();
        $zombies = [];

        switch ($p) {

            //<editor-fold desc="Single Player Professions">
            case    0:case 1011:case 1012:case 1013:case 2030:case 3010: // Citizen
                $z1 = Model_Combat_Zombies_Ghul::factory()->name($p === 1012 ? 'Bill' : 'Barbara')->strength(35, 100, 1);
                $w1 = new Model_Items_Miniknife();
                $w1->enable_use_without_player();
                $z1->add_weapon_default($w1);
                $zombies[] = $z1;

                $z2 = Model_Combat_Zombies_Ghul::factory()->name('Kenny')->strength(25, 50, 1);
                for ($i = 0; $i < 5; $i++) {
                    $w2 = new Model_Items_Concrete();
                    $w2->enable_use_without_player();
                    $z2->add_weapon_default($w2);
                }

                $zombies[] = $z2;
                break;

            case  100:case 1020:case 1021:                               // Soldier
                $z1 = Model_Combat_Zombies_Ghul::factory()->name('Leonard')->strength(75, 100, 1);
                $w1 = new Model_Items_Batgunsplat();
                $w1->enable_use_without_player();
                $w1->set_built_in_ammo(1);
                $z1->add_weapon_default($w1);
                $zombies[] = $z1;
                break;

            case 2020:                                                   // Killer
                $d = ['Adam','Arliss','Kevyn','Dorian','Tim'];
                foreach ($d as $name) {
                    $z = Model_Combat_Zombies_Ghul::factory()->name($name)->strength(5, 100, 1);
                    $z->stats(0,0,0,0);
                    $w = new Model_Items_Oldrifle2();
                    $w->enable_use_without_player();
                    $w->set_built_in_ammo(1);
                    $z->add_weapon_default($w);
                    $zombies[] = $z;
                }
                break;

            case  200:case 1030:case 1031:case 1080:case 1081:           // Pathfinder
                $z = Model_Combat_Zombies_Ghul::factory()->name('Kevin')->strength(15, 50, 1);
                for ($i = 0; $i < 10; $i++) {
                    $w = new Model_Items_Concrete();
                    $w->enable_use_without_player();
                    $z->add_weapon_default($w);
                }

                $zombies[] = $z;
                break;

            case  300:case 1040:case 1041:                               // Priest
                $d = ['Ash','Cheryl','Scott','Linda','Shelly'];
                foreach ($d as $name) {
                    $z = Model_Combat_Zombies_Ghul::factory()->name($name)->strength(5, 100, 1);
                    $w = new Model_Items_Miniknife();
                    $w->enable_use_without_player();
                    $z->add_weapon_default($w);
                    $zombies[] = $z;
                }
                break;

            case  400:case 1050:case 1051:                               // Rich git
                $d = ['Annie','Jake','Bobby Joe','Henrietta','Ed'];
                foreach ($d as $name) {
                    $z = Model_Combat_Zombies_Ghul::factory()->name($name)->strength(5, 100, 1);
                    $w = new Model_Items_Concrete();
                    $w->enable_use_without_player();
                    $z->add_weapon_default($w);
                    $zombies[] = $z;
                }
                break;

            case 3020:                                                   // Painter
                $z1 = Model_Combat_Zombies_Ghul::factory()->name('Jay')->strength(5, 100, 1);
                $w1 = new Model_Items_Machete2P();
                $w1->enable_use_without_player();
                $z1->add_weapon_default($w1);
                $zombies[] = $z1;

                $z2 = Model_Combat_Zombies_Ghul::factory()->name('Bob')->strength(25, 100, 1);
                $w2 = new Model_Items_Bat();
                $w2->enable_use_without_player();
                $z2->add_weapon_default($w2);
                $zombies[] = $z2;
                break;

            case 1060:                                  // Survivalist
                $z = Model_Combat_Zombies_Ghul::factory()->name('Bob')->strength(25, 100, 1);
                $w = new Model_Items_Bat2();
                $w->enable_use_without_player();
                $z->add_weapon_default($w);
                $zombies[] = $z;
                break;

            case 1061:                                  // Wolfman
                $z = Model_Combat_Zombies_Ghul::factory()->name('Jay')->strength(15, 100, 1);
                $w = new Model_Items_Machete2P();
                $w->enable_use_without_player();
                $z->add_weapon_default($w);
                $zombies[] = $z;
                break;

            case 1070:case 1071:case 2010:case 4010:                    // Muscleman & Berserker
                $z = Model_Combat_Zombies_Ghul::factory()->name('Hollywood')->strength(50, 100, 1);
                $z->stats(15,12,null,10);
                $w = new Model_Items_Gush();
                $z->add_weapon_default($w);
                $zombies[] = $z;
                break;

            case 3030:                                                  // Tech
                $d = ['Homer','Marge','Bart','Lisa','Maggie','Abraham','Jacqueline','Patty','Selma'];
                foreach ($d as $name) {
                    $z = Model_Combat_Zombies_Ghul::factory()->name($name)->strength(1, 100, 1);
                    $z->stats(10,20,null,null);
                    $zombies[] = $z;
                }
                break;
            //</editor-fold>

            //<editor-fold desc="Multi Player Professions">
            case 10010: case 10011:                                      // Politician & Presitent
                $z = Model_Combat_Zombies_Ghul::factory()->name('Pepper')->strength(50, 50, 1);
                $w = new Model_Items_Knife();
                $w->enable_use_without_player();
                $z->add_weapon_default($w);
                $zombies[] = $z;
                break;
            case 10020: case 10021:                                                 // Football Coach
                $z = Model_Combat_Zombies_Ghul::factory()->set_unique(false)->name('Football Spieler')->strength(120, 120, 1);
                $z->stats(10,20,20,20);
                $zombies[] = $z;
                break;
            case 10030: case 10031: case 10040:                                    // Med Student & Surgeon & WRA
                $z = Model_Combat_Zombies_Ghul::factory()->set_unique(false)->name('Patient')->strength(1, 1, 5);
                $w = new Model_Items_Gush();
                $w->enable_use_without_player();
                $z->add_weapon_default($w);
                $z->stats(0,0,0,0);
                $zombies[] = $z;
                break;
            case 10041:                                                  // Nerd
                $z = Model_Combat_Zombies_Ghul::factory()->set_unique(false)->name('Irgendjemand')->strength(5, 5, 5);
                $w = new Model_Items_Concrete();
                $w->enable_use_without_player();
                $z->add_weapon_default($w);
                $zombies[] = $z;
                break;
            case 12010: case 12020: case 12030: case 12040: case 12050:  // Townships Professions
            case 12060: case 12070: case 12080:
                $z1 = Model_Combat_Zombies_Ghul::factory()->set_unique(false)->name('Stadtbewohner')->strength(2, 2, 10);
                $z1->stats(0,0,0,0);
                $zombies[] = $z1;
                $z2 = Model_Combat_Zombies_Ghul::factory()->set_unique(false)->name('Stadtbewohner')->strength(2, 2, 20);
                $z2->stats(0,0,0,0);
                $zombies[] = $z2;
                $z3 = Model_Combat_Zombies_Ghul::factory()->set_unique(false)->name('Stadtbewohner')->strength(2, 2, 2);
                $z3->stats(0,0,0,0);
                $w3 = new Model_Items_Concrete();
                $w3->enable_use_without_player();
                $z3->add_weapon_default($w3);
                $zombies[] = $z3;
                $z4 = Model_Combat_Zombies_Ghul::factory()->set_unique(false)->name('Stadtbewohner')->strength(2, 2, 7);
                $z4->stats(0,0,0,0);
                $zombies[] = $z4;
                break;
            //</editor-fold>

            default:
                $z = Model_Combat_Zombies_Ghul::factory()->name('Gabriel')->strength(100, 100, 1);
                $zombies[] = $z;
                break;
        }

        Tool_Scripts::combat([Tool_Scripts::at_location($this->uin()), $zombies], false, 15, $this, 'Ein Überraschungsangriff!');

        if (Globals::CurrentPlayerActualF()->get_status()->alive())
            Globals::CurrentPlayerActualF()->achievements()->achieve( Model_Achievement::MA_HALLOWEEN_19_2, 1 );

        $this->unlock();
    }

    protected function initialize() {
        $d = [];
        $p = Globals::CurrentPlayerActualF()->job();

        switch ($p) {
            //<editor-fold desc="Single Player Professions">
            case    0:case 1011:case 1012:case 1013:case 2030:case 3010: // Citizen
                $d = [$p === 1012 ? 'Bill' : 'Barbara', 'Kenny'];
                break;

            case  100:case 1020:case 1021:                               // Soldier
                $d = ['Leonard'];
                break;

            case 2020:                                                   // Killer
                $d = ['Adam','Arliss','Kevyn','Dorian','Tim'];
                break;

            case  200:case 1030:case 1031:case 1080:case 1081:           // Pathfinder
                $d = ['Kevin'];
                break;

            case  300:case 1040:case 1041:                               // Priest
                $d = ['Ash','Cheryl','Scott','Linda','Shelly'];
                break;

            case  400:case 1050:case 1051:                               // Rich git
                $d = ['Annie','Jake','Bobby Joe','Henrietta','Ed'];
                break;

            case 3020:                                                   // Painter
                $d = ['Jay','Bob'];
                break;

            case 1060:case 1061:                                        // Survivalist & Wolfman
                $d = [$p === 1060 ? 'Bob' : 'Jay'];
                break;

            case 1070:case 1071:case 2010:case 4010:                    // Muscleman & Berserker
                $d = ['Hollywood'];
                break;

            case 3030:                                                  // Tech
                $d = ['Homer','Marge','Bart','Lisa','Maggie','Abraham','Jacqueline','Patty','Selma'];
                break;
            //</editor-fold>

            //<editor-fold desc="Multi Player Professions">
            case 10010: case 10011:                                      // Politician & Presitent
                $d = ['Pepper'];
                break;
            case 10020: case 10021:                                                 // Football Coach
                $d = ['Football Player'];
                break;
            case 10030: case 10031: case 10040:                                    // Med Student & Surgeon & WRA
                for ($i = 0; $i < 5; $i++)
                    $d[] = 'Patient';
                break;
            case 10041:                                                  // Nerd
                for ($i = 0; $i < 5; $i++)
                    $d[] = 'Irgendjemand';
                break;
            case 12010: case 12020: case 12030: case 12040: case 12050:  // Townships Professions
            case 12060: case 12070: case 12080:
                for ($i = 0; $i < 10; $i++)
                    $d[] = 'Stadtbewohner';
                break;
            //</editor-fold>

            default:
                $d = ['Gabriel'];
                break;
        }

        foreach ($d as $name) {
            $trigger = new Model_Items_Trigger('Verdächtige Leiche','body','Hier liegt eine Leiche... vielleicht solltest du sie einmal genauer untersuchen?','Untersuchen',Model_Items_Abstract_Item::MIAI_CAT_FOOD, 95, $name, -90);
            $this->inventory()->add($trigger);
        }
    }
}