<?php
/**
 * @package     CSVI
 * @subpackage  Templates
 *
 * @author      RolandD Cyber Produksi <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

defined('_JEXEC') or die;

/**
 * Templates controller.
 *
 * @package     CSVI
 * @subpackage  Templates
 * @since       6.0
 */
class CsviControllerTemplate extends JControllerForm
{
	/**
	 * Hold the template ID
	 *
	 * @var    int
	 * @since  6.6.0
	 */
	private $templateId = 0;

	/**
	 * Handle the wizard steps.
	 *
	 * @return  void.
	 *
	 * @since   6.5.0
	 *
	 * @throws  Exception
	 */
	public function wizard()
	{
		$input = JFactory::getApplication()->input;
		$step = $input->getInt('step', 1);

		switch ($step)
		{
			case 1:
				// This step doesn't do anything as it is the first step in the process.
				break;
			default:
				// If there are mapped fields save them
				if ($input->get('mappedfields'))
				{
					/** @var CsviModelTemplate $model */
					$model = $this->getModel();
					$model->mapTemplateFields($input->getInt('csvi_template_id'), $input->get('mappedfields', '', 'RAW'));
				}

				if ($this->save())
				{
					$url = 'index.php?option=com_csvi&task=template.edit&step=' . $step . '&csvi_template_id=' . $this->templateId;
					$this->setRedirect($url, JText::_('COM_CSVI_LBL_TEMPLATE_SAVED'));
				}
				break;
		}
	}

	/**
	 * Function that allows child controller access to model data
	 * after the data has been saved.
	 *
	 * @param   JModelLegacy  $model      The data model object.
	 * @param   array         $validData  The validated data.
	 *
	 * @return  void
	 *
	 * @since   6.6.0
	 */
	protected function postSaveHook(JModelLegacy $model, $validData = array())
	{
		/** @var JModelLegacy templateId */
		$this->templateId = $model->getState('template.id');
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   6.6.0
	 *
	 * @throws  Exception
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$append = parent::getRedirectToItemAppend($recordId, $urlVar);
		$step   = $this->input->getInt('step', 0);

		// Setup redirect info.
		if ($step)
		{
			$append .= '&step=' . $step;
		}

		return $append;
	}

	/**
	 * Read the uploaded file headers
	 *
	 * @return  void
	 *
	 * @since   7.8.0
	 *
	 * @throws  Exception
	 */
	public function getUploadedFileHeaders()
	{
		if (!empty($_FILES))
		{
			if ($_FILES['file']['error'] === 0)
			{
				$tempFile = $_FILES['file']['tmp_name'];
				echo file($tempFile)[0];
			}
			else
			{
				echo $_FILES['file']['error'];
			}
		}

		JFactory::getApplication()->close();
	}

	/**
	 * Run the import/export from the template edit page.
	 *
	 * @return  void.
	 *
	 * @since   7.12.0
	 */
	public function runTemplate()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$data = $this->input->post->get('jform', array(), 'array');

		/** @var CsviModelTemplate $model */
		$model      = $this->getModel();
		$templateId = $model->getState('template.id');

		// if the template is not saved then don't proceed
		if (!$model->save($data))
		{
			$this->setRedirect('index.php?option=com_csvi&view=templates', 'COM_CSVI_NO_TEMPLATE_LOADED', 'error');
		}

		$template = $model->getItem($templateId);
		$action   = $template->options['action'];

		switch ($action)
		{
			case 'import':
				$importsModel = JModelLegacy::getInstance('Imports', 'CsviModel');
				$importsModel->initialise($templateId);

				// Prepare the logger
				$importsModel->initialiseLog();

				// Prepare the import run
				$runId = $importsModel->initialiseRun();

				// Redirect to the source view
				$this->setRedirect('index.php?option=com_csvi&task=importsource.source&runId=' . $runId);
				$this->redirect();
				break;
			case 'export':
				$exportsModel = JModelLegacy::getInstance('Exports', 'CsviModel');
				$exportsModel->initialise($templateId);
				$runId = $exportsModel->getRunId();
				$this->input->set('runId', $runId);
				$this->input->set('csvi_template_id', $templateId);

				require JPATH_COMPONENT_ADMINISTRATOR . '/controllers/export.php';
				$exportController = new CsviControllerExport();
				$exportController->start();
				break;
			default:
				break;
		}
	}
}
