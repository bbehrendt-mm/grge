<?php defined('SYSPATH') OR die('No direct access allowed.');

interface Interface_Message {
	
	/**
	 * Renders a title, or returns null
	 * @return string|NULL Title as string or null if no title applies
	 */
	public function render_title();
	
	/**
	 * Renders a body, or returns null
	 * @return string|NULL Body as string or null if no body applies
	 */	
	public function render_body();
	
	/**
	 * Returns the message timecode
	 * @return int
	 */
	public function timecode();

    /**
     * @param Interface_Message $new
     * @return bool
     */
    public function merge($new);
}