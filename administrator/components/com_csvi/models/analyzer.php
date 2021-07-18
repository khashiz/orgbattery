<?php
/**
 * @package     CSVI
 * @subpackage  Analyzer
 *
 * @author      RolandD Cyber Produksi <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die;

/**
 * Analyzer model.
 *
 * @package     CSVI
 * @subpackage  Analyzer
 * @since       6.0
 */
class CsviModelAnalyzer extends JModelLegacy
{
	/**
	 * The file to analyze
	 *
	 * @var    string
	 * @since  5.3.3
	 */
	private $filename = null;

	/**
	 * The real filename of the file to analyze
	 *
	 * @var    string
	 * @since  6.0
	 */
	private $realname = null;

	/**
	 * Number of lines to check
	 *
	 * @var    int
	 * @since  5.3.3
	 */
	private $lines = 3;

	/**
	 * Set if file has column headers
	 *
	 * @var    bool
	 * @since  5.3.3
	 */
	private $columnheader = true;

	/**
	 * Set if file has a BOM
	 *
	 * @var    bool
	 * @since  5.3.3
	 */
	private $bom = false;

	/**
	 * List of errors encountered after checking the CSV file
	 *
	 * @var    array
	 * @since  5.3.3
	 */
	private $csverrors = array();

	/**
	 * List of messages to show
	 *
	 * @var    array
	 * @since  5.3.3
	 */
	private $messages = array();

	/**
	 * List of recommendations
	 *
	 * @var    array
	 * @since  5.3.3
	 */
	private $recommend = array();

	/**
	 * The data from the CSV file
	 *
	 * @var    string
	 * @since  5.3.3
	 */
	private $data = '';

	/**
	 * Text enclosure found
	 *
	 * @var    string
	 * @since  5.3.3
	 */
	private $textEnclosure = '"';

	/**
	 * Field delimiter found
	 *
	 * @var    string
	 * @since  5.3.3
	 */
	private $fieldDelimiter = null;

	/**
	 * The fields found in the CSV file
	 *
	 * @var    array
	 * @since  5.3.3
	 */
	private $fields = array();

	/**
	 * The CSV data
	 *
	 * @var    array
	 * @since  5.3.3
	 */
	private $csvdata = array();

	/**
	 * The XML record to process
	 *
	 * @var    string
	 * @since  6.0
	 */
	private $recordname = array();

	/**
	 * Analyze the uploaded file.
	 *
	 * @return  object  List of analyzer results.
	 *
	 * @since   5.3.3
	 */
	public function analyze()
	{
		$extension = 'csv';

		if ($this->prepare())
		{
			// Check the type of file to analyze
			$extension = JFile::getExt($this->realname);

			switch ($extension)
			{
				case 'xml':
					$template   = new CsviHelperTemplate(0);
					$db         = $this->getDbo();
					$settings   = new CsviHelperSettings($db);
					$log        = new CsviHelperLog($settings, $db);
					$csvihelper = new CsviHelperCsvi;
					$input      = Factory::getApplication()->input;
					$template->set('xml_record_name', $this->recordname);
					$xmlParser = new CsviHelperFileImportXml(
						$template, $log, $csvihelper, $input
					);
					$xmlParser->setFilename($this->filename);
					$xmlParser->processFile();
					$nodeTree = [];
					$dataTree = [];

					for ($lines = 0; $lines < $this->lines; $lines++)
					{
						$result   = $xmlParser->readNextLine();
						$nodeTree = array_merge($nodeTree, $xmlParser->getNodeTree());
						$dataTree = array_merge($dataTree, $xmlParser->getDataTree());

						if (!$result)
						{
							break;
						}
					}

					$data = [];

					foreach ($nodeTree as $node => $nodes)
					{
						if (!array_key_exists($node, $data))
						{
							$data[$node] = [];
						}

						foreach ($nodes as $key => $path)
						{
							if (!array_key_exists($path, $data[$node]))
							{
								$data[$node][$path] = $dataTree[$node][$key];

								continue;
							}

							if (!is_array($data[$node][$path]))
							{
								$value = $data[$node][$path];

								$data[$node][$path]   = [];
								$data[$node][$path][] = $value;
							}

							$data[$node][$path][] = $dataTree[$node][$key];
						}
					}

					$this->csvdata = $data;
					break;
				default:
					$this->analyzeCsv();
					break;
			}
		}

		// Combine the data for showing
		$items            = new stdClass;
		$items->extension = $extension;
		$items->csverrors = $this->csverrors;
		$items->messages  = $this->messages;
		$items->fields    = $this->fields;
		$items->csvdata   = $this->csvdata;
		$items->recommend = $this->recommend;

		return $items;
	}

	/**
	 * Prepare the file for analyzes.
	 *
	 * @return  bool  True if file can be analyzed | False if file cannot be analyzed.
	 *
	 * @since   6.0
	 */
	private function prepare()
	{
		$jinput   = JFactory::getApplication()->input;
		$filename = $jinput->files->get('filename');

		// Assign the local values
		if ($filename['error'] > 0)
		{
			$this->csverrors[] = JText::_('COM_CSVI_ANALYZER_NO_FILE');

			return false;
		}

		$this->filename     = $filename['tmp_name'];
		$this->realname     = $filename['name'];
		$this->columnheader = $jinput->get('columnheader', false, 'bool');
		$this->lines        = $jinput->get('lines', 3, 'int');
		$this->recordname   = $jinput->get('recordname');

		return true;
	}

	/**
	 * Analyze a CSV file.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	private function analyzeCsv()
	{
		// Read the file
		$handle = fopen($this->filename, "r");

		if ($handle)
		{
			// Get the first line
			$this->data = fread($handle, 4096);

			// Check for Mac line-ending
			if ($this->checkMac())
			{
				// Reload the file
				fclose($handle);
				$handle     = fopen($this->filename, "r");
				$this->data = fread($handle, 4096);
			}

			// Check for BOM
			$this->checkBom();

			// Find delimiters
			$this->findDelimiters();

			// Find fields
			$this->findFields($handle);

			// Find data
			for ($i = 0; $i < $this->lines; $i++)
			{
				$this->findData($handle);
			}

			fclose($handle);
		}
	}

	/**
	 * Check if the file has Mac line-endings.
	 *
	 * @return  bool  True if it has | False if it doesn't.
	 *
	 * @since   6.0
	 */
	private function checkMac()
	{
		$matches = array();

		// Check Windows first
		$total = preg_match('/\r\n/', $this->data, $matches);

		if (!$total)
		{
			preg_match('/\r/', $this->data, $matches);

			if (!empty($matches))
			{
				$this->csverrors['MACLINE'] = JText::_('COM_CSVI_ANALYZER_MAC_LINE');
				$this->recommend[]          = JText::_('COM_CSVI_ANALYZER_MAC_LINE_RECOMMEND');

				// Set auto detect to handle the rest of the file
				ini_set('auto_detect_line_endings', true);

				return true;
			}
		}

		return false;
	}

	/**
	 * Check if the file has a Byte Order Mark.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	private function checkBom()
	{
		if (strlen($this->data) > 3)
		{
			if (ord($this->data[0]) == 239 && ord($this->data[1]) == 187 && ord($this->data[2]) == 191)
			{
				$this->csverrors['BOM'] = JText::_('COM_CSVI_ANALYZER_BOM_FOUND');
				$this->bom              = true;
				$this->data             = substr($this->data, 3, strlen($this->data));
			}
		}
	}

	/**
	 * Find the delimiters used.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	private function findDelimiters()
	{
		// 1. Is the user using text enclosures
		$first_char = substr($this->data, 0, 1);
		$pattern    = '/[a-zA-Z0-9_ ]/';
		$matches    = array();
		preg_match($pattern, $first_char, $matches);

		if (count($matches) == 0)
		{
			// User is using text delimiter
			$this->textEnclosure = $first_char;
			$this->messages[]    = JText::sprintf('COM_CSVI_ANALYZER_TEXT_ENCLOSURE', $first_char);

			// 2. What field delimiter is being used
			$match_next_char = strpos($this->data, $this->textEnclosure, 1);
			$second_char     = substr($this->data, $match_next_char + 1, 1);

			if ($first_char == $second_char)
			{
				$this->csverrors['NOFIELD'] = JText::_('COM_CSVI_ANALYZER_FIELD_DELIMITER_NOT_FOUND');
			}
			else
			{
				$this->fieldDelimiter = $second_char;

				$this->messages[] = JText::sprintf('COM_CSVI_ANALYZER_FIELD_DELIMITER', $second_char);
			}
		}
		else
		{
			// Check for tabs
			$tabs = preg_match('/\t/', $this->data, $matches);

			if ($tabs)
			{
				$this->fieldDelimiter = "\t";
				$this->messages[]     = JText::sprintf(
					'COM_CSVI_ANALYZER_FIELD_DELIMITER',
					JText::_('COM_CSVI_ANALYZER_TAB')
				);
			}
			else
			{
				$totalchars = strlen($this->data);

				// 2. What field delimiter is being used
				for ($i = 0; $i <= $totalchars; $i++)
				{
					$current_char = substr($this->data, $i, 1);

					preg_match($pattern, $current_char, $matches);

					if (count($matches) == 0)
					{
						$this->fieldDelimiter = $current_char;

						$this->messages[] = JText::sprintf('COM_CSVI_ANALYZER_FIELD_DELIMITER', $current_char);
						$i                = $totalchars;
					}
				}
			}

			if (is_null($this->fieldDelimiter))
			{
				$this->csverrors['NOFIELD'] = JText::_('COM_CSVI_ANALYZER_FIELD_DELIMITER_NOT_FOUND');
			}
		}
	}

	/**
	 * Find the fields used in the CSV file.
	 *
	 * @param   resource  $handle  The file handle.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	private function findFields($handle)
	{
		rewind($handle);
		$data = fgetcsv($handle, 1000, $this->fieldDelimiter, $this->textEnclosure);

		if ($this->columnheader)
		{
			if ($data !== false)
			{
				if ($this->bom)
				{
					$data[0] = substr($data[0], 3, strlen($data[0]));
				}

				$this->fields = $data;

				// Check the fields for any _id fields
				foreach ($this->fields as $field)
				{
					if (substr($field, -3) == '_id')
					{
						$this->recommend[] = JText::sprintf('COM_CSVI_ANALYZER_FIELD_RECOMMEND', $field);
					}
				}
			}
			else
			{
				$this->csverrors['NOREAD'] = JText::_('COM_CSVI_ANALYZER_NO_READ');
			}
		}
	}

	/**
	 * Read the data from the CSV file.
	 *
	 * @param   resource  $handle  The file handle.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	private function findData($handle)
	{
		$data = fgetcsv($handle, 0, $this->fieldDelimiter, $this->textEnclosure);

		if ($data !== false)
		{
			if ($this->columnheader)
			{
				if (count($this->fields) > count($data))
				{
					$this->csverrors['NODATA'] = JText::_('COM_CSVI_ANALYZER_MORE_FIELDS');
					$this->recommend[]         = JText::_('COM_CSVI_ANALYZER_MORE_FIELDS_RECOMMEND');
				}
				elseif (count($this->fields) < count($data))
				{
					$this->csverrors['NODATA'] = JText::_('COM_CSVI_ANALYZER_LESS_FIELDS');
					$this->recommend[]         = JText::_('COM_CSVI_ANALYZER_LESS_FIELDS_RECOMMEND');
				}
			}

			$this->csvdata[] = $data;
		}
		else
		{
			if (!feof($handle))
			{
				$this->csverrors['NOREAD'] = JText::_('COM_CSVI_ANALYZER_NO_READ');
			}
		}
	}
}
