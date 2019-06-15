<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Location_Landfill extends Model_Items_Abstract_Virtual {

    protected static $graceful_fail = true;

    private $splinter_load = 0;

    protected static $default_action_uses = array(
        'count' => PHP_INT_MAX,
        'dump' => PHP_INT_MAX,
        'press' => PHP_INT_MAX,
    );

    protected function hid(): Model_Hid {
        $tar = floor($this->splinter_load/10.0);
        $w_factor = 1.333;

        return parent::hid()->add_action('Splitter zählen', Model_Action::factory()
            ->buttonskin('location')
            ->effect(Model_Effect::factory()->custom(function(Model_Player $p) use ($tar) {
                    $location = Globals::CurrentPlayerF()->location();
                    /** @var $location Model_Places_Junkyard */
                    $p->log()->add(new Model_Log_Types_String(null,'Hier lagern momentan :num Eimer voller Splitter, aus denen du :num2 Splitterkugeln formen könntest.', array(':num' => $this->splinter_load, ':num2' => floor($tar))));
                }))
            ,'count')
            ->add_action('Schredder verwenden', Model_Action::factory()
                ->buttonskin('location')
                ->description('Mit dieser Aktion kannst du alle Gegenstände, die im Moment auf dem Boden liegen, zerstören um Splitter herzustellen.')
                ->requirement(Model_Status::MS_STAT_ENERGY, 5)
                ->condition(function($p) use ($w_factor) {
                    /** @var Model_Player $p */
                    $g = 0;
                        foreach ($p->location()->inventory()->get() as $item) if (!$item->is_essential())
                        $g += $item->weight() * $w_factor;
                    return ($g >= 1);
                })
                ->fail_message('Hier gibt es nichts, was du schreddern könntest...')
                ->effect(Model_Effect::factory()
                    ->custom(function($p) use ($w_factor) {
                        /** @var Model_Player $p */
                        $g = 0;
                        foreach ($p->location()->inventory()->get() as $item) if (!$item->is_essential())
                        {
                            $g += $item->weight() * $w_factor;
                            $item->grind();
                        }
                        $g = floor($g);
                        $this->splinter_load += $g;

                        $p->achievements()->achieve(Model_Achievement::MA_GARBAGE_GUY, $g);
                        if ($g === 1) $p->log()->add(new Model_Log_Types_String(null, 'Eigentlich ist es ja Energieverschwendung, den Schredder für dieses bisschen Müll anzuwerfen... Aber hey, immerhin hast du einen Eimer mit Splittern gefüllt!'));
                        else $p->log()->add(new Model_Log_Types_String(null, 'Mit unbarmherziger Macht zerstört der Schredder jeden Gegenstand, den du in seinen Schlot wirfst. Am Ende hast du damit :num Eimer mit Splittern gefüllt!', array(':num' => $g)));

                        return true;
                    })
                )
            , 'dump')
            ->add_action('Splitterkugeln herstellen', Model_Action::factory()
                ->buttonskin('location')
                ->description('Aus 10 Eimern mit Splittern kannst du eine Splitterkugel pressen, die du als Munition verwenden kannst.')
                ->requirement(Model_Status::MS_STAT_ENERGY, 50)
                ->condition(function(Interface_Plentity $p) {
                    return ($this->splinter_load > 10);
                })
                ->fail_message('Du brauchst mehr Splitter, um eine solide Splitterkugel zu bauen.')
                ->effect(Model_Effect::factory()
                    ->spawn(new Model_Items_Splinter($tar))
                    ->message('Du setzt deine ganze Kraft ein, um die Splitter in der Presse bestmöglich zu komprimieren. Als Belohnung für deine Leistung hälst du nun :num neue Splitterkugeln in der Hand.', array(':num' => $tar))
                    ->custom(function(Interface_Plentity $p) use ($tar) {
                        $this->splinter_load -= $tar * 10;
                        return true;
                    })
                )
            , 'press');
    }
}	