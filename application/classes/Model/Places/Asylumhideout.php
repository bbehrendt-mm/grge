<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Asylumhideout extends Model_Places_Abstract_Hideout implements Interface_Singularity {

    protected static $name = 'Patientenzimmer';
    protected static $description = 'Dieses Zimmer befindet sich in einem abgelegenen Flügel der Irrenanstalt... du hättest es nie gefunden, wenn dir der Patient nicht den Weg gezeigt hätte. Es ist überraschend groß, und hat ein schönes Erkerfenster mit Blick auf einen überwucherten Garten. Man könnte es fast als schön bezeichnen... wären die Wände nicht mit schauderhaften Fingerzeichnungen aus Blut übersäht. Es hilft auch nicht, dass hier diverse Foltergeräte und Autopsiewerkzeuge herumstehen. Das grauenhafteste in diesem Raum ist jedoch ohne Frage der DVD-Spieler mit eingelegter Helene-Fischer-DVD. Der pure Horror...';
    protected static $icon = 'mental';
    protected $upgradable = false;

    //Base defense
    protected $defense = 0;

    //Base: 15% per day
    protected static $decay_rate = 0;

    //Exp: 8% per day
    protected static $decay_exp = 0;

    public function uin($new = null) {
        if ($new !== null) {
            $this->set_decay(0, true);

            $this->add_upgrades(['hideout','bedr3']);
            $this->home_extensions("hideout", "cursed", true);
            $this->home_extensions("kitchen", "base", true);
            $this->home_extensions("kitchen", "utils", true);
            $this->home_extensions("kitchen", "cursed", true);

            $f1 = mt_rand(1,4);
            $f2 = mt_rand(2,20);
            $f3 = mt_rand(3,10);
            $users = Model_User::random_names($f1);
            for ($i = 0; $i < $f1; $i++) {
                $b = new Model_Items_Body('Toter Patient', 'Der Patient trägt ein Identifikationsarmband, auf dem sich ein Barcode sowie ein Name befindet. Du wirst wohl nie erfahren, wer das war oder was mit ihm in der Irrenanstalt geschehen ist. Wobei... vermutlich willst du das auch lieber gar nicht wissen.');
                $b->give_name($users[$i]);
                $this->inventory()->add($b);
            }
            for ($i = 0; $i < $f2; $i++)
                $this->inventory()->add(new Model_Items_Fleshfood());
            for ($i = 0; $i < $f3; $i++)
                $this->inventory()->add(new Model_Items_Chem(mt_rand(1,6)));
            for ($i = 0; $i < 5; $i++)
                $this->inventory()->add(new Model_Items_Hacksaw());

        }
        return parent::uin($new);
    }

    public function enter($pid = null) {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
        global $game;
        if (!$pid) global $player;
        else $player = $game->get_player($pid);
        parent::enter($pid);
        new Model_Buffs_Home2($player->id());
    }
}	