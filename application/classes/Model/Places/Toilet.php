<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Toilet extends Model_Places_Abstract_Place
{

    protected static $location_name = 'Öffentliche Toiletten';
    protected static $description = 'Diese öffentlichen Toiletten sind im Prinzip das Hilton jedes Penners. Für andere Menschen sind diese Toiletten eher wie Paris Hilton: Ziemlich schmutzig, übler Geruch und die halbe Welt war schonmal drin.';
    protected static $icon = 'toilet';
    protected static $outside = false;

    public function tick($type = Interface_Tickable::IT_TYPE_PLAYER): bool
    {
        if (Globals::CurrentPlayerF()->can(
                Interface_Plentity::IC_TRIGGER_SUPPLIES
            )
            && Globals::CurrentGameF()->config('places.toilet.spawn_sherri')
            && !Globals::CurrentGameF()->get_npc('sherri')
        ) {
            $sherri = new Model_NPC_Special_Sherri();
            $sherri->location_class($this->uin());
            Globals::CurrentGameF()->add_npc($sherri, 'sherri');
            $this->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER, $sherri->id(), true));
        }

        return parent::tick($type);
    }
}