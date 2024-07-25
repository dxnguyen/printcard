<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Students
 * @author     Nguyen Dinh <vb.dinhxuannguyen@gmail.com>
 * @copyright  2023 Nguyen Dinh
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Student\Component\Students\Site\Dispatcher;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcher;
use Joomla\CMS\Language\Text;

/**
 * ComponentDispatcher class for Com_Students
 *
 * @since  1.0.0
 */
class Dispatcher extends ComponentDispatcher
{
	/**
	 * Dispatch a controller task. Redirecting the user if appropriate.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function dispatch()
	{
		parent::dispatch();
	}
}
