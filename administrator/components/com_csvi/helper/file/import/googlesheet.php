<?php
/**
 * @package     CSVI
 * @subpackage  Google sheet
 *
 * @author      RolandD Cyber Produksi <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://csvimproved.com
 */

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

/**
 * CSV Google sheet importer.
 *
 * @package     CSVI
 * @subpackage  Google sheet file
 * @since       7.17.0
 */
class CsviHelperFileImportGooglesheet extends CsviHelperFile
{
	/**
	 * CSVI Template object
	 *
	 * @var    CsviHelperTemplate
	 * @since  7.17.0
	 */
	protected $template;

	/**
	 * The array pointer position
	 *
	 * @var    int
	 * @since  7.17.0
	 */
	private $pointer = 0;

	/**
	 * The fields handler
	 *
	 * @var    CsviHelperImportFields
	 * @since  7.17.0
	 */
	protected $fields;

	/**
	 * The service object
	 *
	 * @var    object
	 * @since  7.17.0
	 */
	protected $service;

	/**
	 * The spreadsheet id
	 *
	 * @var    object
	 * @since  7.17.0
	 */
	private $spreadsheetId;

	/**
	 * The range of columns
	 *
	 * @var    object
	 * @since  7.17.0
	 */
	private $range;

	/**
	 * Array of data
	 *
	 * @var    array
	 * @since  7.17.0
	 */
	private $dataRecords = array();

	/**
	 * The Google client object
	 *
	 * @var    object
	 * @since  7.17.0
	 */
	protected $client;

	/**
	 * The Existing Access Token
	 *
	 * @var    array
	 * @since  7.17.0
	 */
	protected $existingAccessToken;

	/**
	 * Construct the class and its settings.
	 *
	 * @param   CsviHelperTemplate $template An instance of CsviHelperTemplate
	 * @param   CsviHelperLog      $log      An instance of CsviHelperLog
	 * @param   CsviHelperCsvi     $helper   An instance of CsviHelperCsvi
	 * @param   JInput             $input    An instance of JInput
	 *
	 * @since   7.17.0
	 *
	 * @throws  CsviException
	 */
	public function __construct(CsviHelperTemplate $template, CsviHelperLog $log, CsviHelperCsvi $helper, JInput $input)
	{
		$db             = JFactory::getDbo();
		$this->template = $template;

		// Initiate the fields helper
		$className    = 'CsviHelper' . ucfirst($template->get('action')) . 'fields';
		$this->fields = new $className($template, $log, $db);

		// Set the client id and secret as set in template
		$clientId     = $this->template->get('clientid');
		$clientSecret = $this->template->get('clientsecret');

		if (!$this->template->get('accesstoken'))
		{
			throw new CsviException(Text::_('COM_CSVI_NO_ACCESS_TOKEN'));
		}

		$templateId                = $template->getId();
		$googleHelper              = new CsviHelperGoogle($clientId, $clientSecret, $templateId);
		$this->client              = $googleHelper->getClient();
		$this->existingAccessToken = json_decode(json_encode($this->template->get('fulltoken')), true);
		$this->client->setAccessToken($this->existingAccessToken);
		$checkAccessToken = $googleHelper->checkAccessToken($this->existingAccessToken);

		if ($checkAccessToken)
		{
			$googleHelper->redirectToAuth();
		}

		$this->service = new Google_Service_Sheets($this->client);

		// Get the spreadsheet details from template settings
		$this->spreadsheetId = $this->template->get('spreadsheetid');

		if (!$this->spreadsheetId)
		{
			throw new CsviException(Text::_('COM_CSVI_NO_SPREADSHEETID_SET'));
		}

		$sheetName = $this->template->get('sheetname');

		if (!$sheetName)
		{
			throw new CsviException(Text::_('COM_CSVI_NO_SPREADSHEET_SHEET_SET'));
		}

		$rangeFrom = $this->template->get('range_from');
		$rangeTo   = $this->template->get('range_to');

		$this->range = $sheetName;

		if ($rangeFrom)
		{
			$this->range .= '!' . $rangeFrom;
		}

		if ($rangeTo)
		{
			$this->range .= ':' . $rangeTo;
		}

		parent::__construct($template, $log, $helper, $input);
	}

	/**
	 * Open the file to read.
	 *
	 * @return  bool  Return true if file can be opened | False if file cannot be opened.
	 *
	 * @since   7.17.0
	 *
	 * @throws  CsviException
	 */
	public function openFile()
	{
		$this->client->setAccessToken($this->existingAccessToken);
		$response = $this->service->spreadsheets_values->get($this->spreadsheetId, $this->range);

		try
		{
			$this->dataRecords = $response->getValues();
		} catch (Exception $exception)
		{
			$this->log->addStats('incorrect', JText::_('COM_CSVI_ERROR_READING_GOOGLE_SHEET'));
			throw new CsviException($exception->getMessage(), 'error');
		}

		return true;
	}

	/**
	 * Get the file position.
	 *
	 * @return  int  The current position in the file.
	 *
	 * @since   7.17.0
	 */
	public function getFilePos()
	{
		return $this->pointer;
	}

	/**
	 * Set the file position.
	 *
	 * @param   int $position The position to move to
	 *
	 * @return  int if success | -1 if not success.
	 *
	 * @since   7.17.0
	 */
	public function setFilePos($position)
	{
		$result = current($this->dataRecords);

		if (!$result)
		{
			$this->pointer = $position;
		}

		return $result;
	}

	/**
	 * Load the column headers.
	 *
	 * @return   mixed    array when column headers are found | false if column headers cannot be read.
	 *
	 * @since   7.17.0
	 *
	 * @throws  UnexpectedValueException
	 */
	public function loadColumnHeaders()
	{
		$headers = $this->dataRecords[0];
		next($this->dataRecords);

		return $headers;
	}

	/**
	 * Read the next line in the google sheet.
	 *
	 * @param   bool $headers Set if the column headers are being read.
	 *
	 * @return  mixed  Array with the line of data read | false if data cannot be read.
	 *
	 * @since   7.17.0
	 *
	 * @throws  CsviException
	 * @throws  UnexpectedValueException
	 */
	public function readNextLine($headers = false)
	{
		// Make sure we have a records to process
		if (!$this->dataRecords)
		{
			throw new CsviException(JText::_('COM_CSVI_NO_RECORDS_FOUND'));
		}

		// Get the next record
		$dataCurrentRecords = current($this->dataRecords);

		// Check if there is any more data to process
		if (!$dataCurrentRecords)
		{
			return false;
		}

		$counters = array();

		$columnHeaders = $this->fields->getAllFieldnames();

		if (!empty($dataCurrentRecords))
		{
			foreach ($dataCurrentRecords as $key => $value)
			{
				if (isset($columnHeaders[$key]))
				{
					if (!isset($counters[$columnHeaders[$key]]))
					{
						$counters[$columnHeaders[$key]] = 0;
					}

					$counters[$columnHeaders[$key]]++;

					$this->fields->set($columnHeaders[$key], $value, $counters[$columnHeaders[$key]]);
				}
			}
		}

		// Move to the next record
		next($this->dataRecords);

		return $columnHeaders;
	}

	/**
	 * Process the Google sheet to import.
	 *
	 * @return  bool  True if file can be processed | False if file cannot be processed.
	 *
	 * @since   7.17.0
	 *
	 * @throws  CsviException
	 */
	public function processFile()
	{
		if (!$this->openFile())
		{
			return false;
		}

		return true;
	}

	/**
	 * Sets the file pointer back to beginning of array.
	 *
	 * Since there is no rewind available we start all over.
	 *
	 * @return  void.
	 *
	 * @since   7.17.0
	 *
	 * @throws  CsviException
	 */
	public function rewind()
	{
		$this->processFile();
	}

	/**
	 * Return the number of records in a Google sheet
	 *
	 * @return  int  The number of records in Google sheet
	 *
	 * @since   7.17.0
	 */
	public function lineCount()
	{
		return count($this->dataRecords);
	}
}
