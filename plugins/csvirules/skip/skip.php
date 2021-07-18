<?php
/**
 * @package     CSVI
 * @subpackage  Plugin.Skip
 *
 * @author      RolandD Cyber Produksi <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

defined('_JEXEC') or die;

/**
 * Skip values.
 *
 * @package     CSVI
 * @subpackage  Plugin.Skip
 * @since       7.0
 */
class PlgCsvirulesSkip extends RantaiPluginDispatcher
{
	/**
	 * The unique ID of the plugin
	 *
	 * @var    string
	 * @since  7.0
	 */
	private $id = 'csviskip';

	/**
	 * Return the name of the plugin.
	 *
	 * @return  array  The name and ID of the plugin.
	 *
	 * @since   7.0
	 */
	public function getName()
	{
		return array('value' => $this->id, 'text' => 'RO CSVI Skip');
	}

	/**
	 * Method to get the name only of the plugin.
	 *
	 * @param   string  $plugin  The ID of the plugin
	 *
	 * @return  string  The name of the plugin.
	 *
	 * @since   7.0
	 */
	public function getSingleName($plugin)
	{
		if ($plugin === $this->id)
		{
			return 'RO CSVI Skip';
		}

		return '';
	}

	/**
	 * Method to get the field options.
	 *
	 * @param   string  $plugin   The ID of the plugin
	 * @param   array   $options  An array of settings
	 *
	 * @return  string  The rendered form with plugin options.
	 *
	 * @since   7.0
	 * @throws  RuntimeException
	 * @throws  InvalidArgumentException
	 */
	public function getForm($plugin, $options = array())
	{
		if ($plugin === $this->id)
		{
			// Load the language files
			$lang = JFactory::getLanguage();
			$lang->load('plg_csvirules_skip', JPATH_ADMINISTRATOR, 'en-GB', true);
			$lang->load('plg_csvirules_skip', JPATH_ADMINISTRATOR, null, true);

			// Add the form path for this plugin
			JForm::addFormPath(JPATH_PLUGINS . '/csvirules/skip/');

			// Load the helper that renders the form
			$helper = new CsviHelperCsvi;

			// Instantiate the form
			$form = JForm::getInstance('skip', 'form_skip');

			// Bind any existing data
			$form->bind(array('pluginform' => $options));

			// Create some dummies
			$input = new JInput;

			// Set the correct fields after page has loaded
			JFactory::getDocument()->addScriptDeclaration(
<<<JS
			jQuery(function() {
				var method = jQuery('#pluginform_applywhenempty').val();

				if (method === '0')
				{
					Csvi.showFields(1, '#match_method #match_regex #match_values'); 
					jQuery('#pluginform_match_method').trigger('change'); 
				}
				else if (method === '1')
				{
					Csvi.showFields(0, '#match_method #match_regex #match_values');
				}
			});
JS
);

			// Render the form
			return $helper->renderCsviForm($form, $input);
		}
	}

	/**
	 * Run the rule.
	 *
	 * @param   string            $plugin    The ID of the plugin
	 * @param   object            $settings  The plugin settings set for the field
	 * @param   object            $field     The field being process
	 * @param   CsviHelperFields  $fields    All fields used for import/export
	 *
	 * @return  void.
	 *
	 * @since   7.0
	 */
	public function runRule($plugin, $settings, $field, $fields)
	{
		if ($plugin === $this->id && !empty($settings))
		{
			// Check if we have a multiple values
			$matchType        = $settings->match_regex;
			$matchValues      = $settings->match_values;
			$matchMultiValues = explode(',', $matchValues);

			// Set the old value
			$value = $field->value;

			// Check if we need to skip if empty
			if (!isset($settings->applywhenempty))
			{
				$settings->applywhenempty = 1;
			}

			// If the field is empty and applywhenempty setting is no then do nothing
			if ($settings->applywhenempty && '' === $value)
			{
				$fields->setProcessRecord(false);
				$fields->decrementLinenumber();
			}
			elseif (!$settings->applywhenempty)
			{
				switch ($settings->match_method)
				{
					case 'text':
						foreach ($matchMultiValues as $matchVal)
						{
							if (strpos($value, $matchVal) !== false)
							{
								$fields->setProcessRecord(false);
								$fields->decrementLinenumber();
							}
						}
						break;
					case 'regex':
						// Check if the delimiters set are valid
						if (!$this->validateRegex($matchType))
						{
							throw new RuntimeException(JText::_('COM_CSVI_REGEX_DELIMITERS_NO_MATCH'));
						}

						if (preg_match($matchType, $value, $matches))
						{
							$fields->setProcessRecord(false);
							$fields->decrementLinenumber();
						}
						break;
				}
			}
		}
	}

	/**
	 * Validate the regex delimiters.
	 *
	 * @param   string  $regex  The regular expression to validate
	 *
	 * @return  boolean  True on success | False on failure.
	 *
	 * @since   7.4.0
	 */
	private function validateRegex($regex)
	{
		if (!$regex)
		{
			return false;
		}

		// Check if the delimiters set are valid
		$firstCharacter = $regex[0];
		$lastCharacters = substr($regex, -2);
		$lastCharArray  = array(
			'[' => ']',
			'{' => '}',
			'(' => ')',
			'<' => '>'
		);

		// Match the first character with one of the last two characters
		if (stristr($lastCharacters, $firstCharacter))
		{
			return true;
		}

		// Match the last character against a list of known characters
		if ($lastCharArray[$firstCharacter] === $lastCharacters[0])
		{
			return true;
		}

		return false;
	}
}
