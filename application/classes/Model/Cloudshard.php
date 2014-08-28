<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Cloudshard extends Model {

	//Own UIN
	protected $uin;

    /**
     * Returns the own UIN, or sets a new one
     * @param number $new_uin New UIN, omit to return the current one
     * @return number
     */
    public function uin($new_uin = NULL)
	{
		if ($new_uin === NULL) return $this->uin;
		else return $this->uin = $new_uin;
	}

}
