<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Strangewood_Clearing extends Model_Places_Abstract_Trap {
	
	protected static $location_name = 'Lichtung';
	protected static $description = 'Du hast eine Lichtung entdeckt. An einigen Stellen kannst du sogar den Himmel durch die Baumkronen sehen.';
    protected static $outside = true;
    protected static $icon = 'swood';

    protected $zombies_spawned = false;

    public function uin($uin = NULL) {
        $t = parent::uin($uin);
        if ($uin !== null) {

            $items = [
                [Model_Items_Soul::cls(),           mt_rand(1,3)],
                [Model_Items_Body2::cls(),          1],
                [Model_Items_Pumpkin::cls(),        mt_rand(1,2)],
                [Model_Items_Pumpkin2::cls(),       mt_rand(2,5)],
                [Model_Items_Generic_Water0::cls(), mt_rand(1,2)],
                [Model_Items_Paracetin::cls(),      mt_rand(1,2)],
                null
            ];

            $spawn = Tool_Gambling::select( $items );
            if ($spawn) {
                list($cls,$num) = $spawn;
                for ($i = 0; $i < $num; $i++)
                    $this->inventory->add(new $cls());
            }
        }
        return $t;
    }

    protected function trap_spawn(): void
    {
        if (!$this->zombies_spawned) {

            $this->zombies_spawned = true;

            $zombies = [
                [Model_Combat_Zombies_Halloween_Shambler::cls(), mt_rand(2,4)],
                [Model_Combat_Zombies_Halloween_Starver::cls(),  mt_rand(4,6)],
                [Model_Combat_Zombies_Halloween_Runner::cls(),   1],
                [Model_Combat_Zombies_Halloween_Fatass::cls(),   1],
                'special', null,
            ];

            $spawn = Tool_Gambling::select( $zombies );
            if ($spawn === 'special') $spawn = Tool_Gambling::select( $zombies );
            if ($spawn === 'special') {

                foreach (Tool_Scripts::at_location($this->uin(), true, false) as $p)
                    $p->achievements()->achieve( Model_Achievement::MA_HALLOWEEN_19_2, 5 );

                $spawn = [ Model_Combat_Zombies_Halloween_Woodghost::cls(), 1 ];
            }

            if ($spawn) {
                list($cls,$num) = $spawn;
                $this->zombie_factory->add_accumulated_zombies($cls,$num);
            }

        }
    }
}