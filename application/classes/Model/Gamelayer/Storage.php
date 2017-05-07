<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Gamelayer_Storage extends Model {

	protected $set;
	protected $check_hash = 'none';
	protected $uin_cache = array();
    protected $read_only;

    final public function id() {
        return $this->set['gameid'];
    }
	
	final public function __sleep() {		
		//Check if game is properly initialized, and update DB	
		if (!empty($this->set))
			$this->write();

		//Save sleep state
		$ret = array('set');
		if (Kohana::$config->load('server.io.performance.extended_cloud_cache')) $ret[] = 'uin_cache';
		if (!Kohana::$config->load('server.io.performance.force_main_rewrite')) $ret[] = 'check_hash';
		return $ret;
	}

	final public function __wakeup() {
		//Rebind global game variable
		if ($this->set) {
			Globals::setCurrentGame($this);
			
			//Activate current player
			if (isset($this->set['gamedata']->players[Globals::CurrentUser()->uid()]))
				$this->set['gamedata']->uin->get($this->set['gamedata']->players[Globals::CurrentUser()->uid()], 'Model_Player');
		}
		
		//Revalidate everything if required
		if (Kohana::$config->load('server.io.performance.force_revalidate')) $this->read($this->set['gameid']);
		
		//Check if game is properly initialized and process timeline
		elseif ($this->set)
			$this->process();
	}	
	
	final public function __construct($global_instance = true) {
		//Bind global game variable
		if ($global_instance)
			Globals::setCurrentGame($this);
	}

	abstract protected function process();
	abstract public function points();
    /**
     * Returns this games UIN Manager
     * @return Model_Uinmanager
     */
    abstract public function uin();
	
	final public function read($gameid, $write_access = true, $process = null) {
		$this->read_only = !$write_access;
		if ($process === null) $process = $write_access;

		//Find
        $chk = DB::select('gameid')->from('games')->where('gameid', '=', $gameid)->execute()->as_array();
        if (count($chk) != 1)
            return false;

        $counter = 0; $locked = false;
        while ($counter < 30 && !$locked) {
            $counter++;
            $locked = (DB::update('games')->set(array('lock' => time() + 4))->where('gameid', '=', $gameid)->and_where('lock', '<', time())->execute() > 0);
            if (!$locked) usleep(200000);
        }

        if (!$locked)
            throw new Exception('Unable to obtain database lock!');

		//Load from DB
		$set = DB::select()->from('games')->where('gameid', '=', $gameid)->execute()->as_array();

		//If request was successfull, import data from DB into local set
		if ($set && $set[0])
		{
			$this->set = $set[0];
			
			//Decode blob
			try {
				$this->check_hash = md5($this->set['gamedata']);
				$this->set['gamedata'] =  unserialize(gzuncompress($this->set['gamedata']));
                if ($this->read_only)
                    $this->uin()->set_read_only();

			} catch (Exception $e) {
				DB::delete('games')->where('gameid', '=', $this->set['gameid'])->execute();
				DB::delete('games_cloud')->where('gameid', '=', $this->set['gameid'])->execute();
                DB::delete('multiplayer_lobby')->where('gameid', '=', $this->set['gameid'])->execute();
				throw new Exception("Entschuldigung, das hätte nicht passieren dürfen! Dein auf dem Server gespeicherter Spielstand ist beschädigt und muss gelöscht werden. Du kannst dein Spiel nicht fortsetzen. Bitte kontaktiere einen Administrator und teile ihm folgende Fehlermeldung mit: " . $e->getMessage());
			}
			
			//Process ticks
			if ($process) $this->process();
            if ($this->read_only) DB::update('games')->set(array('lock' => 0))->where('gameid', '=', $this->set['gameid'])->execute();
			
			return true;
		} else return false;
	}
	
	final public function write() {
        if ($this->read_only)
            return;

		//Check if something changed, and update accordingly		
		$data = gzcompress(serialize($this->set['gamedata']), (int)Kohana::$config->load('server.io.performance.compression_level'));
		$hash = Kohana::$config->load('server.io.performance.force_main_rewrite') ? '' : md5($data);
		
		if (isset($this->set['gamedata']->head->contest) && $this->set['gamedata']->head->contest) 
			DB::update('contests')->set(array('points' => $this->points()))->where('game_id', '=', $this->set['gameid'])->execute();
			
		if (Kohana::$config->load('server.io.performance.force_main_rewrite') || $hash != $this->check_hash)
			DB::update('games')->set(array('timestamp' => $this->set['gamedata']->timing->last_point, 'gamedata' => $data, 'lock' => 0))->where('gameid', '=', $this->set['gameid'])->execute();
		else DB::update('games')->set(array('lock' => 0))->where('gameid', '=', $this->set['gameid'])->execute();
		
		$this->check_hash = $hash;
	}
}
