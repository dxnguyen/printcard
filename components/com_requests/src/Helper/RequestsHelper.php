<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Requests
 * @author     Nguyen Dinh <vb.dinhxuannguyen@gmail.com>
 * @copyright  2024 Nguyen Dinh
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Requests\Component\Requests\Site\Helper;

defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Class RequestsFrontendHelper
 *
 * @since  1.0.0
 */
class RequestsHelper
{
	

	/**
	 *
	 * Get group names by ID
	 *
	 * @param  string|object|array  $pks  The group id(s)
	 *
	 * @return mixed group name if the group was found, null otherwise
	 */
	public static function getGroupNamesById($pks)
	{
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);

		if (is_object($pks))
		{
			$pks = get_object_vars($pks);
		}

		if (is_array($pks))
		{
			$pks = implode(',', $pks);
		}

		$query
			->select('title')
			->from('#__usergroups')
			->where('FIND_IN_SET(id,' . $db->quote($pks) . ')');

		$db->setQuery($query);

		return implode(', ', $db->loadColumn());
	}

	/**
	 * Gets the files attached to an item
	 *
	 * @param   int     $pk     The item's id
	 *
	 * @param   string  $table  The table's name
	 *
	 * @param   string  $field  The field's name
	 *
	 * @return  array  The files
	 */
	public static function getFiles($pk, $table, $field)
	{
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);

		$query
			->select($field)
			->from($table)
			->where('id = ' . (int) $pk);

		$db->setQuery($query);

		return explode(',', $db->loadResult());
	}

	/**
	 * Gets the edit permission for an user
	 *
	 * @param   mixed  $item  The item
	 *
	 * @return  bool
	 */
	public static function canUserEdit($item)
	{
		$permission = false;
		$user       = Factory::getApplication()->getIdentity();

		if ($user->authorise('core.edit', 'com_requests') || (isset($item->created_by) && $user->authorise('core.edit.own', 'com_requests') && $item->created_by == $user->id) || $user->authorise('core.create', 'com_requests'))
		{
			$permission = true;
		}

		return $permission;
	}
}
