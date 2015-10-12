<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Constructionsite extends Model_Places_Abstract_Place {
	
	protected static $name = 'Baustelle';
	protected static $description = 'Was hier mal gebaut werden sollte ist nicht erkennbar - man sieht nur verrostete Eisenträger und Baumaterial, das am Boden liegt. Einige Zombies haben die Baustelle offenbar zu ihrem Wohnsitz erkoren. Glücklicherweise haben sie hier praktisch keine Möglichkeit, einen Überraschungsangriff zu starten.';
    protected static $icon = 'site';

    public function uin($new = null) {
        if ($new !== null)
            $this->inventory->add(new Model_Items_Virtual_Location_Container());
        return parent::uin($new);
    }

    public function pretick() {
        parent::pretick();

        /** @var Model_Game $game */
        global $game;

        if (Tool_Events::current($game->next_tick()) == 'halloween' && Tool_Gambling::random(0.1)) {

            foreach (Tool_Scripts::at_location($this->uin()) as $pl) {
                $pl->achievements()->achieve(Model_Achievement::MA_HALLOWEEN_15);
                $pl->log()->add('Du willst dich gerade ausruhen, da hörst du wie etwas hinter dir auf den Boden aufschlägt. Es scheint, als wäre eine Leiche von einem Gerüst gefallen!');
            }

            $this->inventory()->add(new Model_Items_Body('Zerfetzte Leiche','Diese Leiche ist ziemlich verstümmelt. Schwer zu sagen, ob das durch den Sturz passiert ist...'));

        }
    }
}	