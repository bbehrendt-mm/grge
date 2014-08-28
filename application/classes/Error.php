<?php defined('SYSPATH') or die('No direct script access.');

define('grge\E_HTTP_AJAX_REQUIRED', 'GRGE-0000-0000');
define('grge\E_HTTP_REQUEST_INCOMPLETE', 'GRGE-0000-0001');
define('grge\E_HTTP_REQUEST_INVALID', 'GRGE-0000-0002');

define('grge\E_CLIENT_CONNECTION_TIMEOUT', 'GRGE-0001-0000');

define('grge\E_SERVER_ERROR', 'GRGE-0002-0000');

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

            case grge\E_CLIENT_CONNECTION_TIMEOUT:      return "Connection timed out.";

            case grge\E_SERVER_ERROR:                   return "Unexpected error while processing the request.";

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