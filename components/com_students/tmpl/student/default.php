<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Students
 * @author     Nguyen Dinh <vb.dinhxuannguyen@gmail.com>
 * @copyright  2023 Nguyen Dinh
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

$canEdit = Factory::getApplication()->getIdentity()->authorise('core.edit', 'com_students.' . $this->item->id);

if (!$canEdit && Factory::getApplication()->getIdentity()->authorise('core.edit.own', 'com_students' . $this->item->id))
{
	$canEdit = Factory::getApplication()->getIdentity()->id == $this->item->created_by;
}
?>

<div class="item_fields">

	<table class="table">
		

		<tr>
			<th><?php echo Text::_('COM_STUDENTS_FORM_LBL_STUDENT_MASV'); ?></th>
			<td><?php echo $this->item->masv; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_STUDENTS_FORM_LBL_STUDENT_CCCD'); ?></th>
			<td><?php echo $this->item->cccd; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_STUDENTS_FORM_LBL_STUDENT_NAME'); ?></th>
			<td><?php echo $this->item->name; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_STUDENTS_FORM_LBL_STUDENT_SHOOL_NAME'); ?></th>
			<td><?php echo $this->item->shool_name; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_STUDENTS_FORM_LBL_STUDENT_IMAGE'); ?></th>
			<td><?php echo $this->item->image; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_STUDENTS_FORM_LBL_STUDENT_ADDRESS'); ?></th>
			<td><?php echo $this->item->address; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_STUDENTS_FORM_LBL_STUDENT_BUILDING_GROUP'); ?></th>
			<td><?php echo $this->item->building_group; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_STUDENTS_FORM_LBL_STUDENT_BUILDING'); ?></th>
			<td><?php echo $this->item->building; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_STUDENTS_FORM_LBL_STUDENT_ROOM'); ?></th>
			<td><?php echo $this->item->room; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_STUDENTS_FORM_LBL_STUDENT_BIRTHDAY'); ?></th>
			<td>				<?php
			$date = $this->item->birthday;
			echo $date > 0 ? HTMLHelper::_('date', $date, Text::_('DATE_FORMAT_LC4')) : '-';
			?>

			</td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_STUDENTS_FORM_LBL_STUDENT_PHONE'); ?></th>
			<td><?php echo $this->item->phone; ?></td>
		</tr>

	</table>

</div>

<?php $canCheckin = Factory::getApplication()->getIdentity()->authorise('core.manage', 'com_students.' . $this->item->id) || $this->item->checked_out == Factory::getApplication()->getIdentity()->id; ?>
	<?php if($canEdit && $this->item->checked_out == 0): ?>

	<a class="btn btn-outline-primary" href="<?php echo Route::_('index.php?option=com_students&task=student.edit&id='.$this->item->id); ?>"><?php echo Text::_("COM_STUDENTS_EDIT_ITEM"); ?></a>
	<?php elseif($canCheckin && $this->item->checked_out > 0) : ?>
	<a class="btn btn-outline-primary" href="<?php echo Route::_('index.php?option=com_students&task=student.checkin&id=' . $this->item->id .'&'. Session::getFormToken() .'=1'); ?>"><?php echo Text::_("JLIB_HTML_CHECKIN"); ?></a>

<?php endif; ?>

<?php if (Factory::getApplication()->getIdentity()->authorise('core.delete','com_students.student.'.$this->item->id)) : ?>

	<a class="btn btn-danger" rel="noopener noreferrer" href="#deleteModal" role="button" data-bs-toggle="modal">
		<?php echo Text::_("COM_STUDENTS_DELETE_ITEM"); ?>
	</a>

	<?php echo HTMLHelper::_(
                                    'bootstrap.renderModal',
                                    'deleteModal',
                                    array(
                                        'title'  => Text::_('COM_STUDENTS_DELETE_ITEM'),
                                        'height' => '50%',
                                        'width'  => '20%',
                                        
                                        'modalWidth'  => '50',
                                        'bodyHeight'  => '100',
                                        'footer' => '<button class="btn btn-outline-primary" data-bs-dismiss="modal">Close</button><a href="' . Route::_('index.php?option=com_students&task=student.remove&id=' . $this->item->id, false, 2) .'" class="btn btn-danger">' . Text::_('COM_STUDENTS_DELETE_ITEM') .'</a>'
                                    ),
                                    Text::sprintf('COM_STUDENTS_DELETE_CONFIRM', $this->item->id)
                                ); ?>

<?php endif; ?>