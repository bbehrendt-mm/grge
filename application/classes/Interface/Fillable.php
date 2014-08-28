<?php defined('SYSPATH') or die('No direct script access.');

interface Interface_Fillable {
	public function interaction_fill($liquid_id);
	public function interaction_fillfrom($bottle_id);
	public function capacity();
	public function fillrate();
}