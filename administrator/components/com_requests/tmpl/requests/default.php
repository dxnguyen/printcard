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
use \Joomla\CMS\Layout\LayoutHelper;
use \Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');

// Import CSS
$wa =  $this->document->getWebAssetManager();
$wa->useStyle('com_requests.admin')
    ->useScript('com_requests.admin');

$user      = Factory::getApplication()->getIdentity();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$canOrder  = $user->authorise('core.edit.state', 'com_requests');

$saveOrder = $listOrder == 'a.ordering';

if (!empty($saveOrder))
{
	$saveOrderingUrl = 'index.php?option=com_requests&task=requests.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
	HTMLHelper::_('draggablelist.draggable');
}

?>

<form action="<?php echo Route::_('index.php?option=com_requests&view=requests'); ?>" method="post"
	  name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
			<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

				<div class="clearfix"></div>
				<table class="table table-striped" id="requestList">
					<thead>
					<tr>
						<th class="w-1 text-center">
							<input type="checkbox" autocomplete="off" class="form-check-input" name="checkall-toggle" value=""
								   title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
						</th>
						
					<?php if (isset($this->items[0]->ordering)): ?>
					<th scope="col" class="w-1 text-center d-none d-md-table-cell">

					<?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>

					</th>
					<?php endif; ?>

						
					<th  scope="col" class="w-1 text-center">
						<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
					</th>
						
						<th class='left'>
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_REQUESTS_REQUESTS_TITLE', 'a.title', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_REQUESTS_REQUESTS_REQUESTER_ID', 'a.requester_id', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_REQUESTS_REQUESTS_TYPE_ID', 'a.type_id', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_REQUESTS_REQUESTS_STATUS', 'a.status', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_REQUESTS_REQUESTS_TECHNICIAN_ID', 'a.technician_id', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_REQUESTS_REQUESTS_CREATED_DATE', 'a.created_date', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_REQUESTS_REQUESTS_START_DATE', 'a.start_date', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo HTMLHelper::_('searchtools.sort',  'COM_REQUESTS_REQUESTS_END_DATE', 'a.end_date', $listDirn, $listOrder); ?>
						</th>
						
					<th scope="col" class="w-3 d-none d-lg-table-cell" >

						<?php echo HTMLHelper::_('searchtools.sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>					</th>
					</tr>
					</thead>
					<tfoot>
					<tr>
						<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
					</tfoot>
					<tbody <?php if (!empty($saveOrder)) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" <?php endif; ?>>
					<?php foreach ($this->items as $i => $item) :
						$ordering   = ($listOrder == 'a.ordering');
						$canCreate  = $user->authorise('core.create', 'com_requests');
						$canEdit    = $user->authorise('core.edit', 'com_requests');
						$canCheckin = $user->authorise('core.manage', 'com_requests');
						$canChange  = $user->authorise('core.edit.state', 'com_requests');
						?>
						<tr class="row<?php echo $i % 2; ?>" data-draggable-group='1' data-transition>
							<td class="text-center">
								<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
							</td>
							
							<?php if (isset($this->items[0]->ordering)) : ?>

							<td class="text-center d-none d-md-table-cell">

							<?php

							$iconClass = '';

							if (!$canChange)

							{
								$iconClass = ' inactive';

							}
							elseif (!$saveOrder)

							{
								$iconClass = ' inactive" title="' . Text::_('JORDERINGDISABLED');

							}							?>							<span class="sortable-handler<?php echo $iconClass ?>">
							<span class="icon-ellipsis-v" aria-hidden="true"></span>
							</span>
							<?php if ($canChange && $saveOrder) : ?>
							<input type="text" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order hidden">
								<?php endif; ?>
							</td>
							<?php endif; ?>

							
							<td class="text-center">
								<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'requests.', $canChange, 'cb'); ?>
							</td>
							
							<td>
								<?php if (isset($item->checked_out) && $item->checked_out && ($canEdit || $canChange)) : ?>
									<?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'requests.', $canCheckin); ?>
								<?php endif; ?>
								<?php if ($canEdit) : ?>
									<a href="<?php echo Route::_('index.php?option=com_requests&task=request.edit&id='.(int) $item->id); ?>">
									<?php echo $this->escape($item->title); ?>
									</a>
								<?php else : ?>
												<?php echo $this->escape($item->title); ?>
								<?php endif; ?>
							</td>
							<td>
								<?php echo $item->requester_id; ?>
							</td>
							<td>
								<?php echo $item->type_id; ?>
							</td>
							<td>
								<?php echo $item->status; ?>
							</td>
							<td>
								<?php echo $item->technician_id; ?>
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
							
							<td class="d-none d-lg-table-cell">
							<?php echo $item->id; ?>

							</td>


						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>

				<input type="hidden" name="task" value=""/>
				<input type="hidden" name="boxchecked" value="0"/>
				<input type="hidden" name="list[fullorder]" value="<?php echo $listOrder; ?> <?php echo $listDirn; ?>"/>
				<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>