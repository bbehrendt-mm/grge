<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Greenhouse extends Model_Places_Abstract_Place {
	
	protected static $name = 'Gewächshaus "Plants & Zombies"';
	protected static $description = 'Die Scheiben um dieses Gewächshauses sind allesamt zersprungen, die meisten Pflanzen sind infolge dessen vertrocknet. Im Zentrum des Gewächshauses steht, von einem kleinen Weg umschlossen, ein riesiges baumartiges Gewächs. Obwohl es wie der Rest der Pflanzen hier ziemlich vertrocknet ist, sieht es irgendwie noch lebendig aus... Vielleicht kannst du es zum Leben erwecken, wenn du es gießt?';
    protected static $icon = 'green';

    public function uin($uin = NULL) {
        if ($uin !== null) {
            $this->inventory->add(new Model_Items_Virtual_Location_Greenhouse());
            $this->inventory->add(new Model_Items_Virtual_Epic_Garden());
        }


        return parent::uin($uin);
    }

}	