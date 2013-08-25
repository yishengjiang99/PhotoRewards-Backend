<?php

/**
 * @file
 *
 * 	EasyContactFormsBackOffice class definition
 */

/*  Copyright championforms.com, 2012-2013 | http://championforms.com  
 * -----------------------------------------------------------
 * Easy Contact Forms
 *
 * This product is distributed under terms of the GNU General Public License. http://www.gnu.org/licenses/gpl-2.0.txt.
 * 
 */

/**
 * 	EasyContactFormsBackOffice
 *
 * 	Performs service functions
 *
 */
class EasyContactFormsBackOffice {

	/**
	 * 	sendNotification
	 *
	 * 	sends an email message in response to user actions
	 *
	 * @param int $uid
	 * 	user id
	 * @param string $type
	 * 	object type
	 * @param int $oid
	 * 	object id
	 * @param string $text
	 * 	message text
	 */
	function sendNotification($uid, $type, $oid, $text) {

		$sender = $this->getSenderData($uid);
		if (!$sender) {
			return;
		}
		$emaillist = $this->getListMemberEmails($type, $oid);
		if (sizeof($emaillist) == 0) {
			return;
		}
		$object = $this->prepareObject($type, $oid);
		$template = $this->getTemplate($type);
		$message = $this->fillInTemplate($template, $object);
		$message = $this->merge($message, $text);
		$this->send($message, $emaillist, $sender);

	}

	/**
	 * 	getSenderData
	 *
	 * 	get current user data from a database
	 *
	 * @param int $uid
	 * 	a current user id
	 *
	 * @return object
	 * 	current user info
	 */
	function getSenderData($uid) {

		if (empty($uid)) {
			return NULL;
		}

		$query = "SELECT
				CONCAT(Users.Description,' ', Users.Name) AS name,
				Users.email,
				Users.email2
			FROM
				#wp__easycontactforms_users AS Users
			WHERE
				Users.id='$uid'";

		$result = EasyContactFormsDB::getObjects($query);
		$sender = $result[0];
		$sender->email = empty($sender->email) ? $sender->email2 : $sender->email;
		if (empty($sender->email)) {
			return NULL;
		}
		return $sender;

	}

	/**
	 * 	getListMemberEmails
	 *
	 * 	gets user data of people in the mailing list
	 *
	 * @param string $type
	 * 	object type
	 * @param int $oid
	 * 	object id
	 *
	 * @return array
	 * 	an array of contact data items
	 */
	function getListMemberEmails($type, $oid) {

		$dbobjname = EasyContactFormsDB::getTableName($type . '_MailingLists');
		$query = "SELECT Contacts FROM $dbobjname WHERE $type='$oid'";
		$rs = EasyContactFormsDB::getObjects($query);
		$result = array();
		foreach ($rs as $item) {
			$user = $this->getSenderData($item->Contacts);
			if (! is_null($user)) {
				$result[$user->email] = $user->email;
			}
		}

		$dbobjname = EasyContactFormsDB::getTableName($type);
		$query = "SELECT ObjectOwner FROM $dbobjname WHERE id='$oid'";
		$ooid = EasyContactFormsDB::getValue($query);
		$user = $this->getSenderData($ooid);
		if (! is_null($user)) {
			$result[$user->email] = $user->email;
		}

		return $result;

	}

	/**
	 * 	prepareObject
	 *
	 * 	Prepares object data
	 *
	 * @param string $type
	 * 	object type
	 * @param int $id
	 * 	object id
	 *
	 * @return object
	 * 	an initialized object
	 */
	function prepareObject($type, $id) {
		return (object) array();
	}

	/**
	 * 	getTemplate
	 *
	 * 	gets a object type specific email template form application settings
	 *
	 * @param string $type
	 * 	object type
	 *
	 * @return object
	 * 	the found email templae
	 */
	function getTemplate($type) {

		$template = (object) array('subject' => '', 'body' => '', 'ishtml' => FALSE);
		$ishtml_pn = $type . '_EmailFormatHTML';
		$bd_pn = $type . '_EmailTemplate';
		$template->ishtml
			= EasyContactFormsApplicationSettings::getInstance()->get($ishtml_pn);
		$template->body
			= EasyContactFormsApplicationSettings::getInstance()->get($bd_pn);
		return $template;

	}

	/**
	 * 	fillInTemplate
	 *
	 * 	fills the template with object data
	 *
	 * @param object $template
	 * 	email template
	 * @param string $object
	 * 	object type
	 *
	 * @return object
	 * 	the filled template
	 */
	function fillInTemplate($template, $object) {

		if (!is_object($object)) {
			$template->body = str_replace('{messagebody}', $object, $template->body);
		}
		else {
			$fields = get_object_vars($object);
			foreach ($fields as $fname => $fvalue) {
				$value = (
					(EasyContactFormsUtils::endsWith($fname, 'date') ||
					EasyContactFormsUtils::endsWith($fname, 'deadline')) &&
					is_numeric($fvalue)) ?
					EasyContactFormsUtils::getDate($fvalue) :
					$fvalue;
				if (isset($template->ishtml) && ($template->ishtml)) {
					$value = nl2br('' . $value);
				}
				if (isset($template->body)) {
					$fldcheck = EasyContactFormsBackOffice::fldTemplateReplace($template->body, $fname, $value);
					if (!$fldcheck) {
						$template->body = str_replace('{' . $fname . '}', $value, $template->body);
					}
					else {
						$template->body = $fldcheck;
					}
				}
				if (isset($template->subject)) {
					$fldcheck = EasyContactFormsBackOffice::fldTemplateReplace($template->subject, $fname, $value);
					if (!$fldcheck) {
						$template->subject = str_replace('{' . $fname . '}', $value, $template->body);
					}
					else {
						$template->subject = $fldcheck;
					}
				}
			}
		}
		return $template;

	}

	/**
	 * 	fldTemplateReplace
	 *
	 * 	replaces fld teplate with value
	 *
	 * @param  $text
	 * 
	 * @param  $template
	 * 
	 * @param  $value
	 * 
	 *
	 * @return object
	 * 
	 */
	function fldTemplateReplace($text, $template, $value) {

		$prefix = explode('.', $template);
		if (count($prefix) != 2) {
			return false;
		}
		$prefix = $prefix[0];
		if ('' . intval($prefix) != '' . $prefix) {
			return false;
		}
		$pattern = '#\{' . $prefix . '.(.+?)\}#s';
		$value = str_replace('$', '\$', $value);
		$content = preg_replace(
			$pattern,
			$value,
			$text
		);
		return $content;

	}

	/**
	 * 	merge
	 *
	 * 	merges a template and input text
	 *
	 * @param object $message
	 * 	a message template
	 * @param string $text
	 * 	user comment
	 *
	 * @return object
	 * 	ready-to-send message
	 */
	function merge($message, $text) {

		$message->subject = isset($text->subject) ?
			$text->subject :
			EasyContactFormsT::get('NotificationSubject');
		$bkp = md5($message->body);
		$message = $this->fillInTemplate($message, $text->body);
		if (md5($message->body) == $bkp) {
			$message->body .= $text->body;
		}
		return $message;

	}

	/**
	 * 	send
	 *
	 * 	sends a message
	 *
	 * @param object $message
	 * 	a message to send
	 * @param array $emaillist
	 * 	a mainling list
	 * @param object $sender
	 * 	sender data
	 * @param string $email
	 * 	custom email address to use instead of default
	 * @param  $attachments
	 * 
	 */
	function send($message, $emaillist, $sender, $email = '', $attachments) {

		if ($email == '') {
			$email = EasyContactFormsApplicationSettings::getInstance()->get('SendFrom');
		}
		if ($email == '') {
			$email = $sender->email;
		}
		$headers = $email != '' ? "From: $sender->name <$email>\r\n" : '';
		if (isset($message->replyto)) {
			$headers .= 'Reply-to: ';
			if (isset($message->replyto->name)) {
				$headers .= $message->replyto->name;
				
			}
			if (isset($message->replyto->email)) {
				$headeremail = $message->replyto->email;
				$headers .= "<{$headeremail}>";
				
			}
			$headers .= "\n";
		}

		if ($message->ishtml) {
			add_filter('wp_mail_content_type', create_function('', 'return "text/html"; '));
		}
		else {
			add_filter('wp_mail_content_type', create_function('', 'return "text/plain"; '));
		}
		global $easycontactforms_request;
		$easycontactforms_request->attachment = $attachments;
		wp_mail($emaillist, $message->subject, $message->body, $headers);

	}

	/**
	 * 	processHistory
	 *
	 * 	performs status/comment/history processing
	 *
	 * @param array $fieldvalues
	 * 	request field data
	 * @param string $type
	 * 	object type
	 * @param int $oid
	 * 	object id
	 * @param int $uid
	 * 	current user id
	 */
	function processHistory($fieldvalues, $type, $oid, $uid) {

		$object = EasyContactFormsClassLoader::getObject($type, TRUE, $oid);

		$is_st_changed = isset($fieldvalues->Status) &&
			($fieldvalues->Status != $object->get('Status'));
		$not_on_sn_chng_var = $type . '_NotifyOnStatusChange';

		$not_on_st_change = EasyContactFormsApplicationSettings::getInstance()->get($not_on_sn_chng_var);

		$is_n_comment = isset($fieldvalues->Comment) && !empty($fieldvalues->Comment);
		$not_on_new_comment_var = $type . '_NotifyOnNewComment';
		$not_on_new_comment = EasyContactFormsApplicationSettings::getInstance()->get($not_on_new_comment_var);

		$send_message = ($is_n_comment && $not_on_new_comment ) ||
			($is_st_changed && $not_on_st_change);

		if (! $is_st_changed && ! $is_n_comment) {
			return $fieldvalues;
		}

		$type_status_map = array();

		$objname = EasyContactFormsDB::getTableName($type_status_map[$type]);

		$status = '';
		if (isset($objname)) {
			$sid = $is_st_changed ?
				intval($fieldvalues->Status) : $object->get('Status');
			$status = EasyContactFormsDB::getValue("SELECT Description FROM $objname WHERE id='$sid'");
			$status .= ' -- ';
		}

		$user = EasyContactFormsDB::getValue(
				"SELECT CONCAT(Description,' ',Name) FROM #wp__easycontactforms_users WHERE id='$uid'");

		$text = (object) array('body' => '', 'subject' => '');

		$comment = '';
		$delimeter = '-- ' . $user . ' -- ' . $status .
			date(EasyContactFormsT::get('DateTimeFormat')) . ' --<br/>';

		if ($is_n_comment) {
			$comment = $fieldvalues->Comment;
			unset($fieldvalues->Comment);
			$text->body = $comment;
			$stemplate = EasyContactFormsApplicationSettings::getInstance()->get('NewCommentSubject');
			$text->subject = sprintf(
				$stemplate,
				$type, $object->get('Description'), $status);
		}
		if ($is_st_changed) {
			$stemplate = EasyContactFormsApplicationSettings::getInstance()->get('StatusChangeSubject');
			$text->subject = sprintf(
				$stemplate,
				$type, $object->get('Description'), $status);
		}
		$history = $delimeter . $text->body . '<br/>' . $object->get('History');
		$fieldvalues->History = $history;

		if ($send_message) {
			$this->sendNotification($uid, $type, $oid, $text);
		}

		return $fieldvalues;

	}

}
