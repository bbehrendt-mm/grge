<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Daytime extends Model_Buffs_Abstract_Buff {

	protected static $bid = 'daytime';
	protected static $visible = true;
    protected static $remotable = false;

    protected $effects = Array(
        Model_Player::MP_STAT_FREEZE => Array(
            Model_Buffs_Abstract_Buff::MB_RAISE_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_ACC => 0,
            Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
            Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
        ),
    );

    public function name() {
        switch (Tool_Scripts::get_timeofday($this->assoc_player)) {
            case "night":
                return "Tageszeit: Nacht"; break;
            case "morning":
                return "Tageszeit: Morgen"; break;
            case "day":
                return "Tageszeit: Tag"; break;
            case "evening":
                return "Tageszeit: Abend"; break;
            // event times
            case "snowynight":
                return "Tageszeit: Ewige Nacht"; break;
            default: return "";
        }
    }

    public function icon() {
        return static::static_icon(Tool_Scripts::get_timeofday($this->assoc_player));
    }

    public static function static_icon($s = null) {
        if (!$s) $s = Tool_Scripts::get_timeofday();
        switch ($s) {
            case "night":
                return "/application/assets/icons/buffs/dtnight.gif"; break;
            case "morning":
                return "/application/assets/icons/buffs/dtmorning.gif"; break;
            case "day":
                return "/application/assets/icons/buffs/dtday.gif"; break;
            case "evening":
                return "/application/assets/icons/buffs/dtevening.gif"; break;

            case "snowynight":
                return "/application/assets/icons/buffs/dtperpetualnight.gif"; break;
            default: return "";
        }
    }

    public function description() {
        return static::static_description(Tool_Scripts::get_timeofday($this->assoc_player));
    }

    public static function static_description($s = null) {
        if (!$s) $s = Tool_Scripts::get_timeofday();
        switch ($s) {
            case "night":
                return "Der Mond steht hoch am Himmel und die Welt ist in Dunkelheit getaucht. Da Zombies keine sonderlich guten Augen haben, kannst du ihnen nachts leichter entkommen. Allerdings wirst du ohne eine Taschenlampe auch weniger gegenstände finden..."; break;
            case "morning":
                return "Morgenstund hat Gold im Mund! Erstens ist dein Kaffee auf magische Art und Weise effektiver als am Rest des Tages, zweitens ist es draußen noch relativ kühl. Du benötigst daher weniger Energie, um Hausverbesserungen durchzuführen."; break;
            case "day":
                return "Die Sonne brennt gnadenlos am Himmel. Wenn du dich jetzt im Freien aufhälst, erhöht sich dein Wasserverbrauch. Lange Märsche zu anderen Orten kosten dich nun nicht nur Energie, sie machen dich auch durstig."; break;
            case "evening":
                return "Nach einem weiteren harten Tag geht die Sonne nun langsam unter. Jetzt hast du dir wirklich ein Feierabend-Bier verdient, immerhin wirkt Alkohol am Abend aus irgend einem Grund weniger schädlich."; break;

            case "snowynight":
                return "Es ist kalt, und Schnee weht dir ins Gesicht. An diesem Ort scheint ewige Nacht zu herrschen... ";
            default: return "";
        }
    }


    public function rebuild() {
        $tod = Tool_Scripts::get_timeofday($this->assoc_player);

        switch ($tod) {
            case 'snowynight':
                $this->effects[Model_Player::MP_STAT_FREEZE][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = 0.75;
                $this->effects[Model_Player::MP_STAT_FREEZE][Model_Buffs_Abstract_Buff::MB_DROP_ACC] = 0;
                break;
            default:
                $this->effects[Model_Player::MP_STAT_FREEZE][Model_Buffs_Abstract_Buff::MB_RAISE_ACC] = 0;
                $this->effects[Model_Player::MP_STAT_FREEZE][Model_Buffs_Abstract_Buff::MB_DROP_ACC] = 5;
        };

        //Control sun buff
        if ($tod != "day" || !$this->assoc_player->location()->is_outside())
            $this->assoc_player->buff_remove("sun");
        elseif ($tod == "day" && $this->assoc_player->location()->is_outside() && !$this->assoc_player->buff_retr("sun"))
            new Model_Buffs_Sun($this->assoc_player->id());

		return parent::rebuild();
	}
}
