<?php
/**
 * @version     $Id$
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Files
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Proxy Controller Class
 *
 * Used to perform cross origin HEAD request calls on resources to see if they exist, and if exists then also pass the Content-length header
 *
 * @author      Stian Didriksen <http://nooku.assembla.com/profile/stiandidriksen>
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Files
 */
 class ComFilesControllerProxy extends ComFilesControllerDefault
{
	//@TODO move into MVC structure?
	public function _actionGet(KCommandContext $context)
	{
		$data = array('url' => KRequest::get('get.url', 'url'), 'status' => 0,  'content-length' => false);

		if (!function_exists('curl_init')) {
			throw new ComFilesDatabaseRowUrlAdapterException('Adapter does not exist');
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $data['url']);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_MAXREDIRS,		 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 		 120);
		//CURLOPT_NOBODY changes the request from GET to HEAD
		curl_setopt($ch, CURLOPT_NOBODY, 		 true);

		$response = curl_exec($ch);

		if (curl_errno($ch)) {
			throw new ComFilesDatabaseRowUrlException('Curl Error: '.curl_error($ch));
		}

		$info = curl_getinfo($ch);
		if (isset($info['http_code']) && $info['http_code'] != 200) {
			$data['status'] = false;
		}
		if (isset($info['download_content_length'])) {
			$data['content-length'] = $info['download_content_length'];
		}

		curl_close($ch);

		return json_encode($data);
	}
}