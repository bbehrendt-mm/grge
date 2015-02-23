<?php defined('SYSPATH') or die('No direct script access.');

define('grge\E_HTTP_AJAX_REQUIRED', 'GRGE-0000-0000');
define('grge\E_HTTP_REQUEST_INCOMPLETE', 'GRGE-0000-0001');
define('grge\E_HTTP_REQUEST_INVALID', 'GRGE-0000-0002');
define('grge\E_HTTP_REQUEST_POINTLESS', 'GRGE-0000-0003');

define('grge\E_CLIENT_CONNECTION_TIMEOUT', 'GRGE-0001-0000');

define('grge\E_SERVER_ERROR', 'GRGE-0002-0000');
define('grge\E_SERVER_INVALID_SESSION', 'GRGE-0002-0001');
define('grge\E_SERVER_ACCESS_DENIED', 'GRGE-0002-0002');
define('grge\E_SERVER_LOGIN_REJECTED', 'GRGE-0002-0003');

define('grge\E_AUTH_INVALID_PROVIDER', 'GRGE-0003-0000');
define('grge\E_AUTH_CONNECTION_FAILED', 'GRGE-0003-0001');
define('grge\E_AUTH_INVALID_CRED', 'GRGE-0003-0002');
define('grge\E_AUTH_INVALID_KEY', 'GRGE-0003-0003');
define('grge\E_AUTH_DOWNTIME', 'GRGE-0003-0004');
define('grge\E_AUTH_ACCOUNT_BANNED', 'GRGE-0003-0005');
define('grge\E_AUTH_INCOMPLETE_REQUEST', 'GRGE-0003-0006');
define('grge\E_AUTH_LOCAL_PROVIDER_FAILED', 'GRGE-0003-0007');
define('grge\E_AUTH_WHITELISTING_FAILED', 'GRGE-0003-0008');
define('grge\E_AUTH_PROFILE_DAMAGED', 'GRGE-0003-0009');

define('grge\E_EXT_SERVICE_UNAVAILABLE', 'GRGE-0004-0000');

define('grge\E_STARTER_GAME_RUNNING', 'GRGE-0005-0000');
define('grge\E_STARTER_INVALID_SETUP', 'GRGE-0005-0001');
define('grge\E_STARTER_CREATION_FAILED', 'GRGE-0005-0002');
define('grge\E_STARTER_JOIN_FAILED', 'GRGE-0005-0003');
define('grge\E_STARTER_PLAYER_BANNED', 'GRGE-0005-0004');
define('grge\E_STARTER_FETCH_FAILED', 'GRGE-0005-0005');
define('grge\E_STARTER_LOBBY_UPDATE_FAILURE', 'GRGE-0005-0006');

define('grge\E_GAME_INDEX_ERROR', 'GRGE-0006-0000');

class Error {

    public static function i() {
        return true;
    }

    /**
     * Converts an error code (GRGE-****-****) into an error constant name (E_*)
     * @param string $c Error code
     * @return string
     */
    public static function r($c) {
        $lookup = @array_flip(get_defined_constants());
        return isset($lookup[$c]) ? str_replace('grge\\','', $lookup[$c]) : 'E_UNKNOWN_ERROR';
    }

    /**
     * Gets an error description for an error code (GRGE-****-****)
     * @param $c $c Error code
     * @return string
     */
    public static function d($c) {
        switch ($c) {
            case grge\E_HTTP_AJAX_REQUIRED:             return "This resource can only be accessed via AJAX.";
            case grge\E_HTTP_REQUEST_INCOMPLETE:        return "The request your browser has sent is incomplete.";
            case grge\E_HTTP_REQUEST_INVALID:           return "There is no handler available for your request.";
            case grge\E_HTTP_REQUEST_POINTLESS:         return "The request your browser has sent is valid, but does not invoke an action.";

            case grge\E_CLIENT_CONNECTION_TIMEOUT:      return "Connection timed out.";

            case grge\E_SERVER_ERROR:                   return "Unexpected error while processing the request.";
            case grge\E_SERVER_INVALID_SESSION:         return "Inconsistent session data, client reset required!";
            case grge\E_SERVER_ACCESS_DENIED:           return "You do not possess the rights to access this resource.";
            case grge\E_SERVER_LOGIN_REJECTED:          return "The authentication process has failed.";

            case grge\E_AUTH_INVALID_PROVIDER:          return "Remote authentication provider is invalid.";
            case grge\E_AUTH_CONNECTION_FAILED:         return "Connection to remote authentication provider failed.";
            case grge\E_AUTH_INVALID_CRED:              return "Remote authentication provider refused to initiate a secure connection.";
            case grge\E_AUTH_INVALID_KEY:               return "Key is invalid";
            case grge\E_AUTH_DOWNTIME:                  return "Remote authentication provider is currently not serving any requests.";
            case grge\E_AUTH_ACCOUNT_BANNED:            return "Your account has been banned.";
            case grge\E_AUTH_INCOMPLETE_REQUEST:        return "Your request is incomplete.";
            case grge\E_AUTH_LOCAL_PROVIDER_FAILED:     return "The local authentication provider failed to authenticate your key.";
            case grge\E_AUTH_WHITELISTING_FAILED:       return "You do not have permission to play on this server.";
            case grge\E_AUTH_PROFILE_DAMAGED:           return "Unable to read profile data.";

            case grge\E_EXT_SERVICE_UNAVAILABLE:        return "An external service provider is not available.";

            case grge\E_STARTER_GAME_RUNNING:           return "Game Starter is unable to initialize while in game client mode.";
            case grge\E_STARTER_INVALID_SETUP:          return "Your game setup is invalid.";
            case grge\E_STARTER_CREATION_FAILED:        return "An error occurred during population and storage of the initialized game.";
            case grge\E_STARTER_JOIN_FAILED:            return "An error occurred during pairing game and user account.";
            case grge\E_STARTER_PLAYER_BANNED:          return "You have been banned from playing using this game setup.";
            case grge\E_STARTER_FETCH_FAILED:           return "An error occurred during retrieving the selected game from database.";
            case grge\E_STARTER_LOBBY_UPDATE_FAILURE:   return "The game lobby could not be updated.";

            case grge\E_GAME_INDEX_ERROR:              return "The game index file could not be loaded.";

            default:                                    return "Undocumented error.";
        }
    }

    /**
     * Turns an error code (GRGE-****-****) into a complete message
     * @param $c $c Error code
     * @return string
     */
    public static function m($c) {
        $name = static::r($c);
        $message = static::d($c);

        return "[$c] $name: $message";
    }
}