<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User extends Model {

	protected $set;
    protected $sid;

	public function __sleep() {
		return array('set', 'sid');
	}
	
	public function __wakeup() {
		//Rebind global user variable
		global $user;
		$user = $this;
	}
	
	public function __construct($session_id) {
		//Bind global user variable
		global $user;
		$user = $this;	
		
		//Save session ID
		$this->sid = $session_id;	
	}
	
	public function valid() {
		$ret = DB::select('session')->from('users')->where('uid', '=', $this->set['uid'])->execute()->get('session',null);
		return ($ret && ($ret == $this->sid));
	}
	
	public static function name_by_id($uid) {
		$set = DB::select('name')->from('users')->where('uid', '=', $uid)->execute()->as_array();
		if (!isset($set[0])) return NULL;
		return ($set[0]['name']);
	}

    public static function avatar_by_id($uid) {
        $set = DB::select('avatar')->from('users')->where('uid', '=', $uid)->execute()->as_array();
        if (!isset($set[0])) return NULL;
        return ($set[0]['avatar']);
    }
	
	public function read($uid) {
		//Load from DB	
		$set = DB::select()->from('users')->where('uid', '=', $uid)->execute()->as_array();
		
		//If request was successfull, import data from DB into local set
		if ($set && $set[0])
		{
			$this->set = $set[0];

			//Write session ID to DB
			DB::update('users')->set(array('session' => $this->sid))->where('uid', '=', $uid)->execute();
			
			return true;
		} else return false;
	}
	
	/**
	 * Returns mentor id; NULL if no mentor is set, -1 if mentoring is disabled for this user
	 * @return NULL|number
	 */
	public function get_mentor_id() {
		if (!$this->set) return null;
		
		//Load from DB
		$data = DB::select("mentor")->from('mentor')->where('uid', '=', $this->set['uid'])->execute()->as_array();
		
		//If request was successfull, return mentor id
		if ($data && $data[0])
			return $data[0]["mentor"];
		else return null;
	}
	
	/**
	 * Returns apprentice ids
	 * @return mixed
	 */
	public function get_apprentice_id() {
		if (!$this->set) return null;
	
		//Load from DB
		$data = DB::select("uid")->from('mentor')->where('mentor', '=', $this->set['uid'])->execute()->as_array();
	
		$ret = Array();
		foreach ($data as $entry) $ret[] = $entry["uid"];
		
		return $ret;
	}
	
	/**
	 * Sets mentor id for this user
	 * @param number $mentor_id
	 * @return boolean
	 */
	public function set_mentor_id($mentor_id) {
		if (!$this->set) return false;
		if ($this->get_mentor_id() !== null) return false;

		if ($this->soulpoints($mentor_id) <= max(99, $this->soulpoints())) return false;

		$chain = $this->get_apprentice_id();

        $list = Array();
		if (count($chain) > 0) {
			$data = DB::select()->from('mentor')->execute()->as_array();

			foreach ($data as $entry) {
				
				if (!isset($list[(int)$entry['uid']])) $list[(int)$entry['uid']] = Array();
				if (!isset($list[(int)$entry['mentor']])) $list[(int)$entry['mentor']] = Array();
				
				$list[(int)$entry['mentor']][] = (int)$entry['uid'];
			}
		}
		
		while (count($chain) > 0) {
			//Check chaining
			if (in_array($mentor_id, $chain)) return false;
			$new_chain = Array();
			foreach ($chain as $user_id) $new_chain = array_merge($new_chain, $list[$user_id]);
			
			$chain = $new_chain;
		}
		
		return DB::insert('mentor', array('uid', 'mentor'))->values(array($this->set['uid'], $mentor_id))->execute();
	}
	
	public function get_current_game() {
		//Check if there is a gameid associated with user id	
		$ret = DB::select('gameid')->from('xref_game_player')->where('uid', '=', $this->set['uid'])->execute()->as_array();

		//Return associated gameid, or false if no game data was found
		if ($ret && $ret[0]) return $ret[0]['gameid'];
		else return false;
	}
	
	public function name() {
		//Get username
		return $this->set['name'];
	}
	
	public function uid() {
		//Get user id
		return $this->set['uid'];
	}
	
	public static function random_names($num) {
		$rq = DB::select( 'name')->from('users')->execute()->as_array();
		$ret = Array();
		while (count($ret) < $num && count($rq) > 0) {
			$key = mt_rand(0, count($rq) - 1);
			$ret[] = $rq[$key]['name'];
			unset($rq[$key]);
			$rq = array_values($rq);
		}
		
		return $ret;
	}

    public function coins($uid = NULL) {
        $userid = ($uid === NULL) ? $this->set['uid'] : $uid;
        return static::get_coins($userid);
    }

    public static function get_coins($userid) {
        $usp = DB::select('coins')->from('users')->where('uid', '=', $userid)->execute()->as_array();
        return (int)$usp[0]['coins'];
    }

    public static function award_coins($userid, $points) {
        return DB::update('users')->set(array('coins' => static::get_coins($userid) + $points))->where('uid', '=', $userid)->execute();
    }

    public static function remove_coins($uid, $coins) {
        if (($c = static::get_coins($uid)) < $coins) return false;
        return DB::update('users')->set(array('coins' => $c - $coins))->where('uid', '=', $uid)->execute();
    }

    public static function get_soulpoints($userid, $job = NULL, $board = NULL) {
        $rq = DB::select( array(DB::expr('SUM(`points`)'), 'points'))->from('ranking')->where('uid', '=', $userid);
        if ($job !== NULL && $job !== TRUE) $rq = $rq->where('job', '=', $job);
        if ($board !== NULL) $rq = $rq->where('board', '=', $board);
        if ($job === TRUE) $rq = $rq->select('job')->group_by('job');

        $sp = $rq->execute()->as_array();
        if ($job !== TRUE) $sp = (int)$sp[0]['points'];

        return $sp;
    }

	public function soulpoints($uid = NULL, $job = NULL, $board = NULL) {
		$userid = ($uid === NULL) ? $this->set['uid'] : $uid;
		return static::get_soulpoints($userid, $job, $board);
	}
	
	public function contest_points() {
		//Check if there is a gameid associated with user id
		$ret = DB::select('points')->from('contests')->where('user_id', '=', $this->set['uid'])->execute()->as_array();
		
		//Return associated gameid, or false if no game data was found
		if ($ret && $ret[0]) return (int)$ret[0]['points'];
		else return false;
	}

    public static function group_soulpoints($sp) {
        $a = array(
            0       =>  'Unbefleckte Seele',
            10      =>  'Frische Seele',
            100     =>  'Anfängerseele',
            400     =>  'Routinierte Seele',
            1000    =>  'Expertenseele',
            2500    =>  'Profiseele',
            5000    =>  'Meisterseele',
            10000   =>  'Großmeisterseele',
            20000   =>  'Epische Seele',
            50000   =>  'Titanenseele',
            100000  =>  'Gottesseele'
        );

        $t = null;

        foreach ($a as $points => $name) {
            if ($sp < $points)
                return array($t, $points);
            $t = $name;
        }

        return array($t, $sp);
    }

    public static function group_karma($k) {
        if ($k < -100)      return 'Das personifizierte Böse';
        elseif ($k < -50)   return 'George Bush';
        elseif ($k < -25)   return 'Banker';
        elseif ($k < -15)   return 'Krimineller';
        elseif ($k < -10)   return 'Fiesling';
        elseif ($k < -5)    return 'Störenfried';
        elseif ($k < 0)     return 'Aussenseiter';
        elseif ($k == 0)    return 'Neutral';
        elseif ($k < 5)     return 'Schleimer';
        elseif ($k < 10)    return 'Lächler';
        elseif ($k < 15)    return 'Gentleman';
        elseif ($k < 25)    return 'Samariter';
        elseif ($k < 50)    return 'Guter Freund';
        elseif ($k < 100)   return 'Altruist';
        else                return 'Mutter Theresa';
    }

    public static function get_karma($user, $rater = null) {
        if ($rater !== null) {
            $k = DB::select('value')->from('karma')->where('subject', '=', $user)->where('rater', '=', $rater)->execute()->as_array();
            return $k ? $k[0]['value'] : 0;
        }

        $k_0 = DB::select(array(DB::expr('SUM(`value`)'), 'value'))->from('karma')->where('subject', '=', $user)->execute()->as_array();
        $k_1 = DB::select(array(DB::expr('SUM(`value`)'), 'value'))->from('karma')->where('subject', '=', $user)->where('timestamp', '>', strtotime('-1 month'))->execute()->as_array();

        $k_0 = $k_0 ? $k_0[0]['value'] : 0;
        $k_1 = $k_1 ? $k_1[0]['value'] : 0;
        return ($k_0 + $k_1)/2;
    }

    public static function set_karma($user, $rater, $value) {
        if ($user == $rater) return;
        $value = min(2, max(-2,$value));

        DB::delete('karma')->where('subject', '=', $user)->where('rater', '=', $rater)->execute();
        DB::insert('karma', array('subject','rater','value','timestamp'))->values(array($user, $rater, $value, time()))->execute();
    }
}
