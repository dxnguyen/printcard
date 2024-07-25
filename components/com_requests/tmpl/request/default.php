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
use \Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

$canEdit = Factory::getApplication()->getIdentity()->authorise('core.edit', 'com_requests.' . $this->item->id);

if (!$canEdit && Factory::getApplication()->getIdentity()->authorise('core.edit.own', 'com_requests' . $this->item->id))
{
	$canEdit = Factory::getApplication()->getIdentity()->id == $this->item->created_by;
}
?>

<div class="item_fields">

	<table class="table">
		

		<tr>
			<th><?php echo Text::_('COM_REQUESTS_FORM_LBL_REQUEST_TITLE'); ?></th>
			<td><?php echo $this->item->title; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_REQUESTS_FORM_LBL_REQUEST_DESCRIPTION'); ?></th>
			<td><?php echo nl2br($this->item->description); ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_REQUESTS_FORM_LBL_REQUEST_REQUESTER_ID'); ?></th>
			<td><?php echo $this->item->requester_id_name; ?></td>
		</tr>
		<?php if ($this->item->requester_id == "> 0"): ?>

		<tr>
			<th><?php echo Text::_('COM_REQUESTS_FORM_LBL_REQUEST_REQUESTER_DEPARMENT'); ?></th>
			<td><?php echo $this->item->requester_deparment; ?>
			</td>
		</tr>

		<?php endif; ?>

		<tr>
			<th><?php echo Text::_('COM_REQUESTS_FORM_LBL_REQUEST_TYPE_ID'); ?></th>
			<td><?php echo $this->item->type_id; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_REQUESTS_FORM_LBL_REQUEST_STATUS'); ?></th>
			<td>
			<?php

			if (!empty($this->item->status) || $this->item->status === 0)
			{
				echo Text::_('COM_REQUESTS_REQUESTS_STATUS_OPTION_' . preg_replace('/[^A-Za-z0-9\_-]/', '',strtoupper(str_replace(' ', '_',$this->item->status))));
			}
			?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_REQUESTS_FORM_LBL_REQUEST_TECHNICIAN_ID'); ?></th>
			<td><?php echo $this->item->technician_id_name; ?></td>
		</tr>
		<?php if ($this->item->technician_id == "> 0"): ?>

		<tr>
			<th><?php echo Text::_('COM_REQUESTS_FORM_LBL_REQUEST_TECH_DEPARTMENT'); ?></th>
			<td><?php echo $this->item->tech_department; ?>
			</td>
		</tr>

		<?php endif; ?>

		<tr>
			<th><?php echo Text::_('COM_REQUESTS_FORM_LBL_REQUEST_CREATED_DATE'); ?></th>
			<td><?php echo $this->item->created_date; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_REQUESTS_FORM_LBL_REQUEST_START_DATE'); ?></th>
			<td>				<?php
			$date = $this->item->start_date;
			echo $date > 0 ? HTMLHelper::_('date', $date, Text::_('DATE_FORMAT_LC4')) : '-';
			?>

			</td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_REQUESTS_FORM_LBL_REQUEST_END_DATE'); ?></th>
			<td>				<?php
			$date = $this->item->end_date;
			echo $date > 0 ? HTMLHelper::_('date', $date, Text::_('DATE_FORMAT_LC4')) : '-';
			?>

			</td>
		</tr>

	</table>

</div>

<?php $canCheckin = Factory::getApplication()->getIdentity()->authorise('core.manage', 'com_requests.' . $this->item->id) || $this->item->checked_out == Factory::getApplication()->getIdentity()->id; ?>
	<?php if($canEdit && $this->item->checked_out == 0): ?>

	<a class="btn btn-outline-primary" href="<?php echo Route::_('index.php?option=com_requests&task=request.edit&id='.$this->item->id); ?>"><?php echo Text::_("COM_REQUESTS_EDIT_ITEM"); ?></a>
	<?php elseif($canCheckin && $this->item->checked_out > 0) : ?>
	<a class="btn btn-outline-primary" href="<?php echo Route::_('index.php?option=com_requests&task=request.checkin&id=' . $this->item->id .'&'. Session::getFormToken() .'=1'); ?>"><?php echo Text::_("JLIB_HTML_CHECKIN"); ?></a>

<?php endif; ?>

<?php if (Factory::getApplication()->getIdentity()->authorise('core.delete','com_requests.request.'.$this->item->id)) : ?>

	<a class="btn btn-danger" rel="noopener noreferrer" href="#deleteModal" role="button" data-bs-toggle="modal">
		<?php echo Text::_("COM_REQUESTS_DELETE_ITEM"); ?>
	</a>

	<?php echo HTMLHelper::_(
                                    'bootstrap.renderModal',
                                    'deleteModal',
                                    array(
                                        'title'  => Text::_('COM_REQUESTS_DELETE_ITEM'),
                                        'height' => '50%',
                                        'width'  => '20%',
                                        
                                        'modalWidth'  => '50',
                                        'bodyHeight'  => '100',
                                        'footer' => '<button class="btn btn-outline-primary" data-bs-dismiss="modal">Close</button><a href="' . Route::_('index.php?option=com_requests&task=request.remove&id=' . $this->item->id, false, 2) .'" class="btn btn-danger">' . Text::_('COM_REQUESTS_DELETE_ITEM') .'</a>'
                                    ),
                                    Text::sprintf('COM_REQUESTS_DELETE_CONFIRM', $this->item->id)
                                ); ?>

<?php endif; ?>