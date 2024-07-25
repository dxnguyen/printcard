<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_footer
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
$infoweb = getInfoweb();
?>
<div class="mod-footer">
    <div class="footer1"><?php echo $infoweb->footer_text; ?></div>
    <p class="hotline text-center">Phone: <?php echo $infoweb->hotline; ?> -  Email: <?php echo $infoweb->email; ?></p>
</div>
