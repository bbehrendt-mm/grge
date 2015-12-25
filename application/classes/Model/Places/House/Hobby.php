<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_House_Hobby extends Model_Places_Abstract_Place {
	
	protected static $name = 'Hobbyraum';
	protected static $description = 'Jeder Mensch braucht nun mal ein Hobby. Das Hobby der Bewohner dieses Hauses beinhaltete anscheinend Peitschen, an der Wand befestigte Ketten und ein Laufgitter voll angebrannter Puppen.';
    protected static $outside = false;

    private $mentalstate = 0;

    public function uin($uin = NULL) {
        if ($uin !== null) {
            $this->inventory->add(new Model_Items_Body('Lächelnder Leichnam', 'Auf welche Art auch immer er gestorben ist, es scheint ihm unglaublichen Spaß gemacht zu haben - zumindest basierend auf seinem Gesichtsausdruck.'));
            $this->inventory->add(new Model_Items_Generic_Supercharger());
            $this->inventory->add(new Model_Items_Dildo2());
        }

        return parent::uin($uin);
    }

    public function pretick() {
        if (mt_rand(0,10) > 2) return true;
        if (count(Tool_Scripts::at_location($this->uin())) <= 0) return true;

        switch ($this->mentalstate)
        {
            case 0:
                $this->log->add(new Model_Log_Types_Text('Hobbykeller', 'Merkwürdige Ereignisse...', 'Die ganze Einrichtung hier erinnert dich irgendwie an den Londoner Dungeon...'));
                break;
            case 1:
                $this->log->add(new Model_Log_Types_Text('Hobbykeller', 'Merkwürdige Ereignisse...', 'In einer Ecke des Raumes findest du, versteckt unter etwas Geröll, mehrere kleine Schälchen mit Blut...'));
                $r_blood = mt_rand(2, 4);
                for ($i = 0; $i < $r_blood; $i++) $this->inventory->add(new Model_Items_Generic_Waterb());
                break;
            case 2:
                $this->log->add(new Model_Log_Types_Text('Hobbykeller', 'Erschreckende Ereignisse...', 'Du spürst einen Luftzug an deinem Nacken... er ist warm, fast als wäre es jemandes Atem. Aber hier ist zum Glück ja niemand...'));
                break;
            case 3:
                $this->log->add(new Model_Log_Types_Text('Hobbykeller', 'Erschreckende Ereignisse...', 'Zwischen den Puppen in der Kinderkrippe liegt noch etwas anderes... ein blutverschmierter Teddybär! Warum kommt es dir nur so vor, als wenn du den schon einmal irgendwo gesehen hättest?'));
                $this->inventory->add(new Model_Items_Generic_Cursed);
                break;
            case 4:
                $this->log->add(new Model_Log_Types_Text('Hobbykeller', 'Erschreckende Ereignisse...', 'Du fühlst erneut einen Luftzug, dann spürst du wie sich etwas von hinten nähert. Noch während du dich umdrehst siehst du etwas aufblitzen, dann fühlst du etwas Kaltes an deinem Hals. Dann wird alles um dich herum schwarz. Herzlichen Glückwunsch, du bist tot.'));

                $s_player = Tool_Scripts::at_location($this->uin());
                $s_player = $s_player[mt_rand(0, count($s_player) - 1)];

                $s_player->achievements()->achieve(Model_Achievement::MA_SLASHER_KILLER);

                $s_player->set_cod("Serienkiller-Opfer");
                $s_player->get_status()->retrieve('heartbeat')->unbuff();
                $this->mentalstate = 3;
                break;
            default:
                $this->mentalstate = 0;
        }

        $this->mentalstate++;
        return true;
    }
}	