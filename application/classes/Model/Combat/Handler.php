<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Handler {

    /**
     * @param number $game_id
     * @param number $season
     * @param Model_Combat_Field $battle
     * @param bool|false $fixed
     * @return int
     * @throws Kohana_Exception
     */
    public static function upload($game_id, $season, $battle, $fixed = false) {
        return DB::insert('battle', ['gameid','season','fixed','data'])->values([$game_id, $season, $fixed, gzcompress(serialize($battle->get_scene()->export()), (int)Kohana::$config->load('server.io.performance.compression_level'))])->execute()[0];
    }

    private static function by_game($gameid, $season) {
        return DB::select('bid')->from('battle')->where('gameid','=',$gameid)->where('season','=',$season)->execute()->as_array(null, 'bid');
    }

    private static function can_delete(array $preselection) {

        return DB::select('tmp_final.bid')->from([
            DB::select('battle.bid',['battle.fixed','c0'], [DB::expr('IFNULL(' . Database::instance()->table_prefix() . 'tmp_gallery.gallery, 0)'), 'c1'], [DB::expr('CASE WHEN ' . Database::instance()->table_prefix() . 'games.timestamp IS NULL THEN 0 ELSE 1 END'), 'c2'])
                ->from('battle')
                ->where('battle.bid', 'IN', $preselection)
                ->join([
                    DB::select('video', [DB::expr('COUNT(video)'), 'gallery'])->from('battle_gallery')->group_by('video')
                , 'tmp_gallery'], 'LEFT')->on('battle.bid','=','tmp_gallery.video')
                ->join('games', 'LEFT')->on('battle.gameid', '=', 'games.gameid')
        ,'tmp_final'])->where(DB::expr(Database::instance()->table_prefix() . 'tmp_final.c0 + ' . Database::instance()->table_prefix() . 'tmp_final.c1 + ' . Database::instance()->table_prefix() . 'tmp_final.c2'), '=', 0)->execute()->as_array(null, 'bid');
    }

    public static function delete_game($game_id, $season) {
        return DB::delete('battle')->where('bid','IN', static::can_delete(static::by_game($game_id, $season)))->execute();
    }

    public static function in_gallery($battle, $user) {
        return count(DB::select('id')->from('battle_gallery')->where('video','=',$battle)->where('user','=',$user)->execute()->as_array()) > 0;
    }

    public static function check_battle($bid) {
        return DB::select('gameid')->from('battle')->where('bid','=',$bid)->execute()->get('gameid', null);
    }

    public static function get_battle_from_gallery($bid, $pid) {
        if ($data = DB::select('battle.data')->from('battle_gallery')->join('battle','LEFT')->on('battle_gallery.video','=','battle.bid')->where('battle_gallery.id','=',$bid)->where('battle_gallery.user','=',$pid)->execute()->get('data'))
            return unserialize(gzuncompress($data));
        return null;
    }

    public static function get_battle($bid) {
        if ($data = DB::select('data')->from('battle')->where('bid','=',$bid)->execute()->get('data'))
            return unserialize(gzuncompress($data));
        return null;
    }

    public static function add_to_gallery($battle, $user, $label) {
        if (!static::in_gallery($battle, $user))
            DB::insert('battle_gallery', ['user','video','label'])->values([$user, $battle, $label])->execute();
        else DB::update('battle_gallery')->set(['label' => $label])->where('video','=',$battle)->where('user','=',$user)->execute();
    }

}