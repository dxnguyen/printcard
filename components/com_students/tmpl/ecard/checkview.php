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

    $user = Factory::getApplication()->getIdentity();
    $userId = $user->get('id');

    $wa = $this->document->getWebAssetManager();
    $wa->useStyle('com_students.list');

?>

<form action="" method="post" name="checkViewCardForm" id="adminForm" style="text-align: center;">
    <p class="noteCheckView text-center">Vui lòng nhập vào ngày sinh của bạn theo dạng "ddmmyyyy"<br/> <small style="font-style: italic;">Ví dụ: Bạn sinh ngày <span class="fw-bold">1/1/2005</span>, vui lòng nhập là <span class="fw-bold">01012005</span></small></p>
    <input type="text" name="jform[birthday]" value="" required />
    <button type="submit" name="jform[submit]">Xem thẻ</button>

    <input type="hidden" name="task" value="Ecard.checkViewCard"/>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
