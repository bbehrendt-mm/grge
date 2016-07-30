<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Chat extends Controller {

    protected static $initialize_session = false;
    protected static $force_ajax = true;

    public function japi_w() {

        $ses = Gateway\decrypt($this->post('t'));
        if (!$ses) return null;

        list($last_update, $user, $game) = $ses;
        $last = (int)$this->post('l');

        $result = DB::select('mid','message','timestamp','sender')->from('chat')->where('room','=',$game)->where('receiver','IN',[$user,-1])->where('mid','>',$last)->execute()->as_array('mid');

        return $this->render($result);


    }
}