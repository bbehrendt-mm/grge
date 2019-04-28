<?php defined('SYSPATH') OR die('No direct access allowed.');

class Tool_Gambling {

    /**
     * Randomizer, expects data to be in the following format: [['chance'=>int, 'value'=>mixed],[...]]
     *
     * @param mixed[] $data
     *
     * @return mixed
     * @throws Exception
     */
    public static function roulette( $data ) {
        $range = 0;
        //Iterate over all elements and calculate length of roulette wheel
        foreach ($data as $elem) $range+=$elem['chance'];

        //Prevent exception for empty dataset
        if ($range < 1)
            return null;

        //Get a mt_random number (we're using super advanced mt space technologie!) and reset range
        $rval = random_int(1,$range);
        $range = 0;

        //Rien ne va plus
        foreach ($data as $elem) if (($range+=$elem['chance']) >= $rval) return $elem['value'];
        return $data[count($data)-1]['value'];
	}

    /**
     * Returns a random element from the given array. If the given parameter is not an array, or is empty, null will be returned
     *
     * @param array $array The array
     *
     * @return mixed|null
     * @throws Exception
     */
    public static function select(array $array) {
        if (!is_array($array) || count($array) === 0)
            return null;
        return array_values($array)[random_int(0,count($array) - 1)];
    }

    public static function random(float $chance): bool
    {
        if ($chance >= 1) return true;
        return $chance <= 0 ? false : (mt_rand()/mt_getrandmax() < $chance);
    }

    /**
     * @param Interface_Plentity $p
     *
     * @return bool
     * @throws Exception
     */
    public static function tumble($p): bool
    {
        return (random_int(15, 100) <= $p->get_status()->get(Model_Status::MS_STAT_DRUNK));
    }

    /**
     * @param int      $min
     * @param int      $max
     * @param callable $func
     *
     * @return int
     * @throws Exception
     */
    public static function repeat($min, $max, callable $func): int
    {
        if ($min > $max || $max <= 0) return 0;
        $count = random_int($min,$max);
        for ($i = 0; $i < $count; $i++) $func();
        return $count;
    }
		
}	