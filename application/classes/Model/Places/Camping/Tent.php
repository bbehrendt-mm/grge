<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Camping_Tent extends Model_Places_Abstract_Place {
	
	protected static $name = 'Zelt';
	protected static $description = 'Ein Zelt bietet leider überhaupt keinen Schutz vor Zombies - der Bewohner dieses Zelts hat das wohl auf die harte Tour lernen müssen. Wenigstens kannst du jetzt ungesraft in seinen Sachen wühlen.';
    protected static $icon = 'tent';
    protected static $outside = false;

    public function uin($uin = NULL) {
        if ($uin !== null) {
            if (mt_rand(0,9) > 1) $this->inventory->add(new Model_Items_Body());
            else $this->inventory->add(new Model_Items_Body2());
        }

        return parent::uin($uin);
    }
}	