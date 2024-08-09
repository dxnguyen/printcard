<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Students
 * @author     Nguyen Dinh <vb.dinhxuannguyen@gmail.com>
 * @copyright  2023 Nguyen Dinh
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Student\Component\Students\Site\View\Ecard;
// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Uri\Uri;

/**
 * View class for a list of Students.
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
	protected $items;

	protected $pagination;

	protected $state;

	protected $params;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$app = Factory::getApplication();

        $session = Factory::getSession();
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
        $sViewECard = @$session->get('canViewECard');

        if (empty($_GET['layout'])) {
            if ($sViewECard != base64_encode(@$this->items['IdCardNumber'])) {
                $bday = (int)$this->items['BDay'];
                $bmonth = (int)$this->items['BMonth'];
                $bday = ($bday > 0 && $bday <= 9) ? '0' . $bday : $bday;
                $bmonth = ($bmonth > 0 && $bmonth <= 9) ? '0' . $bmonth : $bmonth;
                $byear = (int)$this->items['BYear'];
                $birthday = $bday . $bmonth . $byear;
                $session->set('sBirthday', $birthday);
                $IdCardNumber = base64_encode($this->items['IdCardNumber']);
                $checkViewUrl = URI::root() . 'e-card?layout=checkview&code=' . $IdCardNumber;
                $app->redirect($checkViewUrl);
            }
        }

		$this->pagination = $this->get('Pagination');
		$this->params = $app->getParams('com_students');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');


		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \Exception(implode("\n", $errors));
		}

		$this->_prepareDocument();
		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function _prepareDocument()
	{
		$app   = Factory::getApplication();
		$menus = $app->getMenu();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', Text::_('COM_STUDENTS_DEFAULT_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = Text::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

		
            // Add Breadcrumbs
            $pathway = $app->getPathway();
                        $breadcrumbTitle = Text::_('COM_STUDENTS_TITLE_STUDENTS');

                        if(!in_array($breadcrumbTitle, $pathway->getPathwayNames())) {
                            $pathway->addItem($breadcrumbTitle);
                        }
                
	}

	/**
	 * Check if state is set
	 *
	 * @param   mixed  $state  State
	 *
	 * @return bool
	 */
	public function getState($state)
	{
		return isset($this->state->{$state}) ? $this->state->{$state} : false;
	}
}
