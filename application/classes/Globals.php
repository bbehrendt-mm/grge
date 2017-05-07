<?php

class Globals extends Model {

    private static $current_user = null;
    private static $current_game = null;

    private static $primary_player = null;
    private static $current_player = null;

    /**
     * Resets the currently active user object.
     * This will also reset all other active objects.
     */
    public static function resetCurrentUser() {
        static::resetCurrentGame();
        static::$current_user = null;
    }

    /**
     * Sets the currently active user object. If the new user objects is NULL, nothing will happen.
     * @param Model_Euser|null $u
     */
    public static function setCurrentUser(Model_Euser $u = null) {
        if ($u === null) return;
        static::$current_user = $u;
    }

    /**
     * Returns the currently active user object, or NULL if no player object is active.
     * @param bool $force If true, an exception will be thrown instead of returning NULL when no object is active
     * @return Model_Euser|null
     * @throws Exception When $force is set to TRUE and no object is active
     */
    public static function CurrentUser($force = true) {
        if ($force && static::$current_user === null) throw new Exception("Attempted to fetch the current user object when no user object was bound.");
        return static::$current_user;
    }

    /**
     * Returns true if there is an active user object
     * @return bool
     */
    public static function hasCurrentUser() {
        return static::CurrentUser(false) !== null;
    }

    /**
     * Resets the currently active game object.
     * This will also reset the active player object
     */
    public static function resetCurrentGame() {
        static::resetPrimaryPlayer();
        static::$current_game = null;
    }

    /**
     * Sets the currently active game object. If the new game objects is NULL, nothing will happen.
     * @param Model_Game|null $g
     */
    public static function setCurrentGame(Model_Game $g = null) {
        if ($g === null) return;
        static::$current_game = $g;
    }

    /**
     * Returns the currently active game object, or NULL if no game object is active.
     * @param bool $force If true, an exception will be thrown instead of returning NULL when no object is active
     * @return Model_Game|null
     * @throws Exception When $force is set to TRUE and no object is active
     */
    public static function CurrentGame($force = true) {
        if ($force && static::$current_game === null) throw new Exception("Attempted to fetch the current game object when no game object was bound.");
        return static::$current_game;
    }

    /**
     * Returns true if there is an active game object
     * @return bool
     */
    public static function hasCurrentGame() {
        return static::CurrentGame(false) !== null;
    }

    /**
     * Resets the currently active player object.
     */
    public static function resetPrimaryPlayer() {
        static::$primary_player = null;
    }

    /**
     * Resets the currently shadowed player object and restores the primary player object.
     */
    public static function restorePrimaryPlayer() {
        static::$current_player = null;
    }

    /**
     * Sets the currently active player object. If the new player objects is NULL, nothing will happen.
     * @param Model_Player|null $p
     */
    public static function setPrimaryPlayer(Model_Player $p = null) {
        if ($p === null) return;
        static::$primary_player = $p;
    }

    /**
     * Sets the currently shadowed player object, disabling the primary player in the process. If the new player objects is NULL, nothing will happen.
     * @param Interface_Plentity|null $p
     */
    public static function setCurrentPlayer(Interface_Plentity $p = null) {
        if ($p === null) return;
        static::$current_player = $p;
    }

    /**
     * Returns the currently active player object, or NULL if no player object is active.
     * @param bool $force If true, an exception will be thrown instead of returning NULL when no object is active
     * @return Model_Player|null
     * @throws Exception When $force is set to TRUE and no object is active
     */
    public static function PrimaryPlayer($force = true) {
        if ($force && static::$primary_player === null) throw new Exception("Attempted to fetch the primary player object when no player object was bound.");
        return static::$primary_player;
    }

    /**
     * Returns the currently active player object, the primary player object, or NULL if no player object is active.
     * @param bool $force If true, an exception will be thrown instead of returning NULL when no object is active
     * @return Interface_Plentity|null
     * @throws Exception When $force is set to TRUE and no object is active
     */
    public static function CurrentPlayer($force = true) {
        if ($force && static::$primary_player === null && static::$current_player === null) throw new Exception("Attempted to fetch the current player object when no player object was bound.");
        return static::$current_player === null ? static::$primary_player : static::$current_player;
    }

    /**
     * Returns the currently active player object, the primary player object, or NULL if no player object is active.
     * @param bool $force If true, an exception will be thrown instead of returning NULL when no object is active
     * @return Model_Player|null
     * @throws Exception When $force is set to TRUE and no object is active
     */
    public static function CurrentPlayerActual($force = true) {
        $p = static::CurrentPlayer($force);
        if (!$force && $p === null) return null;
        if (Tool_Scripts::is_npc($p)) throw new Exception("Attempted to fetch the non-npc current player object when an npc player object was bound.");
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $p;
    }

    /**
     * Returns true if there is an active player object
     * @return bool
     */
    public static function hasPrimaryPlayer() {
        return static::PrimaryPlayer(false) !== null;
    }

    /**
     * Returns true if there is an active player object
     * @return bool
     */
    public static function hasCurrentPlayer() {
        return static::CurrentPlayer(false) !== null;
    }

    /**
     * @return bool
     */
    public static function shadowPlayerExists() {
        return (static::$current_player !== null && static::$primary_player !== null);
    }

}