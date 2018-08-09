<?php
/**
 * This is the Dispatcher used for cross-module communications and heavily
 * based on http://dustint.com/post/38/building-a-php-publish-subscribe-system
 * from Dustin Thomson. Used here with permission received on 21 Aug 2011.
 *
 * @package     apmProject\system
 */
interface apm_Interfaces_Listener {
	/**
	 * Accepts an event and does something with it
	 *
	 * @param Event $event
	 *        	The event to process
	 */
	public function publish(apm_System_Event $event);
}