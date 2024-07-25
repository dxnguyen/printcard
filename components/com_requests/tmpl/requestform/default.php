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
use \Requests\Component\Requests\Site\Helper\RequestsHelper;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');

// Load admin language file
$lang = Factory::getLanguage();
$lang->load('com_requests', JPATH_SITE);

$user    = Factory::getApplication()->getIdentity();
$canEdit = RequestsHelper::canUserEdit($this->item, $user);


?>

<div class="request-edit front-end-edit">
	<?php if (!$canEdit) : ?>
		<h3>
		<?php throw new \Exception(Text::_('COM_REQUESTS_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?>
		</h3>
	<?php else : ?>
		<?php if (!empty($this->item->id)): ?>
			<h1><?php echo Text::sprintf('COM_REQUESTS_EDIT_ITEM_TITLE', $this->item->id); ?></h1>
		<?php else: ?>
			<h1><?php echo Text::_('COM_REQUESTS_ADD_ITEM_TITLE'); ?></h1>
		<?php endif; ?>

		<form id="form-request"
			  action="<?php echo Route::_('index.php?option=com_requests&task=requestform.save'); ?>"
			  method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
			
	<input type="hidden" name="jform[id]" value="<?php echo isset($this->item->id) ? $this->item->id : ''; ?>" />

	<input type="hidden" name="jform[state]" value="<?php echo isset($this->item->state) ? $this->item->state : ''; ?>" />

	<input type="hidden" name="jform[ordering]" value="<?php echo isset($this->item->ordering) ? $this->item->ordering : ''; ?>" />

	<input type="hidden" name="jform[checked_out]" value="<?php echo isset($this->item->checked_out) ? $this->item->checked_out : ''; ?>" />

	<input type="hidden" name="jform[checked_out_time]" value="<?php echo isset($this->item->checked_out_time) ? $this->item->checked_out_time : ''; ?>" />

				<?php echo $this->form->getInput('created_by'); ?>
				<?php echo $this->form->getInput('modified_by'); ?>
	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'request')); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'request', Text::_('COM_REQUESTS_TAB_REQUEST', true)); ?>
	<?php echo $this->form->renderField('title'); ?>

	<?php echo $this->form->renderField('description'); ?>

	<?php echo $this->form->renderField('requester_id'); ?>

	<?php echo $this->form->renderField('requester_deparment'); ?>

	<?php echo $this->form->renderField('type_id'); ?>

	<?php echo $this->form->renderField('status'); ?>

	<?php echo $this->form->renderField('technician_id'); ?>

	<?php echo $this->form->renderField('tech_department'); ?>

	<?php echo $this->form->renderField('created_date'); ?>

	<?php echo $this->form->renderField('start_date'); ?>

	<?php echo $this->form->renderField('end_date'); ?>

	<?php echo HTMLHelper::_('uitab.endTab'); ?>
			<div class="control-group">
				<div class="controls">

					<?php if ($this->canSave): ?>
						<button type="submit" class="validate btn btn-primary">
							<span class="fas fa-check" aria-hidden="true"></span>
							<?php echo Text::_('JSUBMIT'); ?>
						</button>
					<?php endif; ?>
					<a class="btn btn-danger"
					   href="<?php echo Route::_('index.php?option=com_requests&task=requestform.cancel'); ?>"
					   title="<?php echo Text::_('JCANCEL'); ?>">
					   <span class="fas fa-times" aria-hidden="true"></span>
						<?php echo Text::_('JCANCEL'); ?>
					</a>
				</div>
			</div>

			<input type="hidden" name="option" value="com_requests"/>
			<input type="hidden" name="task"
				   value="requestform.save"/>
			<?php echo HTMLHelper::_('form.token'); ?>
		</form>
	<?php endif; ?>
</div>
