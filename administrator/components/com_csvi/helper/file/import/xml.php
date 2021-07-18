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

/**
 * XML file importer.
 *
 * @package     CSVI
 * @subpackage  File
 * @since       6.0
 */
class CsviHelperFileImportXml extends CsviHelperFile
{
	/**
	 * Contains the data that is read from file
	 *
	 * @var    XMLReader
	 * @since  3.0
	 */
	protected $data;

	/**
	 * Contains the record name to read from the XML
	 *
	 * @var    string
	 * @since  3.0
	 */
	private $recordName = '';

	/**
	 * Internal line pointer
	 *
	 * @var    int
	 * @since  3.0
	 */
	public $linepointer = 0;

	/**
	 * Holds the CSVI fields helper
	 *
	 * @var    CsviHelperImportFields
	 * @since  6.0
	 */
	protected $fields;

	/**
	 * Node tree
	 *
	 * @var    array
	 * @since  7.20.0
	 */
	private $nodeTree = [];

	/**
	 * Data tree
	 *
	 * @var    array
	 * @since  7.20.0
	 */
	private $dataTree = [];

	/**
	 * Set to count the lines in an XML file.
	 *
	 * @var    bool
	 *
	 * @since   7.20.0
	 */
	private $countOnly = false;


	/**
	 * Load the column headers from a file.
	 *
	 * @return   mixed    array when column headers are found | false if column headers cannot be read.
	 *
	 * @since   3.0
	 */
	public function loadColumnHeaders()
	{
		$columnheaders = array();
		$continue      = true;
		$line          = 0;

		// Make sure the file is loaded
		$this->openFile();

		// Start reading the XML file
		while ($this->data->read())
		{
			// Only read the chosen records
			if ($this->data->nodeType == XMLREADER::ELEMENT
				&& $this->data->name == $this->recordName
				&& $continue
			)
			{
				// Start reading the record
				while ($this->data->read() && $continue)
				{
					switch ($this->data->nodeType)
					{
						case (XMLREADER::ELEMENT):
							// Check if it has attributes
							if ($this->data->hasAttributes)
							{
								$parent[] = $this->data->name;

								// Get the attributes
								while ($this->data->moveToNextAttribute())
								{
									// The attribute name
									if (empty($parent))
									{
										$field_name = $this->data->name;
									}
									else
									{
										$field_name = implode('/', $parent) . '/' . $this->data->name;
									}

									$columnheaders[] = $field_name;
								}
							}
							elseif (!$this->data->isEmptyElement)
							{
								$parent[] = $this->data->name;
							}
							break;
						case (XMLREADER::END_ELEMENT):
							$line++;
							array_pop($parent);

							if ($this->data->name == $this->recordName)
							{
								$continue = false;
							}
							break;
						case XMLReader::TEXT:
						case XMLReader::CDATA:
							// The field name
							if (empty($parent))
							{
								$field_name = $this->data->name;
							}
							else
							{
								$field_name = implode('/', $parent);
							}

							$columnheaders[] = $field_name;
							break;
					}
				}
			}
			elseif (!$continue)
			{
				break;
			}
		}

		// Reset the internal pointer
		$this->rewind();

		return $columnheaders;
	}

	/**
	 * Get the file position.
	 *
	 * @return  int  Current position in the file.
	 *
	 * @since   3.0
	 */
	public function getFilePos()
	{
		return $this->linepointer;
	}

	/**
	 * Set the file position
	 *
	 * To be able to set the file position correctly, the XML reader needs to be at the start of the file.
	 *
	 * @param   int  $pos  The position to move to.
	 *
	 * @return  int  Current position in the file.
	 *
	 * @since   3.0
	 */
	public function setFilePos($pos)
	{
		// Close the XML reader
		$this->closeFile(false);

		// Open a new XML reader
		$this->processFile();

		// Move the pointer to the specified position
		return $this->skipXmlRecords($pos);
	}

	/**
	 * Close the file.
	 *
	 * @param   bool  $removeFolder  Set if the folder should be removed.
	 *
	 * @return  void.
	 *
	 * @since   3.0
	 */
	public function closeFile($removeFolder = true)
	{
		if ($this->data->close())
		{
			$this->fp = false;
		}

		$this->closed = true;

		parent::closeFile($removeFolder);
	}

	public function readNextLine()
	{
		$this->nodeTree = [];
		$this->dataTree = [];
		$parents        = [];
		$counter        = -1;
		$depth          = 0;
		$hasChildren    = false;
		$hasData        = false;
		$elementValue   = '';

		while ($read = $this->data->read())
		{
			$isRecord = $this->data->nodeType === XMLREADER::ELEMENT && $this->data->name === $this->recordName;

			if ($depth === 0 && $isRecord)
			{
				$depth = $this->data->depth;
			}

			if ($isRecord)
			{
				$counter++;
				$this->linepointer++;

				if ($this->countOnly)
				{
					return true;
				}
			}

			if ($counter === 1)
			{
				break;
			}

			if (
				$this->data->hasAttributes
				&& $this->data->nodeType === XMLReader::ELEMENT
				&& ($this->data->name === $this->recordName)
			)
			{
				while ($this->data->moveToNextAttribute())
				{
					$parent                     = implode('/', $parents);
					$attributeName              = $this->data->name;
					$this->nodeTree[$counter][] = $parent ? $parent . '/' . $attributeName : $attributeName;
					$this->dataTree[$counter][] = $this->data->value;
				}
			}

			if ($depth === 0 || $this->data->name === $this->recordName)
			{
				continue;
			}

			switch ($this->data->nodeType)
			{
				case XMLReader::ELEMENT:
					$parentName  = $this->data->name;
					$parentEmpty = false;

					if ($this->data->isEmptyElement === true)
					{
						$parent                     = implode('/', $parents);
						$this->nodeTree[$counter][] = $parent ? $parent . '/' . $parentName : $parentName;
						$this->dataTree[$counter][] = '';
						$parentEmpty                = true;
					}

					if ($parentName && $parentEmpty === false)
					{
						$parents[] = $parentName;
					}

					if ($this->data->hasAttributes)
					{
						while ($this->data->moveToNextAttribute())
						{
							$parent        = implode('/', $parents);
							$attributeName = $this->data->name;

							if ($parentEmpty)
							{
								$attributeName = $parentName . '/' . $attributeName;
							}

							$this->nodeTree[$counter][] = $parent ? $parent . '/' . $attributeName : $attributeName;
							$this->dataTree[$counter][] = $this->data->value;
						}
					}

					if (count($parents) > 1)
					{
						$hasChildren = true;
					}

					$hasData      = false;
					$elementValue = '';
					break;
				case XMLReader::END_ELEMENT:
					$parent = implode('/', $parents);

					if ($hasChildren || $hasData)
					{
						$this->nodeTree[$counter][] = $parent;
						$this->dataTree[$counter][] = $elementValue;
					}

					array_pop($parents);

					if (count($parents) === 1)
					{
						$hasChildren = false;
					}

					$hasData      = false;
					$elementValue = '';
					break;
				case XMLReader::TEXT:
				case XMLReader::CDATA:
					$hasData      = true;
					$elementValue .= $this->data->value;
					break;
			}
		}

		if ($read)
		{
			$filePosition = $this->getFilePos();
			$this->rewind();
			$this->setFilePos(--$filePosition);
		}

		if ($this->fields !== null)
		{
			foreach ($this->nodeTree as $index => $tree)
			{
				foreach ($tree as $key => $fieldName)
				{
					if (is_array($this->dataTree[$index][$key]))
					{
						foreach ($this->dataTree[$index][$key] as $counter => $value)
						{
							$this->fields->set($fieldName, $value, $counter);
						}
					}
					else
					{
						$this->fields->set($fieldName, $this->dataTree[$index][$key]);
					}
				}
			}
		}

		return $this->dataTree !== [];
	}

	/**
	 * Open the file to read.
	 *
	 * @return  bool  Returns true.
	 *
	 * @since   3.0
	 */
	public function openFile()
	{
		if (!$this->fp)
		{
			// Use a streaming approach to support large files
			$this->data = new XMLReader;
			$this->fp   = $this->data->open($this->filename);

			if ($this->fp === false)
			{
				$this->log->addStats('incorrect', JText::_('COM_CSVI_ERROR_XML_READING_FILE'));
				$app = JFactory::getApplication();
				$app->enqueueMessage(JText::_('COM_CSVI_ERROR_XML_READING_FILE'), 'error');

				return false;
			}
		}

		return true;
	}

	/**
	 * Process the file to import.
	 *
	 * @return  bool  Always returns true.
	 *
	 * @since   3.0
	 */
	public function processFile()
	{
		// Open the file
		if ($this->openFile())
		{
			// Set the record name
			$this->recordName = $this->template->get('xml_record_name', 'general');

			return true;
		}

		return false;
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
		$this->linepointer = 0;

		// Close the file, so we can start reading from the beginning
		if ($this->data->close())
		{
			$this->fp = false;
		}

		$this->processFile();
	}

	/**
	 * Advances the file pointer 1 forward.
	 *
	 * @param   bool  $preview  True if called from preview.
	 *
	 * @return  void.
	 *
	 * @since   3.0
	 */
	public function next($preview = false)
	{
		if (!$preview)
		{
			// Read one line and discard it
			$this->readNextLine();
		}
	}

	/**
	 * Skips through the XML file until the the required number 'record' nodes has been read
	 * Assume the file pointer is at the start of file.
	 *
	 * @param   int  $pos  The number of records to skip.
	 *
	 * @return  bool  True if records are skipped | false if records are not skipped.
	 *
	 * @since   3.0
	 */
	private function skipXmlRecords($pos)
	{
		$this->log->add('Forwarding to position: ' . $pos, false);

		// Check whether the pointer needs to be moved
		if ($pos <= 0)
		{
			return true;
		}

		$count = 0;

		while ($this->data->read())
		{
			// Searching for a valid record - must be the start of a node and in the list of valid record types
			if ($this->data->nodeType == XMLREADER::ELEMENT && $this->data->name == $this->recordName)
			{
				// Found a valid record
				while ($this->data->nodeType == XMLREADER::ELEMENT && $this->data->name == $this->recordName)
				{
					// Node is a valid record type - skip to the end of the record
					$this->data->next();
					$count++;

					if ($count == $pos)
					{
						$this->linepointer = $pos;

						return true;
					}
				}
			}
			else
			{
				// Not found - try again
				continue;
			}
		}

		// Hit EOF before skipping the required number of records
		return false;
	}

	/**
	 * Returns the number of lines.
	 *
	 * @return  int  Returns the number of lines in the file.
	 *
	 * @since   6.0
	 */
	public function linecount()
	{
		// Set number of lines
		$linecount = 0;

		if ($this->fp)
		{
			$currentPosition = $this->getFilePos();

			$this->rewind();

			$this->countOnly = true;

			while ($this->readNextLine())
			{
				$linecount++;
			}

			$this->setFilePos($currentPosition);
		}

		return $linecount;
	}

	/**
	 * Return the node tree.
	 *
	 * @return  array  The list of nodes.
	 *
	 * @since   7.20.0
	 */
	public function getNodeTree(): array
	{
		return $this->nodeTree;
	}

	/**
	 * Return the data tree.
	 *
	 * @return  array  The list of values.
	 *
	 * @since   7.20.0
	 */
	public function getDataTree(): array
	{
		return $this->dataTree;
	}
}
