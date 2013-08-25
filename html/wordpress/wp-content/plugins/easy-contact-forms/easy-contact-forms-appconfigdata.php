<?php
/**
 * @file
 * Easy Contact Forms configuration constants.
 */
/*  Copyright championforms.com, 2012-2013 | http://championforms.com  
 * -----------------------------------------------------------
 * Easy Contact Forms
 *
 * This product is distributed under terms of the GNU General Public License. http://www.gnu.org/licenses/gpl-2.0.txt.
 * 
 */


	DEFINE('EASYCONTACTFORMS__helpRoot',	'http://easy-contact-forms.com');
	DEFINE('EASYCONTACTFORMS__prodVersion', '1.4.2');
	$ds = DIRECTORY_SEPARATOR;
	/**
	 * A file system application root
	 */
	DEFINE('EASYCONTACTFORMS__APPLICATION_DIR', dirName(__FILE__));
	/**
	 * A session data root directory
	 */
	DEFINE('EASYCONTACTFORMS__SESSION_DIR', EASYCONTACTFORMS__APPLICATION_DIR);
	/**
	 * A web application root
	 */
	DEFINE('EASYCONTACTFORMS__engineRoot', admin_url( 'admin-ajax.php' ) . '?action=easy-contact-forms-submit');

	/**
	 * file folder subdir name
	 */
	DEFINE('EASYCONTACTFORMS__fileFolder', EasyContactFormsApplicationSettings::getInstance()->get('FileFolder'));
	/**
	 * A directory to store regular files and direct access files(images)
	 */
	DEFINE('EASYCONTACTFORMS__fileUploadDir', ABSPATH . 'wp-content/plugins/easycontact_templates' . $ds . EASYCONTACTFORMS__fileFolder);

	$base = get_bloginfo('wpurl');
	$base = rtrim($base, '/');
	if (!defined ('EASYCONTACTFORMS__APPLICATION_ROOT'))
		DEFINE('EASYCONTACTFORMS__APPLICATION_ROOT', $base);
	if (!defined ('EASYCONTACTFORMS__FILE_DONWLOAD'))
		DEFINE('EASYCONTACTFORMS__FILE_DONWLOAD', EASYCONTACTFORMS__APPLICATION_ROOT . '/wp-content/plugins/easycontact_templates');
	DEFINE('EASYCONTACTFORMS__notLoggenInRedirect', '<script>document.location.href="index.php"</script>');
