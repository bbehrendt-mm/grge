<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Bottle extends Model_Items_Abstract_Label implements Interface_Countable {
	protected static $cat = Model_Items_Abstract_Item::MIAI_CAT_BOTTLES;

	protected static $capacity = 4;

	private $bottle_fillrate;
	public $bottle_toxicity;

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Einen Schluck trinken',
                Model_Action::factory()
                   ->allow_auto(false)
                   ->condition(function() {
                        return $this->fillrate() > 0;
                   })
                    ->fail_message('Du führst die Flasche an deinen Mund, um zu trinken - aber sie ist leer.')
                    ->show_as(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_THIRST, 25)
                            ->ambiguous_effect(Model_Status::MS_STAT_HEALTH)
                    )
                    ->effect(
                        Model_Effect::factory()
                            ->effect(Model_Status::MS_STAT_THIRST, 25)
                            ->effect(Model_Status::MS_STAT_HEALTH, -$this->bottle_toxicity)
                            ->ambiguous_effect(Model_Status::MS_STAT_HEALTH)
                            ->custom(function(Interface_Plentity $p) {
                                /** @var Model_Player|Interface_Plentity $p */
                                if (!Tool_Scripts::is_npc($p) &&
                                    $this->toxicity() > max(50, $p->get_status()->get(Model_Status::MS_STAT_HEALTH)))
                                    $p->achievements()->achieve(Model_Achievement::MA_POISON_DRINK);
                            })
                            ->consume($this)
                            ->message($this->drinkmsg())
                    )
            )
            ->add_action('Micropur hineinwerfen',
                Model_Action::factory()
                    ->allow_remote(false)
                    ->requirement(Model_Items_Generic_Micropur::cls(), 1)
                    ->condition(function() {
                        return $this->fillrate() > 0;
                    })
                    ->fail_message('Du musst Wasser in der Flasche haben, damit du sie reinigen kannst!')
                    ->effect(
                        Model_Effect::factory()
                            ->custom(function() {
                                if (Globals::CurrentGameF()->config('items.bottle.allow_full_detox')) $this->bottle_toxicity = 0;
                                else {
                                    $detox_amount = floor(Globals::CurrentGameF()->config('items.bottle.detox_amount') / $this->fillrate());
                                    $this->bottle_toxicity = max($this->bottle_toxicity - $detox_amount, 0);
                                }
                            })
                            ->message($this->cleanmsg())
                    )
            )
            ->add_action('Untersuchen ...',
                Model_Action::factory()
                    ->allow_remote(false)
                    ->condition(function() {
                        return $this->fillrate() > 0;
                    })
                    ->fail_message('Diese Flasche ist leer.')
                    ->effect(
                        Model_Effect::factory()
                            ->message($this->observemsg())
                    )
            )
            ->add_action( 'Füllen' ,
                          Model_Action::factory()
                              ->buttonskin( 'batch' )
                              ->allow_remote(false)
                              ->effect(
                                  Model_Effect::factory()
                                      ->custom( function(Model_Player $p) {
                                          $bottles = Tool_Scripts::get_items( static::cls() );
                                          $water = Tool_Scripts::get_items( Model_Items_Abstract_Liquid::cls() );

                                          while (!empty($bottles) && !empty($water)) {

                                              /** @var Model_Items_Abstract_Bottle $latest_bottle */
                                              $latest_bottle = array_pop($bottles);

                                              while ( $latest_bottle->count() < $latest_bottle->capacity() && !empty($water) )
                                                  $latest_bottle->interaction_fill( array_pop($water) );
                                          }
                                      } )
                              )
            )
            ->add_action( 'Leeren' ,
                          Model_Action::factory()
                              ->buttonskin( 'batch' )
                              ->allow_remote(false)
                              ->effect(
                                  Model_Effect::factory()
                                      ->custom( function(Model_Player $p) {
                                          $bottles = Tool_Scripts::get_items( static::cls() );

                                          while (!empty($bottles)) {

                                              /** @var Model_Items_Abstract_Bottle $latest_bottle */
                                              $latest_bottle = array_pop($bottles);
                                              if ($latest_bottle->count() > 0)
                                                $latest_bottle->interaction_extract(true);
                                          }
                                      } )
                              )
            );
    }
	
	public function consume($count = 1): int {
		if ($this->bottle_fillrate > 0) {
			$this->bottle_toxicity -= ($this->bottle_toxicity * ($count/$this->bottle_fillrate));
			$this->bottle_fillrate--;
			return 1;
		}
		return 0;
	}
	
	public function capacity() {
		return static::$capacity;
	}
	
	public function fillrate($take = NULL) {
		if ($take === NULL) return (int)$this->bottle_fillrate;
		elseif (($take > 0) && ($take <= $this->bottle_fillrate)) {
			$this->consume($take);
			return true;
		}			
		else return false;
	}
	
	public function count() {
		return $this->fillrate();
	}
	
	public function toxicity() {
		return $this->bottle_toxicity;
	}
	
	public function add_water($num, $toxicity) {
		if ($num > 0 && ($this->bottle_fillrate + $num <= static::$capacity))
		{
			$this->bottle_fillrate += $num;
			$this->bottle_toxicity += $num * $toxicity;
			return true;
		}
		else return false;
	}
	
	public function get_water($num) {
		return $this->fillrate($num);
	}
	
	public function stackname(): ?string
    {
		return (static::$capacity === 1) ? 'Ration' : 'Rationen';
	}
	
	public function label(): ?string
    {
		return $this->label;
	}

    private function drinkmsg():string {
        if ($this->bottle_toxicity === 0) 		return 'Du nimmst einen Schluck aus deiner Flasche. Dein Durst verschwindet und du fühlst dich erfrischt!';
        elseif ($this->bottle_toxicity <= 5)	return 'Du nimmst einen Schluck aus deiner Flasche. Das Wasser hat einen leicht modrigen Nachgeschmack, dennoch hilft es gegen deinen Durst.';
        elseif ($this->bottle_toxicity <= 10)	return 'Du nimmst einen Schluck aus deiner Flasche. Es fällt dir schwer, den Güllegeschmack des Wassers zu ignorieren, aber irgendwie musst du ja gegen deinen Durst vorgehen.';
        elseif ($this->bottle_toxicity <= 30)	return 'Du nimmst einen Schluck aus deiner Flasche. Das Wasser ist schleimig und verklebt dir die Kehle. Außerdem schmeckt es, als hätte sich darin ein Zombie aufgelöst.';
        elseif ($this->bottle_toxicity <= 70)	return 'Du öffnest die Flasche, und sofort triebt dir der üble Geruch Tränen in die Augen. Du schickst ein Stoßgebet in den Himmel, schließt deine Augen und schluckst die widerliche Brühe hinunter.';
        else 							return 'Das Wasser in deiner Flasche hat inzwischen eine teerartige Konsistenz erreicht. Herzlichen Glückwunsch, das Innere der Flasche ist vermutlich auf Jahrzehnte verseucht. Nachdem du einen Schluck genommen hast fühlst du sofort, wie alle deine Organe weggeätzt werden. Lecker!';
    }

    private function cleanmsg():string {
        $tox = max(0, Globals::CurrentGameF()->config('items.bottle.allow_full_detox') ? 0 : $this->bottle_toxicity - 50);
        if ($tox <= 2)		return 'Du wirfst die Tablette ins Wasser - es sprudelt ein wenig, danach verbreitet sich angenehmer Zitronenduft. Deine Wasserflasche ist wieder komplett gereinigt!';
        elseif ($tox <= 10)	return 'Du wirfst die Tablette ins Wasser - es sprudelt ein wenig, danach verbreitet sich angenehmer Zitronenduft. Zwar ist das Wasser noch immer nicht ganz sauber, aber wesentlich trinkbarer als zuvor!';
        elseif ($tox <= 30)	return 'Du wirfst die Tablette ins Wasser - es sprudelt ein wenig, danach mischt sich angenehmer Zitronenduft in den Güllegeruch des Wassers. Naja, besser als nichts...';
        elseif ($tox <= 70)	return 'Du wirfst die Tablette ins Wasser - es sprudelt ein wenig, aber so wirklich sauber ist das Wasser nicht geworden. Eventuell solltest du eine zweite Tablette reinwerfen...';
        else			    return 'Du wirfst die Tablette ins Wasser - es dauert eine Weile, bis sie in dem zähflüssigen Inhalt deiner Flasche versinkt. Um die Tablette herum löst sich der Schleim etwas auf, der größte Teil des Wassers in der Flasche zeigt sich jedoch von deinen Reinigungsversuchen unbeeindruckt.';
    }

    private function observemsg():string  {
        if ($this->bottle_toxicity <= 2) 		return 'Das Wasser in der Flasche scheint relativ klar zu sein ...';
        elseif ($this->bottle_toxicity <= 10) 	return 'Das Wasser in der Flasche ist etwas löhmerig ...';
        elseif ($this->bottle_toxicity <= 30)	return 'Ein modriger Geruch steigt aus der Flasche auf... Aber wer wird schon wählerisch sein, wenn es um Wasser geht?';
        elseif ($this->bottle_toxicity <= 70)	return 'Das Wasser in dieser Flasche stinkt erbärmlich! Das solltest du nur trinken, wenn du absolut verzweifelt bist!';
        else 							return 'Der Geruch, der aus der Flasche aufsteigt, lässt dir die Augen tränen. Was immer da drin ist, trinken solltest du es nicht.';
    }


    /**
     * @param Model_Items_Abstract_Liquid $item
     *
     * @return bool
     * @throws Exception
     */
    public function interaction_fill($item): bool
    {
		if ($this->bottle_fillrate >= static::$capacity)
		{
            Globals::PrimaryPlayerF()->log()->add(new Model_Log_Types_String(null, 'Diese Flasche ist bereits bis zum Rand gefüllt. Du kannst unmöglich eine weitere Ration Wasser darin unterbringen.'));
			return true;
		}
		
		if (!Tool_System::instance_of($item, Model_Items_Abstract_Liquid::cls())) return false;
		
		if ($this->bottle_toxicity <= 0 || $item->toxicity() > $this->bottle_toxicity)
		    $this->bottle_toxicity += $item->toxicity();
		else $this->bottle_toxicity += round( ($item->toxicity()*$item->toxicity())/$this->bottle_toxicity );
		
		$item->consume();
		$this->bottle_fillrate++;
		
		return true;
    }

    /**
     * @param Model_Items_Abstract_Bottle $item
     * @return bool
     * @throws Exception
     */
    public function interaction_fillfrom($item): bool
    {
	
		if ($this->bottle_fillrate >= static::$capacity)
		{
            Globals::PrimaryPlayerF()->log()->add(new Model_Log_Types_String(null, 'Diese Flasche ist bereits bis zum Rand gefüllt. Du kannst unmöglich eine weitere Ration Wasser darin unterbringen.'));
			return true;
		}
	
		if (!Tool_System::instance_of($item, Model_Items_Abstract_Bottle::cls())) return false;
	
		if ($item->toxicity() >= $this->bottle_toxicity) $this->bottle_toxicity += $item->toxicity();
		else $this->bottle_toxicity += round( ($item->toxicity()*$item->toxicity())/$this->bottle_toxicity );
	
		$item->consume();
		$this->bottle_fillrate++;
	
		return true;
	}
	
	public function interaction_extract(bool $all = false) {
		if ($this->bottle_fillrate <= 0) {
            Globals::PrimaryPlayerF()->log()->add(new Model_Log_Types_String(null, 'Diese Flasche ist leider leer...'));
			return true;
		}

        $t = $all ? 0 : ($this->bottle_fillrate - 1);
        while ($this->bottle_fillrate > $t) {
            Globals::PrimaryPlayerF()->location()->inventory()->add(new Model_Items_Generic_Waterv($this->toxicity()));
            $this->consume();
        }
	
		return true;
	}
		
	public function mixchem($chemval): bool
    {
        if ($this->bottle_fillrate === 0) return parent::mixchem($chemval);

        switch ($chemval)
        {
            case 6:
                $this->bottle_toxicity = 0;
                Globals::PrimaryPlayerF()->log()->add(new Model_Log_Types_String(null, 'Zunächst hörst du ein Zischen aus deiner Flasche, danach stellst du fest dass die Chemikalie dein Wasser gereinigt hat! Hurra!'));
                return true;
            case 10:
                $res = [];
                while ($this->fillrate(1))
                    $res[] = Globals::PrimaryPlayerF()->job([1040,1041]) ? new Model_Items_Wine() : new Model_Items_Beer();
                $this->bottle_toxicity = 0;

                Tool_Scripts::chem_reaction(
                    'Heilige Scheiße! Du hast ein Wunder verbracht und Wasser in Alkohol verwandelt!',
                    $chemval,$this, $res);
                return true;
            default:
                Globals::PrimaryPlayerF()->log()->add(new Model_Log_Types_String(null, 'Zunächst hörst du ein Zischen aus deiner Flasche, danach bemerkst du einen beissenden Geruch. Willst du das Zeug jetzt wirklich noch trinken ... ?'));
                $this->bottle_toxicity += 9 * $chemval;
                return false;
        }
	}
}	