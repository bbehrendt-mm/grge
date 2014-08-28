<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Factory_Items extends Model {

    private $config = 'default';
	private $type;
	private $found;
	private $drying;
	private $bias = Array();
		
	public function __construct($assoc, $drying = 1, $config = 'default') {
		$this->type = $assoc;
		$this->drying = $drying;
		$this->found = 0;
        $this->config = $config;
	}
	
	private function traverse($data) {
		if (!is_array($data) || count($data) == 0) return null;
		
		if (count($data) == 1) {
			$tmp = array_keys($data);
			$next = $tmp[0];
		} else {
			$tmp = Array();
			foreach ($data as $key => $chance) $tmp[] = Array("value" => $key, "chance" => $chance);
			$next = Tool_Gambling::roulette($tmp);
		}
		
		if (strpos($next, 'Model_Items_') === 0) {
			if (class_exists($next)) return new $next;
			else throw new Exception("Unable to spawn item: {$next}");
		} elseif (!($data = Kohana::$config->load("items/{$this->config}.groups.{$next}"))) throw new Exception("Unable to traverse to next item tree leaf: {$next}");
		else return $this->traverse($data);
	}

    public function replenish($factor = 1) {
        $this->found = round($this->found - $this->found * $factor);
    }

    public function setDryingFactors($drying = null, $found = null) {
        if ($drying !== null)
            $this->drying = 0;

        if ($found !== null)
            $this->found = 0;
    }
	
	public function spawn($force = false, $factor = 1, $chance = 1) {
		//Get Config
		if (!$config = Kohana::$config->load("items/{$this->config}.groups.{$this->type}")) {
            if (!$config = Tool_System::config_tree("items/{$this->config}.buildings", $this->type))
                throw new Exception('Failed to load item spawn configuration!');
        } else $config = array('size' => 1, 'content' => $config);

		if ($config['size'] <= 0 || $chance <= 0) return null;
		
		if (!$force)
			if (!Tool_Gambling::roulette(Array(Array("chance" => round($config['size'] * 100 * $chance), "value" => true), Array("chance" => round($this->found * 100), "value" => false))))
				return null;
		
		
		$ret = $this->traverse($config["content"]);
		if (!$ret) return null;
		else {
			if (!isset($this->bias[get_class($ret)])) $this->bias[get_class($ret)] = 0;
			if (!Tool_Gambling::roulette(Array(Array("chance" => 2, "value" => true), Array("chance" => $this->bias[get_class($ret)], "value" => false))))
				$ret = $this->traverse($config["content"]);
			
			$this->found += ($this->drying * $factor);
			
			if (!isset($this->bias[get_class($ret)])) $this->bias[get_class($ret)] = 0;
			$this->bias[get_class($ret)]++;
			
			return $ret;
		}
	}

}
