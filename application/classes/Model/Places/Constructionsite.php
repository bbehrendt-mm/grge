<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Constructionsite extends Model_Places_Abstract_Place {
	
	protected static $name = 'Baustelle';
	protected static $description = 'Was hier mal gebaut werden sollte ist nicht erkennbar - man sieht nur verrostete Eisenträger und Baumaterial, das am Boden liegt. Einige Zombies haben die Baustelle offenbar zu ihrem Wohnsitz erkoren. Glücklicherweise haben sie hier praktisch keine Möglichkeit, einen Überraschungsangriff zu starten.';
    protected static $icon = 'site';

    public function uin($new = null) {
        if ($new !== null)
            $this->inventory->add(new Model_Items_Virtual_Location_Container());
        return parent::uin($new);
    }
}	