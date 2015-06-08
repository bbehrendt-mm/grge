<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Achievement extends Model {
	
	const MA_KILLED_ZOMBIES = 1;
	const MA_SLEEP = 2;
	const MA_BROKEN_CHAIRS = 3;
	const MA_CLOSE_ESCAPES = 4;
	const MA_GAMETIME = 5;
	const MA_BODY_EATER = 6;
	const MA_PILL_EATER = 7;
	const MA_CONSTRUCTIONS = 8;
	const MA_ADMINISTRATOR = 9;
	const MA_CHICKEN = 10;
	const MA_FIST_FIGHT = 11;
	const MA_BUILD_MKII = 12;
	const MA_HORROR = 13;
	const MA_POISON_DRINK = 14;
	const MA_CREATIVE_INPUT = 15;
	const MA_SOME_COMPANY = 16;
	const MA_ITEM_COUNT = 17;
	const MA_BANDAGE_MUMMY = 18;
	const MA_SLASHER_KILLER = 19;
	const MA_GARBAGE_GUY = 20;
	const MA_PRINCESS = 21;
	const MA_ENTOMOLOGIST = 22;
	const MA_BREAK_INS = 23;
	const MA_ALCOHOLIC = 24;
	const MA_PUMPKINHEAD = 25;
	const MA_ALPHATESTER = 26;
	const MA_SCIENCE = 27;
	const MA_NOSCIENCE = 28;
	const MA_XMAS = 29;
	const MA_NAMEGIVER = 30;
	const MA_GRAVEROBBER = 31;
	const MA_BLOODSUCKER = 32;
	const MA_EVENTWINNER = 33;
	const MA_EVENTLOOSER = 34;
	const MA_GENEROUS = 35;
	const MA_EVENT = 36;
	const MA_CAPITALISM = 37;
    const MA_LETTERS = 38;
    const MA_GLADIATOR = 39;
    const MA_DALAD = 40;
    const MA_MENTOR = 41;
    const MA_LORD = 42;
    const MA_MASOCHIST = 43;
    const MA_GAMEWEEK = 44;
    const MA_GAMEMONTH = 45;
    const MA_MERCYKILL = 46;
    const MA_WIKI = 47;
    const MA_APRIL = 48;
    const MA_RAVEN = 49;
    const MA_EASTER = 50;
    const MA_EASTER_BAD = 51;
    const MA_CLOCK = 52;
	const MA_CLOWN = 53;
	
	const MA_RANKING_SURVIVAL = 1000;
	const MA_RANKING_HARDCORE = 1100;
	const MA_RANKING_MASSACRE = 2000;
    const MA_RANKING_LONESCOUT = 3000;
    const MA_RANKING_COLLOSSEUM = 4000;
    const MA_RANKING_MULTI_OPEN = 10000;

    const MA_RANKING_MULTI_PRIVATE_SMALL = 10100;
    const MA_RANKING_MULTI_PRIVATE_LARGE = 10200;
    const MA_RANKING_MULTI_PRIVATE_ROADTRIP = 11000;

    private static $data = array(
			Model_Achievement::MA_KILLED_ZOMBIES 		            => array('name' => "Getötete Zombies",                      'points' => 0,),
			Model_Achievement::MA_SLEEP 				            => array('name' => "Schlafmütze",                           'points' => 1,),
			Model_Achievement::MA_BROKEN_CHAIRS		                => array('name' => "Wrestling Extrem",                      'points' => 1,),
			Model_Achievement::MA_CLOSE_ESCAPES		                => array('name' => "Knapp entkommen",                       'points' => 2,),
			Model_Achievement::MA_GAMETIME			                => array('name' => "Kreuze auf deinem Kalender",            'points' => 1,),
			Model_Achievement::MA_BODY_EATER			            => array('name' => "Om Nom Nom",                            'points' => 5,),
			Model_Achievement::MA_PILL_EATER			            => array('name' => "Verzweifelter Junkie",                  'points' => 1,),
			Model_Achievement::MA_CONSTRUCTIONS		                => array('name' => "Heimwerker",                            'points' => 3,),
			Model_Achievement::MA_ADMINISTRATOR		                => array('name' => "Alpha & Omega",                         'points' => 500,),
			Model_Achievement::MA_CHICKEN				            => array('name' => "Feiges Huhn",                           'points' => 1,),
			Model_Achievement::MA_FIST_FIGHT			            => array('name' => "Faustkampf",                            'points' => 1,),
			Model_Achievement::MA_BUILD_MKII			            => array('name' => "MK II-Fetischist",                      'points' => 5,),
			Model_Achievement::MA_HORROR				            => array('name' => "Spuren des Grauens",                    'points' => 5,),
			Model_Achievement::MA_POISON_DRINK		                => array('name' => "Nichts haut mich um",                   'points' => 5,),//Bambii
			Model_Achievement::MA_CREATIVE_INPUT		            => array('name' => "Kreativer Input",                       'points' => 100,),
			Model_Achievement::MA_SOME_COMPANY		                => array('name' => "Ein wenig Gesellschaft",                'points' => 2,),//Panther
			Model_Achievement::MA_ITEM_COUNT			            => array('name' => "3... 2... 1... MEINS!",                 'points' => 0,),//Panther
			Model_Achievement::MA_BANDAGE_MUMMY		                => array('name' => "Wandelnde Mumie",                       'points' => 3,),//Bambii
			Model_Achievement::MA_SLASHER_KILLER		            => array('name' => "Schlimmer als Zombies ...",             'points' => 2,),//MisterD
			Model_Achievement::MA_GARBAGE_GUY			            => array('name' => "Müllmann",                              'points' => 1,),
			Model_Achievement::MA_PRINCESS			                => array('name' => "Prinzessin auf der Erbse",              'points' => 1,),//Bambii
			Model_Achievement::MA_ENTOMOLOGIST	                    => array('name' => "Entomologe",                            'points' => 142,),
			Model_Achievement::MA_BREAK_INS			                => array('name' => "Sie kamen von hinten!",                 'points' => 4,),
			Model_Achievement::MA_ALCOHOLIC			                => array('name' => "Nicht-so-anonymer Alkoholiker",         'points' => 3,),
			Model_Achievement::MA_PUMPKINHEAD			            => array('name' => "Visagist von Pumpkinhead",              'points' => 20,),
			Model_Achievement::MA_ALPHATESTER			            => array('name' => "Testsubjekt",                           'points' => 200,),
			Model_Achievement::MA_SCIENCE				            => array('name' => "Verrückter Wissenschaftler",            'points' => 3,),
			Model_Achievement::MA_NOSCIENCE			                => array('name' => "Wissenschafts-Azubi",                   'points' => 1,),
			Model_Achievement::MA_XMAS				                => array('name' => "In der Weihnachtsbäckerei",             'points' => 20,),
			Model_Achievement::MA_NAMEGIVER			                => array('name' => "Namensgeber",                           'points' => 500,),
			Model_Achievement::MA_GRAVEROBBER			            => array('name' => "Grabräuber",                            'points' => 10,),
			Model_Achievement::MA_BLOODSUCKER			            => array('name' => "Blutsauger",                            'points' => 5,),//Advo
			Model_Achievement::MA_EVENTWINNER			            => array('name' => "Eventsieger",                           'points' => 50,),
			Model_Achievement::MA_EVENT				                => array('name' => "Event-Teilnehmer",                      'points' => 5,),
			Model_Achievement::MA_EVENTLOOSER			            => array('name' => "Trostpreis",                            'points' => 10,),
			Model_Achievement::MA_GENEROUS			                => array('name' => "Edler Spender",                         'points' => 40,),
			Model_Achievement::MA_CAPITALISM			            => array('name' => "Kapitalismus",                          'points' => 10,),
            Model_Achievement::MA_LETTERS	    		            => array('name' => "Brieffreund",                           'points' => 0,),
            Model_Achievement::MA_GLADIATOR	    	                => array('name' => "Gladiator",                             'points' => 5,),
            Model_Achievement::MA_DALAD                             => array('name' => "Dalad Jelly Auszeichnung",              'points' => 5,),
            Model_Achievement::MA_MENTOR                            => array('name' => "Geduldiger Lehrmeister",                'points' => 20,),
            Model_Achievement::MA_LORD                              => array('name' => "Burgherr",                              'points' => 50,),
            Model_Achievement::MA_MASOCHIST                         => array('name' => "Masochist",                             'points' => 3,),
            Model_Achievement::MA_GAMEWEEK                          => array('name' => "Survival 24/7",                         'points' => 15,),
            Model_Achievement::MA_GAMEMONTH                         => array('name' => "Kalenderblatt-Verschwender",            'points' => 45,),
            Model_Achievement::MA_MERCYKILL                         => array('name' => "Gnadenstoß",                            'points' => 30,),
            Model_Achievement::MA_WIKI                              => array('name' => "Berühmter Autor",                       'points' => 40,),
            Model_Achievement::MA_APRIL                             => array('name' => "April April",                           'points' => 10,),
            Model_Achievement::MA_RAVEN                             => array('name' => "Opfer der Raben",                       'points' => 5,),
            Model_Achievement::MA_EASTER                            => array('name' => "Oster-Glückspilz",                      'points' => 15,),
            Model_Achievement::MA_EASTER_BAD                        => array('name' => "Oster-Pechvogel",                       'points' => 0,),
            Model_Achievement::MA_CLOCK                             => array('name' => "Geöltes Uhrwerk",                       'points' => 5,),
			Model_Achievement::MA_CLOWN                             => array('name' => "Blutiger Clown",                        'points' => 50,),

			Model_Achievement::MA_RANKING_SURVIVAL	                => array('name' => "Berühmter Überlebenskünstler",          'points' => 50,),
			Model_Achievement::MA_RANKING_HARDCORE	                => array('name' => "Berühmter Hardcore-Überlebenskünstler", 'points' => 75,),
			Model_Achievement::MA_RANKING_MASSACRE	                => array('name' => "Berühmter Serienkiller",                'points' => 50,),
            Model_Achievement::MA_RANKING_LONESCOUT	                => array('name' => "Goldene Arschkarte",                    'points' => 100,),
            Model_Achievement::MA_RANKING_COLLOSSEUM                => array('name' => "Titan",                                 'points' => 100,),
            Model_Achievement::MA_RANKING_MULTI_OPEN                => array('name' => "Einer Für Alle!",                       'points' => 50,),
            Model_Achievement::MA_RANKING_MULTI_PRIVATE_SMALL	    => array('name' => "Elitärer Club",                         'points' => 40,),
            Model_Achievement::MA_RANKING_MULTI_PRIVATE_LARGE       => array('name' => "Elitärer Club",                         'points' => 40,),
            Model_Achievement::MA_RANKING_MULTI_PRIVATE_ROADTRIP    => array('name' => "Roadkill-Experte",                      'points' => 50,)
    );
    
	private $container = Array();
	
	static public function points_aid($aid) {
		if (!isset(static::$data[$aid])) return 0;
        else return static::$data[$aid]['points'];
	}

    static public function class_aid($aid) {
        $p = static::points_aid($aid);
        if ($p == 0) return 0;
        elseif ($p <= 2) return 1;
        elseif ($p <= 5) return 2;
        elseif ($p <= 10) return 3;
        elseif ($p <= 20) return 4;
        elseif ($p <= 100) return 5;
        else return 6;
    }
	
	static public function decode_aid($aid) {
        if (!isset(static::$data[$aid])) return "Mysteriöse Auszeichnung #{$aid}";
        else return static::$data[$aid]['name'];
	}
		
	/**
	 * Initializes an achievement counter
	 * @param number $achievement
	 */
	private function init($achievement) {
		if (!isset($this->container[$achievement])) $this->container[$achievement] = 0;
	}
	
	/**
	 * Add an achievement, with an optional number
	 * @param number $achievement
	 * @param number $num
	 */
	public function achieve($achievement, $num = 1) {
		$this->init($achievement);
		$this->container[$achievement] += $num;
	}
	
	/**
	 * Set achievement counter to a fixed value
	 * @param number $achievement
	 * @param number $value
	 */
	public function achieve_force($achievement, $value = 0) {
		$this->init($achievement);
		$this->container[$achievement] = $value;
	}
	
	/**
	 * Return achievement counter
	 * @param number $achievement
	 * @return NULL|number
	 */
	public function get_achievements($achievement) {
		if (!isset($this->container[$achievement])) return 0;
		else return floor($this->container[$achievement]);
	}
	
	/**
	 * Compiles internal storage
	 */
	public function compile() {
		$tmp = Array();
		foreach ($this->container as $a => $v)
			if (floor($v) > 0) $tmp[$a] = floor($v);

		$this->container = $tmp;
	}
	
	/**
	 * Award all gained achievements
	 * @param number $pid
	 * @param number $gid
     * @param number $season
	 */
	public function award($pid, $gid, $season = -1) {
		$this->compile();

        if (!$this->container)
            return;

		$query = DB::insert('achievements', array('gameid', 'uid', 'aid', 'value', 'season'));
		foreach ($this->container as $a => $v)
			$query->values(array($gid, $pid, $a, $v, $season));
		
		$query->execute();
	}
	
	public function get_all() {
		return $this->container;
	}
}	