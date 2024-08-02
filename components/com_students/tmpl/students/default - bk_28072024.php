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
<!--<div style="width: 100%; text-align: center; margin-bottom: 40px;"><img src="/uploads/thesv.jpg" style="width: 86mm; margin-bottom: 40px; margin: 0 auto;"></div>-->
<?php
    $pathImg = "https://ql.ktxhcm.edu.vn/SharedData/HinhSV/";
    if ($this->items):
        $studentImg = $pathImg . $this->items['IdCardNumber'] . ".jpg";

        if (!isAvailableImage($studentImg)) {
            $students = getRowsByFieldValue('#__students', 'cccd', $this->items['IdCardNumber']);
            if ($students && !empty($students[0]->image)) {
                $studentImg = $pathImg . $students[0]->image . ".jpg";
            } else {
                $studentImg = URI::root() . 'inc/no-image.jpg';
            }
        }

        $schoolName = $this->items['UniversityName'];
        if (strpos($this->items['UniversityName'], 'ĐHQG TP.HCM') !== false) {
            $schoolName = substr($this->items['UniversityName'], 0, -14);
            $schoolName = $schoolName . '<br>Đại học Quốc gia TP. Hồ Chí Minh';
        }
        ?>
        <div id="cardBox">
            <div class="controlbox">
                <p style="text-align: center; margin-bottom: 0;"><span class="control-text">Font-size (Họ tên):</span> <input type="number"
                                                                                                       id="textFullname"
                                                                                                       value="12"
                                                                                                       style="width: 50px; margin-right: 20px; border: 1px solid #ccc; border-radius: 5px;color: rgba(11,16,22,0.92);"/>
                    <span class="control-text" style="margin-left: 20px;">Mã hình SV:</span> <input type="text"
                                                                                                 id="textImgCode"
                                                                                                 value=""
                                                                                                 style="width: 100px; margin-right: 30px; border: 1px solid #ccc; border-radius: 5px;color: rgba(11,16,22,0.92);"/>
                    <button id="printBtn" class="btn btn-sm btn-danger" onclick="printDiv('printCard');//print();">In thẻ</button>
                </p>
            </div>
            <div class="frameCard" id="printCard">
                <div class="overlay"></div>
                <div class="studentCard" id="studentCard">
                    <div class="infobox">
                        <div class="img-barcode contain-img-box">
                            <div class="imgBox">
                                <img id="draggable-image" src="<?php echo $studentImg ?>" alt=""
                                     title="<?php echo $this->items['IdCardNumber'] ?>"/>
                            </div>
                        </div>
                        <div class="studentInfo margin-info">
                            <!--<h4 class="cardTitle">THẺ NỘI TRÚ</h4>-->
                            <p class="pinfo"><span class="fullName"><?php echo $this->items['Name']; ?></span></p>
                            <p class="pinfo"> <span
                                        class="birthday"><?php echo $this->items['BDay'] . '/' . $this->items['BMonth'] . '/' . $this->items['BYear']; ?></span>
                            </p>
                            <p class="pinfo"><span class="school"><?php echo $schoolName; ?></span></p>
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
                    </div>
                </div>

            </div>
        </div>
    <?php else: ?>
        <div class="data-not-exist"><h4>Không có dữ liệu được tìm thấy trong hệ thống</h4></div>
    <?php endif; ?>


<script>
    $(document).ready(function () {
        //set font size
        $('#textFullname').change(function () {
            var fontFullname = $(this).val();
            $('.fullName').css('font-size', fontFullname + 'px');
        });

        $('#textImgCode').change(function () {
            var textImgCode = $(this).val();

            if (textImgCode != '') {
                var pathImg = "<?php echo $pathImg;?>" + textImgCode + ".jpg";
                $('#draggable-image').attr('src', pathImg);
            }
        });

        $('.imgBox').mousedown(function(){
            $(this).css('border', '1px solid #ccc');
        });
        $('.imgBox').mouseup(function(){
            $(this).css('border', 'none');
        });

        // process zoom image

        const frame = document.querySelector('.imgBox');
        const img = document.getElementById('draggable-image');

        let scale = 1;
        let posX = 0;
        let posY = 0;
        let isDragging = false;
        let startDragX = 0;
        let startDragY = 0;

        frame.addEventListener('wheel', function (event) {
            event.preventDefault();

            // Xác định hướng cuộn chuột
            const delta = Math.max(-1, Math.min(1, event.deltaY));

            if (delta > 0) {
                scale -= 0.1;
            } else {
                scale += 0.1;
            }

            // limit zoom
            scale = Math.min(Math.max(0.5, scale), 2);

            // get mouse's new position with frame
            const rect = frame.getBoundingClientRect();
            const offsetX = event.clientX - rect.left;
            const offsetY = event.clientY - rect.top;

            // new position
            posX = (posX - offsetX) * (scale - 1);
            posY = (posY - offsetY) * (scale - 1);

            // apply scale
            img.style.transform = `scale(${scale}) translate(${posX}px, ${posY}px)`;
        });

        // Bắt sự kiện bấm chuột để di chuyển ảnh
        img.addEventListener('mousedown', function (event) {
            event.preventDefault();
            isDragging = true;
            startDragX = event.clientX - posX;
            startDragY = event.clientY - posY;
            img.style.cursor = 'grabbing';
        });

        // mouse move
        document.addEventListener('mousemove', function (event) {
            event.preventDefault();
            if (isDragging) {
                posX = event.clientX - startDragX;
                posY = event.clientY - startDragY;
                img.style.transform = `scale(${scale}) translate(${posX}px, ${posY}px)`;
            }
        });

        // drop mouse
        document.addEventListener('mouseup', function () {
            isDragging = false;
            img.style.cursor = 'move';
        });

    });

// print
    function printDiv(divId) {
        var printWindow = window.open('', '', 'height=800,width=1000');
        var divContent = document.getElementById(divId).innerHTML;
        printWindow.document.write('<html><head><meta name="viewport" content="width=device-width, initial-scale=1"><title>Print</title><link href="<?php echo URI::root()?>media/templates/site/studentcard/css/template.min.css?59858bfad5a4425bda3fa90f86d1c91b" rel="stylesheet" /><link href="<?php echo URI::root()?>media/templates/site/studentcard/css/custom.css" rel="stylesheet" /><style> @media print { body{ width:100%;margin:0; padding:0;} #printCard{ } .codebox{ } .barcode, .qrcode{margin-top:7px;} .barcode img{max-width:100%;} } </style></head><body>');
        printWindow.document.write(divContent);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.focus();
        setTimeout(() => {
            printWindow.print();
        }, 500);
    }

</script>

    <!-- Thêm thư viện html2canvas -->
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-beta4/html2canvas.min.js"></script>

<script>
    function exportToImage() {
        html2canvas(document.getElementById('printCard'), {
            useCORS: true,
            logging: true,
            scale: 8
        }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');

            // Mở ảnh trong cửa sổ mới
            const imgWindow = window.open('', '', 'height=600,width=800');
            imgWindow.document.write('<html><head><title>Print Image</title></head><body>');
            imgWindow.document.write('<img src="' + imgData + '" style="width:100%;"/>');
            imgWindow.document.write('</body></html>');
            imgWindow.document.close();
            imgWindow.focus();

            setTimeout(() => {
                imgWindow.print();
            }, 500);
        }).catch(error => {
            console.error('Error capturing the element:', error);
        });
    }
</script>-->
