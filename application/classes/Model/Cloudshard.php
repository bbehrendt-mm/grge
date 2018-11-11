<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Cloudshard extends Model implements Interface_Cloudshard {

	//Own UIN
	protected $obj_uin;

    /**
     * Returns the own UIN, or sets a new one
     * @param number $new_uin New UIN, omit to return the current one
     * @return number
     */
    public function uin($new_uin = NULL)
	{
		if ($new_uin === NULL) return $this->obj_uin;
		else return $this->obj_uin = $new_uin;
	}

}
