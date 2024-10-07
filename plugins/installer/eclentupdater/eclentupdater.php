<?php
/**
 * @copyright	Copyright © 2020 by eclent s.r.l.s. - All rights reserved.
 * @license		GNU General Public License v2.0 or later.
*/

defined('_JEXEC') or die;

class plgInstallerEclentUpdater extends JPlugin {

	public function onInstallerBeforePackageDownload(&$url, &$headers) {

		$uri = JUri::getInstance($url);

		// Only process if user attempt to update extensions purchase on your site

		/*$host       = $uri->getHost();
		$validHosts = array('eclent.com', 'www.eclent.com');

		if (!in_array($host, $validHosts))
		{
			return true;
		}*/

		// Only process if update is handled via Membership Pro
		$option = $uri->getVar('option');
		$documentId = (int) $uri->getVar('document_id');

		if ($option != 'com_osmembership' || !$documentId) {
			return true;
		}

		$downloadId = $this->params->get('download_id');

		// Append the Download ID to the download URL
		if (!empty($downloadId)) {
			$uri->setVar('download_id', $downloadId);

			// Append the current site domain to URL for logging and validation as our rule is each Download ID will only valid for one domain
			$siteUri = JUri::getInstance();
			$uri->setVar('domain', $siteUri->getHost());

			$url = $uri->toString();
		}
		
		$headers['User-Agent'] = 'Membership Pro';

		return true;
	}
}
