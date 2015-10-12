<?php defined('SYSPATH') OR die('No direct access allowed.');

//This is a generic building class that has multiple names; on is randomly selected when this class is constructed
class Model_Places_Burgerjoint extends Model_Places_Abstract_Place {
	
	protected static $namelist = Array('"MacUndead" Restaurant', '"ZombieKing" Restaurant', '"Kentucky Fried Eyeballs" Restaurant', '"Ostsee" Restaurant', '"Corpseland" Restaurant', '"Bloodway" Restaurant');
	protected static $description = 'Dieses Franchise hat schonmal bessere Zeiten erlebt ... Die dicken Kinder, die sich hier früher Essensschlachten geliefert haben sind schon lange verschwunden - dafür schleichen nun Zombies zwischen den Tischen und in der Küche umher. Die gute Nachricht ist: Selbst ein Zombie würde das Zeug, das hier serviert wurde, nicht anrühren - du kannst hier also bestimmt noch ein paar Combomenüs abstauben.';
    protected static $icon = 'restaurant';
    protected static $outside = false;

    protected $horror = false;

    public function uin($new = null) {
        if ($new !== null) {
            $this->inventory->add(new Model_Items_Virtual_Location_Cooler());
            $this->inventory->add(new Model_Items_Virtual_Location_Ffkitchen());
            Model_Blueprints::fast_apply($this, 'items', 'ktc_burgerjoint');
        }
        return parent::uin($new);
    }

    public function pretick() {
        parent::pretick();

        /** @var Model_Game $game */
        global $game;

        if (Tool_Events::current($game->next_tick()) == 'halloween' && !$this->horror && Tool_Gambling::random(0.2)) {

            $this->horror = true;

            /** @var Model_Items_Virtual_Location_Cooler[] $vi */
            $vi = $this->inventory()->get('Model_Items_Virtual_Location_Cooler');
            if ($vi && !$vi[0]->remaining_actions('cooler_open')) {
                $vi[0]->remaining_actions('cooler_open_again', 1);

                foreach (Tool_Scripts::at_location($this->uin()) as $pl) {
                    $pl->achievements()->achieve(Model_Achievement::MA_HALLOWEEN_15);
                    $pl->log()->add('Die Tür zur Kühlkammer ist mit einem Knall zugefallen. Komisch, eigentlich warst du dir sicher, sie mit einem Keil gesichert zu haben...');
                }
            }

        }
    }

}	