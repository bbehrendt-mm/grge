<?php

class Globals extends Model {

    private static $current_user;
    private static $current_game;

    private static $primary_player;
    private static $current_player;

    /**
     * Resets the currently active user object.
     * This will also reset all other active objects.
     */
    public static function resetCurrentUser(): void
    {
        static::resetCurrentGame();
        static::$current_user = null;
    }

    /**
     * Sets the currently active user object. If the new user objects is NULL, nothing will happen.
     * @param Model_Euser|null $u
     */
    public static function setCurrentUser(Model_Euser $u = null): void
    {
        if ($u === null) return;
        static::$current_user = $u;
    }

    /**
     * Returns the currently active user object, or NULL if no player object is active.
     * @return Model_Euser|null
     */
    public static function CurrentUser(): ?\Model_Euser
    {
        return static::$current_user;
    }

    /**
     * Returns the currently active user object
     * @return Model_Euser
     * @throws Exception When no object is active
     */
    public static function CurrentUserF(): \Model_Euser
    {
        if (static::$current_user === null) throw new LogicException(
            'Attempted to fetch the current user object when no user object was bound.'
        );
        return static::$current_user;
    }

    /**
     * Returns true if there is an active user object
     * @return bool
     */
    public static function hasCurrentUser(): bool
    {
        return static::CurrentUser() !== null;
    }

    /**
     * Resets the currently active game object.
     * This will also reset the active player object
     */
    public static function resetCurrentGame(): void
    {
        static::resetPrimaryPlayer();
        static::$current_game = null;
    }

    /**
     * Sets the currently active game object. If the new game objects is NULL, nothing will happen.
     * @param Model_Game|null $g
     */
    public static function setCurrentGame(Model_Game $g = null): void
    {
        if ($g === null) return;
        static::$current_game = $g;
    }

    /**
     * Returns the currently active game object, or NULL if no game object is active.
     * @return Model_Game|null
     */
    public static function CurrentGame(): ?\Model_Game
    {
        return static::$current_game;
    }

    /**
     * Returns the currently active game object
     * @return Model_Game
     * @throws Exception When no object is active
     */
    public static function CurrentGameF(): \Model_Game
    {
        if (static::$current_game === null) throw new LogicException(
            'Attempted to fetch the current game object when no game object was bound.'
        );
        return static::$current_game;
    }

    /**
     * Returns true if there is an active game object
     * @return bool
     */
    public static function hasCurrentGame(): bool
    {
        return static::CurrentGame() !== null;
    }

    /**
     * Resets the currently active player object.
     */
    public static function resetPrimaryPlayer(): void
    {
        static::$primary_player = null;
    }

    /**
     * Resets the currently shadowed player object and restores the primary player object.
     */
    public static function restorePrimaryPlayer(): void
    {
        static::$current_player = null;
    }

    /**
     * Sets the currently active player object. If the new player objects is NULL, nothing will happen.
     * @param Model_Player|null $p
     */
    public static function setPrimaryPlayer(Model_Player $p = null): void
    {
        if ($p === null) return;
        static::$primary_player = $p;
    }

    /**
     * Sets the currently shadowed player object, disabling the primary player in the process. If the new player objects is NULL, nothing will happen.
     * @param Interface_Plentity|null $p
     */
    public static function setCurrentPlayer(Interface_Plentity $p = null): void
    {
        if ($p === null) return;
        static::$current_player = $p;
    }

    /**
     * Returns the currently active player object, or NULL if no player object is active.
     * @return Model_Player|null
     */
    public static function PrimaryPlayer(): ?\Model_Player
    {
        return static::$primary_player;
    }

    /**
     * Returns the currently active player object, or NULL if no player object is active.
     * @return Model_Player
     * @throws Exception When $force is set to TRUE and no object is active
     */
    public static function PrimaryPlayerF(): \Model_Player
    {
        if (static::$primary_player === null) throw new LogicException(
            'Attempted to fetch the primary player object when no player object was bound.'
        );
        return static::$primary_player;
    }

    /**
     * Returns the currently active player object, the primary player object, or NULL if no player object is active.
     * @return Interface_Plentity|null
     */
    public static function CurrentPlayer(): ?\Interface_Plentity
    {
        return static::$current_player ?? static::$primary_player;
    }

    /**
     * Returns the currently active player object, the primary player object, or NULL if no player object is active.
     * @return Interface_Plentity
     * @throws Exception When no object is active
     */
    public static function CurrentPlayerF(): \Interface_Plentity
    {
        if (static::$primary_player === null && static::$current_player === null) throw new LogicException(
            'Attempted to fetch the current player object when no player object was bound.'
        );
        return static::$current_player ?? static::$primary_player;
    }

    /**
     * Returns the currently active player object, the primary player object, or NULL if no player object is active.
     * @return Model_Player|null
     * @throws Exception When  no object is active
     */
    public static function CurrentPlayerActual(): ?\Model_Player
    {
        $p = static::CurrentPlayer();
        if (!$p === null) return null;
        if (Tool_Scripts::is_npc($p)) throw new LogicException(
            'Attempted to fetch the non-npc current player object when an npc player object was bound.'
        );
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $p;
    }

    /**
     * Returns the currently active player object, the primary player object, or NULL if no player object is active.
     * @return Model_Player
     * @throws Exception When no object is active
     */
    public static function CurrentPlayerActualF(): \Model_Player
    {
        $p = static::CurrentPlayerF();
        if (Tool_Scripts::is_npc($p)) throw new LogicException(
            'Attempted to fetch the non-npc current player object when an npc player object was bound.'
        );
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $p;
    }

    /**
     * Returns true if there is an active player object
     * @return bool
     */
    public static function hasPrimaryPlayer(): bool
    {
        return static::PrimaryPlayer() !== null;
    }

    /**
     * Returns true if there is an active player object
     * @return bool
     */
    public static function hasCurrentPlayer(): bool
    {
        return static::CurrentPlayer() !== null;
    }

    /**
     * @return bool
     */
    public static function shadowPlayerExists(): bool
    {
        return (static::$current_player !== null && static::$primary_player !== null);
    }

}