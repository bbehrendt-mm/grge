<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Mental extends Model_Places_Abstract_Place {
	
	protected static $name = 'Verlassene Irrenanstalt';
	protected static $description = 'Vor langer Zeit war dieses Gebäude mal ein normales Krankenhaus. Irgendwann wurde es zu einer "Heilanstalt für Geisteskranke" umfunktioniert. Gerüchte besagen, dass niemand, der dort eingeliefert wurde, jemals wieder herausgekommen ist. Natürlich ist das Gebäude längst verlassen, es gibt also überhaupt keinen Grund vor irgendwas dort drin Angst zu haben. Obwohl es so scheint als würden selbst die Zombies dieses Gebäude meiden ...';
    protected static $icon = 'mental';
    protected static $outside = false;

	private $mentalstate = 0;

    public function get_mental_state() {
        return $this->mentalstate;
    }
	
	public function pretick() {
        foreach (Globals::CurrentGame()->get_initialized_events() as $ev)
            $ev->event_locationTick($this);

        if (mt_rand(0,10) > 3) return true;
		if (count(Tool_Scripts::at_location($this->uin())) <= 0) return true;

		switch ($this->mentalstate)
		{
			case 0:
                $this->log->add(new Model_Log_Types_Text('Erforschung der Irrenanstalt', 'Merkwürdige Ereignisse...', 'Dir läuft ein Schauer über den Rücken, als du durch die schlecht beleuchteten Gänge schleichst ...'));
			    break;
			case 1:
                $this->log->add(new Model_Log_Types_Text('Erforschung der Irrenanstalt', 'Merkwürdige Ereignisse...', 'Du siehst Blutspuren an der Wand. Die müssen entstanden sein, als die Anstalt von Zombies überrannt wurde. Allerdings sieht das Blut überraschend frisch aus ...'));
                break;
			case 2:
                $this->log->add(new Model_Log_Types_Text('Erforschung der Irrenanstalt', 'Merkwürdige Ereignisse...', 'Du hörst ein knackendes Geräusch hinter dir, und spürst einen Luftzug. Du machst dich bereit, auf Zombies zu treffen. Aber nichts geschieht ...'));
			    break;
			case 3:
                $this->log->add(new Model_Log_Types_Text('Erforschung der Irrenanstalt', 'Erschreckende Ereignisse...', 'Hinter einem Tresen liegen ein paar aufgestapelte Leichen. Sie scheinen ausschließlich Verletzungen am Hals zu haben, der Rest ist unversehrt. Untypisch für Zombies...'));
                $r_bodies = mt_rand(2, 5);
                for ($i = 0; $i < $r_bodies; $i++) $this->inventory->add(new Model_Items_Body);
                break;
			case 4:
                $this->log->add(new Model_Log_Types_Text('Erforschung der Irrenanstalt', 'Erschreckende Ereignisse...', 'Du findest einen Spiegel auf dem Boden, auf dem ein blutverschmierter Teddybär liegt. Das Blut scheint frisch zu sein ... Was ist nur in dieser Anstalt geschehen? Du verspürst den immer stärker werdenden Drang, dieses Gebäude zu verlassen und nie wieder zurückzukehren...'));
			    $this->inventory->add(new Model_Items_Generic_Cursed);
			    break;
			case 5:
                $this->log->add(new Model_Log_Types_Text('Erforschung der Irrenanstalt', 'Erschreckende Ereignisse...', 'Du fühlst erneut einen Luftzug, dann spürst du wie sich etwas von hinten nähert. Noch während du dich umdrehst siehst du etwas aufblitzen, dann fühlst du etwas Kaltes an deinem Hals. Dann wird alles um dich herum schwarz. Herzlichen Glückwunsch, du bist tot.'));
			
                $s_player = Tool_Scripts::at_location($this->uin(), true, true);
                $s_player = $s_player[mt_rand(0, count($s_player) - 1)];

                if (!Tool_Scripts::is_npc($s_player)) {
                    $s_player->achievements()->achieve(Model_Achievement::MA_SLASHER_KILLER);
                    $s_player->get_status()->set_cause_of_death("Serienkiller-Opfer");
                    $s_player->get_status()->retrieve('heartbeat')->unbuff();
                } else $s_player->kill();

                $this->mentalstate = 4;
			    break;
            default:
                $this->mentalstate = 0;
		}
		
		$this->mentalstate++;

		return true;
	}
}	