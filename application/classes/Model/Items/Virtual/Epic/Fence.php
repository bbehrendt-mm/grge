<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Epic_Fence extends Model_Items_Abstract_Virtual implements Interface_Tickable {

    protected static $manual_ui = true;

    private $power = 0;
    private $on = false;

    public function get_status() {
        return $this->on;
    }

    public function get_remaining_power() {
        return $this->power + 4 * Tool_Scripts::count_available_items(Model_Items_Energy::cls(), false);
    }

    protected function hid(): Model_Hid {
        $hid = parent::hid();

        if (!$this->on && $this->power)
            $hid->add_action('Einschalten', Model_Action::factory()
                ->buttonskin('epic')
                ->description('Aktiviert den Laserzaun. Während der eingeschaltet ist konsumiert er Energie, dafür können Zombies unmöglich in dein Versteck einbrechen.')
                ->effect(Model_Effect::factory()
                    ->message('Du hast den Laserzaun aktiviert.')
                    ->custom(function() {
                        $this->on = true;
                    })
                )
            , 'pow1');
        elseif (!$this->on && !$this->power)
            $hid->add_action('Einschalten', Model_Action::factory()
                ->buttonskin('epic')
                ->description('Aktiviert den Laserzaun. Während der eingeschaltet ist konsumiert er Energie, dafür können Zombies unmöglich in dein Versteck einbrechen.')
                ->requirement(Model_Items_Energy::cls(),1)
                ->effect(Model_Effect::factory()
                    ->message('Du hast den Laserzaun aktiviert.')
                    ->custom(function() {
                        $this->on = true;
                        $this->power = 4;
                    })
                )
                , 'pow2');
        else
            $hid->add_action('Ausschalten', Model_Action::factory()
                ->buttonskin('epic')
                ->description('Deaktiviert den Laserzaun. Es wird keine Energie mehr verbraucht, aber dafür können die Zombies wieder eindringen.')
                ->effect(Model_Effect::factory()
                    ->message('Du hast den Laserzaun aufgeladen.')
                    ->custom(function() {
                        $this->on = false;
                    })
                )
            );

        return $hid;
    }

    public function tick($id, $type = Interface_Tickable::IT_TYPE_PLAYER): void
    {
        if ($type !== Interface_Tickable::IT_TYPE_LOCATION) return;
        if ($this->on) {
            if ($this->power >= 1) $this->power--;
            elseif ($power = Tool_Scripts::first_available_item(Model_Items_Energy::cls(),false)) {
                $this->power+=3;
                $power->consume();
            }

            else $this->on = false;
        }
    }
}