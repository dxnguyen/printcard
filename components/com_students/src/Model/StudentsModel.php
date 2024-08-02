<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Students
 * @author     Nguyen Dinh <vb.dinhxuannguyen@gmail.com>
 * @copyright  2023 Nguyen Dinh
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Student\Component\Students\Site\Model;
// No direct access.
defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\Model\ListModel;
use \Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use \Joomla\CMS\Helper\TagsHelper;
use \Joomla\CMS\Layout\FileLayout;
use \Joomla\Database\ParameterType;
use \Joomla\Utilities\ArrayHelper;
use \Student\Component\Students\Site\Helper\StudentsHelper;
use Joomla\CMS\Uri\Uri;
use Picqer\Barcode\BarcodeGeneratorPNG;

/**
 * Methods supporting a list of Students records.
 *
 * @since  1.0.0
 */
class StudentsModel extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see    JController
	 * @since  1.0.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'state', 'a.state',
				'ordering', 'a.ordering',
				'created_by', 'a.created_by',
				'modified_by', 'a.modified_by',
				'masv', 'a.masv',
				'cccd', 'a.cccd',
				'name', 'a.name',
				'shool_name', 'a.shool_name',
				'image', 'a.image',
				'address', 'a.address',
				'building_group', 'a.building_group',
				'building', 'a.building',
				'room', 'a.room',
				'birthday', 'a.birthday',
				'phone', 'a.phone',
			);
		}

		parent::__construct($config);
	}

	

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 *
	 * @since   1.0.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState("a.id", "ASC");

		$app = Factory::getApplication();
		$list = $app->getUserState($this->context . '.list');

		$value = $app->getUserState($this->context . '.list.limit', $app->get('list_limit', 25));
		$list['limit'] = $value;
		
		$this->setState('list.limit', $value);

		$value = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $value);

		$ordering  = $this->getUserStateFromRequest($this->context .'.filter_order', 'filter_order', "a.id");
		$direction = strtoupper($this->getUserStateFromRequest($this->context .'.filter_order_Dir', 'filter_order_Dir', "ASC"));
		
		if(!empty($ordering) || !empty($direction))
		{
			$list['fullordering'] = $ordering . ' ' . $direction;
		}

		$app->setUserState($this->context . '.list', $list);

		

		$context = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $context);

		// Split context into component and optional section
		if (!empty($context))
		{
			$parts = FieldsHelper::extract($context);

			if ($parts)
			{
				$this->setState('filter.component', $parts[0]);
				$this->setState('filter.section', $parts[1]);
			}
		}
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  DatabaseQuery
	 *
	 * @since   1.0.0
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
			'list.select', 'DISTINCT a.*'
			)
		);

		$query->from('`#__students` AS a');
			
		// Join over the users for the checked out user.
		$query->select('uc.name AS uEditor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the created by field 'created_by'
		$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

		// Join over the created by field 'modified_by'
		$query->join('LEFT', '#__users AS modified_by ON modified_by.id = a.modified_by');
			
		if (!Factory::getApplication()->getIdentity()->authorise('core.edit', 'com_students'))
		{
			$query->where('a.state = 1');
		}
		else
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in title
			$search = $this->getState('filter.search');
			if (!empty($search)) {
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( a.cccd LIKE ' . $search . '  OR  a.masv LIKE ' . $search . '  OR  a.phone LIKE ' . $search . ' )');
			} else if (!empty($_GET['code'])) {
				$query->where('a.masv = ' . $db->Quote($db->escape($_GET['code'], true)));
			}

			return $query;
	}

	/**
	 * Method to get an array of data items
	 *
	 * @return  mixed An array of data on success, false on failure.
	 */
	public function getItems()
	{
		$db          = $this->getDbo();
		$search      = $this->getState('filter.search');
		$studentCode = !empty($_GET['code']) ? $_GET['code'] : '';
		$code    = !empty($search) ? $db->escape($search, true) : $studentCode;
		$domain  = 'http://apihealthcare.ktxhcm.edu.vn';
		$api_key = getInfoweb()->web_api;
		$token   = @$this->getTokenApi(trim($api_key));
		$items   = array();
		if (!empty($code)) {
			$curl_handle = curl_init();
			$url = $domain."/api/health-care/get-student-detail?idCardNumber=".$code;
			curl_setopt($curl_handle, CURLOPT_URL, $url);
			curl_setopt ($curl_handle, CURLOPT_HTTPHEADER, array ('auth_key: '.$token));
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
			$curl_data = curl_exec($curl_handle);
			curl_close($curl_handle);
			$result = json_decode($curl_data, true);

			if ($result['data']) {
                $idCardNumber = $result['data']['IdCardNumber'];
                $PNG_WEB_DIR = 'uploads/barcode';
                $barcodeFile = $PNG_WEB_DIR.'/'.time().'_' . $idCardNumber . '.png';
                require 'vendor/autoload.php';
                $generator = new BarcodeGeneratorPNG();
                $barcode = $generator->getBarcode($idCardNumber, $generator::TYPE_CODE_128);
                if (file_put_contents($barcodeFile, $barcode)) {
                    $result['data']['barcode'] = basename($barcodeFile);
                }
                //QRCODE
                require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."functions.php";
                $url = 'https://id.ktxhcm.edu.vn/scan?code=' . $idCardNumber;
                $qrcode = @qrCode($url, $idCardNumber);
                $result['data']['qrcode'] = $qrcode;

                $items = $result['data'];
			}
		}

		return $items;
	}

	public function getTokenApi($api_key) {
		$domain      = 'http://apihealthcare.ktxhcm.edu.vn';
		$url         = $domain."/api/health-care/get-token";
		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt ($curl_handle, CURLOPT_HTTPHEADER, array ('api_key: '.$api_key));
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
		$curl_data = curl_exec($curl_handle);
		curl_close($curl_handle);
		$result = json_decode($curl_data, true);
		return $result['data']['token'];
	}

	/**
	 * Overrides the default function to check Date fields format, identified by
	 * "_dateformat" suffix, and erases the field if it's not correct.
	 *
	 * @return void
	 */
	protected function loadFormData()
	{
		$app              = Factory::getApplication();
		$filters          = $app->getUserState($this->context . '.filter', array());
		$error_dateformat = false;

		foreach ($filters as $key => $value)
		{
			if (strpos($key, '_dateformat') && !empty($value) && $this->isValidDate($value) == null)
			{
				$filters[$key]    = '';
				$error_dateformat = true;
			}
		}

		if ($error_dateformat)
		{
			$app->enqueueMessage(Text::_("COM_STUDENTS_SEARCH_FILTER_DATE_FORMAT"), "warning");
			$app->setUserState($this->context . '.filter', $filters);
		}

		return parent::loadFormData();
	}

	/**
	 * Checks if a given date is valid and in a specified format (YYYY-MM-DD)
	 *
	 * @param   string  $date  Date to be checked
	 *
	 * @return bool
	 */
	private function isValidDate($date)
	{
		$date = str_replace('/', '-', $date);
		return (date_create($date)) ? Factory::getDate($date)->format("Y-m-d") : null;
	}
}
