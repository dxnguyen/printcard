<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Requests
 * @author     Nguyen Dinh <vb.dinhxuannguyen@gmail.com>
 * @copyright  2024 Nguyen Dinh
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Layout\LayoutHelper;
use \Joomla\CMS\Session\Session;
use \Joomla\CMS\User\UserFactoryInterface;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$user       = Factory::getApplication()->getIdentity();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canCreate  = $user->authorise('core.create', 'com_requests') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'requestform.xml');
$canEdit    = $user->authorise('core.edit', 'com_requests') && file_exists(JPATH_COMPONENT .  DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'requestform.xml');
$canCheckin = $user->authorise('core.manage', 'com_requests');
$canChange  = $user->authorise('core.edit.state', 'com_requests');
$canDelete  = $user->authorise('core.delete', 'com_requests');

// Import CSS
$wa = $this->document->getWebAssetManager();
$wa->useStyle('com_requests.list');
?>

<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post"
	  name="adminForm" id="adminForm">
	<?php if(!empty($this->filterForm)) { echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); } ?>
	<div class="table-responsive">
		<table class="table table-striped" id="requestList">
			<thead>
			<tr>
				
					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_REQUESTS_REQUESTS_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>

					<th >
						<?php echo HTMLHelper::_('grid.sort', 'JPUBLISHED', 'a.state', $listDirn, $listOrder); ?>
					</th>

					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_REQUESTS_REQUESTS_TITLE', 'a.title', $listDirn, $listOrder); ?>
					</th>

					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_REQUESTS_REQUESTS_REQUESTER_ID', 'a.requester_id', $listDirn, $listOrder); ?>
					</th>

					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_REQUESTS_REQUESTS_TYPE_ID', 'a.type_id', $listDirn, $listOrder); ?>
					</th>

					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_REQUESTS_REQUESTS_STATUS', 'a.status', $listDirn, $listOrder); ?>
					</th>

					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_REQUESTS_REQUESTS_TECHNICIAN_ID', 'a.technician_id', $listDirn, $listOrder); ?>
					</th>

					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_REQUESTS_REQUESTS_CREATED_DATE', 'a.created_date', $listDirn, $listOrder); ?>
					</th>

					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_REQUESTS_REQUESTS_START_DATE', 'a.start_date', $listDirn, $listOrder); ?>
					</th>

					<th class=''>
						<?php echo HTMLHelper::_('grid.sort',  'COM_REQUESTS_REQUESTS_END_DATE', 'a.end_date', $listDirn, $listOrder); ?>
					</th>

						<?php if ($canEdit || $canDelete): ?>
					<th class="center">
						<?php echo Text::_('COM_REQUESTS_REQUESTS_ACTIONS'); ?>
					</th>
					<?php endif; ?>

			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
					<div class="pagination">
						<?php echo $this->pagination->getPagesLinks(); ?>
					</div>
				</td>
			</tr>
			</tfoot>
			<tbody>
			<?php foreach ($this->items as $i => $item) : ?>
				<?php $canEdit = $user->authorise('core.edit', 'com_requests'); ?>
				<?php if (!$canEdit && $user->authorise('core.edit.own', 'com_requests')): ?>
				<?php $canEdit = Factory::getApplication()->getIdentity()->id == $item->created_by; ?>
				<?php endif; ?>

				<tr class="row<?php echo $i % 2; ?>">
					
					<td>
						<?php echo $item->id; ?>
					</td>
					<td>
						<?php $class = ($canChange) ? 'active' : 'disabled'; ?>
						<a class="btn btn-micro <?php echo $class; ?>" href="<?php echo ($canChange) ? Route::_('index.php?option=com_requests&task=request.publish&id=' . $item->id . '&state=' . (($item->state + 1) % 2), false, 2) : '#'; ?>">
						<?php if ($item->state == 1): ?>
							<i class="icon-publish"></i>
						<?php else: ?>
							<i class="icon-unpublish"></i>
						<?php endif; ?>
						</a>
					</td>
					<td>
						<?php $canCheckin = Factory::getApplication()->getIdentity()->authorise('core.manage', 'com_requests.' . $item->id) || $item->checked_out == Factory::getApplication()->getIdentity()->id; ?>
						<?php if($canCheckin && $item->checked_out > 0) : ?>
							<a href="<?php echo Route::_('index.php?option=com_requests&task=request.checkin&id=' . $item->id .'&'. Session::getFormToken() .'=1'); ?>">
							<?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'request.', false); ?></a>
						<?php endif; ?>
						<a href="<?php echo Route::_('index.php?option=com_requests&view=request&id='.(int) $item->id); ?>">
							<?php echo $this->escape($item->title); ?></a>
					</td>
					<td>
								<?php $container = \Joomla\CMS\Factory::getContainer();
								$userFactory = $container->get(UserFactoryInterface::class);?>
								<?php $user = $userFactory->loadUserById($item->requester_id); ?>
								<?php echo $user->name; ?>
					</td>
					<td>
						<?php echo $item->type_id; ?>
					</td>
					<td>
						<?php echo $item->status; ?>
					</td>
					<td>
								<?php $container = \Joomla\CMS\Factory::getContainer();
								$userFactory = $container->get(UserFactoryInterface::class);?>
								<?php $user = $userFactory->loadUserById($item->technician_id); ?>
								<?php echo $user->name; ?>
					</td>
					<td>
						<?php echo $item->created_date; ?>
					</td>
					<td>
						<?php
						$date = $item->start_date;
						echo $date > 0 ? HTMLHelper::_('date', $date, Text::_('DATE_FORMAT_LC4')) : '-';
						?>
					</td>
					<td>
						<?php
						$date = $item->end_date;
						echo $date > 0 ? HTMLHelper::_('date', $date, Text::_('DATE_FORMAT_LC4')) : '-';
						?>
					</td>
					<?php if ($canEdit || $canDelete): ?>
						<td class="center">
							<?php $canCheckin = Factory::getApplication()->getIdentity()->authorise('core.manage', 'com_requests.' . $item->id) || $item->checked_out == Factory::getApplication()->getIdentity()->id; ?>

							<?php if($canEdit && $item->checked_out == 0): ?>
								<a href="<?php echo Route::_('index.php?option=com_requests&task=request.edit&id=' . $item->id, false, 2); ?>" class="btn btn-mini" type="button"><i class="icon-edit" ></i></a>
							<?php endif; ?>
							<?php if ($canDelete): ?>
								<a href="<?php echo Route::_('index.php?option=com_requests&task=requestform.remove&id=' . $item->id, false, 2); ?>" class="btn btn-mini delete-button" type="button"><i class="icon-trash" ></i></a>
							<?php endif; ?>
						</td>
					<?php endif; ?>

				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php if ($canCreate) : ?>
		<a href="<?php echo Route::_('index.php?option=com_requests&task=requestform.edit&id=0', false, 0); ?>"
		   class="btn btn-success btn-small"><i
				class="icon-plus"></i>
			<?php echo Text::_('COM_REQUESTS_ADD_ITEM'); ?></a>
	<?php endif; ?>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value=""/>
	<input type="hidden" name="filter_order_Dir" value=""/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>

<?php
	if($canDelete) {
		$wa->addInlineScript("
			jQuery(document).ready(function () {
				jQuery('.delete-button').click(deleteItem);
			});

			function deleteItem() {

				if (!confirm(\"" . Text::_('COM_REQUESTS_DELETE_MESSAGE') . "\")) {
					return false;
				}
			}
		", [], [], ["jquery"]);
	}
?>