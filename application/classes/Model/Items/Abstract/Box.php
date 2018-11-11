<?php

abstract class Model_Items_Abstract_Box extends Model_Items_Abstract_Item implements Interface_Countable {

    protected static $config = null;
    protected static $content = 1;
    protected static $energy = 0;

    /** @var  Model_Factory_Items $item_factory */
    protected $item_factory;
    protected $remaining;
    protected $open;

    public function __construct($count = null) {
        $this->item_factory = Model_Factory_Items::read(static::$config, Globals::CurrentGameF()->config('game.config.itemset'));
        $this->remaining = $count > 0 ? $count : static::$content;
        $this->open = static::$energy <= 0;
        
        parent::__construct(null);
    }
    
    public function count() {
        return $this->remaining;
    }

    /**
     * @param $p Model_Player
     *
     * @throws Exception
     */
    protected function perform_spawn($p) {
        if ($item = $this->item_factory->nd_spawn()) {
            $this->remaining--;
            if ($this->remaining <= 0) $this->consume();

            /** @var Model_Player $p */
            $p->location()->inventory()->add($item);
            $p->location()->log()->add(new Model_Log_Types_Item(1, $item, $p->id()));
        }
    }

    protected function hid() {
        $hid = parent::hid();
        
        if ($this->open)
            $hid->add_action('Item entnehmen', Model_Action::factory()
                ->allow_auto(false)
                ->deny_for(Interface_Plentity::IC_NPC_ANIMAL)
                ->show_as(Model_Effect::factory()->ambiguous_effect())
                ->effect(
                    Model_Effect::factory()
                        ->message('Du hast einen Gegenstand erhalten.')
                        ->custom(function ($p) {
                            $this->perform_spawn($p);
                        })
                )
            );
        else $hid->add_action(static::$energy > 10 ? 'Aufbrechen' : 'Öffnen', Model_Action::factory()
            ->allow_auto(false)
            ->deny_for(Interface_Plentity::IC_NPC_ANIMAL)
            ->show_as(Model_Effect::factory()->ambiguous_effect())
            ->requirement(Model_Status::MS_STAT_ENERGY, static::$energy)
            ->effect(
                Model_Effect::factory()
                    ->message('Geschafft! Jetzt kannst du einen Blick hinein werfen...')
                    ->custom(function () {
                        $this->open = true;
                    })
            )
        );

        return $hid;
    }

}