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
$canCreate  = $user->authorise('core.create', 'com_students') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'studentform.xml');
$canEdit    = $user->authorise('core.edit', 'com_students') && file_exists(JPATH_COMPONENT .  DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'studentform.xml');
$canCheckin = $user->authorise('core.manage', 'com_students');
$canChange  = $user->authorise('core.edit.state', 'com_students');
$canDelete  = $user->authorise('core.delete', 'com_students');

// Import CSS
$wa = $this->document->getWebAssetManager();
$wa->useStyle('com_students.list');
?>

<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post"
	  name="adminForm" id="adminForm">
	<?php if(!empty($this->filterForm)) { echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); } ?>
	
	<div class="item_fields">
		<?php if ($this->items): 
			$studentImg  = "https://ql.ktxhcm.edu.vn/SharedData/HinhSV/".$this->items['IdCardNumber'].".jpg";

			if (!isAvailableImage($studentImg)) {
				$students = getRowsByFieldValue('#__students', 'cccd', $this->items['IdCardNumber']);
				if ($students && !empty($students[0]->image)) {
					$studentImg = "https://ql.ktxhcm.edu.vn/SharedData/HinhSV/".$students[0]->image.".jpg";
				} else {
					$studentImg = '/inc/no-image.jpg';
				}				 
			}
		?>
		<div class="row">
			<div class="col-lg-5 col-md-5 col-sm-6 col-xs-12 text-center">
				<img class="sv-image" src="<?php echo $studentImg?>" alt="" title="<?php echo $this->items['IdCardNumber']?>" />
			</div>
			<div class="col-lg-7 col-md-7 col-sm-6 col-xs-12">
				<table class="table table-responsive svinfo">			
					<?php if ($this->items['Name']) : ?>
					<tr>
						<th class="sv-name" colspan="2"><h2 class="text-name"><?php echo $this->items['Name']; ?></h2></th>
					</tr>
					<?php endif; ?>

					<?php if ($this->items['IdCardNumber']) : ?>
					<tr>
						<th><?php echo Text::_('CMND/CCCD'); ?></th>
						<td><?php echo $this->items['IdCardNumber']; ?></td>
					</tr>
					<?php endif; ?>

					<?php if ($this->items['StudentCode']) : ?>
					<tr>
						<th><?php echo Text::_('COM_STUDENTS_FORM_LBL_STUDENT_MASV'); ?></th>
						<td><?php echo $this->items['StudentCode']; ?></td>
					</tr>
					<?php endif; ?>

					<?php if ($this->items['UniversityName']) : ?>
					<tr>
						<th><?php echo Text::_('COM_STUDENTS_FORM_LBL_STUDENT_SHOOL_NAME'); ?></th>
						<td><?php echo $this->items['UniversityName']; ?></td>
					</tr>
					<?php endif; ?>

					<tr>
						<th><?php echo Text::_('Giới tính'); ?></th>
						<td><?php echo ($this->items['Gender']) ? 'Nữ' : 'Nam'; ?></td>
					</tr>

					<?php if ($this->items['clusterName']) : ?>
					<tr>
						<th><?php echo Text::_('Cụm nhà'); ?></th>
						<td><?php echo $this->items['clusterName']; ?></td>
					</tr>
					<?php endif; ?>

					<?php if ($this->items['DormitoryHouseName']) : ?>
					<tr>
						<th><?php echo Text::_('COM_STUDENTS_FORM_LBL_STUDENT_BUILDING'); ?></th>
						<td><?php echo $this->items['DormitoryHouseName']; ?></td>
					</tr>
					<?php endif; ?>
					
					<?php if ($this->items['RoomName']) : ?>
					<tr>
						<th><?php echo Text::_('Phòng'); ?></th>
						<td><?php echo $this->items['RoomName']; ?></td>
					</tr>
					<?php endif; ?>	
					
					<?php if (isset($this->items['LicensePlate'])) : ?>
					<tr>
						<th><?php echo Text::_('Biển số xe'); ?></th>
						<td><?php echo $this->items['LicensePlate']; ?></td>
					</tr>
					<?php endif; ?>

					<?php if ($this->items['Birthday']) : ?>
					<tr>
						<th><?php echo Text::_('COM_STUDENTS_FORM_LBL_STUDENT_BIRTHDAY'); ?></th>
						<td><?php
						echo $this->items['BDay'].'/'.$this->items['BMonth'].'/'.$this->items['BYear'];
						?>
						</td>
					</tr>
					<?php endif; ?>

					<!-- <//?php if ($this->items['Phone']) : ?>
					<tr>
						<th><//?php echo Text::_('COM_STUDENTS_FORM_LBL_STUDENT_PHONE'); ?></th>
						<td><//?php echo $this->items['Phone']; ?></td>
					</tr>
					<//?php endif; ?>					 -->
				</table>
			</div>
		</div>
		<?php else: ?>
			<div class="data-not-exist"><h4>Không có dữ liệu được tìm thấy trong hệ thống</h4></div>
		<?php endif; ?>
	</div>

	<input type="hidden" name="task" value=""/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>