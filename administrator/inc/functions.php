<?php
defined('_JEXEC') or die('Restricted access');

/**/
// $app = \Joomla\CMS\Factory::getApplication('site');
// define('TEMPLATE_PATH', JURI::root() . 'templates/' . $app->getTemplate() . '/');
// define('UPLOADS_PATH', JURI::root() . 'uploads/');

function like()
{
	$url = urlencode(JURI::getInstance()->toString());
	$str = '<iframe src="//www.facebook.com/plugins/like.php?href=' . $url . '&amp;send=false&amp;layout=button_count&amp;width=90&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21&amp;locale=vi_VN" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:90px; height:21px;" allowTransparency="true"></iframe>';
	/*$str = '<iframe src="//www.facebook.com/plugins/like.php?href=' . $url . '&amp;send=false&amp;layout=button_count&amp;width=90&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21&amp;locale=vi_VN" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:90px; height:21px;" allowTransparency="true"></iframe>

			<g:plusone size="medium"></g:plusone>
			<script type="text/javascript">
			window.___gcfg = {lang: "vi"};
			(function() {
				var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
				po.src = "https://apis.google.com/js/plusone.js";
				var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
			})();
			</script>
	';*/
	return $str;
}

function fanpage($fb, $w, $h)
{
	$str = '<iframe id="fbIframe" width="' . $w . '" height="' . $h . '" frameborder="0"
                        src="https://www.facebook.com/plugins/like_box.php?href=' . $fb . '&locale=vi_VN&show_border=true&show_faces=true&stream=false&width=' . $w . '&height=' . $h . '"></iframe>';

	return $str;
}

function showModule($position, $style = '')
{
	$document = \Joomla\CMS\Factory::getDocument();
	$renderer = $document->loadRenderer('modules');
	$options = array('style' => $style);
	echo $renderer->render($position, $options, null);
}

function getInfoweb($field = null)
{
	$db = \Joomla\CMS\Factory::getDbo();
	$query = $db->getQuery(true);
	$query->select('*')
		->from($db->quoteName('#__infoweb'))
		->where('id = 1');
	$db->setQuery($query);
	$result = $db->loadObject();
	if ($field != null) {
		return $result->$field;
	} else {
		return $result;
	}

	return;

}

function rrmdir($path) {
    $i = new DirectoryIterator($path);
    foreach($i as $f) {
        if($f->isFile()) {
            unlink($f->getRealPath());
        } else if(!$f->isDot() && $f->isDir()) {
            rrmdir($f->getRealPath());
            rmdir($f->getRealPath());
        }
    }
    rmdir($path);
}

function getAttribsByArticle($article_id, $group_id = 0)
{  //using for both article and categories
	$db = \Joomla\CMS\Factory::getDbo();
	$query = $db->getQuery(true);
	$query->select('f.name AS fieldname, fv.value AS fieldvalue');
	$query->from('`#__fields_values` AS fv');
	$query->join('LEFT', '#__fields AS f ON f.id = fv.field_id');
	$query->where('fv.item_id = ' . $article_id);
	if ($group_id > 0) {
		$query->where('f.group_id=' . $group_id);
	}
	$db->setQuery($query);
	$rs = $db->loadObjectList();
	if ($rs) {
		$result = array();
		foreach ($rs as $item) {
			$result["{$item->fieldname}"] = $item->fieldvalue;
		}
		return $result;
	}
}

function getSubcategoryIDFromCatID($catid)
{
	$categories = JCategories::getInstance('Content');
	$cat = $categories->get($catid);
	return $cat->getChildren();
}

function getFieldsNameByCatID($catid)
{
	JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
	$catFields = FieldsHelper::getFields('com_content.categories', $catid, true);
	return $catFields;
}

function getFieldsValuesByCatID($catid)
{
	JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
	$catFields = FieldsHelper::getFields('com_content.categories', $catid, true);
	$model = JModelLegacy::getInstance('Field', 'FieldsModel', array('ignore_request' => true));

	$categoryFieldsIds = array('21', '22');
	$currentCatFields = $model->getFieldValues($categoryFieldsIds, $catid);
	return $currentCatFields;
}

function getFetchAllFieldsFromCatID($catid)
{
	JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
	$jcategories = JCategories::getInstance('Content');
	$category = $jcategories->get($catid);
	$currentCatFields = FieldsHelper::getFields('com_content.categories', $category, true);
	return $currentCatFields;
}

function getCategory($catid)
{
	$categories = JCategories::getInstance('Content');
	return $categories->get($catid);
}

function getSubmenu($parent_id)
{ //chon list menu con
	$db = \Joomla\CMS\Factory::getDbo();
	$query = $db->getQuery(true);
	$query->select('*')
		->from($db->quoteName('#__menu'))
		->where('parent_id = ' . $db->quote($parent_id));
	$db->setQuery($query);
	return $db->loadObjectList();
}

function getRowsTableByFieldValue($table, $field, $value)
{
	$db = \Joomla\CMS\Factory::getDbo();
	$query = $db->getQuery(true);
	$query->select('*')
			->from($db->quoteName("{$table}"))
			->where("{$field} = " . $db->quote($value))
			->order('name ASC');
	$db->setQuery($query);
	return $db->loadObjectList();
}

function getListStoreInPrograms($ids){
    $arrids = @explode(',', $ids);
    $rs = array();
    foreach ($arrids as $id) {
        $db = \Joomla\CMS\Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('d.*')
            ->from('#__district AS d')
            ->join('INNER','#__strores AS s ON s.district = d.id')
            ->where('d.province_id =' . $id )
            ->where('d.state = 1')
            ->group('s.district')
            ->order('d.name ASC');
        $db->setQuery($query);
        $results = $db->loadObjectList();
        if ($results):
            $arrstores = array();
            foreach ($results as $val):
                $objStores = new stdClass();
                $liststore = getRowsTableByFieldValue('#__strores', 'district', $val->id);
                $objStores->district_id = $val->id;
                $objStores->district_name = $val->name;
                if($liststore) {
                    $objStores->list_store = $liststore;
                }
                $arrstores[] = $objStores;
            endforeach;
            $rs[$id] = $arrstores;
        endif;

    }
    return $rs;
}

function getSubmenuImage()
{
	$lang = \Joomla\CMS\Factory::getLanguage()->getTag();
	$menu = \Joomla\CMS\Factory::getApplication()->getMenu();
	$parentid = ($lang == 'vi-VN') ? 137 : 161;
	$childs = $menu->getItems('parent_id', $parentid);
	return $childs;
}

function getRelateProducts($catid, $id, $lang)
{
	$db = \Joomla\CMS\Factory::getDbo();
	$query = $db->getQuery(true);
	$query->select('*')
		->from('#__content')
		->where('state = 1')
		->where('catid = ' . $catid)
		->where('language = "' . $lang . '"')
		->where('id != ' . $id)
		->order('ordering ASC');
	$db->setQuery($query);
	$items = $db->loadObjectList();

	return $items;
}

function getFieldById($table, $id, $field = null)
{
	$db = \Joomla\CMS\Factory::getDbo();
	$query = $db->getQuery(true);
	$query->select('*')
		->from($db->quoteName($table))
		->where('id =' . $db->quote($id));
	$db->setQuery($query);
	$result = $db->loadObject();
	if ($field != null) {
		return $result->$field;
	} else {
		return $result;
	}
}

function checkPermisionUpdateInDay($update_date){
	$upd = date('Y-m-d 00:00:00', strtotime($update_date));
	$today = date('Y-m-d H:i:s');
	$datetime = new DateTime($upd);
	$datetime->modify('+1 day');
	$expireday = $datetime->format('Y-m-d H:i:s');
	if(strtotime($upd) <= strtotime($today) && strtotime($today) < strtotime($expireday)){
		$ischecked = true;
	}else{
		$ischecked = false;
	}
	return $ischecked;
}
function getCurrentUrl()
{
	return JURI::current();
}

// khoi tao session
function setSession($sName, $sValue)
{
	$session = \Joomla\CMS\Factory::getSession();
	$session->set($sName, $sValue);
}

//get session
function getSession($sName)
{
	$session = \Joomla\CMS\Factory::getSession();
	return $session->get($sName);
}

//huy session
function deleteSession($sName)
{
	$session = \Joomla\CMS\Factory::getSession();
	$session->set($sName, NULL);
}

function share()
{
	$str = '<!-- AddThis Button BEGIN -->
			<div class="addthis_toolbox addthis_default_style addthis_32x32_style">
				<a class="addthis_button_preferred_2"></a>
				<a class="addthis_button_preferred_3"></a>
				<a class="addthis_button_preferred_1"></a>
				<a class="addthis_button_preferred_4"></a>
				<a class="addthis_button_compact"></a>
			</div>
			<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4f9f765d777c2fee"></script>
			<!-- AddThis Button END -->';
	return $str;
}

function share3()
{
	echo '
<a href="https://api.addthis.com/oexchange/0.8/forward/facebook/offer?url=http%3A%2F%2Fwww.addthis.com&pubid=ra-505bc9ef0543faa5&ct=1&title=AddThis%20-%20Get%20likes%2C%20get%20shares%2C%20get%20followers&pco=tbxnj-1.0" target="_blank"><img src="https://cache.addthiscdn.com/icons/v3/thumbs/32x32/facebook.png" border="0" alt="Facebook"/></a>
<a href="https://api.addthis.com/oexchange/0.8/forward/twitter/offer?url=http%3A%2F%2Fwww.addthis.com&pubid=ra-505bc9ef0543faa5&ct=1&title=AddThis%20-%20Get%20likes%2C%20get%20shares%2C%20get%20followers&pco=tbxnj-1.0" target="_blank"><img src="https://cache.addthiscdn.com/icons/v3/thumbs/32x32/twitter.png" border="0" alt="Twitter"/></a>
<a href="https://api.addthis.com/oexchange/0.8/forward/google_plusone_share/offer?url=http%3A%2F%2Fwww.addthis.com&pubid=ra-505bc9ef0543faa5&ct=1&title=AddThis%20-%20Get%20likes%2C%20get%20shares%2C%20get%20followers&pco=tbxnj-1.0" target="_blank"><img src="https://cache.addthiscdn.com/icons/v3/thumbs/32x32/google_plusone_share.png" border="0" alt="Google+"/></a>
';
}

function ham_loc_dau($st)
{
	/*
	 * Func trả về một chuỗi ký tự không dấu
	 * Ví dụ: "trả Về một chuỗi ký" =>"tra-Ve-mot-chuoi-ky"
	 *
	 */
	$codau = array("à", "á", "ạ", "ả", "ã", "â", "ầ", "ấ", "ậ", "ẩ", "ẫ", "ă",
		"ằ", "ắ", "ặ", "ẳ", "ẵ", "è", "é", "ẹ", "ẻ", "ẽ", "ê", "ề"
	, "ế", "ệ", "ể", "ễ",
		"ì", "í", "ị", "ỉ", "ĩ",
		"ò", "ó", "ọ", "ỏ", "õ", "ô", "ồ", "ố", "ộ", "ổ", "ỗ", "ơ"
	, "ờ", "ớ", "ợ", "ở", "ỡ",
		"ù", "ú", "ụ", "ủ", "ũ", "ư", "ừ", "ứ", "ự", "ử", "ữ",
		"ỳ", "ý", "ỵ", "ỷ", "ỹ",
		"đ",
		"À", "Á", "Ạ", "Ả", "Ã", "Â", "Ầ", "Ấ", "Ậ", "Ẩ", "Ẫ", "Ă"
	, "Ằ", "Ắ", "Ặ", "Ẳ", "Ẵ",
		"È", "É", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ề", "Ế", "Ệ", "Ể", "Ễ",
		"Ì", "Í", "Ị", "Ỉ", "Ĩ",
		"Ò", "Ó", "Ọ", "Ỏ", "Õ", "Ô", "Ồ", "Ố", "Ộ", "Ổ", "Ỗ", "Ơ"
	, "Ờ", "Ớ", "Ợ", "Ở", "Ỡ",
		"Ù", "Ú", "Ụ", "Ủ", "Ũ", "Ư", "Ừ", "Ứ", "Ự", "Ử", "Ữ",
		"Ỳ", "Ý", "Ỵ", "Ỷ", "Ỹ",
		"Đ", " ", "/");
	$khongdau = array("a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a"
	, "a", "a", "a", "a", "a", "a",
		"e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e",
		"i", "i", "i", "i", "i",
		"o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o"
	, "o", "o", "o", "o", "o",
		"u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u",
		"y", "y", "y", "y", "y",
		"d",
		"A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A"
	, "A", "A", "A", "A", "A",
		"E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E",
		"I", "I", "I", "I", "I",
		"O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O"
	, "O", "O", "O", "O", "O",
		"U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U",
		"Y", "Y", "Y", "Y", "Y",
		"D", "-", "-");
	return str_replace($codau, $khongdau, $st);
}

function cutString($str, $num)
{
	/*
	 * hàm trả về một chuỗi ký tự sau khi cắt
	 *
	 * $str là chuỗi ký tự đưa vào
	 *
	 * $num là ký tự đã cắt
	 *
	 */
	if (strlen($str) > $num) {
		$str = substr($str, 0, $num);
		$str = substr($str, 0, strripos($str, ' '));
		return $str . '...';
	}
	return $str;
}

function get_first_image($text)
{
	/*
	 * Trả về image đầu tiên của bài viết
	 * $text là nội dung của bài viết
	 */

	preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $text, $matches);
	if (count($matches[1]) > 0) {
		$first_img = $matches [1][0];
	} else {
		$first_img = TEMPLATE_PATH . 'images/no-image.png';
	}
	return $first_img;
}

function getItemByID($table, $id)
{
	$db = \Joomla\CMS\Factory::getDbo();
	$query = $db->getQuery(true);
	$query->select('*')
		->from($db->quoteName($table))
		->where('id = '.$id)
		;
	$db->setQuery($query);
	return $db->loadObject();

}

function makeThumbnail($sourcefile, $max_width, $max_height, $endfile, $type)
{
	// Takes the sourcefile (path/to/image.jpg) and makes a thumbnail from it
	// and places it at endfile (path/to/thumb.jpg).
	// Load image and get image size.
	//
	switch ($type) {
		case'image/png':
			$img = imagecreatefrompng($sourcefile);
			break;
		case'image/jpeg':
			$img = imagecreatefromjpeg($sourcefile);
			break;
		case'image/gif':
			$img = imagecreatefromgif($sourcefile);
			break;
		default :
			return 'Un supported format';
	}

	$width = imagesx($img);
	$height = imagesy($img);

	if ($width > $height) {
		if ($width < $max_width)
			$newwidth = $width;

		else

			$newwidth = $max_width;


		$divisor = $width / $newwidth;
		$newheight = floor($height / $divisor);
	} else {

		if ($height < $max_height)
			$newheight = $height;
		else
			$newheight = $max_height;

		$divisor = $height / $newheight;
		$newwidth = floor($width / $divisor);
	}

	// Create a new temporary image.
	$tmpimg = imagecreatetruecolor($newwidth, $newheight);

	imagealphablending($tmpimg, false);
	imagesavealpha($tmpimg, true);

	// Copy and resize old image into new image.
	imagecopyresampled($tmpimg, $img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

	// Save thumbnail into a file.

	//compressing the file


	switch ($type) {
		case'image/png':
			imagepng($tmpimg, $endfile, 0);
			break;
		case'image/jpeg':
			imagejpeg($tmpimg, $endfile, 100);
			break;
		case'image/gif':
			imagegif($tmpimg, $endfile, 0);
			break;

	}

	// release the memory
	imagedestroy($tmpimg);
	imagedestroy($img);
}

function getWeeks($start_date , $end_date ){
	$w = date('W', strtotime($start_date));
	$y = date('Y', strtotime($start_date));
	$dto = new DateTime();
	$start_week = $dto->setISODate($y,$w)->format('Y-m-d');//get first week
	//////////
	$startdate = new DateTime($start_week); //nếu tuần cuối vượt wa ngày kết thúc thì lấy ngày kết thúc,
	$enddate = new DateTime($end_date);
	$numday = $enddate->diff($startdate)->format("%a");

	$n = floor($numday / 7);
	$m =  $numday % 7;
	if($m == 0){
		$numweek = $n;
	}else{
		$numweek = $n + 1;
	}
	$i = 1;
	$arrweek = array();
	for($i = 1; $i <= $numweek; $i++){
		$weeks = array();
		$weeks = checkWeeks($startdate , $enddate);
		if($i == 1){
			$weeks['start_week'] = $start_date;
		}
		$arrweek[$i] = $weeks;

	}
	return $arrweek;
}

function checkWeeks($startdate , $enddate){
	if($startdate != null && $enddate != null){
		$weeks = array();
		$endd = $enddate->format('Y-m-d 23:59:59');
		$beginw = $startdate;
		$bgw = $beginw->format('Y-m-d 00:00:00');
		$beginw->modify('+7 day');
		$endw = $beginw->format('Y-m-d 23:59:59');
		$weeks['start_week'] = $bgw;
		if(strtotime($endw) > strtotime($endd)) {
			$weeks['end_week'] = $endd;
		}else{
			$endwk = new DateTime($endw);
			$endwk->modify('-1 day');
			$ewk = $endwk->format('Y-m-d 23:59:59');
			$weeks['end_week'] = $ewk;
		}

		return $weeks;
	}
}


function isPublishedWeek($programid=null, $week=null){    ////check published week
    $db = \Joomla\CMS\Factory::getDbo();
    $query = $db->getQuery(true);
    $query->select('*')
        ->from($db->quoteName('#__programweeks'))
        ->where('program_id = '.$programid)
        ->where('week = '. $week)
    ;
    $db->setQuery($query);
    $rs = $db->loadResult();
    if($rs && $rs > 0){
        return true;
    }
    else{
        return false;
    }
}

function isPublishedCity($programid=null, $week=null, $city=null){    ////check published week
    $db = \Joomla\CMS\Factory::getDbo();
    $query = $db->getQuery(true);
    $query->select('*')
        ->from($db->quoteName('#__programcity'))
        ->where('program_id = '.$programid)
        ->where('week = '. $week)
        ->where('city = '. $city)
    ;
    $db->setQuery($query);
    $rs = $db->loadResult();
    if($rs && $rs > 0){
        return true;
    }
    else{
        return false;
    }
}