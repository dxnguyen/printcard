<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Gernerals
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

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');
?>

<form
	action="<?php echo Route::_('index.php?option=com_gernerals&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="general-form" class="form-validate form-horizontal">

	
	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'general')); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'general', Text::_('COM_GERNERALS_TAB_GENERAL', true)); ?>
	<div class="row-fluid">
		<div class="span10 form-horizontal">
			<fieldset class="adminform">
				<legend><?php echo Text::_('COM_GERNERALS_FIELDSET_GENERAL'); ?></legend>
				<?php echo $this->form->renderField('slogan'); ?>
				<?php echo $this->form->renderField('website'); ?>
				<?php echo $this->form->renderField('web_api'); ?>
				<?php echo $this->form->renderField('facebook_link'); ?>
				<?php echo $this->form->renderField('zalo_link'); ?>
				<?php echo $this->form->renderField('instagram_link'); ?>
				<?php echo $this->form->renderField('twitter_link'); ?>
				<?php echo $this->form->renderField('telephone'); ?>
				<?php echo $this->form->renderField('hotline'); ?>
				<?php echo $this->form->renderField('footer_text'); ?>
				<?php echo $this->form->renderField('email'); ?>
				<?php if ($this->state->params->get('save_history', 1)) : ?>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('version_note'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('version_note'); ?></div>
					</div>
				<?php endif; ?>
			</fieldset>
		</div>
	</div>
	<?php echo HTMLHelper::_('uitab.endTab'); ?>
	<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
	<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
	<input type="hidden" name="jform[checked_out]" value="<?php echo ($this->item->checked_out) ? $this->item->checked_out : 0; ?>" />
	<input type="hidden" name="jform[checked_out_time]" value="<?php echo ($this->item->checked_out_time) ? $this->item->checked_out_time : date('Y-m-d H:i:s'); ?>" />
	<?php echo $this->form->renderField('created_by'); ?>
	<?php echo $this->form->renderField('modified_by'); ?>

	<?php if (Factory::getApplication()->getIdentity()->authorise('core.admin','gernerals')) : ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'permissions', Text::_('JGLOBAL_ACTION_PERMISSIONS_LABEL', true)); ?>
		<?php echo $this->form->getInput('rules'); ?>
	<?php echo HTMLHelper::_('uitab.endTab'); ?>
<?php endif; ?>
	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>

	<input type="hidden" name="task" value=""/>
	<?php echo HTMLHelper::_('form.token'); ?>

</form>
