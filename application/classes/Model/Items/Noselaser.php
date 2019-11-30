<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Noselaser extends Model_Combat_Weapons_Energy implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Rudolph\'s Nasenlaser',
			'icon' => 'nose',
			'description' => '',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 0;

	protected static $damage = [10,30];
	protected static $range = [10,90];
	protected static $accuracy = 0.95;
	protected static $use_fixed_accuracy = false;
	protected static $aoe = true;

	protected $reload_variant = false;
	protected $ammo = 1;

	protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_SHOT_RLASER;

	public function __construct($type = null, bool $reload = false) {
        parent::__construct($type);
        $this->reload_variant = $reload;
    }

    public function energy(): int
    {
        return $this->registered_user && $this->registered_user->get_status()->get(Model_Status::MS_STAT_DRUNK) > 50 ? 0 : 25;
    }

    public function usable(): bool {
        return $this->ammo > 0 && parent::usable();
    }

    /**
     * @param Model_Combat_Actor $me
     * @param Model_Combat_Actor $opponent
     * @param number             $damage
     * @param Model_Combat_Scene $scene
     *
     * @return bool
     * @throws Exception
     */
    public function trigger_usage(Model_Combat_Actor $me, Model_Combat_Actor $opponent, $damage, Model_Combat_Scene $scene): bool {
        $b = parent::trigger_usage($me,$opponent,$damage,$scene);

        $this->ammo--;
        if (!$this->reload_variant || !$this->registered_user) return $b;

        $me->add_modifier('drunk', $me->ki_mod_strength('drunk') + 0.25);
        $this->registered_user->get_status()->modify(Model_Status::MS_STAT_DRUNK, 25);
        $scene->popup($me, '+25', 'status_drunk.gif', false);

        $d = $me->ki_mod_strength('drunk');

        if ($d < 0.50)
            $scene->dialog($me, 'Hui, das zieht einem die Hufe aus!');
        elseif ($d <= 0.90)
            $scene->dialog($me, 'Hey... meine Nase kribbelt ... *hicks*');
        else {
            $scene->dialog($me, '*hicks* machnurma kurz... Augen zu... *hicks*');
            $me->add_modifier('sleep', 0.95);
            $this->ammo = 0;
        }

        $scene->character_sfx($me,true,'drunk', $d);

        if ($d <= 0.90 && Tool_Gambling::random(0.95)) {
            $this->ammo++;
            $scene->popup($me, 'Aufgeladen', $this->icon());
        }

        return $b;
    }
}	