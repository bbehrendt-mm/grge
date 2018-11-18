<?php defined('SYSPATH') OR die('No direct access allowed.');

class Tool_Numerics {

	//Makes sure, value is between min and max
	public static function bounds(&$value, $min, $max): void
    {
		if ($min <= $max) $value = min($max, max($min, $value));
	}
	
	//Takes 2 values and a list of steps, and returns the index of the last step those values cross
	public static function cross($value_before, $value_after, $steps) {
		//Swap values if they are in the wrong order
		$mod = 1;
		if ($value_after>$value_before) {
			$mod = -1;
			self::swap($value_after, $value_before);
		//Return 0 if both values are identical
		} elseif ($value_after === $value_before) return 0;
		$ret = 0;

		//Iterate over all steps and memorize the last crossed step
        foreach ($steps as $i => $iValue)
			if ($value_before >= $iValue && $value_after < $iValue) $ret = $i+1;

        //Return step ID; negate if values needed to be swapped
		return $mod*$ret;
	}
	
	//Swaps the contends of both given variables
	public static function swap(&$a, &$b): void
    {
		$t = $a;
		$a = $b;
		$b = $t;
	}
	
	//Calculates the points for a game based on its duration (in ticks)
	public static function duration_to_points( $duration ) {
		$days = $duration/144;
		return ($days < 1) ? 0 : ceil(($days*($days-1))/2);
	}
	
	//Converts a duration (in ticks) into a readable string
	public static function duration_to_string( $duration ): string
    {
		$letters = __('WTHM');

        if ($duration <= 0) return '0' . $letters[3];
		
		$ret = array();
			
		$weeks = floor($duration/2016); 
		if ($weeks) $ret[] = $weeks . $letters[0];
		$duration -= $weeks*2016;
		
		$days = floor($duration/288); 
		if ($days) $ret[] = $days . $letters[1];
		$duration -= $days*288;
		
		$hours = floor($duration/12); 
		if ($hours) $ret[] = $hours . $letters[2];
		$duration -= $hours*12;
		
		$minutes = $duration * 5;
		if ($minutes) $ret[] = $minutes . $letters[3];
		
		return implode(' ', $ret);
	}
	
	//Converts a duration (in ticks) into a readable string
	public static function duration_to_split( $duration ): array
    {
		$ret = array();
			
		$weeks = floor($duration/2016);
		$ret[3] = $weeks;
		$duration -= $weeks*2016;
	
		$days = floor($duration/288);
		$ret[2] = $days;
		$duration -= $days*288;
	
		$hours = floor($duration/12);
		$ret[1] = $hours;
		$duration -= $hours*12;
	
		$minutes = $duration * 5;
		$ret[0] = $minutes;
	
		return $ret;
	}
		
}	