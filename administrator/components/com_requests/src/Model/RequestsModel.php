<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Requests
 * @author     Nguyen Dinh <vb.dinhxuannguyen@gmail.com>
 * @copyright  2024 Nguyen Dinh
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Requests\Component\Requests\Administrator\Model;
// No direct access.
defined('_JEXEC') or die;

use \Joomla\CMS\MVC\Model\ListModel;
use \Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Helper\TagsHelper;
use \Joomla\Database\ParameterType;
use \Joomla\Utilities\ArrayHelper;
use Requests\Component\Requests\Administrator\Helper\RequestsHelper;

/**
 * Methods supporting a list of Requests records.
 *
 * @since  1.0.0
 */
class RequestsModel extends ListModel
{
	/**
	* Constructor.
	*
	* @param   array  $config  An optional associative array of configuration settings.
	*
	* @see        JController
	* @since      1.6
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
				'title', 'a.title',
				'description', 'a.description',
				'requester_id', 'a.requester_id',
				'requester_deparment', 'a.requester_deparment',
				'type_id', 'a.type_id',
				'status', 'a.status',
				'technician_id', 'a.technician_id',
				'tech_department', 'a.tech_department',
				'created_date', 'a.created_date',
				'start_date', 'a.start_date',
		'start_date.from', 'start_date.to',
				'end_date', 'a.end_date',
		'end_date.from', 'end_date.to',
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
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState("a.id", "ASC");

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
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string A store id.
	 *
	 * @since   1.0.0
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

		
		return parent::getStoreId($id);
		
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
		$query->from('`#__requests` AS a');
		
		// Join over the users for the checked out user
		$query->select("uc.name AS uEditor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		// Join over the user field 'modified_by'
		$query->select('`modified_by`.name AS `modified_by`');
		$query->join('LEFT', '#__users AS `modified_by` ON `modified_by`.id = a.`modified_by`');

		// Join over the user field 'requester_id'
		$query->select('`requester_id`.name AS `requester_id`');
		$query->join('LEFT', '#__users AS `requester_id` ON `requester_id`.id = a.`requester_id`');

		// Join over the user field 'technician_id'
		$query->select('`technician_id`.name AS `technician_id`');
		$query->join('LEFT', '#__users AS `technician_id` ON `technician_id`.id = a.`technician_id`');
		

		// Filter by published state
		$published = $this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif (empty($published))
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( a.title LIKE ' . $search . '  OR requester_id.name LIKE ' . $search . '  OR  a.type_id LIKE ' . $search . '  OR  a.status LIKE ' . $search . '  OR technician_id.name LIKE ' . $search . '  OR  a.created_date LIKE ' . $search . '  OR  a.start_date LIKE ' . $search . '  OR  a.end_date LIKE ' . $search . ' )');
			}
		}
		

		// Filtering requester_id
		$filter_requester_id = $this->state->get("filter.requester_id");

		if ($filter_requester_id !== null && !empty($filter_requester_id))
		{
			$query->where("a.`requester_id` = '".$db->escape($filter_requester_id)."'");
		}

		// Filtering type_id
		$filter_type_id = $this->state->get("filter.type_id");

		if ($filter_type_id !== null && (is_numeric($filter_type_id) || !empty($filter_type_id)))
		{
			$query->where("a.`type_id` = '".$db->escape($filter_type_id)."'");
		}

		// Filtering status
		$filter_status = $this->state->get("filter.status");

		if ($filter_status !== null && (is_numeric($filter_status) || !empty($filter_status)))
		{
			$query->where("a.`status` = '".$db->escape($filter_status)."'");
		}

		// Filtering technician_id
		$filter_technician_id = $this->state->get("filter.technician_id");

		if ($filter_technician_id !== null && !empty($filter_technician_id))
		{
			$query->where("a.`technician_id` = '".$db->escape($filter_technician_id)."'");
		}

		// Filtering start_date
		$filter_start_date_from = $this->state->get("filter.start_date.from");

		if ($filter_start_date_from !== null && !empty($filter_start_date_from))
		{
			$query->where("a.`start_date` >= '".$db->escape($filter_start_date_from)."'");
		}
		$filter_start_date_to = $this->state->get("filter.start_date.to");

		if ($filter_start_date_to !== null  && !empty($filter_start_date_to))
		{
			$query->where("a.`start_date` <= '".$db->escape($filter_start_date_to)."'");
		}

		// Filtering end_date
		$filter_end_date_from = $this->state->get("filter.end_date.from");

		if ($filter_end_date_from !== null && !empty($filter_end_date_from))
		{
			$query->where("a.`end_date` >= '".$db->escape($filter_end_date_from)."'");
		}
		$filter_end_date_to = $this->state->get("filter.end_date.to");

		if ($filter_end_date_to !== null  && !empty($filter_end_date_to))
		{
			$query->where("a.`end_date` <= '".$db->escape($filter_end_date_to)."'");
		}
		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', "a.id");
		$orderDirn = $this->state->get('list.direction', "ASC");

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();
		
		foreach ($items as $oneItem)
		{

			if (isset($oneItem->type_id))
			{
				$values    = explode(',', $oneItem->type_id);
				$textValue = array();

				foreach ($values as $value)
				{
					if (!empty($value))
					{
						$db = $this->getDbo();
						$query = "select id, name from #__request_types where id = '$value' AND state=1";
						$db->setQuery($query);
						$results = $db->loadObject();

						if ($results)
						{
							$textValue[] = $results->name;
						}
					}
				}

				$oneItem->type_id = !empty($textValue) ? implode(', ', $textValue) : $oneItem->type_id;
			}
					$oneItem->status = !empty($oneItem->status) ? Text::_('COM_REQUESTS_REQUESTS_STATUS_OPTION_' . preg_replace('/[^A-Za-z0-9\_-]/', '',strtoupper(str_replace(' ', '_',$oneItem->status)))) : '';
		}

		return $items;
	}
}
