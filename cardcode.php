<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  User.contactcreator
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\String\StringHelper;

/**
 * Class for Contact Creator
 *
 * A tool to automatically create and synchronise contacts with a user
 *
 * @since  1.6
 */
class PlgUserCardCode extends JPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	/**
	 * Utility method to act on a user after it has been saved.
	 *
	 * This method creates a contact for the saved user
	 *
	 * @param   array    $user     Holds the new user data.
	 * @param   boolean  $isnew    True if a new user is stored.
	 * @param   boolean  $success  True if user was succesfully stored in the database.
	 * @param   string   $msg      Message.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function onUserAfterSave($user, $isnew, $success, $msg)
	{
		// Se l'utente non è stato memorizzato, non risincronizzeremo
		if (!$success)
		{
			return false;
		}

		// Se l'utente non è nuovo, non si sincronizza
		if (!$isnew)
		{
			return false;
		}

		var_dump($msg);

		// Ensure the user id is really an int
		$user_id = (int) $user['id'];
		$username = $user['username'];
		$password = $user['password'];

		$cardcode = substr(md5($user_id.$username.$password), 0, 5);

		// If the user id appears invalid then bail out just in case
		if (empty($user_id))
		{
			return false;
		}


		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Insert columns.
		$columns = array('user_id', 'cardcode');

		// Insert values.
		$values = array($user_id, $db->quote($cardcode));

		// Prepare the insert query.
		$query
		    ->insert($db->quoteName('#__cardcode'))
		    ->columns($db->quoteName($columns))
		    ->values(implode(',', $values));

		// Set the query using our newly populated query object and execute it.
		$db->setQuery($query);
		$db->execute();


		return false;
	}
}
