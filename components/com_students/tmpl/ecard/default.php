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
    // Get the user groups
    $userId = $user->get('id');
    $listOrder = $this->state->get('list.ordering');
    $listDirn = $this->state->get('list.direction');
    $canCreate = $user->authorise('core.create', 'com_students') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'studentform.xml');
    $canEdit = $user->authorise('core.edit', 'com_students') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'studentform.xml');
    $canCheckin = $user->authorise('core.manage', 'com_students');
    $canChange = $user->authorise('core.edit.state', 'com_students');
    $canDelete = $user->authorise('core.delete', 'com_students');

    // Import CSS
    $wa = $this->document->getWebAssetManager();
    $wa->useStyle('com_students.list');
?>

<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post"
      name="adminForm" id="adminForm">
    <?php if (!empty($this->filterForm)) {
        echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
    } ?>

    <input type="hidden" name="task" value=""/>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
<?php
    $pathImg = "https://ql.ktxhcm.edu.vn/SharedData/HinhSV/";

    if ($this->items):
        $iconCheck = ($this->items['Status']) ? 'userAvailable fas fa-user-check' : 'userUnAvailable fas fa-user-times';
        $ImageAvartar = $this->items['ImageAvartar'];
        $componentImg = @explode('.', $ImageAvartar);
        $imgName      = (!empty($componentImg[1])) ? $ImageAvartar : $ImageAvartar.'.jpg';
        $studentImg   = $pathImg . $imgName;

        if (!isAvailableImage($studentImg)) {
            $studentImg = URI::root() . 'inc/no-image.jpg';
        }

        $schoolName = $this->items['UniversityName'];
        if (strpos($this->items['UniversityName'], 'ĐHQG TP.HCM') !== false) {
            $schoolName = substr($this->items['UniversityName'], 0, -14);
            $schoolName = $schoolName . '<br>Đại học Quốc gia TP. Hồ Chí Minh';
        }
        ?>
        <div id="cardBox">
            <div class="controlbox">
                <p class="text-center fw-bold">THẺ NỘI TRÚ ĐIỆN TỬ</p>
            </div>

            <div class="frameCard" id="eCard" >
                <div class="overlay"></div>
                <div class="studentCard" id="studentCard">
                    <div class="infobox">
                        <div class="img-barcode contain-img-box">
                            <div class="imgBox">
                                <img id="draggable-image" src="<?php echo $studentImg; //URI::root().'uploads/svimg.jpg'; ?>" alt=""
                                     title="<?php echo $this->items['IdCardNumber'] ?>"/>
                            </div>
                        </div>
                        <div class="studentInfo margin-info">
                            <p class="pinfo"><span class="label">Họ và tên:</span><span class="fullName"><?php echo $this->items['Name']; ?></span></p>
                            <p class="pinfo"><span class="label">Ngày sinh:</span><span
                                        class="birthday"><?php echo $this->items['BDay'] . '/' . $this->items['BMonth'] . '/' . $this->items['BYear']; ?></span>
                            </p>
                            <p class="pinfo"><span class="label">Trường/Khoa:</span><span class="school"><?php echo $schoolName; ?></span></p>
                        </div>
                    </div>
                    <div class="codebox">
                        <div class="img-barcode">
                            <div class="barcode"><img
                                        src="<?php echo URI::root() . 'uploads/barcode/' . $this->items['barcode']; ?>"
                                        atl=""/>
                            </div>
                        </div>
                        <div class="studentInfo">
                            <div class="qrcode"><img src="<?php echo URI::root() . $this->items['qrcode']; ?>" atl=""/>
                            </div>
                        </div>
                        <div id="isAvailable"><i class="<?php echo $iconCheck;?>"></i></div>
                    </div>
                </div>

            </div>

            <!-- mobile card -->
            <div class="frameCard" id="eCardSp" >
                <div class="overlay"></div>
                <div class="studentCard" id="studentCard">
                    <div class="infobox">
                        <div class="img-barcode contain-img-box">
                            <div class="imgBox">
                                <img id="draggable-image" src="<?php echo $studentImg;?>" alt=""
                                     title="<?php echo $this->items['IdCardNumber'] ?>"/>
                            </div>
                        </div>
                        <div class="studentInfo margin-info">
                            <p class="pinfo"><span class="label">Họ và tên:</span><span class="fullName"><?php echo $this->items['Name']; ?></span></p>
                            <p class="pinfo"><span class="label">Ngày sinh:</span><span
                                        class="birthday"><?php echo $this->items['BDay'] . '/' . $this->items['BMonth'] . '/' . $this->items['BYear']; ?></span>
                            </p>
                            <p class="pinfo"><span class="label">Trường/Khoa:</span><span class="school"><?php echo $schoolName; ?></span></p>
                        </div>
                    </div>
                    <div class="codebox">
                        <div class="img-barcode">
                            <div class="barcode"><img
                                        src="<?php echo URI::root() . 'uploads/barcode/' . $this->items['barcode']; ?>"
                                        atl=""/>
                            </div>
                        </div>
                        <div class="studentInfo">
                            <div class="qrcode"><img src="<?php echo URI::root() . $this->items['qrcode']; ?>" atl=""/>
                            </div>
                        </div>
                        <div id="isAvailable"><i class="<?php echo $iconCheck;?>"></i></div>
                    </div>
                </div>

            </div>
            <!-- end mobile card -->

        </div>
    <?php else: ?>
        <div class="data-not-exist"><h4>Không có dữ liệu được tìm thấy trong hệ thống</h4></div>
    <?php endif; ?>
