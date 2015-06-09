<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Xmasfair extends Model_Places_Abstract_Xmas {
	
	protected static $name = 'Weihnachtsmarkt';
	protected static $description = 'Leckerer Duft nach gerösteten Nüssen und Zuckerwatte... Buden mit bunten Lichtern überall... dieser Ort weckt wahrlich Erinnerungen an die Zeit, als du solche Weihnachtsmärkte immer mit deinen Eltern besucht hast. Für einen Moment fühlst du dich wieder wie ein kleines Kind - dann siehst du einen Zombie durch die Gegend schlurfen. Gottverdammt, warum müssen diese Zombies jeden Moment ruinieren?';
    protected static $icon = 'xmastree';

    private $status = Array(
        'tree' => Array(
            'current' => Array(0,1),
            'items' => Array('Model_Items_Stick' => 5, 'Model_Items_Generic_Ducttape' => 2),
            'name' => 'Provisorischen Weihnachtsbaum aufstellen',
            'points' => 0,
        ),
        'treesize' => Array(
            'current' => Array(0,0),
            'items' => Array('Model_Items_Stick' => 4, 'Model_Items_Generic_Ducttape' => 2),
            'name' => 'Weitere Zweige hinzufügen',
            'points' => 0,
        ),
        'needles' => Array(
            'current' => Array(0,0),
            'items' => Array('Model_Items_Generic_Xmasneedles' => 2),
            'name' => 'Weihnachtsbaum mit Nadeln bedecken',
            'points' => 2,
        ),
        'lametta' => Array(
            'current' => Array(0,0),
            'items' => Array('Model_Items_Generic_Lametta' => 1),
            'name' => 'Lametta aufhängen',
            'points' => 1,
        ),
        'rope' => Array(
            'current' => Array(0,0),
            'items' => Array('Model_Items_Generic_Xmasrope' => 1),
            'name' => 'Schmuckseil umlegen',
            'points' => 2,
        ),
        'lights' => Array(
            'current' => Array(0,0),
            'items' => Array('Model_Items_Generic_Xmaslights' => 1),
            'name' => 'Lichterkette umlegen',
            'points' => 5,
        ),
        'bauble' => Array(
            'current' => Array(0,0),
            'items' => Array('Model_Items_Generic_Bauble' => 10),
            'name' => 'Baum mit Weihnachtsbaumkugeln schmücken',
            'points' => 5,
        ),
        'mistle' => Array(
            'current' => Array(0,0),
            'items' => Array('Model_Items_Generic_Mistletoe' => 1),
            'name' => 'Mistelzweig aufhängen',
            'points' => 3,
        ),
    );

    public function uin($uin = NULL) {
        if ($uin === NULL) return parent::uin();
        else $t = parent::uin($uin);

        $this->inventory()->add(new Model_Items_Virtual_Location_Ffxmas());
        for ($i = 0; $i < 5; $i++) $this->inventory->add(new Model_Items_Stick());
        for ($i = 0; $i < 2; $i++) $this->inventory->add(new Model_Items_Generic_Ducttape());
        Model_Blueprints::fast_apply($this, 'items', 'manu_northpole');
        return $t;
    }

    public function get_construction_info() {
        return $this->status;
    }

    public function interaction_xmas() {
        /**
         * @global Model_Game $game
         * @global Model_Player $player
         */
        global $game, $player;

        $project = Request::current()->post('project');
        if (!isset($this->status[$project]))
            return false;

        if ($this->status[$project]['current'][0] >= $this->status[$project]['current'][1])
            return false;

        if ($game->mass_consume($this->status[$project]['items'])) {
            $this->status[$project]['current'][0]++;
            $player->log()->add(new Model_Log_Types_Text(null, null, 'Du hast den Weihnachtsbaum dekoriert. Gut gemacht!'));
            if ($project == 'tree' || $project == 'treesize') {
                $this->status['treesize']['current'][1] = 9;
                $this->status['needles']['current'][1] += 1;
                $this->status['lametta']['current'][1] += 2;
                $this->status['rope']['current'][1] += 2;
                $this->status['lights']['current'][1] += 1;
                $this->status['bauble']['current'][1] += 1;
                $this->status['mistle']['current'][1] += 3;
            }
        } else $player->log()->add(new Model_Log_Types_Text(null, null, 'Du hast nicht genug Material, um den Baum zu dekorieren.'));
    }

    public function get_decoration_value() {
        $p = 0;
        foreach ($this->status as $data)
            $p += $data['current'][0] * $data['points'];

        return floor($p/4);
    }
}	