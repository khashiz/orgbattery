<?php
/**
* @package RSSeo!
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class rsseoViewReport extends JViewLegacy
{	
	public function display($tpl = null) {
		$layout = $this->getLayout();
		
		if ($layout == 'generate') {
			$this->data			= $this->get('Data');
			$this->config		= rsseoHelper::getConfig();
			$this->statistics	= $this->get('Statistics');
			$this->lcrawled		= $this->get('LastCrawled');
			$this->mvisited		= $this->get('MostVisited');
			$this->notitle		= $this->get('NoTitle');
			$this->nodesc		= $this->get('NoDesc');
			$this->elinks		= $this->get('ErrorLinks');
			$this->competitors	= $this->get('Competitors');
			$this->keywords		= $this->get('GKeywords');
		} else {
			JHtml::_('formbehavior.chosen', '.advancedSelect');
			
			$this->form 		= $this->get('Form');
			$this->tabs 		= $this->get('Tabs');
		
			$this->addToolBar();
		}
		
		ob_start();
		parent::display($tpl);
		
		if ($layout == 'generate') {
			JFactory::getDocument()->setMimeEncoding('application/pdf');
			require_once JPATH_ADMINISTRATOR.'/components/com_rsseo/helpers/pdf.php';
			$out = ob_get_clean();
			
			$filename = JText::_('COM_RSSEO_REPORT_FILENAME').' '.JHtml::_('date', 'now', 'Y-m-d H:i:s').'.pdf';
			
			$pdf = RsseoPDF::getInstance();
			$pdf->render($filename, $out);
			die;
		}
	}
	
	protected function addToolBar() {
		JToolBarHelper::title(JText::_('COM_RSSEO_REPORT'),'rsseo');
		
		JToolBarHelper::apply('report.save');
		JToolBarHelper::link('index.php?option=com_rsseo&view=report&layout=generate', JText::_('COM_RSSEO_REPORT_GENERATE'), 'cog');
		
		if (JFactory::getUser()->authorise('core.admin', 'com_rsseo'))
			JToolBarHelper::preferences('com_rsseo');
	}
}