<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Students
 * @author     Nguyen Dinh <vb.dinhxuannguyen@gmail.com>
 * @copyright  2023 Nguyen Dinh
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Student\Component\Students\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;

/**
 * Students class.
 *
 * @since  1.0.0
 */
class EcardController extends FormController
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional
	 * @param   array   $config  Configuration array for model. Optional
	 *
	 * @return  object	The model
	 *
	 * @since   1.0.0
	 */
	public function getModel($name = 'Ecard', $prefix = 'Site', $config = array())
	{
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}

    public function checkViewCard() {
        $code      = $_GET['code'];
        $app       = Factory::getApplication();
        $session   = Factory::getSession();
        $formData  = $app->input->get('jform', array(), 'array');
        $birthday  = $formData['birthday'];
        $sBirthday = $session->get('sBirthday');
        if (trim($birthday) == $sBirthday) {
            $session->set('canViewECard', $code);
            $ecardUrl = URI::root().'e-card?code='.base64_decode($code);
            $this->setRedirect($ecardUrl);
        } else {
            $app->enqueueMessage('Ngày sinh bạn nhập chưa đúng! Vui lòng thử lại', 'error');
            $this->setRedirect($_SERVER['REQUEST_URI']);
        }

    }
}
