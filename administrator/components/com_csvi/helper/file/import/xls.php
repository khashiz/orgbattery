<?php
/**
 * @package     CSVI
 * @subpackage  File
 *
 * @author      RolandD Cyber Produksi <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

defined('_JEXEC') or die;

use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * XLS file processor class.
 *
 * @package     CSVI
 * @subpackage  File
 * @since       6.0
 */
class CsviHelperFileImportXls extends CsviHelperFile
{
	/**
	 * The fields handler
	 *
	 * @var    CsviHelperImportFields
	 * @since  6.0
	 */
	protected $fields = null;

	/**
	 * The number of rows
	 *
	 * @var    int
	 * @since  7.13.0
	 */
	protected $numRows = 0;

	/**
	 * The number of columns
	 *
	 * @var    int
	 * @since  7.13.0
	 */
	protected $numCols = 0;

	/**
	 * The worksheet
	 *
	 * @var    int
	 * @since  7.13.0
	 */
	protected $workSheet = '';

	/**
	 * Open the file to read.
	 *
	 * @return   bool  Always returns true.
	 *
	 * @since   3.0
	 */
	public function openFile()
	{
		$this->fp = true;

		// Include the XLS reader
		require_once JPATH_ADMINISTRATOR . '/components/com_csvi/assets/spreadsheet_reader/vendor/autoload.php';

		// Get the file extension of the import file
		$fileDetails   = pathinfo($this->filename);
		$extension     = $fileDetails['extension'];
		$inputFileType = ($this->template->get('use_file_extension')) ? ucfirst($this->template->get('use_file_extension')) : ucfirst($extension);
		$reader        = IOFactory::createReader($inputFileType);
		$reader->setLoadAllSheets();
		$spreadsheet     = $reader->load($this->filename);
		$this->workSheet = $spreadsheet->getActiveSheet();
		$this->numRows   = $this->workSheet->getHighestDataRow();
		$this->numCols   = $this->workSheet->getHighestDataColumn();
		$this->data      = $spreadsheet->getActiveSheet()->toArray();

		return true;
	}

	/**
	 * Load the column headers from a file.
	 *
	 * @return  bool  Always return true.
	 *
	 * @since   3.0
	 */
	public function loadColumnHeaders()
	{
		$this->linepointer++;

		return $this->data[0];
	}

	/**
	 * Get the file position.
	 *
	 * @return  int	current position in the file.
	 *
	 * @since   3.0
	 */
	public function getFilePos()
	{
		return $this->linepointer;
	}

	/**
	 * Set the file position.
	 *
	 * @param   int  $pos  The position to move to
	 *
	 * @return  int  current position in the file.
	 *
	 * @since   3.0
	 */
	public function setFilePos($pos)
	{
		$this->linepointer = $pos;

		return $this->linepointer;
	}

	/**
	 * Read the next line in the file.
	 *
	 * @return  bool True if data read | false if data cannot be read.
	 *
	 * @since   3.0
	 */
	public function readNextLine()
	{
		if ($this->linepointer < $this->numRows)
		{
			$columnheaders = $this->fields->getAllFieldnames();

			if (isset($this->data[$this->linepointer]))
			{
				$newdata = $this->data[$this->linepointer];

				// Add the data to the fields
				$counters = array();

				foreach ($newdata as $key => $value)
				{
					if (isset($columnheaders[$key]))
					{
						if (!isset($counters[$columnheaders[$key]]))
						{
							$counters[$columnheaders[$key]] = 0;
						}

						$counters[$columnheaders[$key]]++;

						$this->fields->set($columnheaders[$key], $value, $counters[$columnheaders[$key]]);
					}
				}
			}

			$this->linepointer++;

			return true;
		}

		return false;
	}

	/**
	 * Process the file to import.
	 *
	 * @return  bool True if file can be processed.
	 *
	 * @since   3.0
	 */
	public function processFile()
	{
		// Open the file
		$this->openFile();

		return true;
	}

	/**
	 * Sets the file pointer back to beginning.
	 *
	 * @return  void.
	 *
	 * @since   3.0
	 */
	public function rewind()
	{
		$this->setFilePos(1);
	}

	/**
	 * Return the number of lines in a XLS file.
	 *
	 * @return  int	the number of lines in the XLS file.
	 *
	 * @since   6.0
	 */
	public function lineCount()
	{
		return count($this->data);
	}
}
