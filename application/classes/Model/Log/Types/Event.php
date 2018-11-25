<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Event extends Model_Log_Types_String  {

    private $key;

    /**
     * Model_Log_Types_Event constructor.
     *
     * @param String $name
     * @param String $key
     * @param bool   $enabled
     * @param String $flavour
     *
     * @throws Exception
     */
    public function __construct($name, $key, $enabled, $flavour) {
        $this->key = $key;
        parent::__construct(
            "$name :year",
            $enabled
            ? ':falvour Das :event :year wurde soeben für dein Spiel aktiviert. Details zu den Änderungen durch dieses Event findest du im News-Bereich. Viel Spaß beim Spielen!'
            : ':falvour Das :event :year ist nun beendet. Hoffentlich hat es dir gefallen! Wenn du Feedback zu diesem Event abgeben möchtest, besuche bitte das Forum.',
            [
                ':event' => [$name],
                ':year' => date('Y'),
                ':falvour' => [$flavour]
            ]
        );
    }

    public function as_notification(): ?array
    {
        $tmp = parent::as_notification();
        $tmp[0] = "event {$this->key}";
        return $tmp;
    }
}