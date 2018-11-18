<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Buffs_Abstract_Fragile extends Model_Buffs_Abstract_Buff {
	
	protected static $bid = 'fragile';
    protected static $alt_id = 'fragile';
	/** @var bool $abortable */
    protected static $abortable = false;

    /**
     * This function is called upon aborting the buff
     * @return mixed
     */
    abstract protected function action_on_complete();

    /**
     * Returns weather this buff is abortable
     * @return bool
     */
    public function abortable(): bool {
		return static::$abortable;
	}

    /**
     * Cancels the buff if it is abortable, otherwise returns false
     *
     * @return bool True, when the buff was removed, otherwise false
     */
    public function cancel(): bool {
		if (static::$abortable)	return parent::unbuff();
        else return false;
	}
	
	public function unbuff(): bool
    {
		$tmp = parent::unbuff();
        $this->action_on_complete();
        return $tmp;
	}
}