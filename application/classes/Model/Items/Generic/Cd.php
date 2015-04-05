<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Cd extends Model_Items_Abstract_Item implements Interface_Static {

    protected static $static_info = Array(
        'name' => 'Schlimme Musik-CD',
        'icon' => 'cd/generic',
        'description' => '',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_MISC,
        'deco' => 1,
    );

    protected static $instances_info = Array(
        Array('name' => 'Mario Barth LIVE CD',
            'icon' => 'cd/boy',
            'description' => 'Auf dem Booklet steht was von "totlachen"... "Tod" ist schonmal sehr treffend, "lachen" eher nicht. Die Witze (bzw. der eine Witz, der über 90 Minuten immer wieder wiederholt wird) verursachen selbst bei Zombies noch den sofortigen Hirntod - was dann irgendwie doch wieder etwas beeindruckend ist.'),
        Array('name' => 'CD einer DSDS Gewinnerin',
            'icon' => 'cd/girl',
            'description' => 'Diese CD enthält Musik von irgend einer Gewinnerin von DSDS, deren Namen wahrscheinlich nicht mal sie selber kennt. Leider hat sie sich nicht allzu gut verkauft, und wurde in den Top 100 Albumcharts von den CDs "Fahrstuhlmusik heute" und "Furzgeräusche rund um die Welt" vernichtend geschlagen.'),
        Array('name' => 'Modern Talking CD',
            'icon' => 'cd/band',
            'description' => 'OH MEIN GOTT, VERNICHTE ES MIT FEUER!!!'),
        Array('name' => 'Heino Rock CD',
            'icon' => 'cd/band2',
            'description' => 'OH MEIN GOTT, VERNICHTE ES MIT FEUER!!!'),
    );

	protected static $weight = 2;
}	