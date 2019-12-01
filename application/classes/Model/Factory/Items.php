<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Factory_Items extends Model_Factory_Abstract {

    protected static $base = 'items';
    protected static $expected_result_class = 'Model_Items_Abstract_Item';

    private $fillrate = 1.0;
    private $decay = 0.1;

    /**
     * @param float $d
     *
     * @return Model_Factory_Items
     * @throws Exception
     */
    public function set_decay_factor(float $d): \Model_Factory_Items
    {
        $df = Globals::hasCurrentGame() ? Globals::CurrentGameF()->config('places.dryout_factor') : 1;
        $this->decay = $d * $df;
        return $this;
    }

    /**
     * @param bool $force
     * @param bool $apply_decay
     * @param int  $chances_modifier
     *
     * @return null|Model_Items_Abstract_Item
     */
    public function spawn($force = false, $apply_decay = true, $chances_modifier = 1): ?\Model_Items_Abstract_Item
    {
        if (!$this->equalized || !($k = $this->get_element()) || (!$force && (mt_rand()/mt_getrandmax() > ($this->fillrate * $chances_modifier))))
            return null;

        /** @var Model_Items_Virtual_Invoke_Abstract|string $k */
        /** @noinspection NotOptimalIfConditionsInspection */
        if (Tool_System::instance_of($k, Model_Items_Virtual_Invoke_Abstract::cls()) && !$k::countAsItem())
            $apply_decay = false;

        if ($apply_decay) {
            $this->fillrate -= $this->fillrate * $this->decay;
            $this->equalized[$k] -= $this->equalized[$k] * $this->decay;

            $this->realign();
        }
        return new $k;
    }

    /**
     * Forced, non-decay and non-trigger
     * @param int $itd
     * @return null|Model_Items_Abstract_Item
     */
    public function nd_spawn($itd = 0): ?\Model_Items_Abstract_Item
    {
        if ($itd >= 10 || !$this->equalized || !($k = $this->get_element()))
            return null;

        /** @var Model_Items_Virtual_Invoke_Abstract|string $k */
        if (Tool_System::instance_of($k, Model_Items_Virtual_Invoke_Abstract::cls()))
            return $this->nd_spawn($itd + 1);

        else return new $k;
    }

    /**
     * Replenishes the dryout. Set factor to 1 to completely reset dryout.
     * @param float $factor Set to 1 for full replenishment, 0 for no effect. If negative, dryout is increased.
     * @return $this
     */
    public function replenish($factor = 1.0): self
    {
        if ($factor > 0)     $this->fillrate += (1 - $this->fillrate) * max(0,min(1,$factor));
        elseif ($factor < 0) $this->fillrate *= max(0,min(1,1.0+$factor));
        return $this;
    }

    /**
     * Modifies the default dryout factor.
     * @param float $modifier Modification factor
     * @return $this
     */
    public function modify_decay(float $modifier): self
    {
        if ($modifier === 1.0) return $this;
        $this->decay = $modifier < 1.0 ? ( $this->decay * $modifier ) : ( 1.0 - (1.0 - $this->decay)/$modifier );
        $this->decay = max(0.0,min(1.0,$this->decay));
        return $this;
    }

    public function get_fillrate(): float {
        return $this->fillrate;
    }

    public function findings_left(): int
    {
        $f = $this->fillrate;
        $r = 0;
        if (!$this->equalized) return 0;
        elseif ($this->decay <= 0) return PHP_INT_MAX;
        while ($f > 0.05) {
            $f -= $f * $this->decay;
            $r++;
        }
        return $r;
    }
}	