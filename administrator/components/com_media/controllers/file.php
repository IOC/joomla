<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   (C) 2007 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Media File Controller
 *
 * @since  1.5
 */
class MediaControllerFile extends JControllerLegacy
{
	/**
	 * The folder we are uploading into
	 *
	 * @var   string
	 */
	protected $folder = '';

	/**
	 * Upload one or more files
	 *
	 * @return  boolean
	 *
	 * @since   1.5
	 */
	public function upload()
	{
		// Check for request forgeries
		$this->checkToken('request');

		$params = JComponentHelper::getParams('com_media');

		// Get some data from the request
		$files        = $this->input->files->get('Filedata', array(), 'array');
		$return       = JFactory::getSession()->get('com_media.return_url');
		$this->folder = $this->input->get('folder', '', 'path');

		// Instantiate the media helper
		$mediaHelper = new JHelperMedia;

		// Don't redirect to an external URL.
		if (!JUri::isInternal($return))
		{
			$return = '';
		}

		// Set the redirect
		if ($return)
		{
			$this->setRedirect($return . '&folder=' . $this->folder);
		}
		else
		{
			$this->setRedirect('index.php?option=com_media&folder=' . $this->folder);
		}

		// First check against unfiltered input.
		if (!$this->input->files->get('Filedata', null, 'RAW'))
		{
			// Total length of post back data in bytes.
			$contentLength = $this->input->server->get('CONTENT_LENGTH', 0, 'INT');

			// Maximum allowed size of post back data in MB.
			$postMaxSize = $mediaHelper->toBytes(ini_get('post_max_size'));

			// Maximum allowed size of script execution in MB.
			$memoryLimit = $mediaHelper->toBytes(ini_get('memory_limit'));

			// Check for the total size of post back data.
			if (($postMaxSize > 0 && $contentLength > $postMaxSize)
				|| ($memoryLimit != -1 && $contentLength > $memoryLimit))
			{
				// Files are too large.
				JError::raiseWarning(100, JText::_('COM_MEDIA_ERROR_WARNUPLOADTOOLARGE'));

				return false;
			}

			// No files were provided.
			$this->setMessage(JText::_('COM_MEDIA_ERROR_UPLOAD_INPUT'), 'warning');

			return false;
		}

		if (!$files)
		{
			// Files were provided but are unsafe to upload.
			$this->setMessage(JText::_('COM_MEDIA_ERROR_WARNFILENOTSAFE'), 'error');

			return false;
		}

		// Authorize the user
		if (!$this->authoriseUser('create'))
		{
			return false;
		}

		$uploadMaxSize = $params->get('upload_maxsize', 0) * 1024 * 1024;
		$uploadMaxFileSize = $mediaHelper->toBytes(ini_get('upload_max_filesize'));

		// Perform basic checks on file info before attempting anything
		foreach ($files as &$file)
		{
			// Make the filename safe
			$file['name'] = JFile::makeSafe($file['name']);

			// We need a url safe name
			$fileparts = pathinfo(COM_MEDIA_BASE . '/' . $this->folder . '/' . $file['name']);

            // @PATCH IOC001
            // CODI ORIGINAL
            // if (strpos(realpath($fileparts['dirname']), JPath::clean(realpath(COM_MEDIA_BASE))) !== 0)
            // CODI MODIFICAT
            if (strpos($fileparts['dirname'], JPath::clean(COM_MEDIA_BASE)) !== 0)
            // FI
			{
				JError::raiseWarning(100, JText::_('COM_MEDIA_ERROR_WARNINVALID_FOLDER'));

				return false;
			}

			// Transform filename to punycode, check extension and transform it to lowercase
			$fileparts['filename'] = JStringPunycode::toPunycode($fileparts['filename']);
			$tempExt = !empty($fileparts['extension']) ? strtolower($fileparts['extension']) : '';

			// Neglect other than non-alphanumeric characters, hyphens & underscores.
			$safeFileName = preg_replace(array("/[\\s]/", '/[^a-zA-Z0-9_\-]/'), array('_', ''), $fileparts['filename']) . '.' . $tempExt;

			$file['name'] = $safeFileName;

			$file['filepath'] = JPath::clean(implode(DIRECTORY_SEPARATOR, array(COM_MEDIA_BASE, $this->folder, $file['name'])));

			if (($file['error'] == 1)
				|| ($uploadMaxSize > 0 && $file['size'] > $uploadMaxSize)
				|| ($uploadMaxFileSize > 0 && $file['size'] > $uploadMaxFileSize))
			{
				// File size exceed either 'upload_max_filesize' or 'upload_maxsize'.
				JError::raiseWarning(100, JText::_('COM_MEDIA_ERROR_WARNFILETOOLARGE'));

				return false;
			}

			if (JFile::exists($file['filepath']))
			{
				// A file with this name already exists
				JError::raiseWarning(100, JText::_('COM_MEDIA_ERROR_FILE_EXISTS'));

				return false;
			}

			if (!isset($file['name']))
			{
				// No filename (after the name was cleaned by JFile::makeSafe)
				$this->setRedirect('index.php', JText::_('COM_MEDIA_INVALID_REQUEST'), 'error');

				return false;
			}
		}

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');
		JPluginHelper::importPlugin('content');
		$dispatcher = JEventDispatcher::getInstance();

		foreach ($files as &$file)
		{
			// The request is valid
			$err = null;

			if (!MediaHelper::canUpload($file, $err))
			{
				// The file can't be uploaded
				return false;
			}

			// Trigger the onContentBeforeSave event.
			$object_file = new JObject($file);
			$result = $dispatcher->trigger('onContentBeforeSave', array('com_media.file', &$object_file, true));

			if (in_array(false, $result, true))
			{
				// There are some errors in the plugins
				JError::raiseWarning(100, JText::plural('COM_MEDIA_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));

				return false;
			}

			if (!JFile::upload($object_file->tmp_name, $object_file->filepath))
			{
				// Error in upload
				JError::raiseWarning(100, JText::_('COM_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE'));

				return false;
			}

			// Trigger the onContentAfterSave event.
			$dispatcher->trigger('onContentAfterSave', array('com_media.file', &$object_file, true));
			$this->setMessage(JText::sprintf('COM_MEDIA_UPLOAD_COMPLETE', substr($object_file->filepath, strlen(COM_MEDIA_BASE))));
		}

		return true;
	}

	/**
	 * Check that the user is authorized to perform this action
	 *
	 * @param   string  $action  - the action to be performed (create or delete)
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	protected function authoriseUser($action)
	{
		if (!JFactory::getUser()->authorise('core.' . strtolower($action), 'com_media'))
		{
			// User is not authorised
			JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_' . strtoupper($action) . '_NOT_PERMITTED'));

			return false;
		}

		return true;
	}

	/**
	 * Deletes paths from the current path
	 *
	 * @return  boolean
	 *
	 * @since   1.5
	 */
	public function delete()
	{
		$this->checkToken('request');

		$user = JFactory::getUser();

		// Get some data from the request
		$tmpl   = $this->input->get('tmpl');
		$paths  = $this->input->get('rm', array(), 'array');
		$folder = $this->input->get('folder', '', 'path');

		$redirect = 'index.php?option=com_media&folder=' . $folder;

		if ($tmpl == 'component')
		{
			// We are inside the iframe
			$redirect .= '&view=mediaList&tmpl=component';
		}

		$this->setRedirect($redirect);

		// Just return if there's nothing to do
		if (empty($paths))
		{
			$this->setMessage(JText::_('JERROR_NO_ITEMS_SELECTED'), 'error');

			return true;
		}

		if (!$user->authorise('core.delete', 'com_media'))
		{
			// User is not authorised to delete
			JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));

			return false;
		}

		// Need this to enqueue messages.
		$app = JFactory::getApplication();

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');

		JPluginHelper::importPlugin('content');
		$dispatcher = JEventDispatcher::getInstance();

		$ret = true;

		$safePaths = array_intersect($paths, array_map(array('JFile', 'makeSafe'), $paths));

		foreach ($safePaths as $key => $path)
		{
			$fullPath = implode(DIRECTORY_SEPARATOR, array(COM_MEDIA_BASE, $folder, $path));

            // @PATCH IOC001
            // CODI ORIGINAL
            // if (strpos(realpath($fullPath), JPath::clean(realpath(COM_MEDIA_BASE))) !== 0)
            // CODI MODIFICAT
            if (strpos($fullPath, JPath::clean(COM_MEDIA_BASE)) !== 0)
            // FI
			{
				unset($safePaths[$key]);
			}
		}

		$unsafePaths = array_diff($paths, $safePaths);

		foreach ($unsafePaths as $path)
		{
			$path = JPath::clean(implode(DIRECTORY_SEPARATOR, array($folder, $path)));
			$path = htmlspecialchars($path, ENT_COMPAT, 'UTF-8');
			$app->enqueueMessage(JText::sprintf('COM_MEDIA_ERROR_UNABLE_TO_DELETE_FILE_WARNFILENAME', $path), 'error');
		}

		foreach ($safePaths as $path)
		{
			$fullPath = JPath::clean(implode(DIRECTORY_SEPARATOR, array(COM_MEDIA_BASE, $folder, $path)));
			$object_file = new JObject(array('filepath' => $fullPath));

			if (is_file($object_file->filepath))
			{
				// Trigger the onContentBeforeDelete event.
				$result = $dispatcher->trigger('onContentBeforeDelete', array('com_media.file', &$object_file));

				if (in_array(false, $result, true))
				{
					// There are some errors in the plugins
					$errors = $object_file->getErrors();
					JError::raiseWarning(100, JText::plural('COM_MEDIA_ERROR_BEFORE_DELETE', count($errors), implode('<br />', $errors)));

					continue;
				}

				$ret &= JFile::delete($object_file->filepath);

				// Trigger the onContentAfterDelete event.
				$dispatcher->trigger('onContentAfterDelete', array('com_media.file', &$object_file));
				$app->enqueueMessage(JText::sprintf('COM_MEDIA_DELETE_COMPLETE', substr($object_file->filepath, strlen(COM_MEDIA_BASE))));
			}
			elseif (is_dir($object_file->filepath))
			{
				$contents = JFolder::files($object_file->filepath, '.', true, false, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html'));

				if (!empty($contents))
				{
					// This makes no sense...
					$folderPath = substr($object_file->filepath, strlen(COM_MEDIA_BASE));
					JError::raiseWarning(100, JText::sprintf('COM_MEDIA_ERROR_UNABLE_TO_DELETE_FOLDER_NOT_EMPTY', $folderPath));

					continue;
				}

				// Trigger the onContentBeforeDelete event.
				$result = $dispatcher->trigger('onContentBeforeDelete', array('com_media.folder', &$object_file));

				if (in_array(false, $result, true))
				{
					// There are some errors in the plugins
					$errors = $object_file->getErrors();
					JError::raiseWarning(100, JText::plural('COM_MEDIA_ERROR_BEFORE_DELETE', count($errors), implode('<br />', $errors)));

					continue;
				}

				$ret &= !JFolder::delete($object_file->filepath);

				// Trigger the onContentAfterDelete event.
				$dispatcher->trigger('onContentAfterDelete', array('com_media.folder', &$object_file));
				$app->enqueueMessage(JText::sprintf('COM_MEDIA_DELETE_COMPLETE', substr($object_file->filepath, strlen(COM_MEDIA_BASE))));
			}
		}

		return $ret;
	}
}
