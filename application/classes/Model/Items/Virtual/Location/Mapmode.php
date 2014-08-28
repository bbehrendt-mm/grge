<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Mapmode extends Model_Items_Abstract_Virtual {

    protected $remaining = array(
        'hideout_asknews' => PHP_INT_MAX,
        'hideout_cashout' => PHP_INT_MAX,
    );

    public $last_news = 0;

    protected function hid() {
        $phpbb53 = $this;
        return parent::hid()->add_action('Kartendaten an deine Stadt übermitteln', Model_Action::factory()
                ->buttonskin('mapmode')
                ->effect(Model_Effect::factory()
                    ->custom(function() use ($phpbb53) {
                            /**
                             * @global $player Model_Player
                             */
                            global $player;

                            /** @var $items Model_Items_Maptool[] */
                            if (!($items = $player->inventory()->get('Model_Items_Maptool'))) return;
                            $points = $items[0]->retrieve_info(true);

                            if ($points <= 0) $player->log()->add(new Model_Log_Types_Text(null, null, 'Du hast leider keine neuen Informationen, die du an deine Stadt senden könntest...'));
                            else {

                                $player->location()->set_map_points($player->location()->get_map_points() + $points);
                                $player->log()->add(new Model_Log_Types_Text(null, null, 'Deine Stadt ist dir äußerst dankbar für diese neuen Informationen.'));
                            }
                        })
                )
            , 'hideout_cashout')
            ->add_action('Nach Neuigkeiten aus der Stadt fragen', Model_Action::factory()
                    ->buttonskin('mapmode')
                    ->effect(Model_Effect::factory()
                            ->custom(function() use ($phpbb53) {
                                /**
                                 * @global $game Model_Game
                                 * @global $player Model_Player
                                 */
                                global $player, $game;

                                if (($phpbb53->last_news + 288) > $game->duration()) {
                                    $player->log()->add(new Model_Log_Types_Text(null, null, 'Derzeit gibt es nichts Neues aus der Stadt zu berichten... probiere es in :duration noch einmal!', array(':duration' => Tool_Numerics::duration_to_string($phpbb53->last_news + 288 - $game->duration()))));
                                    return;
                                }

                                $phpbb53->last_news = $game->duration();

                                $str = "";
                                $user = array();
                                switch (mt_rand(0,5)) {
                                    case 0: 	$str = "Die Bürger in der Stadt sind beunruhigt.... seit mehrern Tagen ist niemand mehr verdurstet oder in der Aussenwelt verschwunden. Es verbreiten sich Gerüchte, dass eine Meta in dieser Stadt anwesend wäre...";
                                        break;
                                    case 1:		$user = Model_User::random_names(2);
                                        $str = "Seit :rnd[0] gestern Abend sturzbetrunken in den Brunnen gekotzt hat, hat sich unser Wasserverbrauch halbiert. Stadtverwalter :rnd[1] lies jedoch vor einer halben Stunde verlauten, das eine hätte nichts mit dem anderen zu tun.";
                                        break;
                                    case 2:		$user = Model_User::random_names(3);
                                        $str = "Nachtwächter :rnd[0] behauptet, gestern gegen 22 Uhr :rnd[1] dabei erwischt zu haben, wie er :rnd[2]'s Malteser \"Schnuffel\" ein Bein gestohlen hat.";
                                        break;
                                    case 3:		$user = Model_User::random_names(1);
                                        $str = "Die Bevölkerung ist schockiert! Heute Morgen wurde zu allgemeiner Bestürzung festgestellt, dass Bürgermeister :rnd[0] seit nunmehr 2 Wochen ein Zombie ist! Im Nachhinein erklärt dies natürlich zum Einen, warum die Stadt seit 2 Wochen wesentlich kompetenter geführt wird, und zum Anderen warum sich der Beamtenapparat in dieser Zeit stark verkleiner hat.";
                                        break;
                                    case 4:		$user = Model_User::random_names(5);
                                        $str = "Eine Expeditionsgruppe, bestehend aus :rnd[0], :rnd[1], :rnd[2], :rnd[3] und :rnd[4], wird seit zwei Tagen vermisst. Wir hoffen noch immer, dass sie wieder auftauchen - immerhin hatten sie unsere Kettensäge und einen MarkII dabei.";
                                        break;
                                    case 5:		$user = Model_User::random_names(2);
                                        $str = "In einem rührseligen Akt aus Mitgefühl hat :rnd[0] seinen Teddybären an :rnd[1] verschenkt. :rnd[1] leidet offenbar seit Tagen an einer schlimmen Angststarre, jedenfalls wurde er bisher insgesamt 12 mal dabei gesehen, wie er sich den Vibrator sowie eine Batterie aus der Bank nahm.";
                                        break;
                                    case 6:		$str = "Gestern ist es ziemlich knapp mit der Stadtverteidigung geworden... wir mussten sogar die Alkohol- und Drogenvorräte aufbrauchen. Wenigstens stehen Stadtmauer und Wachturm jetzt... auch das Dach des Wachturms verkehrt herum draufgesetzt wurde.";
                                        break;
                                }

                                $tmp = array();
                                foreach ($user as $id => $name)
                                    $tmp[":rnd[{$id}]"] = $name;

                                $player->log()->add(new Model_Log_Types_Text(null, null, $str, $tmp));
                            })
                    )
                , 'hideout_asknews');
    }
}	