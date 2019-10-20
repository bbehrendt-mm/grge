<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Trigger extends Model_Items_Abstract_Item {

    protected $info;
	public function __construct(string $name, string $icon, string $desc, string $action, int $cat, float $weight, ?string $label = null, int $deco = 0) {
		parent::__construct();
		$this->info = [
            'name' => $name,
            'icon' => $icon,
            'description' => $desc,
            'category' => $cat,
            'deco' => $deco,
            'weight' => $weight,
            'label' => $label,
            'action' => $action
        ];
	}

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action($this->info['action'], Model_Action::factory()
                    ->allow_auto(false)
                    ->allow_remote(false)
                    ->effect(
                        Model_Effect::factory()
                            ->custom(function(Interface_Plentity $p) {
                                $p->location()->trigger_item_action($this);
                            })
                    )
            );
    }

    public function can_take(string &$message): bool {
        $message = 'Du solltest das hier liegen lassen...';
	    return false;
    }

    public function cat(): int {
        return $this->info['category'];
    }

    public function name(): string {
        return $this->info['name'];
    }

    public function icon(): string {
        return 'items/' . $this->info['icon'];
    }

    public function description(): string {
        return $this->info['description'];
    }

    public function deco(): int {
        return $this->info['deco'];
    }

    public function weight(): int {
        return $this->info['weight'];
    }

    public function label(): ?string {
        return $this->info['label'];
    }

}	