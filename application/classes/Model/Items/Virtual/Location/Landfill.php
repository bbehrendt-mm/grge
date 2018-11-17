<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Landfill extends Model_Items_Abstract_Virtual {

    protected static $graceful_fail = true;
    private $spawn_twinoid = false;

    protected static $default_action_uses = array(
        'count' => PHP_INT_MAX,
        'dump' => PHP_INT_MAX,
        'press' => PHP_INT_MAX,
    );

    protected function hid(): Model_Hid {
        $tar = floor(Globals::CurrentPlayerF()->location()->splinters()/10);
        return parent::hid()->add_action('Splitter zählen', Model_Action::factory()
            ->buttonskin('location')
            ->effect(Model_Effect::factory()->custom(function($p) {
                    /** @var Model_Player $p */
                    $p->log()->add(new Model_Log_Types_String(null,'Hier lagern momentan :num Eimer voller Splitter, aus denen du :num2 Splitterkugeln formen könntest.', array(':num' => $p->location()->splinters(), ':num2' => floor($p->location()->splinters()/10))));
                }))
            ,'count')
            ->add_action('Schredder verwenden', Model_Action::factory()
                ->buttonskin('location')
                ->description('Mit dieser Aktion kannst du alle Gegenstände, die im Moment auf dem Boden liegen, zerstören um Splitter herzustellen.')
                ->requirement(Model_Status::MS_STAT_ENERGY, 5)
                ->condition(function($p) {
                    /** @var Model_Player $p */
                    $g = 0;
                        foreach ($p->location()->inventory()->get() as $item) if (!$item->is_essential())
                        $g += $item->weight();
                    return ($g > 0);
                })
                ->fail_message('Hier gibt es nichts, was du schreddern könntest...')
                ->effect(Model_Effect::factory()
                    ->custom(function($p) {
                        /** @var Model_Player $p */
                        $g = 0;
                        foreach ($p->location()->inventory()->get() as $item) if (!$item->is_essential())
                        {
                            $p->location()->splinters($item->weight());
                            $g += $item->weight();
                            $item->grind();
                        }

                        $p->achievements()->achieve(Model_Achievement::MA_GARBAGE_GUY, $g);
                        if ($g == 1) $p->log()->add(new Model_Log_Types_String(null, 'Eigentlich ist es ja Energieverschwendung, den Schredder für dieses bisschen Müll anzuwerfen... Aber hey, immerhin hast du einen Eimer mit Splittern gefüllt!'));
                        else $p->log()->add(new Model_Log_Types_String(null, 'Mit unbarmherziger Macht zerstört der Schredder jeden Gegenstand, den du in seinen Schlot wirfst. Am Ende hast du damit :num Eimer mit Splittern gefüllt!', array(':num' => $g)));

                        return true;
                    })
                )
            , 'dump')
            ->add_action('Splitterkugeln herstellen', Model_Action::factory()
                ->buttonskin('location')
                ->description('Aus 10 Eimern mit Splittern kannst du eine Splitterkugel pressen, die du als Munition verwenden kannst.')
                ->requirement(Model_Status::MS_STAT_ENERGY, 50)
                ->condition(function($p) {
                    /** @var Model_Player $p */
                    return ($p->location()->splinters() > 10);
                })
                ->fail_message('Du brauchst mehr Splitter, um eine solide Splitterkugel zu bauen.')
                ->effect(Model_Effect::factory()
                    ->spawn(new Model_Items_Splinter($tar))
                    ->message('Du setzt deine ganze Kraft ein, um die Splitter in der Presse bestmöglich zu komprimieren. Als Belohnung für deine Leistung hälst du nun :num neue Splitterkugeln in der Hand.', array(':num' => $tar))
                    ->custom(function($p) {
                        /** @var Model_Player $p */
                        $p->location()->splinters(floor($p->location()->splinters()/10) * -10);
                        return true;
                    })
                )
            , 'press');
    }
}	