<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User extends Model {

	protected $set;
    protected $sid;
	
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
        $avatar = $set[0]['avatar'];
        if (substr($avatar,0,5) == 'http:') $avatar = substr($avatar,5);
        return $avatar;
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
     * Returns mentor id; NULL if no mentor is set, false if mentoring is disabled for this user
     *
     * @param $uid
     *
     * @return bool|NULL|number
     * @throws Kohana_Exception
     */
	public static function mentor_id($uid) {
		//Load from DB
		$data = DB::select("mentor")->from('mentor')->where('uid', '=', $uid)->execute()->get('mentor');

		//If request was successfull, return mentor id
		return ($data && $data > 0) ? $data : ((($data !== null && $data < 0) || Model_Euser::get_soulpoints($uid) >= (int)Kohana::$config->load('balancing.mentor.sp_threshold')) ? false : null);
	}

	public function get_mentor_id() {
		if (!$this->set) return null;

		return static::mentor_id($this->set['uid']);
	}

	/**
	 * Returns apprentice ids
	 * @param $uid
	 * @return mixed
	 */
	public static function apprentice_id($uid) {
		if (!$uid || $uid <= 0) return [];

		//Load from DB
		return DB::select("uid")->from('mentor')->where('mentor', '=', $uid)->execute()->as_array(null, 'uid');
	}

	/**
	 * Returns apprentice ids
	 * @return mixed
	 */
	public function get_apprentice_id() {
		if (!$this->set) return null;
	
		//Load from DB
		return static::apprentice_id($this->set['uid']);
	}

	public static function unset_mentor($pupil_id) {
		return DB::update('mentor')->set(['mentor' => -1])->where('uid','=',$pupil_id)->execute();
	}

	public static function check_mentor($uid, $mentor_id) {
		$lg_mentor_id = static::mentor_id($uid);

		if ($mentor_id === true)
			return !($lg_mentor_id || $lg_mentor_id === false || static::mentor_id($uid) === false || static::get_soulpoints($uid) >= min((int)Kohana::$config->load('balancing.mentor.sp_threshold')));
		if ($uid == $mentor_id || $lg_mentor_id || $lg_mentor_id === false || static::get_soulpoints($uid) >= min((int)Kohana::$config->load('balancing.mentor.sp_threshold'),static::get_soulpoints($mentor_id))) return false;

		$chain = static::apprentice_id($uid);

		$list = [];
		if (count($chain) > 0) {
			$data = DB::select()->from('mentor')->execute()->as_array();

			foreach ($data as $entry) {

				if (!isset($list[(int)$entry['uid']])) $list[(int)$entry['uid']] = [];
				if (!isset($list[(int)$entry['mentor']])) $list[(int)$entry['mentor']] = [];

				$list[(int)$entry['mentor']][] = (int)$entry['uid'];
			}
		}

		while (count($chain) > 0) {
			//Check chaining
			if (in_array($mentor_id, $chain)) return false;
			$new_chain = [];
			foreach ($chain as $user_id) $new_chain = array_merge($new_chain, $list[$user_id]);

			$chain = $new_chain;
		}

		return true;
	}

	public static function get_mentoring_ref($uid) {
		$name = strtoupper(substr(static::name_by_id($uid), 0, 3));

		$nid = array_search($uid, DB::select('uid')->from('users')->where('name','LIKE',"{$name}%")->order_by('uid', 'ASC')->execute()->as_array(null, 'uid'));
		if ($nid === false) return null;

		while (strlen($nid . '') < 3)
			$nid = '0' . $nid;

		return $name . $nid;
	}

	public static function get_uid_from_mentoring_ref($mref) {
		preg_match_all('/^(.{1,3})(\d\d\d)$/',$mref, $a, PREG_SET_ORDER);

		if (!$a || count($a[0]) != 3) return null;

		list(list(,$name, $id)) = $a;
		if (!is_numeric($id) || $id < 0) return null;

		$d = DB::select('uid')->from('users')->where('name','LIKE',"{$name}%")->order_by('uid', 'ASC')->execute()->as_array(null, 'uid');

		if (!isset($d[(int)$id])) return null;
		else return (int)$d[(int)$id];
	}

	public static function get_mentor_braincoins($uid, $pupil = null, $harvest_state = null) {
		$pupils = static::apprentice_id($uid);
		if (!$pupils || ($pupil !== null && !in_array($pupil, $pupils))) return 0;

		$factor = Kohana::$config->load('balancing.mentor.sp_bc_factor');
		$query = DB::select([DB::expr("SUM(FLOOR(points * $factor))"), 'bc'])->from('ranking');

		if ($pupil !== null) $query->where('uid','=',$pupil);
		else $query->where('uid','IN',$pupils);

		if ($harvest_state !== null) $query->where('harvested','=', $harvest_state ? 1 : 0);

		return (int)$query->execute()->get('bc', 0);
	}

	public static function reset_mentor_braincoins($uid, $pupil = null) {
		$pupils = static::apprentice_id($uid);
		if (!$pupils || ($pupil !== null && !in_array($pupil, $pupils))) false;

		$query = DB::update('ranking')->set(['harvested' => true]);

		if ($pupil !== null) $query->where('uid','=',$pupil);
		else $query->where('uid','IN',$pupils);

		return (bool)$query->execute();
    }

    /**
     * Sets mentor id for this user
     * @param number $mentor_id
     * @return boolean
     * @throws Kohana_Exception
*/
	public function set_mentor_id($mentor_id) {
		if ($mentor_id != -1 && !static::check_mentor($this->set['uid'], $mentor_id)) return false;
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
			$key = random_int(0, count($rq) - 1);
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
