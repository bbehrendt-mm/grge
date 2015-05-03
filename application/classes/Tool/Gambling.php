<?php defined('SYSPATH') OR die('No direct access allowed.');

class Tool_Gambling {

    /**
     * Randomizer, expects data to be in the following format: [['chance'=>int, 'value'=>mixed],[...]]
     * @param mixed[] $data
     * @return mixed
     */
    public static function roulette( $data ) {
        $range = 0;
        //Iterate over all elements and calculate length of roulette wheel
        foreach ($data as $elem) $range+=$elem['chance'];

        //Prevent exception for empty dataset
        if ($range < 1)
            return null;

        //Get a mt_random number (we're using super advanced mt space technologie!) and reset range
        $rval = mt_rand(1,$range);
        $range = 0;

        //Rien ne va plus
        foreach ($data as $elem) if (($range+=$elem['chance']) >= $rval) return $elem['value'];
        return $data[count($data)-1]['value'];
	}

    /**
     * Returns a random element from the given array. If the given parameter is not an array, or is empty, null will be returned
     * @param array $array The array
     * @return mixed|null
     */
    public static function select(array $array) {
        if (!is_array($array) || count($array) == 0)
            return null;
        return array_values($array)[mt_rand(0,count($array) - 1)];
    }
		
}	