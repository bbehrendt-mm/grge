<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Hospital_Private extends Model_Places_Abstract_Hideout {

    protected static $name = 'Einzelzimmer';
    protected static $description = 'Privatpatienten haben mehr Geld, also sind sie die besseren Menschen und verdienen bessere medizinische Versorgung. Dazu gehört auch dieses Luxuszimmer, mit Besuchersessel aus Leder, Heimkinoanlage und natürlich einer gut bestückten Bar. Eigentlich könntest du dich auch selbst hier "einliefern" lassen und diesen Ort zu einem Versteck umbauen...';
    protected static $icon = 'hospital_private';

    //Base defense
    protected $defense = 2;

    //Base: 15% per day
    protected static $decay_rate = 0.10;

    //Exp: 8% per day
    protected static $decay_exp = 0.15;

    public function uin($new = null) {
        if ($new !== null)
            $this->add_upgrades(['bedr1','bedr2','bedr3']);

        return parent::uin($new);
    }

}	