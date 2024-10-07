<?php
/**
 * @copyright	Copyright (c) 2019-2022 by eclent. All rights reserved.
 * @license		GNU General Public License v2.0 or later.
 */

// no direct access
defined('_JEXEC') or die;

error_reporting(E_ALL & ~E_NOTICE);

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;

jimport('joomla.plugin.plugin');

class plgsystemForcePasswordReset extends JPlugin {


	public function __construct(& $subject, $config) {
		parent::__construct ($subject, $config);
		$this->loadLanguage();
	}


	function onBeforeRender() {

		// Get some needed data
		$app = Factory::getApplication();
		$user = Factory::getUser();
		$todayDatetime = Factory::getDate();
		$input = $app->input;
		$option = $input->get('option');
		$view = $input->get('view');
		$layout = $input->get('layout');
		$currentMenuitem = $app->getMenu()->getActive()->id;

		// Check if user is logged in before continue and avoid Akeeba Login Guard conflict. Also check if the plugin is running in the front-end or in the back-end.
		if ($app->isClient('administrator') && $this->params->get('backend_running_allowed') == true && !$user->guest && $option != 'com_loginguard') {
			$runningAllowed = true;
		} elseif (!$app->isClient('administrator') && !$user->guest && $option != 'com_loginguard') {
			$runningAllowed = true;
		} else {
			$runningAllowed = false;
		}

		// Check if the plugin can run.
		if ($runningAllowed == true) {

			// Get user data
			$userID = $user->id;
			$userGroups = $user->groups;
			$registerDate = $user->registerDate;
			$lastPasswordReset = $user->lastResetTime;

			// Get plugin parameters
			$changeAtFirstLogin = $this->params->get('reset_first_login');
			$x = $this->params->get('days');
			$profileHandler = $this->params->get('profile_handler');
			$customMenuItem = $this->params->get('custom_menu_item');
			if ($this->params->get('usergroups_excluded')) {
				$excludedGroups = $this->params->get('usergroups_excluded');
			} else {
				$excludedGroups = array(); // Needed to avoid warning
			}
			if ($this->params->get('users_excluded')) {
				$excludedUsers = explode(',',$this->params->get('users_excluded'));
			} else {
				$excludedUsers = array(); // Needed to avoid warning
			}

			$resetNeeded = false;
			$userExcluded = true;

			// Do nothing if user is member of excluded user groups
			if (!array_intersect($userGroups, $excludedGroups) && !in_array($userID, $excludedUsers)) {
				// Check if the password reset is needed
				if ($lastPasswordReset == 0 && $changeAtFirstLogin == 0) {
					$allowedTime = Factory::getDate($registerDate)->modify('+'.$x.' days');
					if ($allowedTime <= $todayDatetime) {
						$resetNeeded = true;
					}
				} elseif ($lastPasswordReset == 0 && $changeAtFirstLogin == 1) {
					$resetNeeded = true;
				} elseif ($lastPasswordReset != 0) {
					$allowedTime = Factory::getDate($lastPasswordReset)->modify('+'.$x.' days');
					if ($allowedTime <= $todayDatetime) {
						$resetNeeded = true;
					}
				}
				$userExcluded = false;
			}

			// Redirect to the correct profile handler
			if ($resetNeeded == true && !$app->isClient('administrator')) {
				switch ($profileHandler) {
					case 'joomla': // JOOMLA
						if ($option != 'com_users' || ($view != 'profile' && $layout != 'edit')) {
							$redirect = Route::_('index.php?option=com_users&view=profile&layout=edit');
							$app->enqueueMessage(Text::_('PLG_SYSTEM_FORCEPASSWORDRESET_CHANGE_PASSWORD'), 'error');
							$app->redirect($redirect, 302);
						}
					break;
					case 'cb': // COMMUNITY BUILDER
						if ($option != 'com_comprofiler' && $view != 'userdetails') {
							$redirect = Route::_('index.php?option=com_comprofiler&view=userdetails');
							$app->enqueueMessage(Text::_('PLG_SYSTEM_FORCEPASSWORDRESET_CHANGE_PASSWORD'), 'error');
							$app->redirect($redirect, 302);
						}
					break;
					case 'es': // EASY SOCIAL
						if ($option != 'com_easysocial' || ($view != 'profile' && $layout != 'edit')) {
							$redirect = Route::_('index.php?option=com_easysocial&view=profile&layout=edit&Itemid='.$userID);
							$app->enqueueMessage(Text::_('PLG_SYSTEM_FORCEPASSWORDRESET_CHANGE_PASSWORD'), 'error');
							$app->redirect($redirect, 302);
						}
					break;
					case 'custom': // CUSTOM MENU ITEM
					if ($currentMenuitem && $customMenuItem != $currentMenuitem) {
						$redirect = Route::_("index.php?Itemid={$customMenuItem}");
						$app->enqueueMessage(Text::_('PLG_SYSTEM_FORCEPASSWORDRESET_CHANGE_PASSWORD'), 'error');
						$app->redirect($redirect, 302);
					}
					break;
				}
			} elseif ($resetNeeded == true && $app->isClient('administrator')) { // BACKEND ONLY (JOOMLA DEFAULT PROFILE HANDLER)
				if ($option != 'com_users' || ($view != 'user' && $layout != 'edit' && $input->get('id') != $userID)) {
					$app->enqueueMessage(Text::_('PLG_SYSTEM_FORCEPASSWORDRESET_CHANGE_PASSWORD'), 'error');
					$app->redirect('index.php?option=com_users&task=user.edit&id='.$userID, 302);
				}
			}

		} else { // If the plugin is not enabled to run in the backend, we still need to save the password hash without other checks

			$userExcluded = true;
			$resetNeeded = false;

		}

		$session = Factory::getSession();
		$session->set('userExcluded', $userExcluded);
		$session->set('resetNeeded', $resetNeeded);

	}


	// Do some checks on user input and prepare data to be saved
	function onUserBeforeSave($oldUser, $isNew, $newUser) {

		$app = Factory::getApplication();

		$session = Factory::getSession();
		$userExcluded = $session->get('userExcluded');
		$resetNeeded = $session->get('resetNeeded');

		if ($userExcluded == false && $resetNeeded == true && $newUser['password_clear'] == '') { // Do not continue if entered password is empty, user is not excluded end reset is needed

			$app->enqueueMessage(Text::_('PLG_SYSTEM_FORCEPASSWORDRESET_ERROR_EMPTYPWD'), 'error');
			$saveNewPassword = false;
			return false;

		} elseif ($userExcluded == false && $newUser['password_clear'] != '') { // If user is not excluded and the password is not empty keep going to do all the other ckecks

			$db = Factory::getDbo();

			// Get the required parameters
			$allowedResetHours = $this->params->get('reset_hours'); // Hours, before actual time, during which the user can change password a certain number of times
			$allowedResetCount = $this->params->get('reset_count'); // Number of times the user can change password
			$latestRecordsToCheck = $this->params->get('records_to_check'); // Check the match of the new password with all this number of latest records

			// Get the interval of hours, before actual time, during which the user can change password a certain number of times
			$timezone = Factory::getUser()->getTimezone();
			$allowedResetInterval = Factory::getDate()->setTimezone($timezone)->modify('-'.$allowedResetHours.' hour');

			// Get the number of times the user has already changed the password in the specified interval
			$db = Factory::getDbo();
			$query = $db->getQuery(true)
				->select('COUNT(*)')
				->from($db->quoteName('#__forcepasswordreset_pwdlogs'))
				->where($db->quoteName('user_id') . ' = ' . $db->quote($oldUser['id']))
				->where($db->quoteName('pwd_creation_date') . ' >= ' . $db->quote($allowedResetInterval));
			$db->setQuery($query);
			$count = $db->loadResult();

			// If the user has already changed the password too many times in the specified range, prevent him from doing it again
			if ($count >= $allowedResetCount && $resetNeeded == false) {
				$app->enqueueMessage(Text::sprintf('PLG_SYSTEM_FORCEPASSWORDRESET_ERROR_ALLOWED_COUNTS', $allowedResetCount, $allowedResetHours), 'error');
				$saveNewPassword = false;
				return false;
			}

			// Get the records to be used to verify the match with the new password entered
			$query = $db->getQuery(true)
				->select('*')
				->from($db->quoteName('#__forcepasswordreset_pwdlogs'))
				->where($db->quoteName('user_id') . ' = ' . $db->quote($oldUser['id']))
				->order($db->quoteName('id') . ' DESC')
				->setLimit($latestRecordsToCheck);
			$db->setQuery($query);
			$recordsToCheck = $db->loadAssocList();

			// Check how many records match
			$recPasswordMatchCount = 0;
			foreach ($recordsToCheck as $row) {
				if (JUserHelper::verifyPassword($newUser['password_clear'], $row['pwd_hash'], null)) {
					++$recPasswordMatchCount;
				}
			}

			// Check if entered password is the same as one of the previous and allow or disallow the password change
			$latestPasswordMatch = JUserHelper::verifyPassword($newUser['password_clear'], $oldUser['password'], $oldUser['id']);
			if ($recPasswordMatchCount > 0 || $latestPasswordMatch == true) {
				$app->enqueueMessage(Text::_('PLG_SYSTEM_FORCEPASSWORDRESET_ERROR_SAMEPWD'), 'error');
				$saveNewPassword = false;
				return false;
			} else {
				$saveNewPassword = true;
			}

		} elseif ($newUser['password_clear'] != '') { // Save hash if user is excluded or reset is not needed or running in the backend is disabled
			$saveNewPassword = true;
		}

		$session->set('saveNewPassword', $saveNewPassword);

	}


	// Change the last reset datetime and save the hash of the new password to the plugin table after password save
	function onUserAfterSave($user, $isNew, $success, $msg) {

		$session = Factory::getSession();
		$saveNewPassword = $session->get('saveNewPassword');

		if ($success == true && $saveNewPassword == true) {

			$todayDatetime = Factory::getDate()->toSQL();

			$app = Factory::getApplication();
			$db = Factory::getDbo();

			// Keep this number of latest records and delete all the older ones in order to reduce the plugin table dimension
			$latestRecordsToKeep = $this->params->get('records_to_keep');

			// Get all the records related to the user
			$query = $db->getQuery(true)
				->select($db->quoteName('id'))
				->from($db->quoteName('#__forcepasswordreset_pwdlogs'))
				->where($db->quoteName('user_id') . ' = ' . $db->quote($user['id']));
			$db->setQuery($query);
			$allUserRecords = $db->loadColumn();

			// Get only the latest records to keep
			$query = $db->getQuery(true)
				->select($db->quoteName('id'))
				->from($db->quoteName('#__forcepasswordreset_pwdlogs'))
				->where($db->quoteName('user_id') . ' = ' . $db->quote($user['id']))
				->order($db->quoteName('id') . ' DESC')
				->setLimit($latestRecordsToKeep);
			$db->setQuery($query);
			$recordsToKeep = $db->loadColumn();

			// Get the records to be deleted
			$recordsToDelete = array_diff($allUserRecords, $recordsToKeep);

			// Delete all the records no more needed
			foreach ($recordsToDelete as $record) {
				$query = $db->getQuery(true)
					->delete($db->quoteName('#__forcepasswordreset_pwdlogs'))
					->where($db->quoteName('id') . ' = ' . $db->quote($record));
				$db->setQuery($query);
				$db->execute();
			}

			// Save the hash of the new password in the plugin table
			try {
				$query = $db->getQuery(true)
			    ->insert($db->quoteName('#__forcepasswordreset_pwdlogs'))
			    ->columns($db->quoteName(array('user_id', 'pwd_hash')))
			    ->values(implode(',', array($db->quote($user['id']), $db->quote($user['password']))));
				$db->setQuery($query);
				$db->execute();
			} catch (Exception $e) {
				$app->enqueueMessage(Text::_('PLG_SYSTEM_FORCEPASSWORDRESET_ERROR_SAVE').$e, 'error');
			}

			// Update the last reset timestamp
			try {
				$query = $db->getQuery(true)
					->update($db->quoteName('#__users'))
					->set($db->quoteName('lastResetTime') . '=' . $db->quote($todayDatetime))
					->where($db->quoteName('id') . ' = ' . $db->quote($user['id']));
				$db->setQuery($query);
				$db->execute();
			} catch (Exception $e) {
				$app->enqueueMessage(Text::_('PLG_SYSTEM_FORCEPASSWORDRESET_ERROR_SAVE'), 'error');
			}

			// $app->enqueueMessage(Text::_('PLG_SYSTEM_FORCEPASSWORDRESET_SAVED_SUCCESS'));

		}

	}


	// Delete all the records related to a deleted user from the plugin table
	function onUserAfterDelete($user, $succes, $msg) {

		$db = Factory::getDbo();

		// Delete the records only if the user is really deleted from Joomla
		if ($succes == true) {
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__forcepasswordreset_pwdlogs'))
				->where($db->quoteName('user_id') . ' = ' . $db->quote($user['id']));
			$db->setQuery($query);
			$db->execute();
		}

	}


}
