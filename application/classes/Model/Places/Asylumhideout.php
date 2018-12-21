<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Asylumhideout extends Model_Places_Abstract_Hideout implements Interface_Singularity {

    //Base deco value
    protected static $base_deco_value = -500;

    protected static $location_name = 'Patientenzimmer';
    protected static $description = 'Dieses Zimmer befindet sich in einem abgelegenen Flügel der Irrenanstalt... du hättest es nie gefunden, wenn dir der Patient nicht den Weg gezeigt hätte. Es ist überraschend groß, und hat ein schönes Erkerfenster mit Blick auf einen überwucherten Garten. Man könnte es fast als schön bezeichnen... wären die Wände nicht mit schauderhaften Fingerzeichnungen aus Blut übersäht. Es hilft auch nicht, dass hier diverse Foltergeräte und Autopsiewerkzeuge herumstehen. Das grauenhafteste in diesem Raum ist jedoch ohne Frage der DVD-Spieler mit eingelegter Helene-Fischer-DVD. Der pure Horror...';
    protected static $icon = 'mental';
    protected static $upgradable = false;

    //Base defense
    protected static $base_defense = 0;

    //Base: 15% per day
    protected static $decay_rate = 0;

    //Exp: 8% per day
    protected static $decay_exp = 0;

    public function uin($new = null) {
        if ($new !== null) {
            $f1 = random_int(1,4);
            $f2 = random_int(2,20);
            $f3 = random_int(3,10);
            $users = Model_User::random_names($f1);
            foreach ($users as $iValue) {
                $b = new Model_Items_Body('Toter Patient', 'Der Patient trägt ein Identifikationsarmband, auf dem sich ein Barcode sowie ein Name befindet. Du wirst wohl nie erfahren, wer das war oder was mit ihm in der Irrenanstalt geschehen ist. Wobei... vermutlich willst du das auch lieber gar nicht wissen.');
                $b->give_name($iValue);
                $this->inventory()->add($b);
            }
            for ($i = 0; $i < $f2; $i++)
                $this->inventory()->add(new Model_Items_Fleshfood());
            for ($i = 0; $i < $f3; $i++)
                $this->inventory()->add(new Model_Items_Chem(random_int(1,6)));
            for ($i = 0; $i < 5; $i++)
                $this->inventory()->add(new Model_Items_Hacksaw());

        }
        return parent::uin($new);
    }

    public function enter($pid = null, $type = Interface_Tickable::IT_TYPE_PLAYER): bool
    {
        if (!$pid) $player = Globals::CurrentPlayerF();
        elseif ($type === Interface_Tickable::IT_TYPE_PLAYER) $player = Globals::CurrentGameF()->get_player($pid);
        else $player = Globals::CurrentGameF()->get_npc($pid);

        new Model_Buffs_Home2($player);
        return parent::enter($pid, $type);
    }

    public function setup_additional_rooms(): void
    {
        $this->setup_new_room($this->create_new_room(10,['inside']),
                              ['kitchen','kitchen_cursed'],
                              ['ktc2','ktc3','ktc4'],
            'Küchenbereich'
        );
        $this->setup_new_room($this->create_new_room(10,['inside']),
                              ['bedroom','bedroom_cursed'],
                              ['bedr1','bedr2','bedr3'],
            'Schlafzimmer'
        );
    }
}	