<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Book extends Model_Items_Abstract_Item {
	
	protected static $effects = array();
    protected $uses = array();
    protected $pages = 0;
    protected static $reading_speed = 1;
    protected static $pagerange = null;

	protected static $cat = Model_Items_Abstract_Item::MIAI_CAT_LITERATURE;

    public function __construct($type = null) {
        if (static::$pagerange != null)
            $this->pages = random_int(static::$pagerange[0], static::$pagerange[1]);
        parent::__construct($type);
    }

	private function get_uses($pid = null) {
        if ($pid === null)
            $pid = Globals::CurrentPlayerF()->id();

        return (isset($this->uses[$pid]) ? $this->uses[$pid] : 0);
    }

    public function label() {
        if (($read = $this->get_uses()) == 0)
            return __(":num Seiten", array(':num' => $this->pages));
        elseif ($read >= $this->pages)
            return __(":num Seiten (bereits gelesen)", array(':num' => $this->pages));
        else return __("Noch :left von :num Seiten zu lesen", array(':left' => $this->pages - $read, ':num' => $this->pages));
    }

    public function read($pid = null) {
        if ($pid === null)
            $pid = Globals::CurrentPlayerF()->id();


        if (!isset($this->uses[$pid]))
            $this->uses[$pid] = static::$reading_speed;
        else {
            if ($this->uses[$pid] >= $this->pages)
                return false;
            $this->uses[$pid] += static::$reading_speed;
        }

        return true;
    }

    public function remaining_ticks($pid = null) {
        return ceil(($this->pages - $this->get_uses($pid))/static::$reading_speed);
    }

    protected function hid() {
        if ($this->get_uses() >= $this->pages)
            return parent::hid();
        else {
            $show_eff = Model_Effect::factory();
            foreach (static::$effects as $stat => $value) {
                if ($value < -1) $s = '---';
                elseif ($value < -0.25) $s = '--';
                elseif ($value < 0)     $s = '-';
                elseif ($value < 0.25)  $s = '+';
                elseif ($value < 1)     $s = '++';
                else $s = '+++';
                $show_eff->effect($stat, $s);
            }

            return parent::hid()
                ->add_action('Lesen',
                    Model_Action::factory()
                        ->allow_for(Interface_Plentity::IC_NPC_NONPC)
                        ->condition(function($p) {
                            /** @var Model_Player $p */
                            /** @noinspection PhpUndefinedMethodInspection */
                            if ((Tool_Scripts::location_type($p->location_class()) != 2) || $p->location()->get_defense() < 1)
                                return "hideout";
                            if (!$p->inventory()->has($this->uin()))
                                return "noinv";
                            return true;
                        })
                        ->fail_message('Hier musst du immer wachsam sein; wenn du in Ruhe lesen willst, kehre in dein Versteck zurück.', 'hideout')
                        ->fail_message('Du musst dieses Buch aufheben, bevor du es lesen kannst.', 'noinv')
                        ->show_as($show_eff)
                        ->effect(
                            Model_Effect::factory()
                                ->custom(function($p) {
                                    /** @var Model_Player $p */
                                    /** @noinspection PhpParamsInspection */
                                    new Model_Buffs_Read($this->uin(), static::$effects, $this->remaining_ticks($p->id()));
                                })
                                ->message('Zeit zu lesen! Dieses Buch wird dir sicherlich helfen, all die schlimmen Dinge in dieser Welt für einen Augenblick zu vergessen.')
                        )
                );
        }
    }
	
}	