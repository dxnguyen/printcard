<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Students
 * @author     Nguyen Dinh <vb.dinhxuannguyen@gmail.com>
 * @copyright  2023 Nguyen Dinh
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Student\Component\Students\Administrator\Model;
// No direct access.
defined('_JEXEC') or die;

use \Joomla\CMS\MVC\Model\ListModel;
use \Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Helper\TagsHelper;
use \Joomla\Database\ParameterType;
use \Joomla\Utilities\ArrayHelper;
use Student\Component\Students\Administrator\Helper\StudentsHelper;

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
		'birthday.from', 'birthday.to',
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
		$query->from('`#__students` AS a');
		
		// Join over the users for the checked out user
		$query->select("uc.name AS uEditor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		// Join over the user field 'modified_by'
		$query->select('`modified_by`.name AS `modified_by`');
		$query->join('LEFT', '#__users AS `modified_by` ON `modified_by`.id = a.`modified_by`');
		

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
				$query->where('( a.masv LIKE ' . $search . '  OR  a.cccd LIKE ' . $search . '  OR  a.name LIKE ' . $search . '  OR  a.shool_name LIKE ' . $search . '  OR  a.address LIKE ' . $search . '  OR  a.building_group LIKE ' . $search . '  OR  a.building LIKE ' . $search . '  OR  a.room LIKE ' . $search . '  OR  a.birthday LIKE ' . $search . '  OR  a.phone LIKE ' . $search . ' )');
			}
		}
		

		// Filtering building_group
		$filter_building_group = $this->state->get("filter.building_group");

		if ($filter_building_group !== null && (is_numeric($filter_building_group) || !empty($filter_building_group)))
		{
			$query->where("a.`building_group` = '".$db->escape($filter_building_group)."'");
		}

		// Filtering building
		$filter_building = $this->state->get("filter.building");

		if ($filter_building !== null && (is_numeric($filter_building) || !empty($filter_building)))
		{
			$query->where("a.`building` = '".$db->escape($filter_building)."'");
		}

		// Filtering birthday
		$filter_birthday_from = $this->state->get("filter.birthday.from");

		if ($filter_birthday_from !== null && !empty($filter_birthday_from))
		{
			$query->where("a.`birthday` >= '".$db->escape($filter_birthday_from)."'");
		}
		$filter_birthday_to = $this->state->get("filter.birthday.to");

		if ($filter_birthday_to !== null  && !empty($filter_birthday_to))
		{
			$query->where("a.`birthday` <= '".$db->escape($filter_birthday_to)."'");
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

			if (isset($oneItem->building_group))
			{
				$values    = explode(',', $oneItem->building_group);
				$textValue = array();

				foreach ($values as $value)
				{
					if (!empty($value))
					{
						$db = $this->getDbo();
						$query = "Select id, name from #__building_groups WHERE id = '$value' ";
						$db->setQuery($query);
						$results = $db->loadObject();

						if ($results)
						{
							$textValue[] = $results->name;
						}
					}
				}

				$oneItem->building_group = !empty($textValue) ? implode(', ', $textValue) : $oneItem->building_group;
			}

			if (isset($oneItem->building))
			{
				$values    = explode(',', $oneItem->building);
				$textValue = array();

				foreach ($values as $value)
				{
					if (!empty($value))
					{
						$db = $this->getDbo();
						$query = "Select id, name from #__buildings where id = '$value' AND state=1";
						$db->setQuery($query);
						$results = $db->loadObject();

						if ($results)
						{
							$textValue[] = $results->name;
						}
					}
				}

				$oneItem->building = !empty($textValue) ? implode(', ', $textValue) : $oneItem->building;
			}
		}

		return $items;
	}
}
