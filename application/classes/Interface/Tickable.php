<?php defined('SYSPATH') or die('No direct script access.');

interface Interface_Tickable {

    public const IT_TYPE_LOCATION = 0;
    public const IT_TYPE_PLAYER = 1;
    public const IT_TYPE_NPC = 2;

    public function tick($id, $type = Interface_Tickable::IT_TYPE_PLAYER): void;

}