<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Bottle extends Model_Items_Abstract_Item implements Interface_Countable {
	protected static $cat = Model_Items_Abstract_Item::MIAI_CAT_BOTTLES;

	protected static $capacity = 4;

	private $fillrate;
	public $toxicity;
	private $label;

    protected function hid() {
        $php53pb = $this;
        return parent::hid()
            ->add_action('Einen Schluck trinken',
                Model_Action::factory()
                   ->condition(function() use ($php53pb) {
                        /** @var Model_Items_Abstract_Bottle $php53pb */
                        return $php53pb->fillrate() > 0;
                   })
                    ->fail_message('Du führst die Flasche an deinen Mund, um zu trinken - aber sie ist leer.')
                    ->show_as(
                        Model_Effect::factory()
                            ->effect(Model_Player::MP_STAT_THIRST, 25)
                            ->ambiguous_effect(Model_Player::MP_STAT_HEALTH)
                    )
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Player::MP_STAT_THIRST, 25)
                            ->effect(Model_Player::MP_STAT_HEALTH, -$this->toxicity)
                            ->ambiguous_effect(Model_Player::MP_STAT_HEALTH)
                            ->custom(function($p) use ($php53pb) {
                                /**
                                 * @var Model_Items_Abstract_Bottle $php53pb
                                 * @var Model_Player $p
                                 */
                                if ($php53pb->toxicity() > 50 && $p->stats_get(Model_Player::MP_STAT_HEALTH) > $php53pb->toxicity())
                                    $p->achievements()->achieve(Model_Achievement::MA_POISON_DRINK);
                            })
                            ->consume($this)
                            ->message($this->drinkmsg())
                    )
            )
            ->add_action('Micropur hineinwerfen',
                Model_Action::factory()
                    ->requirement('Model_Items_Generic_Micropur', 1)
                    ->condition(function() use ($php53pb) {
                        /** @var Model_Items_Abstract_Bottle $php53pb */
                        return $php53pb->fillrate() > 0;
                    })
                    ->fail_message('Du musst Wasser in der Flasche haben, damit du sie reinigen kannst!')
                    ->effect(
                        Model_Effect::factory()
                            ->custom(function() use ($php53pb) {
                                /**
                                 * @var Model_Items_Abstract_Bottle $php53pb
                                 * @global Model_Game $game
                                 */
                                global $game;
                                if ($game->config('items.bottle.allow_full_detox')) $php53pb->toxicity = 0;
                                else $php53pb->toxicity = max($php53pb->toxicity - 50, 0);
                            })
                            ->message($this->cleanmsg())
                    )
            )
            ->add_action('Untersuchen ...',
                Model_Action::factory()
                    ->condition(function() use ($php53pb) {
                        /** @var Model_Items_Abstract_Bottle $php53pb */
                        return $php53pb->fillrate() > 0;
                    })
                    ->fail_message('Diese Flasche ist leer.')
                    ->effect(
                        Model_Effect::factory()
                            ->message($this->observemsg())
                    )
            );
    }
	
	public function consume($count = 1) {
		if ($this->fillrate > 0)
		{
			$this->toxicity -= ($this->toxicity * ($count/$this->fillrate));
			$this->fillrate--;
			return true;
		}
		return false;
	}
	
	public function capacity() {
		return static::$capacity;
	}
	
	public function fillrate($take = NULL) {
		if ($take === NULL) return $this->fillrate;
		elseif (($take > 0) && ($take <= $this->fillrate)) {
			$this->consume($take);
			return true;
		}			
		else return false;
	}
	
	public function count() {
		return $this->fillrate();
	}
	
	public function toxicity() {
		return $this->toxicity;
	}
	
	public function add_water($num, $toxicity) {
		if ($num > 0 && ($this->fillrate + $num <= static::$capacity))
		{
			$this->fillrate += $num;
			$this->toxicity += $num * $toxicity;
			return true;
		}
		else return false;
	}
	
	public function get_water($num) {
		return $this->fillrate($num);
	}
	
	public function stackname() {
		return (static::$capacity == 1) ? 'Ration' : 'Rationen';
	}
	
	public function label() {
		return $this->label;
	}

    private function drinkmsg() {
        if ($this->toxicity == 0) 		return 'Du nimmst einen Schluck aus deiner Flasche. Dein Durst verschwindet und du fühlst dich erfrischt!';
        elseif ($this->toxicity <= 5)	return 'Du nimmst einen Schluck aus deiner Flasche. Das Wasser hat einen leicht modrigen Nachgeschmack, dennoch hilft es gegen deinen Durst.';
        elseif ($this->toxicity <= 10)	return 'Du nimmst einen Schluck aus deiner Flasche. Es fällt dir schwer, den Güllegeschmack des Wassers zu ignorieren, aber irgendwie musst du ja gegen deinen Durst vorgehen.';
        elseif ($this->toxicity <= 30)	return 'Du nimmst einen Schluck aus deiner Flasche. Das Wasser ist schleimig und verklebt dir die Kehle. Außerdem schmeckt es, als hätte sich darin ein Zombie aufgelöst.';
        elseif ($this->toxicity <= 70)	return 'Du öffnest die Flasche, und sofort triebt dir der üble Geruch Tränen in die Augen. Du schickst ein Stoßgebet in den Himmel, schließt deine Augen und schluckst die widerliche Brühe hinunter.';
        else 							return 'Das Wasser in deiner Flasche hat inzwischen eine teerartige Konsistenz erreicht. Herzlichen Glückwunsch, das Innere der Flasche ist vermutlich auf Jahrzehnte verseucht. Nachdem du einen Schluck genommen hast fühlst du sofort, wie alle deine Organe weggeätzt werden. Lecker!';
    }

    private function cleanmsg() {
        /** @global Model_Game $game */
        global $game;
        $tox = max(0, $game->config('items.bottle.allow_full_detox') ? 0 : $this->toxicity - 50);
        if ($tox <= 2)		return 'Du wirfst die Tablette ins Wasser - es sprudelt ein wenig, danach verbreitet sich angenehmer Zitronenduft. Deine Wasserflasche ist wieder komplett gereinigt!';
        elseif ($tox <= 10)	return 'Du wirfst die Tablette ins Wasser - es sprudelt ein wenig, danach verbreitet sich angenehmer Zitronenduft. Zwar ist das Wasser noch immer nicht ganz sauber, aber wesentlich trinkbarer als zuvor!';
        elseif ($tox <= 30)	return 'Du wirfst die Tablette ins Wasser - es sprudelt ein wenig, danach mischt sich angenehmer Zitronenduft in den Güllegeruch des Wassers. Naja, besser als nichts...';
        elseif ($tox <= 70)	return 'Du wirfst die Tablette ins Wasser - es sprudelt ein wenig, aber so wirklich sauber ist das Wasser nicht geworden. Eventuell solltest du eine zweite Tablette reinwerfen...';
        else			    return 'Du wirfst die Tablette ins Wasser - es dauert eine Weile, bis sie in dem zähflüssigen Inhalt deiner Flasche versinkt. Um die Tablette herum löst sich der Schleim etwas auf, der größte Teil des Wassers in der Flasche zeigt sich jedoch von deinen Reinigungsversuchen unbeeindruckt.';
    }

    private function observemsg() {
        if ($this->toxicity <= 2) 		return 'Das Wasser in der Flasche scheint relativ klar zu sein ...';
        elseif ($this->toxicity <= 10) 	return 'Das Wasser in der Flasche ist etwas löhmerig ...';
        elseif ($this->toxicity <= 30)	return 'Ein modriger Geruch steigt aus der Flasche auf... Aber wer wird schon wählerisch sein, wenn es um Wasser geht?';
        elseif ($this->toxicity <= 70)	return 'Das Wasser in dieser Flasche stinkt erbärmlich! Das solltest du nur trinken, wenn du absolut verzweifelt bist!';
        else 							return 'Der Geruch, der aus der Flasche aufsteigt, lässt dir die Augen tränen. Was immer da drin ist, trinken solltest du es nicht.';
    }


    /**
     * @param Model_Items_Abstract_Liquid $item
     * @return bool
     */
    public function interaction_fill($item) {
		/** @global Model_Player $player */
        global $player;
		
		if ($this->fillrate >= static::$capacity)
		{
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Diese Flasche ist bereits bis zum Rand gefüllt. Du kannst unmöglich eine weitere Ration Wasser darin unterbringen.'));
			return true;
		}
		
		if (!Tool_System::instance_of($item, 'Model_Items_Abstract_Liquid')) return false;
		
		if ($item->toxicity() > $this->toxicity || $this->toxicity <= 0) $this->toxicity += $item->toxicity();
		else $this->toxicity += round( ($item->toxicity()*$item->toxicity())/$this->toxicity );
		
		$item->consume();
		$this->fillrate++;
		
		return true;
	}

    /**
     * @param Model_Items_Abstract_Bottle $item
     * @return bool
     */
    public function interaction_fillfrom($item) {
        /** @global Model_Player $player */
        global $player;
	
		if ($this->fillrate >= static::$capacity)
		{
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Diese Flasche ist bereits bis zum Rand gefüllt. Du kannst unmöglich eine weitere Ration Wasser darin unterbringen.'));
			return true;
		}
	
		if (!Tool_System::instance_of($item, 'Model_Items_Abstract_Bottle')) return false;
	
		if ($item->toxicity() >= $this->toxicity) $this->toxicity += $item->toxicity();
		else $this->toxicity += round( ($item->toxicity()*$item->toxicity())/$this->toxicity );
	
		$item->consume();
		$this->fillrate++;
	
		return true;
	}
	
	public function interaction_extract() {
        /** @global Model_Player $player */
        global $player;
	
		if ($this->fillrate <= 0)
		{
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Diese Flasche ist leider leer...'));
			return true;
		}
	
		$player->location()->inventory()->add(new Model_Items_Generic_Waterv($this->toxicity()));
		$this->consume();
	
		return true;
	}
	
	public function interaction_label($arg) {
        /** @global Model_Player $player */
        global $player;

		$arg = substr(preg_replace('/[^0-9a-zA-ZäöüÄÖÜ\+\-&.,:% _]+/i', ' ', $arg), 0, 12);
		$this->label = $arg;
		
		if ($this->label == '') $player->log()->add(new Model_Log_Types_Text(null, null, 'Du hast die alte Beschriftung weggewischt.'));
		else $player->log()->add(new Model_Log_Types_Text(null, null, 'Du hast die alte Beschriftung weggewischt und ":label" auf die Flasche geschrieben.', array(':label' => $this->label)));
		
		return true;
	}
		
	public function mixchem($chemval) {
        /** @global Model_Player $player */
        global $player;

        if ($this->fillrate == 0) return parent::mixchem($chemval);

        switch ($chemval)
        {
            case 6:
                $this->toxicity = 0;
                $player->log()->add(new Model_Log_Types_Text(null, null, 'Zunächst hörst du ein Zischen aus deiner Flasche, danach stellst du fest dass die Chemikalie dein Wasser gereinigt hat! Hurra!'));
                return true;
            case 10:
                $res = [];
                while ($this->fillrate(1))
                    $res[] = $player->job(1040) ? new Model_Items_Wine() : new Model_Items_Beer();
                $this->toxicity = 0;

                Tool_Scripts::chem_reaction(
                    'Heilige Scheiße! Du hast ein Wunder verbracht und Wasser in Alkohol verwandelt!',
                    $chemval,$this, $res);
                return true;
            default:
                $player->log()->add(new Model_Log_Types_Text(null, null, 'Zunächst hörst du ein Zischen aus deiner Flasche, danach bemerkst du einen beissenden Geruch. Willst du das Zeug jetzt wirklich noch trinken ... ?'));
                $this->toxicity += 9 * $chemval;
                return false;
        }
	}
}	