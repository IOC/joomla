<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contactioc
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Mailer Component Controller.
 *
 * @package     Joomla.Site
 * @subpackage  com_contactioc
 * @since       1.5
 */
class ContactiocController extends JControllerLegacy
{
	private $emails;
	private $mimetypes;
	const MAXFILESIZE = 2048;

	/**
	 * Class Constructor
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.5
	 * @deprecated  4.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->emails = array(
			'gesinfo@ioc.cat',
			'batxinfo@ioc.cat',
			'fpinfo@ioc.cat',
			'eoiinfo@ioc.cat',
			'pacfinfo@ioc.cat',
			'cpicgsinfo@ioc.cat',
			'competic@ioc.cat',
			'miniops@ioc.cat',
			'direccio.fpd@ioc.cat',
			'batxinfo@ioc.cat',
			'fpnoreglada@ioc.cat',
			'ioc@ioc.cat',
			'queixes_agraiments_suggeriments@ioc.cat'
		);

		$this->mimetypes = array(
			'image/png',
			'image/jpeg',
			'application/pdf',
			'application/vnd.oasis.opendocument.text',
			'application/msword',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
		);
	}

	/**
	 * Show the form so that the user can send the link to someone.
	 *
	 * @return  void
	 *
	 * @since 1.5
	 */
	public function contactioc($error = '', $script = false)
	{
		$tpl = empty($error) ? 'contactioc' : 'error';
		$this->input->set('view', $tpl);
		$view = $this->getView($tpl, 'html');
		if (!empty($error)) {
			$view->error  = $error;
			$view->script = $script;
		}
		$this->display();
	}

	/**
	 * Send the message and display a notice
	 *
	 * @return  void
	 *
	 * @since  1.5
	 */
	public function send()
	{
		$script = $this->input->get('script', false, 'bool');

		// Check for request forgeries
		if (!JSession::checkToken())
		{
			$error = JText::_('JINVALID_TOKEN');
			return $this->contactioc($error, $script);
		}

		// Load data
		$contactType       = $this->input->get('contactType', 0, 'int');
		$contactStudy      = $this->input->get('contactStudy', 0, 'int');
		$contactSubject    = trim($this->input->get('contactSubject', '', 'string'));
		$contactContent    = trim($this->input->get('contactContent', '', 'string'));
		$contactFile1 	   = $this->input->files->get('contactFile1');
		$contactFile2 	   = $this->input->files->get('contactFile2');
		$contactFile3 	   = $this->input->files->get('contactFile3');
		$contactFirstname  = trim($this->input->get('contactFirstname', '', 'string'));
		$contactLastname   = trim($this->input->get('contactLastname', '', 'string'));
		$contactEmail      = trim($this->input->get('contactEmail', '', 'string'));
		$contactTypeID     = $this->input->get('contactTypeID', 0, 'int');
		$contactID         = trim($this->input->get('contactID', '', 'string'));

		$error = $contactType < 0 || $contactType > 1;
		$error = $error || $contactStudy < 1 || $contactStudy > (count($this->emails) - 1);
		$error = $error || $contactTypeID < 1 || $contactTypeID > 4;
		$error = $error || empty($contactSubject) || empty($contactContent) || empty($contactFirstname) || empty($contactLastname)
					|| empty($contactEmail) || !JMailHelper::isEmailAddress($contactEmail) || empty($contactID);

		if ($error)
		{
			$error = JText::_('COM_CONTACTIOC_INVALID_VALUES');
			return $this->contactioc($error, $script);
		}

		$typename    = JText::_('COM_CONTACTIOC_TYPE_SHORT' . $contactType);
		$study       = JText::_('COM_CONTACTIOC_STUDY' . $contactStudy);
		$tmpfiles    = array();
		$attachments = array();

		if (!empty($contactFile1['name']))
		{
			$tmpfiles[] = $contactFile1;
		}
		if (!empty($contactFile2['name']))
		{
			$tmpfiles[] = $contactFile2;
		}
		if (!empty($contactFile3['name']))
		{
			$tmpfiles[] = $contactFile3;
		}

		if (!$this->validAttachments($tmpfiles))
		{
			$error = JText::_('COM_CONTACTIOC_INVALID_FILE');
			return $this->contactioc($error, $script);
		}

		$contactContent = substr($contactContent, 0, 2048);
		$contactEmail = JStringPunycode::emailToPunycode($contactEmail);
		$contactTypeIDName = JText::_('COM_CONTACTIOC_TYPEID' . $contactTypeID);

		// Build the message to send
		$msg  = JText::_('COM_CONTACTIOC_EMAIL_MSG');
		$body = sprintf($msg, $contactFirstname, $contactLastname, $contactTypeIDName, $contactID, $contactEmail, $contactContent);

		// Attachments
		if (!empty($tmpfiles))
		{
			jimport('joomla.filesystem.file');
			foreach ($tmpfiles as $tmpfile) {
				$config   = JFactory::getConfig();
				$filename = JFile::makeSafe($tmpfile['name']);
				$src = $tmpfile['tmp_name'];
				$attachment = $config->get('tmp_path') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $filename;
				if (!JFile::upload($src, $attachment))
				{
					$error = JText::_('COM_CONTACTIOC_UPLOAD_ERROR');
					return $this->contactioc($error, $script);
			   	}
			   	$attachments[] = $attachment;
			}
		}

		$contactStudy = $contactStudy - 1;

		if ($contactType > 0) {
			$contactSubject = $typename . ' (' . $study . '): ' . $contactSubject;
			$contactStudy = count($this->emails) - 1;
		}

		// Clean the email data
		$subject = JMailHelper::cleanSubject($contactSubject);
		$body    = JMailHelper::cleanBody($body);
		$body    = $body . "\n\n" . str_repeat('-', 68) . "\n" . JText::_('COM_CONTACTIOC_NOREPLY');

		$email = $this->emails[$contactStudy];

		$from = 'noreply@' . JUri::getInstance()->getHost();
		$sender = $contactFirstname . ' ' . $contactLastname;

		// Send the email
		if (JFactory::getMailer()->sendMail($from, $sender, $email, $subject, $body, false, null, null, $attachments) !== true)
		{
			$error = JText::_('COM_CONTACTIOC_EMAIL_NOT_SENT');
			$this->cleanAttachments($attachments);
			return $this->contactioc($error, $script);
		}

		// Remove temp files
		$this->cleanAttachments($attachments);

		$view = $this->getView('sent', 'html');
		$view->script = $script;
		$view->data = array(
			'email' => $contactEmail,
			'typename' => $typename,
			'study' => $study
		);
		$view->display();
	}

	/**
	 * Validate Attachments
	 *
	 * @param  array  $attachments
	 * @return boolean
	 */
	private function validAttachments($attachments = array())
	{
		$valid = true;
		if (empty($attachments))
		{
			return true;
		}
		foreach ($attachments as $attachment) {
			$valid = $valid && ($attachment['size'] / 1024) <= self::MAXFILESIZE;
			$valid = $valid && in_array($attachment['type'], $this->mimetypes);
		}
		return $valid;
	}

	/**
	 * Clean files
	 *
	 * @param  array  $attachments
	 */
	private function cleanAttachments($attachments = array())
	{
		if (!empty($attachments))
		{
			foreach ($attachments as $attachment) {
				unlink($attachment);
			}
		}
	}
}
