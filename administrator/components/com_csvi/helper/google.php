<?php
/**
 * @package     CSVI
 *
 * @author      RolandD Cyber Produksi <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://csvimproved.com
 */

defined('_JEXEC') or die;

/**
 * Google API helper
 *
 * @package  CSVI
 * @since    7.17.0
 */
class CsviHelperGoogle
{
	/**
	 * Holds the Google client object
	 *
	 * @var   Object
	 * @since  7.17.0
	 */
	private $client;

	/**
	 * Holds the auth url
	 *
	 * @var    string
	 * @since  7.17.0
	 */
	private $authUrl = '';

	/**
	 * Id of the google sheet template
	 *
	 * @var   Object
	 * @since  7.17.0
	 */
	private $templateId;

	/**
	 * Constructor.
	 *
	 * @param   string  $clientId               Client Id
	 * @param   string  $clientSecret           Client secret
	 * @param   string  $templateId             Template id
	 *
	 * @since   7.17.0
	 */
	public function __construct($clientId = '', $clientSecret = '', $templateId = '')
	{
		if ($clientId && $clientSecret && $templateId)
		{
			try
			{
				$this->client = new Google_Client();
				$uri          = JUri::getInstance();
				$redirect_uri = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port', 'path')) . '?option=com_csvi&view=template&layout=edit&csvi_template_id=' . $templateId;
				$this->client->setClientId($clientId);
				$this->client->setClientSecret($clientSecret);
				$this->client->setRedirectUri($redirect_uri);
				$this->client->setScopes(Google_Service_Sheets::SPREADSHEETS);
				$this->client->setApprovalPrompt('force');
				$this->client->setAccessType("offline");
				$this->client->setIncludeGrantedScopes(true);
				$this->templateId = $templateId;
			}
			catch (Exception $exception)
			{
				throw new CsviException($exception->getMessage());
			}
		}
	}

	/**
	 * Get client access token
	 *
	 * @param  string  $code                 Code to generate access token
	 * @param  array   $existingAccessToken  Existing token
	 *
	 * @return  String  Access token
	 *
	 * @since   7.17.0
	 *
	 * @throws  CsviException
	 */
	public function getAccessToken($code, $existingAccessToken)
	{
		$accessToken = '';

		if ($code)
		{
			$accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
		}
		else
		{
			if (!$this->checkAccessToken($existingAccessToken))
			{
				$accessToken = $this->client->getAccessToken();
			}
		}

		return $accessToken;
	}

	/**
	 * Check access token for validity
	 *
	 * @return  Mixed  Google authentication URL if expired else fresh access token
	 *
	 * @since   7.17.0
	 *
	 */
	public function checkAccessToken($existingAccessToken)
	{
		$accessTokenArray = $this->client->getAccessToken();

		if (!$accessTokenArray)
		{
			$accessTokenArray = (array) $existingAccessToken;
			$accessTokenArray = ($accessTokenArray[0]) ?? $accessTokenArray;
		}

		$redirect = false;

		if ($accessTokenArray)
		{
			$this->client->setAccessToken($accessTokenArray);

			if ($this->client->isAccessTokenExpired())
			{
				// Refresh the token if possible, else fetch a new one.
				if (isset($accessTokenArray['refresh_token']))
				{
					$accessToken = $this->client->fetchAccessTokenWithRefreshToken($accessTokenArray['refresh_token']);
					$this->client->setAccessToken($accessToken);
					$this->updateNewAccessToken($accessToken);
				}
				else
				{
					$redirect = true;
				}
			}
		}

		return $redirect;
	}

	/**
	 * Get client authorisation URL
	 *
	 * @return  string  Google authentication URL
	 *
	 * @since   7.17.0
	 *
	 */
	public function getAuthURL()
	{
		// Request authorization from the user.
		$this->authUrl = $this->client->createAuthUrl();

		return $this->authUrl;
	}

	/**
	 * Get client object
	 *
	 * @return  Object  Google client object
	 *
	 * @since   7.17.0
	 *
	 */
	public function getClient()
	{
		return $this->client;
	}

	/**
	 * Redirect to authenticate
	 *
	 * @since   7.17.0
	 *
	 * @throws  CsviException
	 */
	public function redirectToAuth()
	{
		JFactory::getApplication()->redirect($this->authUrl);
	}

	/**
	 * Update new access token to database
	 *
	 * @param array $accessToken Array of access token details
	 *
	 * @return mixed False if no template id
	 *
	 * @since   7.17.0
	 *
	 */
	private function updateNewAccessToken($accessToken)
	{
		if (!$this->templateId)
		{
			return false;
		}

		$table = JTable::getInstance('Template', 'Table');
		$table->load($this->templateId);
		$templateSettings                = json_decode($table->settings, true);
		$templateSettings['fulltoken']   = $accessToken;
		$templateSettings['accesstoken'] = $accessToken['access_token'];
		$newTemplateSettings             = json_encode($templateSettings);
		$table->csvi_template_id         = $this->templateId;
		$table->settings                 = $newTemplateSettings;
		$table->store();
	}
}
