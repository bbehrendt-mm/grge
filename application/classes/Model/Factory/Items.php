<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Factory_Items extends Model_Factory_Abstract {

    protected static $base = 'items';
    protected static $expected_result_class = 'Model_Items_Abstract_Item';

    private $fillrate = 1;
    private $decay = 0.1;

    /**
     * @param number $d
     * @return Model_Factory_Items
     */
    public function set_decay_factor($d) {
        /** @global Model_Game $game*/
        global $game;
        $df = $game ? $game->config('places.dryout_factor') : 1;

        $this->decay = $d * $df;
        return $this;
    }

    /**
     * @param number $d
     * @return Model_Factory_Items
     */
    public function set_fillrate($d) {
        $this->fillrate = $d;
        return $this;
    }

    /**
     * @param bool $force
     * @param bool $apply_decay
     * @return null|Model_Items_Abstract_Item
     */
    public function spawn($force = false, $apply_decay = true, $chances_modifier = 1) {
        if (!$this->equalized || (!$force && (mt_rand()/mt_getrandmax() > ($this->fillrate * $chances_modifier))) || !($k = $this->get_element()))
            return null;

        if ($apply_decay) {
            $this->fillrate -= $this->fillrate * $this->decay;
            $this->equalized[$k] -= $this->equalized[$k] * $this->decay;

            $this->realign();
        }
        return new $k;
    }

    /**
     * Replenishes the dryout. Set factor to 1 to completely reset dryout.
     * @param float $factor Set to 1 for full replenishment, 0 for no effect.
     * @return $this
     */
    public function replenish($factor = 1.0) {
        $this->fillrate += (1 - $this->fillrate) * max(0,min(1,$factor));
        return $this;
    }

    /**
     * Modifies the default dryout factor.
     * @param float $modifier Modification factor
     * @return $this
     */
    public function modify_decay($modifier) {
        $this->decay = max(0,min(1,1 - (1 - $this->decay)/$modifier));
        return $this;
    }

    public function findings_left() {
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