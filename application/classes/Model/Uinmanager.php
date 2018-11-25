<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Uinmanager extends Model {
	
	private $game_id = null;
	private $data = Array();
	private $prefetch = Array();
	private $cleanup = Array();
    private $reserved = Array();
    private $readonly = false;

	public function __construct($game_id = null) {
		if (Kohana::$config->load('server.io.performance.use_cloud')) $this->game_id = $game_id;
	}

    /**
     * Activates read only mode
     */
    public function set_read_only(): void
    {
        $this->readonly = true;
    }

    /**
     * Writes back to DB
     *
     * @return string[]
     * @throws Kohana_Exception
     */
	public function __sleep() {
		if (!$this->readonly && $this->game_id)	{
			foreach ($this->data as $uin => $entry) {
				$data = serialize($entry['obj']);
				if (md5($data) !== $entry['hash']) {
                    $compressed = $data ? gzcompress($data, Kohana::$config->load('server.io.performance.compression_level')) : null;
                    if ($compressed)
                        DB::update('games_cloud')->set(array('data' => $compressed))->where('gameid', '=', $this->game_id)->and_where('uin', '=', $uin)->execute();
                }
			}
			
			if (count($this->cleanup) > 0)
				DB::delete('games_cloud')->where('gameid', '=', $this->game_id)->and_where('uin', 'IN', array_keys($this->cleanup))->execute();
		}

		return ($this->game_id && !$this->readonly) ? Array('game_id',
                                                            'reserved') : Array('data',
                                                                                'game_id',
                                                                                'reserved');
	}

    /**
     * Adds an object to cache
     * @param Interface_Cloudshard $obj
     * @param bool $no_hash Create a hash for the new object
     */
	private function cache_set(Interface_Cloudshard &$obj = null, $no_hash = false): void
    {
        if (!$obj) return;
        if (isset($this->cleanup[$obj->uin()])) return;
        if (isset($this->reserved[$obj->uin()])) unset($this->reserved[$obj->uin()]);
		$this->data[$obj->uin()] = Array('obj' => $obj, 'hash' => $no_hash ? ''
            : md5(serialize($obj)));
	}

    /**
     * Reserves a cloud slot in cache without adding an actual object
     * @param $id
     */
    private function cache_reserve($id): void
    {
        if (isset($this->data[$id]) || isset($this->cleanup[$id])) return;
        $this->data[$id] = Array('obj' => null, 'hash' => '');
        $this->reserved[$id] = true;
    }
	
	/**
	 * Prefetches a list of objects
	 * @param int[] $list
	 */
	public function prefetch($list): void
    {
		if (!$this->game_id) return;
		foreach ($list as $entry) if (!isset($this->data[$entry]) && !isset($this->cleanup[$entry])) $this->prefetch[$entry] = true;
	}
	
	/**
	 * Loads all prefetch data from DB
	 */
	private function fetch(): void
    {
		if (!$this->game_id) return;
		$list = array_diff(array_keys($this->prefetch), array_keys($this->cleanup));
		if (count($list) === 0) return;
		elseif (count($list) === 1)	$set = DB::select('data')->from('games_cloud')->where('gameid', '=', $this->game_id)->and_where('uin', '=', $list[0])	->execute()->as_array();
		else						$set = DB::select('data')->from('games_cloud')->where('gameid', '=', $this->game_id)->and_where('uin', 'IN', $list)		->execute()->as_array();
		
		foreach ($set as $obj) {
			try {
                $object = unserialize(gzuncompress($obj['data']), ['allowed_classes' => ['Interface_Cloudshard']]);
                $this->cache_set($object);
            } catch (Exception $e) {
                continue;
            }

		}
		$this->prefetch = Array();
	}
	
	/**
	 * Passes an object if it matches expected_class (if set)
	 * @param Interface_Cloudshard $object
	 * @param string $expected_class
	 * @param boolean $heritage
     * @return Interface_Cloudshard|null
     */
	private function passthrough($object, $expected_class = NULL, $heritage = true): ?\Interface_Cloudshard
    {
		//Check if it matches expected class
		if ($expected_class === NULL) return $object;
		elseif (($heritage && ($object instanceof $expected_class))
            || get_class($object) === $expected_class
            || in_array($expected_class, class_implements($object), true)
        ) return $object;
		else return NULL;
	}
	
	/**
	 * 
	 * @param number $uin
	 * @param string $expected_class
	 * @param boolean $heritage
	 * @return null|Interface_Cloudshard
	 */
	public function get($uin, $expected_class = NULL, $heritage = true): ?\Interface_Cloudshard
    {
		if (isset($this->data[$uin])) return $this->passthrough($this->data[$uin]['obj'], $expected_class, $heritage);

		$this->prefetch(Array($uin));
		$this->fetch();
		
		if (isset($this->data[$uin])) return $this->passthrough($this->data[$uin]['obj'], $expected_class, $heritage);

		return null;
    }

    /**
     * Reserves a cloud slot
     * @return int
     * @throws Kohana_Exception
     */
    public function reserve(): int
    {
        if ($this->readonly) return null;

        if ($this->game_id) {
            $target_uin = DB::insert('games_cloud', array('gameid', 'data'))->values(array($this->game_id, ''))->execute();
           $ret = $target_uin[0];
        } else $ret = $target_uin = count($this->data);

        $this->cache_reserve($ret);
        return $ret;
    }

    /**
     * Adds an object to cloud by filling a previously reserved slot
     * @param number $id Reserved slot ID
     * @param Interface_Cloudshard $data
     * @throws Exception
     * @return int
     */
    public function fill_reservation($id, Interface_Cloudshard $data): int {
        if (!$data) throw new LogicException(
            'Cannot register NULL objects in game cloud!', 1);

        if (!isset($this->reserved[$id])) throw new LogicException("Cloud ID $id is not reserved!", 1);

        $data->uin($id);
        $this->cache_set($data, true);

        return $id;
    }
	
	/**
	 * Adds an object to cloud
	 * @param Interface_Cloudshard $data
	 * @throws Exception
	 * @return number
	 */
	public function set(Interface_Cloudshard &$data) {
		if (!$data) throw new RuntimeException(
            'Cannot register NULL objects in game cloud!', 1);
		
		$target_uin = $this->reserve();
		$data->uin($target_uin);
		$this->cache_set($data, true);
		
		return $target_uin;
	}
	
	/**
	 * Removes an object from cloud
	 * @param number|Interface_Cloudshard $obj
	 */
	public function remove($obj): void
    {
		$num = is_numeric($obj) ? $obj : $obj->uin();
		$this->cleanup[$num] = true;
		unset($this->data[$num]);
	}
	
	/**
	 * Removes ALL cloud objects
	 */
	public function clean(): void
    {
        if ($this->readonly) return;
        if ($this->game_id) DB::delete('games_cloud')->where('gameid', '=', $this->game_id)->execute();
		$this->data = Array();
		$this->prefetch = Array();
		$this->cleanup = Array();
	}
}
