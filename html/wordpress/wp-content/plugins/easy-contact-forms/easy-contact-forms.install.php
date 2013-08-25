<?php
/**
 * @file
 * Install functions for the Easy Contact Forms plugin.
 */

function easycontactforms_install() {
	global $wpdb;
	$collate = '';
	if (!empty($wpdb->charset))
		$collate = 'DEFAULT CHARACTER SET '. $wpdb->charset;
	if (!empty($wpdb->collate))
		$collate .= ' COLLATE ' . $wpdb->collate;

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	require_once dirName(__FILE__) . DIRECTORY_SEPARATOR . 'easy-contact-forms-database.php';

	$sqls = array();
	$table_name = EasyContactFormsDB::wptn('#wp__easycontactforms_customformfields');
	if($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name) {
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX FieldSet";
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX Description";
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX CustomForms";
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX Type";
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX ListPosition";
	}
	$table_name = EasyContactFormsDB::wptn('#wp__easycontactforms_customformsentries');
	if($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name) {
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX Description";
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX CustomForms";
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX Users";
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX SiteUser";
	}
	$table_name = EasyContactFormsDB::wptn('#wp__easycontactforms_contacttypes');
	if($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name) {
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX Description";
	}
	$table_name = EasyContactFormsDB::wptn('#wp__easycontactforms_customformentryfiles');
	if($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name) {
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX Description";
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX CustomFormsEntries";
	}
	$table_name = EasyContactFormsDB::wptn('#wp__easycontactforms_customforms_mailinglists');
	if($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name) {
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX CustomForms";
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX Contacts";
	}
	$table_name = EasyContactFormsDB::wptn('#wp__easycontactforms_users');
	if($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name) {
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX ContactType";
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX CMSId";
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX Description";
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX Role";
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX descriptionname";
	}
	$table_name = EasyContactFormsDB::wptn('#wp__easycontactforms_customformentrystatistics');
	if($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name) {
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX CustomForms";
	}
	$table_name = EasyContactFormsDB::wptn('#wp__easycontactforms_customforms');
	if($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name) {
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX ObjectOwner";
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX Description";
	}
	$table_name = EasyContactFormsDB::wptn('#wp__easycontactforms_applicationsettings');
	if($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name) {
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX Description";
	}
	$table_name = EasyContactFormsDB::wptn('#wp__easycontactforms_options');
	if($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name) {
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX OptionGroup";
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX Description";
	}
	$table_name = EasyContactFormsDB::wptn('#wp__easycontactforms_customformfieldtypes');
	if($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name) {
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX Description";
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX ListPosition";
	}
	$table_name = EasyContactFormsDB::wptn('#wp__easycontactforms_files');
	if($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name) {
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX Docid";
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX typefieldid";
		$sqls[] = "ALTER TABLE {$table_name} DROP INDEX ObjectOwner";
	}
	foreach($sqls as $sql) {
		$wpdb->query($sql);
	}
				
	$sqls = array();
	$sqls[] = "CREATE TABLE #wp__easycontactforms_applicationsettings (
				id INT(11) NOT NULL auto_increment ,
				Description VARCHAR(200) NOT NULL DEFAULT '',
				TinyMCEConfig TEXT,
				UseTinyMCE BOOLEAN,
				ApplicationWidth INT(10),
				ApplicationWidth2 INT(10),
				DefaultStyle VARCHAR(50),
				DefaultStyle2 VARCHAR(50),
				SecretWord VARCHAR(50),
				NotLoggenInText TEXT,
				FileFolder VARCHAR(900),
				SendFrom VARCHAR(100),
				FixJSLoading BOOLEAN,
				FormCompletionMinTime INT(10),
				FormCompletionMaxTime INT(10),
				FixStatus0 BOOLEAN,
				ProductVersion VARCHAR(25),
				PhoneRegEx VARCHAR(100),
				DateFormat VARCHAR(500),
				DateTimeFormat VARCHAR(500),
				InitTime INT(11),
				ShowPoweredBy BOOLEAN,
				PRIMARY KEY  (id),
				INDEX Description (Description)) $collate;";
				
	$sqls[] = "CREATE TABLE #wp__easycontactforms_contacttypes (
				id INT(11) NOT NULL auto_increment ,
				Description VARCHAR(200) NOT NULL DEFAULT '',
				Notes TEXT,
				PRIMARY KEY  (id),
				INDEX Description (Description)) $collate;";
				
	$sqls[] = "CREATE TABLE #wp__easycontactforms_customformentryfiles (
				id INT(11) NOT NULL auto_increment ,
				Description VARCHAR(200) NOT NULL DEFAULT '',
				Date INT(11),
				CustomFormsEntries INT(11) NOT NULL DEFAULT 0,
				PRIMARY KEY  (id),
				INDEX Description (Description),
				INDEX CustomFormsEntries (CustomFormsEntries)) $collate;";
				
	$sqls[] = "CREATE TABLE #wp__easycontactforms_customformentrystatistics (
				id INT(11) NOT NULL auto_increment ,
				PageName VARCHAR(300),
				TotalEntries INT(10),
				IncludeIntoReporting BOOLEAN,
				CustomForms INT(11) NOT NULL DEFAULT 0,
				Impressions INT(10),
				PRIMARY KEY  (id),
				INDEX CustomForms (CustomForms)) $collate;";
				
	$sqls[] = "CREATE TABLE #wp__easycontactforms_customformfields (
				id INT(11) NOT NULL auto_increment ,
				Description VARCHAR(200) NOT NULL DEFAULT '',
				Type INT(11) NOT NULL DEFAULT 0,
				Settings TEXT,
				Template TEXT,
				ListPosition INT(10) NOT NULL DEFAULT 0,
				CustomForms INT(11) NOT NULL DEFAULT 0,
				FieldSet INT(10),
				PRIMARY KEY  (id),
				INDEX FieldSet (FieldSet),
				INDEX Description (Description),
				INDEX CustomForms (CustomForms),
				INDEX Type (Type),
				INDEX ListPosition (ListPosition)) $collate;";
				
	$sqls[] = "CREATE TABLE #wp__easycontactforms_customformfieldtypes (
				id INT(11) NOT NULL auto_increment ,
				Description VARCHAR(200) NOT NULL DEFAULT '',
				CssClass VARCHAR(100),
				Settings TEXT,
				Signature TEXT,
				ListPosition INT(10) NOT NULL DEFAULT 0,
				ValueField BOOLEAN,
				HelpLink TEXT,
				PRIMARY KEY  (id),
				INDEX Description (Description),
				INDEX ListPosition (ListPosition)) $collate;";
				
	$sqls[] = "CREATE TABLE #wp__easycontactforms_customforms (
				id INT(11) NOT NULL auto_increment ,
				Description VARCHAR(200) NOT NULL DEFAULT '',
				NotificationSubject VARCHAR(200),
				SendFrom VARCHAR(200),
				SendConfirmation BOOLEAN,
				ConfirmationSubject VARCHAR(200),
				ConfirmationText TEXT,
				Redirect BOOLEAN,
				RedirectURL TEXT,
				ShortCode VARCHAR(300),
				Template BOOLEAN,
				ObjectOwner INT(11) NOT NULL DEFAULT 0,
				SubmissionSuccessText TEXT,
				StyleSheet TEXT,
				HTML MEDIUMTEXT,
				SendFromAddress VARCHAR(200),
				ShowSubmissionSuccess BOOLEAN,
				SuccessMessageClass VARCHAR(200),
				FailureMessageClass VARCHAR(200),
				Width INT(10),
				WidthUnit VARCHAR(5),
				LineHeight INT(10),
				LineHeightUnit VARCHAR(5),
				FormClass VARCHAR(200),
				FormStyle TEXT,
				Style VARCHAR(50),
				ConfirmationStyleSheet TEXT,
				TotalEntries INT(10),
				TotalProcessedEntries INT(10),
				Impressions INT(10),
				NotificationText TEXT,
				IncludeVisitorsAddressInReplyTo BOOLEAN,
				ReplyToNameTemplate VARCHAR(200),
				ConfirmationReplyToName VARCHAR(200),
				ConfirmationReplyToAddress VARCHAR(200),
				NotificationStyleSheet TEXT,
				SendConfirmationAsText BOOLEAN,
				SendNotificationAsText BOOLEAN,
				FadingDelay INT(10),
				MessageDelay INT(10),
				IncludeIntoReporting BOOLEAN,
				PRIMARY KEY  (id),
				INDEX ObjectOwner (ObjectOwner),
				INDEX Description (Description)) $collate;";
				
	$sqls[] = "CREATE TABLE #wp__easycontactforms_customforms_mailinglists (
				id INT(11) NOT NULL auto_increment ,
				CustomForms INT(11) NOT NULL DEFAULT 0,
				Contacts INT(11) NOT NULL DEFAULT 0,
				PRIMARY KEY  (id),
				INDEX CustomForms (CustomForms),
				INDEX Contacts (Contacts)) $collate;";
				
	$sqls[] = "CREATE TABLE #wp__easycontactforms_customformsentries (
				id INT(11) NOT NULL auto_increment ,
				Date INT(11),
				Content MEDIUMTEXT,
				Header TEXT,
				Data TEXT,
				CustomForms INT(11) NOT NULL DEFAULT 0,
				Users INT(11) NOT NULL DEFAULT 0,
				Description VARCHAR(200) NOT NULL DEFAULT '',
				SiteUser INT(11) NOT NULL DEFAULT 0,
				PageName VARCHAR(300),
				PRIMARY KEY  (id),
				INDEX Description (Description),
				INDEX CustomForms (CustomForms),
				INDEX Users (Users),
				INDEX SiteUser (SiteUser)) $collate;";
				
	$sqls[] = "CREATE TABLE #wp__easycontactforms_files (
				id INT(11) NOT NULL auto_increment ,
				Doctype VARCHAR(80),
				Docfield VARCHAR(80),
				Docid INT(10),
				Name VARCHAR(300),
				Type VARCHAR(80),
				Size INT(10),
				Protected BOOLEAN,
				Webdir BOOLEAN,
				Count INT(11),
				Storagename VARCHAR(300),
				ObjectOwner INT(11),
				PRIMARY KEY  (id),
				INDEX Docid (Docid),
				INDEX typefieldid (Doctype, Docfield, Docid),
				INDEX ObjectOwner (ObjectOwner)) $collate;";
				
	$sqls[] = "CREATE TABLE #wp__easycontactforms_options (
				id INT(11) NOT NULL auto_increment ,
				Description VARCHAR(200) NOT NULL DEFAULT '',
				Value TEXT,
				OptionGroup VARCHAR(20),
				PRIMARY KEY  (id),
				INDEX OptionGroup (OptionGroup),
				INDEX Description (Description)) $collate;";
				
	$sqls[] = "CREATE TABLE #wp__easycontactforms_roles (
				id INT(11) NOT NULL auto_increment ,
				Description VARCHAR(100) NOT NULL DEFAULT '',
				Admin BOOLEAN,
				Employee BOOLEAN,
				PRIMARY KEY  (id)) $collate;";
				
	$sqls[] = "CREATE TABLE #wp__easycontactforms_users (
				id INT(11) NOT NULL auto_increment ,
				Description VARCHAR(200) NOT NULL DEFAULT '',
				Name VARCHAR(100),
				ContactType INT(11) NOT NULL DEFAULT 0,
				Birthday INT(11),
				Role INT(11) NOT NULL DEFAULT 0,
				CMSId INT(11),
				Notes TEXT,
				email VARCHAR(100),
				email2 VARCHAR(100),
				Cell VARCHAR(30),
				Phone1 VARCHAR(30),
				Phone2 VARCHAR(30),
				Phone3 VARCHAR(30),
				SkypeId VARCHAR(100),
				Website VARCHAR(200),
				ContactField3 TEXT,
				ContactField4 TEXT,
				Country VARCHAR(300),
				Address TEXT,
				City VARCHAR(300),
				State VARCHAR(300),
				Zip VARCHAR(20),
				Comment TEXT,
				History TEXT,
				Options TEXT,
				PRIMARY KEY  (id),
				INDEX ContactType (ContactType),
				INDEX CMSId (CMSId),
				INDEX Description (Description),
				INDEX Role (Role),
				INDEX descriptionname (Description, Name)) $collate;";
				
	$sqls[] = "CREATE TABLE #wp__easycontactforms_acl (
				id INT(11) NOT NULL auto_increment,
				objtype VARCHAR(50) NOT NULL,
				method VARCHAR(50) NOT NULL,
				name VARCHAR(50) NOT NULL,
				role VARCHAR(50) NOT NULL,
				PRIMARY KEY  (id)) $collate;";
				
	$sqls[] = "CREATE TABLE #wp__easycontactforms_sessions (
				id INT(11) NOT NULL auto_increment,
				opentime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				value TEXT,
				sid CHAR(32) NOT NULL,
				PRIMARY KEY  (id)) $collate;";
				
	foreach ($sqls as $sql){
		$sql = EasyContactFormsDB::wptn($sql);
		dbDelta($sql);
	}
}
function easycontactforms_install_data() {
	global $current_user, $wpdb;
	$userid = NULL;
	if (isset($current_user)) {;
		$userid = $current_user->ID;
	}
	$adminemail = get_option('admin_email');

	$rows = array(
		array(
			'id' => 254,
			'Description' => 'Flores',
			'Name' => 'Leroy',
			'ContactType' => 1,
			'Birthday' => 330213600,
			'Role' => 4,
			'Notes' => 'Suspendisse potenti.',
			'email' => 'leroy@example.com',
			'email2' => 'leroy2@example.com',
			'Cell' => '+11 222-7766',
			'Phone1' => '+60 033-3993',
			'Phone2' => '+37 744-4000',
			'Phone3' => '+00 999-9977',
			'ContactField3' => 'Fusce massa odio, aliquam ac ullamcorper congue, rutrum sed felis.',
			'ContactField4' => 'In nunc purus, volutpat vitae pharetra non, mattis vel quam.',
			'Country' => 'USA',
			'Address' => '258 Bell Street',
			'City' => 'New York',
			'State' => 'NY',
			'Zip' => '10031',
		),
		array(
			'id' => 225,
			'Description' => 'Admin',
			'Name' => 'Admin',
			'ContactType' => 4,
			'Birthday' => 109202400,
			'Role' => 1,
			'CMSId' => $userid,
			'email' => $adminemail,
		),
		array(
			'id' => 255,
			'Description' => 'Jackson',
			'Name' => 'Brian',
			'ContactType' => 1,
			'Birthday' => 297036000,
			'Role' => 4,
			'Notes' => 'Duis purus ipsum, consectetur quis scelerisque in, fringilla id nunc. In bibendum eros quis nulla tempus vitae iaculis ante euismod.',
			'email' => 'brian@example.com',
			'email2' => 'brian2@example.com',
			'Cell' => '+22 444-3322',
			'Phone1' => '+55 115-5226',
			'Phone2' => '+66 322-1228',
			'Phone3' => '+80 014-4877',
			'ContactField3' => 'Cras massa libero, laoreet non semper id, vulputate nec neque.',
			'ContactField4' => 'Aliquam facilisis dolor id diam tempus sed vestibulum magna varius.',
			'Country' => 'USA',
			'Address' => '18 Filbert Street',
			'City' => 'Ridley Park',
			'State' => 'PA',
			'Zip' => '19078',
		),
	);

	$table_name = EasyContactFormsDB::wptn('#wp__easycontactforms_users');
	$count = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $table_name . ';' );
	if ($count == 0) {
		foreach ($rows as $row) {
			$wpdb->insert($table_name, $row);
		}
	}


	$rows = array(
		array(
			'id' => 659,
			'Description' => 'Last Name',
			'Type' => 14,
			'Settings' => '<?xml version="1.0"?>
<form><LabelPosition>left-align-left</LabelPosition><Advanced/><LabelCSSClass/><LabelCSSStyle/><ShowDescription>off</ShowDescription><Description/><DescriptionPosition>bottom</DescriptionPosition><DescriptionCSSClass>ufo-customfields-field-description</DescriptionCSSClass><DescriptionCSSStyle/><SetDefaultValue>off</SetDefaultValue><DefaultValue/><IsBlankValue>on</IsBlankValue><DefaultValueCSSClass/><Required>off</Required><RequiredMessage>This field is required</RequiredMessage><RequiredMessagePosition>right</RequiredMessagePosition><SetRequiredSuffix>on</SetRequiredSuffix><RequiredSuffix>*</RequiredSuffix><RequiredSuffixCSSClass>ufo-customfields-required-suffix</RequiredSuffixCSSClass><RequiredSuffixCSSStyle/><AbsolutePosition>on</AbsolutePosition><InvalidCSSClass/><RequiredMessageCSSClass/><RequiredMessageCSSStyle/><Validate>off</Validate><MinLength/><MaxLength/><SetValidMessage>off</SetValidMessage><ValidMessageAbsolutePosition>on</ValidMessageAbsolutePosition><ValidMessage/><ValidMessagePosition>right</ValidMessagePosition><ValidCSSClass/><ValidCSSStyle/><SetStyle>off</SetStyle><CSSClass/><CSSStyle/><RowCSSClass/><SetSize>off</SetSize><Width>230</Width><WidthUnit>px</WidthUnit><Label><![CDATA[Last Name]]></Label><ShowLabel><![CDATA[on]]></ShowLabel></form>',
			'Template' => '<field>
      <ShowLabel position="left"><![CDATA[
   <label for=\'ufo-field-id-659\'  style=\'text-align:left\'>
     Last Name   </label>]]></ShowLabel>
            <Input ><![CDATA[<input type=\'text\' id=\'ufo-field-id-659\' value=\'{id-659}\' name=\'id-659\' >]]></Input>
</field>',
			'ListPosition' => 639,
			'CustomForms' => 2,
			'FieldSet' => 622,
		),
		array(
			'id' => 622,
			'Description' => 'Section',
			'Type' => 2,
			'Settings' => '<?xml version="1.0"?>
<form>
  
  
  <LabelTagName>h3</LabelTagName>
  <Advanced/>
  <LabelCSSClass/>
  <LabelCSSStyle/>
  
  <Description/>
  
  
  <DescriptionCSSStyle/>
  
  <CSSClass/>
  
  
  <Width>230</Width>
  <WidthUnit>px</WidthUnit>
<DescriptionCSSClass><![CDATA[ufo-customfields-container-description]]></DescriptionCSSClass><DescriptionPosition><![CDATA[top-inside]]></DescriptionPosition><CSSStyle><![CDATA[]]></CSSStyle><SetStyle><![CDATA[off]]></SetStyle><SetSize><![CDATA[off]]></SetSize><ShowDescription><![CDATA[off]]></ShowDescription><ShowLabel><![CDATA[off]]></ShowLabel><AddCF>off</AddCF><Label><![CDATA[Section]]></Label></form>',
			'Template' => '<field>
    <Container containertag="div" addcf="off"><![CDATA[<div>
     ]]></Container>
</field>',
			'ListPosition' => 625,
			'CustomForms' => 2,
			'FieldSet' => 622,
		),
		array(
			'id' => 630,
			'Description' => 'Submit',
			'Type' => 6,
			'Settings' => '<?xml version="1.0"?>
<form>
  
  
  
  <Advanced/>
  <LabelCSSClass/>
  <LabelCSSStyle/>
  <SetStyle>off</SetStyle>
  <CSSClass/>
  <CSSStyle/>
  <RowCSSClass/>
  <SetSize>off</SetSize>
  <Width>100</Width>
  <WidthUnit>px</WidthUnit>
<Label><![CDATA[Submit]]></Label><InputPosition><![CDATA[left]]></InputPosition><ShowLabel><![CDATA[on]]></ShowLabel></form>',
			'Template' => '<field><Validation><![CDATA[<script type=\'text/javascript\'>var c = {};c.id = \'ufo-field-id-630\';c.form = \'ufo-form-id-2\';c.Label = \'Submit\';ufoFormsConfig.submits.push(c);</script>]]></Validation><Input><![CDATA[<span id=\'ufo-field-id-630-span\'><noscript><button type=\'submit\' id=\'ufo-field-id-630\' name=\'id-630\' >Submit</button></noscript></span>]]></Input></field>',
			'ListPosition' => 675,
			'CustomForms' => 2,
			'FieldSet' => 622,
		),
		array(
			'id' => 625,
			'Description' => 'Email',
			'Type' => 5,
			'Settings' => '<?xml version="1.0"?>
<form>
  
  <Label>Email</Label>
  
  <Advanced/>
  <LabelCSSClass/>
  <LabelCSSStyle/>
  <ShowDescription>off</ShowDescription>
  <Description/>
  <DescriptionPosition>bottom</DescriptionPosition>
  <DescriptionCSSClass>ufo-customfields-field-description</DescriptionCSSClass>
  <DescriptionCSSStyle/>
  <SetDefaultValue>off</SetDefaultValue>
  <DefaultValue>Your email</DefaultValue>
  <IsBlankValue>on</IsBlankValue>
  <DefaultValueCSSClass/>
  <Required>on</Required>
  <RequiredMessage>Please enter you email</RequiredMessage>
  
  <SetRequiredSuffix>on</SetRequiredSuffix>
  <RequiredSuffix>*</RequiredSuffix>
  <RequiredSuffixCSSClass>ufo-customfields-required-suffix</RequiredSuffixCSSClass>
  <RequiredSuffixCSSStyle/>
  
  <InvalidCSSClass/>
  <RequiredMessageCSSClass/>
  <RequiredMessageCSSStyle/>
  <Validate>on</Validate>
  
  <ValidMessageAbsolutePosition>on</ValidMessageAbsolutePosition>
  <ValidMessage/>
  <ValidMessagePosition>right</ValidMessagePosition>
  <ValidCSSClass/>
  <ValidCSSStyle/>
  <SetStyle>off</SetStyle>
  <CSSClass/>
  <CSSStyle/>
  <RowCSSClass/>
  <SetSize>off</SetSize>
  <Width>230</Width>
  <WidthUnit>px</WidthUnit>
  
  
  
<LinkToAppField><![CDATA[Users_email]]></LinkToAppField><SetValidMessage><![CDATA[on]]></SetValidMessage><AbsolutePosition><![CDATA[on]]></AbsolutePosition><SetContactOptions><![CDATA[on]]></SetContactOptions><LabelPosition><![CDATA[left-align-left]]></LabelPosition><RequiredMessagePosition><![CDATA[right]]></RequiredMessagePosition><RegistredUsersOptions><![CDATA[showfill]]></RegistredUsersOptions><ShowLabel><![CDATA[on]]></ShowLabel></form>',
			'Template' => '<field><ShowLabel position="left"><![CDATA[<label for=\'ufo-field-id-625\'  style=\'text-align:left\'>Email<span class=\'ufo-customfields-required-suffix\'>*</span></label>]]></ShowLabel><RequiredMessage position="right"><![CDATA[<div id=\'ufo-field-id-625-invalid\'  style=\'display:none\'></div>]]></RequiredMessage><ValidMessage position="right"><![CDATA[<div id=\'ufo-field-id-625-valid\'  style=\'display:none\'></div>]]></ValidMessage><Validation><![CDATA[<script type=\'text/javascript\'>ufoFormsConfig.validations.push({"events":{"blur":["required","email"]},"Required":true,"Validate":true,"showValid":true,"ValidMessageAbsolutePosition":true,"ValidMessagePosition":"right","RequiredMessage":"Please enter you email","AbsolutePosition":true,"RequiredMessagePosition":"right","id":"ufo-field-id-625","form":"ufo-form-id-2"});</script>]]></Validation><Input ><![CDATA[<input type=\'text\' id=\'ufo-field-id-625\' value=\'{id-625}\' name=\'id-625\' >]]></Input></field>',
			'ListPosition' => 648,
			'CustomForms' => 2,
			'FieldSet' => 622,
		),
		array(
			'id' => 647,
			'Description' => 'Last name',
			'Type' => 4,
			'Settings' => '<?xml version="1.0"?>
<form>
  
  
  <LabelPosition>left-align-left</LabelPosition>
  <Advanced/>
  <LabelCSSClass/>
  <LabelCSSStyle/>
  <ShowDescription>off</ShowDescription>
  <Description/>
  <DescriptionPosition>bottom</DescriptionPosition>
  <DescriptionCSSClass>ufo-customfields-field-description</DescriptionCSSClass>
  <DescriptionCSSStyle/>
  <SetDefaultValue>off</SetDefaultValue>
  <DefaultValue/>
  <IsBlankValue>on</IsBlankValue>
  <DefaultValueCSSClass/>
  
  
  
  <SetRequiredSuffix>on</SetRequiredSuffix>
  <RequiredSuffix>*</RequiredSuffix>
  <RequiredSuffixCSSClass>ufo-customfields-required-suffix</RequiredSuffixCSSClass>
  <RequiredSuffixCSSStyle/>
  
  <InvalidCSSClass/>
  <RequiredMessageCSSClass/>
  <RequiredMessageCSSStyle/>
  
  
  
  
  <ValidMessageAbsolutePosition>on</ValidMessageAbsolutePosition>
  <ValidMessage/>
  <ValidMessagePosition>right</ValidMessagePosition>
  <ValidCSSClass/>
  <ValidCSSStyle/>
  <SetStyle>off</SetStyle>
  <CSSClass/>
  <CSSStyle/>
  <RowCSSClass/>
  <SetSize>off</SetSize>
  <Width>230</Width>
  <WidthUnit>px</WidthUnit>
  
  
  
<Label><![CDATA[Last name]]></Label><Required><![CDATA[on]]></Required><Validate><![CDATA[on]]></Validate><SetValidMessage><![CDATA[on]]></SetValidMessage><AbsolutePosition><![CDATA[on]]></AbsolutePosition><LinkToAppField><![CDATA[Users_Description]]></LinkToAppField><SetContactOptions><![CDATA[on]]></SetContactOptions><MinLength><![CDATA[2]]></MinLength><MaxLength><![CDATA[45]]></MaxLength><RequiredMessage><![CDATA[Your last name is required (from 2 to 45 characters)]]></RequiredMessage><RequiredMessagePosition><![CDATA[right]]></RequiredMessagePosition><RegistredUsersOptions><![CDATA[showfill]]></RegistredUsersOptions><ShowLabel><![CDATA[on]]></ShowLabel></form>',
			'Template' => '<field>
      <ShowLabel position="left"><![CDATA[
   <label for=\'ufo-field-id-647\'  style=\'text-align:left\'>
     Last name         <span class=\'ufo-customfields-required-suffix\'>
           *         </span>
            </label>]]></ShowLabel>
          <RequiredMessage position="right"><![CDATA[<div id=\'ufo-field-id-647-invalid\'  style=\'display:none\'></div>]]></RequiredMessage>
        <ValidMessage position="right"><![CDATA[<div id=\'ufo-field-id-647-valid\'  style=\'display:none\'>
        </div>]]></ValidMessage>
        <Validation><![CDATA[<script type=\'text/javascript\'>ufoFormsConfig.validations.push({"events":{"blur":["required","minmax"]},"Required":true,"Validate":true,"showValid":true,"ValidMessageAbsolutePosition":true,"ValidMessagePosition":"right","RequiredMessage":"Your last name is required (from 2 to 45 characters)","AbsolutePosition":true,"RequiredMessagePosition":"right","min":"2","max":"45","id":"ufo-field-id-647","form":"ufo-form-id-2"});</script>]]></Validation>
    <Input ><![CDATA[<input type=\'text\' id=\'ufo-field-id-647\' value=\'{id-647}\' name=\'id-647\' >]]></Input>
</field>',
			'ListPosition' => 647,
			'CustomForms' => 2,
			'FieldSet' => 622,
		),
		array(
			'id' => 627,
			'Description' => 'First name',
			'Type' => 4,
			'Settings' => '<?xml version="1.0"?>
<form>
  
  
  
  <Advanced/>
  <LabelCSSClass/>
  <LabelCSSStyle/>
  <ShowDescription>off</ShowDescription>
  <Description/>
  <DescriptionPosition>bottom</DescriptionPosition>
  <DescriptionCSSClass>ufo-customfields-field-description</DescriptionCSSClass>
  <DescriptionCSSStyle/>
  <SetDefaultValue>off</SetDefaultValue>
  <DefaultValue/>
  <IsBlankValue>on</IsBlankValue>
  <DefaultValueCSSClass/>
  
  
  
  <SetRequiredSuffix>on</SetRequiredSuffix>
  <RequiredSuffix>*</RequiredSuffix>
  <RequiredSuffixCSSClass>ufo-customfields-required-suffix</RequiredSuffixCSSClass>
  <RequiredSuffixCSSStyle/>
  
  <InvalidCSSClass/>
  <RequiredMessageCSSClass/>
  <RequiredMessageCSSStyle/>
  
  
  
  
  <ValidMessageAbsolutePosition>on</ValidMessageAbsolutePosition>
  <ValidMessage/>
  <ValidMessagePosition>right</ValidMessagePosition>
  <ValidCSSClass/>
  <ValidCSSStyle/>
  <SetStyle>off</SetStyle>
  <CSSClass/>
  <CSSStyle/>
  <RowCSSClass/>
  <SetSize>off</SetSize>
  <Width>230</Width>
  <WidthUnit>px</WidthUnit>
  
  
  
<Required><![CDATA[on]]></Required><Validate><![CDATA[on]]></Validate><LinkToAppField><![CDATA[Users_Name]]></LinkToAppField><Label><![CDATA[First name]]></Label><SetValidMessage><![CDATA[on]]></SetValidMessage><AbsolutePosition><![CDATA[on]]></AbsolutePosition><SetContactOptions><![CDATA[on]]></SetContactOptions><MinLength><![CDATA[2]]></MinLength><MaxLength><![CDATA[45]]></MaxLength><RequiredMessagePosition><![CDATA[right]]></RequiredMessagePosition><RequiredMessage><![CDATA[Your first name is required (from 2 to 45 characters)]]></RequiredMessage><LabelPosition><![CDATA[left-align-left]]></LabelPosition><RegistredUsersOptions><![CDATA[showfill]]></RegistredUsersOptions><ShowLabel><![CDATA[on]]></ShowLabel></form>',
			'Template' => '<field>
      <ShowLabel position="left"><![CDATA[
   <label for=\'ufo-field-id-627\'  style=\'text-align:left\'>
     First name         <span class=\'ufo-customfields-required-suffix\'>
           *         </span>
            </label>]]></ShowLabel>
          <RequiredMessage position="right"><![CDATA[<div id=\'ufo-field-id-627-invalid\'  style=\'display:none\'></div>]]></RequiredMessage>
        <ValidMessage position="right"><![CDATA[<div id=\'ufo-field-id-627-valid\'  style=\'display:none\'>
        </div>]]></ValidMessage>
        <Validation><![CDATA[<script type=\'text/javascript\'>ufoFormsConfig.validations.push({"events":{"blur":["required","minmax"]},"Required":true,"Validate":true,"showValid":true,"ValidMessageAbsolutePosition":true,"ValidMessagePosition":"right","RequiredMessage":"Your first name is required (from 2 to 45 characters)","AbsolutePosition":true,"RequiredMessagePosition":"right","min":"2","max":"45","id":"ufo-field-id-627","form":"ufo-form-id-2"});</script>]]></Validation>
    <Input ><![CDATA[<input type=\'text\' id=\'ufo-field-id-627\' value=\'{id-627}\' name=\'id-627\' >]]></Input>
</field>',
			'ListPosition' => 643,
			'CustomForms' => 2,
			'FieldSet' => 622,
		),
		array(
			'id' => 648,
			'Description' => 'Your request',
			'Type' => 10,
			'Settings' => '<?xml version="1.0"?>
<form>
  
  
  
  <Advanced/>
  <LabelCSSClass/>
  <LabelCSSStyle/>
  
  
  
  <DescriptionCSSClass>ufo-customfields-field-description</DescriptionCSSClass>
  <DescriptionCSSStyle/>
  <SetDefaultValue>off</SetDefaultValue>
  <DefaultValue/>
  <IsBlankValue>on</IsBlankValue>
  <DefaultValueCSSClass/>
  
  <RequiredMessage>This field is required</RequiredMessage>
  <RequiredMessagePosition>right</RequiredMessagePosition>
  <SetRequiredSuffix>on</SetRequiredSuffix>
  <RequiredSuffix>*</RequiredSuffix>
  <RequiredSuffixCSSClass>ufo-customfields-required-suffix</RequiredSuffixCSSClass>
  <RequiredSuffixCSSStyle/>
  <AbsolutePosition>on</AbsolutePosition>
  <InvalidCSSClass/>
  <RequiredMessageCSSClass/>
  <RequiredMessageCSSStyle/>
  <Validate>off</Validate>
  <MinLength/>
  <MaxLength/>
  <SetValidMessage>off</SetValidMessage>
  <ValidMessageAbsolutePosition>on</ValidMessageAbsolutePosition>
  <ValidMessage/>
  <ValidMessagePosition>right</ValidMessagePosition>
  <ValidCSSClass/>
  <ValidCSSStyle/>
  <SetStyle>off</SetStyle>
  <CSSClass/>
  <CSSStyle/>
  <RowCSSClass/>
  
  
  <WidthUnit>px</WidthUnit>
  
  <HeightUnit>px</HeightUnit>
<Label><![CDATA[Your request]]></Label><Description><![CDATA[Please provide us with your request details]]></Description><DescriptionPosition><![CDATA[top]]></DescriptionPosition><Required><![CDATA[on]]></Required><SetSize><![CDATA[on]]></SetSize><LabelPosition><![CDATA[top-align-left]]></LabelPosition><ShowDescription><![CDATA[off]]></ShowDescription><Height><![CDATA[150]]></Height><Width><![CDATA[360]]></Width><ShowLabel><![CDATA[on]]></ShowLabel></form>',
			'Template' => '<field><ShowLabel position="top"><![CDATA[<label for=\'ufo-field-id-648\'  style=\'text-align:left\'>Your request<span class=\'ufo-customfields-required-suffix\'>*</span></label>]]></ShowLabel><RequiredMessage position="right"><![CDATA[<div id=\'ufo-field-id-648-invalid\'  style=\'display:none\'></div>]]></RequiredMessage><Validation><![CDATA[<script type=\'text/javascript\'>ufoFormsConfig.validations.push({"events":{"blur":["required"]},"Required":true,"RequiredMessage":"This field is required","AbsolutePosition":true,"RequiredMessagePosition":"right","id":"ufo-field-id-648","form":"ufo-form-id-2"});</script>]]></Validation><Input  width="360px"><![CDATA[<textarea id=\'ufo-field-id-648\' name=\'id-648\'  style=\'height:150px;width:360px\'>{id-648}</textarea>]]></Input></field>',
			'ListPosition' => 660,
			'CustomForms' => 2,
			'FieldSet' => 622,
		),
		array(
			'id' => 675,
			'Description' => 'Please fill in the fields below',
			'Type' => 2,
			'Settings' => '<?xml version="1.0"?>
<form>
  
  
  <LabelTagName>h3</LabelTagName>
  <Advanced/>
  <LabelCSSClass/>
  <LabelCSSStyle/>
  <ShowDescription>off</ShowDescription>
  <Description/>
  <DescriptionPosition>top</DescriptionPosition>
  <DescriptionCSSClass>ufo-customfields-container-description</DescriptionCSSClass>
  <DescriptionCSSStyle/>
  <SetStyle>off</SetStyle>
  <CSSClass/>
  <CSSStyle/>
  <AddCF>off</AddCF>
  <SetSize>off</SetSize>
  <Width>230</Width>
  <WidthUnit>px</WidthUnit>
<ShowLabel><![CDATA[off]]></ShowLabel><Label><![CDATA[Please fill in the fields below]]></Label></form>',
			'Template' => '<field><Container containertag="div" addcf="off"><![CDATA[<div>]]></Container></field>',
			'ListPosition' => 624,
			'CustomForms' => 2,
			'FieldSet' => 675,
		),
	);

	$table_name = EasyContactFormsDB::wptn('#wp__easycontactforms_customformfields');
	$count = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $table_name . ';' );
	if ($count == 0) {
		foreach ($rows as $row) {
			$wpdb->insert($table_name, $row);
		}
	}


	$rows = array(
		array(
			'id' => 2,
			'Description' => 'Contact form',
			'NotificationSubject' => 'New request received',
			'SendFrom' => 'Champion Forms',
			'SendConfirmation' => 0,
			'ConfirmationSubject' => 'We have received your request',
			'Redirect' => 0,
			'ShortCode' => '[champion_forms fid=2]',
			'Template' => 0,
			'ObjectOwner' => 225,
			'SubmissionSuccessText' => 'Thank you for contacting us! We are glad to hear from you.',
			'HTML' => '<script type=\'text/javascript\'>if (typeof(ecfconfig) == \'undefined\'){var ecfconfig={};}ecfconfig[2]={};var ufobaseurl =  \'http://192.168.1.100/wordpress-3.3/wp-admin/admin-ajax.php\';if (typeof(ufoFormsConfig) == \'undefined\') {var ufoFormsConfig = {};ufoFormsConfig.submits = [];ufoFormsConfig.resets = [];ufoFormsConfig.validations = [];}ufoFormsConfig.phonenumberre = /^(\+{0,1}\d{1,2})*\s*(\(?\d{3}\)?\s*)*\d{3}(-{0,1}|\s{0,1})\d{2}(-{0,1}|\s{0,1})\d{2}$/;</script><link href=\'http://192.168.1.100/wordpress-3.3/wp-content/plugins/champion-forms/forms/styles/easyform/css/std.css?ver=1.5\' rel=\'stylesheet\' type=\'text/css\'/><style>.ufo-row-659{display:none;}</style><div class=\'ufo-form\' id=\'ufo-form-id-2\'><noscript><form method=\'POST\'><input type=\'hidden\' name=\'cf-no-script\' value=\'1\'></noscript><input type=\'hidden\' value=\'ufo-form-id-2\' name=\'hidden-2\' id=\'ufo-form-hidden-2\'><input type=\'hidden\' value=\'{__pagename}\' name=\'ufo-form-pagename\' id=\'ufo-form-pagename\'>{preview}<input type=\'hidden\' value=\'{ufosignature}\' name=\'ufo-sign\' id=\'ufo-sign\'><div></div><div>
     <div class=\'ufo-fieldtype-14 ufo-customform-row ufo-row-659\' style=\'margin-top:2px;{display-659}\'><div class=\'ufo-cell-659-2-row\' id=\'ufo-cell-659-2\'><span class=\'ufo-cell-left\' id=\'ufo-cell-659-2-left\'>
   <label for=\'ufo-field-id-659\'  style=\'text-align:left\'>
     Last Name   </label></span><span class=\'ufo-cell-center\' id=\'ufo-cell-659-2-center\'><input type=\'text\' id=\'ufo-field-id-659\' value=\'{id-659}\' name=\'id-659\' ></span></div></div><div class=\'ufo-fieldtype-4 ufo-customform-row ufo-row-627\' style=\'margin-top:2px;{display-627}\'><div class=\'ufo-cell-627-2-row\' id=\'ufo-cell-627-2\'><span class=\'ufo-cell-left\' id=\'ufo-cell-627-2-left\'>
   <label for=\'ufo-field-id-627\'  style=\'text-align:left\'>
     First name         <span class=\'ufo-customfields-required-suffix\'>
           *         </span>
            </label></span><span class=\'ufo-cell-center\' id=\'ufo-cell-627-2-center\'><script type=\'text/javascript\'>ufoFormsConfig.validations.push({"events":{"blur":["required","minmax"]},"Required":true,"Validate":true,"showValid":true,"ValidMessageAbsolutePosition":true,"ValidMessagePosition":"right","RequiredMessage":"Your first name is required (from 2 to 45 characters)","AbsolutePosition":true,"RequiredMessagePosition":"right","min":"2","max":"45","id":"ufo-field-id-627","form":"ufo-form-id-2"});</script><input type=\'text\' id=\'ufo-field-id-627\' value=\'{id-627}\' name=\'id-627\' ></span><span class=\'ufo-cell-right\' id=\'ufo-cell-627-2-right\'><div id=\'ufo-field-id-627-invalid\'  style=\'display:none\'></div><div id=\'ufo-field-id-627-valid\'  style=\'display:none\'>
        </div></span></div></div><div class=\'ufo-fieldtype-4 ufo-customform-row ufo-row-647\' style=\'margin-top:2px;{display-647}\'><div class=\'ufo-cell-647-2-row\' id=\'ufo-cell-647-2\'><span class=\'ufo-cell-left\' id=\'ufo-cell-647-2-left\'>
   <label for=\'ufo-field-id-647\'  style=\'text-align:left\'>
     Last name         <span class=\'ufo-customfields-required-suffix\'>
           *         </span>
            </label></span><span class=\'ufo-cell-center\' id=\'ufo-cell-647-2-center\'><script type=\'text/javascript\'>ufoFormsConfig.validations.push({"events":{"blur":["required","minmax"]},"Required":true,"Validate":true,"showValid":true,"ValidMessageAbsolutePosition":true,"ValidMessagePosition":"right","RequiredMessage":"Your last name is required (from 2 to 45 characters)","AbsolutePosition":true,"RequiredMessagePosition":"right","min":"2","max":"45","id":"ufo-field-id-647","form":"ufo-form-id-2"});</script><input type=\'text\' id=\'ufo-field-id-647\' value=\'{id-647}\' name=\'id-647\' ></span><span class=\'ufo-cell-right\' id=\'ufo-cell-647-2-right\'><div id=\'ufo-field-id-647-invalid\'  style=\'display:none\'></div><div id=\'ufo-field-id-647-valid\'  style=\'display:none\'>
        </div></span></div></div><div class=\'ufo-fieldtype-5 ufo-customform-row ufo-row-625\' style=\'margin-top:2px;{display-625}\'><div class=\'ufo-cell-625-2-row\' id=\'ufo-cell-625-2\'><span class=\'ufo-cell-left\' id=\'ufo-cell-625-2-left\'><label for=\'ufo-field-id-625\'  style=\'text-align:left\'>Email<span class=\'ufo-customfields-required-suffix\'>*</span></label></span><span class=\'ufo-cell-center\' id=\'ufo-cell-625-2-center\'><script type=\'text/javascript\'>ufoFormsConfig.validations.push({"events":{"blur":["required","email"]},"Required":true,"Validate":true,"showValid":true,"ValidMessageAbsolutePosition":true,"ValidMessagePosition":"right","RequiredMessage":"Please enter you email","AbsolutePosition":true,"RequiredMessagePosition":"right","id":"ufo-field-id-625","form":"ufo-form-id-2"});</script><input type=\'text\' id=\'ufo-field-id-625\' value=\'{id-625}\' name=\'id-625\' ></span><span class=\'ufo-cell-right\' id=\'ufo-cell-625-2-right\'><div id=\'ufo-field-id-625-invalid\'  style=\'display:none\'></div><div id=\'ufo-field-id-625-valid\'  style=\'display:none\'></div></span></div></div><div class=\'ufo-fieldtype-10 ufo-customform-row ufo-row-648\' style=\'margin-top:2px;{display-648}\'><div class=\'ufo-cell-648-1-row\' id=\'ufo-cell-648-1\'><span class=\'ufo-cell-center\' style=\'width:360px\' id=\'ufo-cell-648-1-center\'><label for=\'ufo-field-id-648\'  style=\'text-align:left\'>Your request<span class=\'ufo-customfields-required-suffix\'>*</span></label></span><span class=\'ufo-cell-right\' id=\'ufo-cell-648-1-right\'><p style=\'display:none\'></p></span></div><div class=\'ufo-cell-648-2-row\' id=\'ufo-cell-648-2\'><span class=\'ufo-cell-center\' style=\'width:360px\' id=\'ufo-cell-648-2-center\'><script type=\'text/javascript\'>ufoFormsConfig.validations.push({"events":{"blur":["required"]},"Required":true,"RequiredMessage":"This field is required","AbsolutePosition":true,"RequiredMessagePosition":"right","id":"ufo-field-id-648","form":"ufo-form-id-2"});</script><textarea id=\'ufo-field-id-648\' name=\'id-648\'  style=\'height:150px;width:360px\'>{id-648}</textarea></span><span class=\'ufo-cell-right\' id=\'ufo-cell-648-2-right\'><div id=\'ufo-field-id-648-invalid\'  style=\'display:none\'></div></span></div></div><div class=\'ufo-fieldtype-6 ufo-customform-row ufo-row-630\' style=\'margin-top:2px;{display-630}\'><div class=\'ufo-cell-630-2-row\' id=\'ufo-cell-630-2\'><span class=\'ufo-cell-center\' id=\'ufo-cell-630-2-center\'><script type=\'text/javascript\'>var c = {};c.id = \'ufo-field-id-630\';c.form = \'ufo-form-id-2\';c.Label = \'Submit\';ufoFormsConfig.submits.push(c);</script><span id=\'ufo-field-id-630-span\'><noscript><button type=\'submit\' id=\'ufo-field-id-630\' name=\'id-630\' >Submit</button></noscript></span></span></div></div></div><div id=\'ufo-form-id-2-message\'></div><noscript></form></noscript></div>',
			'ShowSubmissionSuccess' => 1,
			'WidthUnit' => 'px',
			'LineHeight' => 2,
			'LineHeightUnit' => 'px',
			'Style' => 'easyform',
			'IncludeVisitorsAddressInReplyTo' => 1,
			'SendConfirmationAsText' => 0,
			'SendNotificationAsText' => 0,
			'IncludeIntoReporting' => 1,
		),
	);

	$table_name = EasyContactFormsDB::wptn('#wp__easycontactforms_customforms');
	$count = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $table_name . ';' );
	if ($count == 0) {
		foreach ($rows as $row) {
			$wpdb->insert($table_name, $row);
		}
	}


	$rows = array(
		array(
			'id' => 1,
			'Description' => 'Client',
			'Notes' => 'Maecenas eget lectus ut odio mattis fringilla. Nunc sem leo, interdum id euismod sit amet, varius vel lorem. Nam quis augue a lectus ultrices suscipit a facilisis lacus. Morbi at nisl sit amet nunc porttitor posuere id id risus. Vestibulum eget enim ornare augue venenatis placerat sed vitae mi. Nam elit justo, tincidunt id venenatis et, ullamcorper non tellus. Phasellus quis lorem tortor. <br /><br />Praesent ut facilisis odio. Maecenas congue neque ut nisi placerat vitae suscipit mauris fermentum. Praesent non sem tincidunt odio placerat tincidunt. Nulla gravida lectus sed urna mollis lacinia. Ut egestas viverra volutpat. Vestibulum quis nunc erat. Donec faucibus magna at quam condimentum convallis lobortis ante aliquet. Mauris tempor, ipsum a sollicitudin molestie, orci dui laoreet nulla, non accumsan erat libero a nisl. Quisque id luctus enim. Pellentesque id purus odio. Sed cursus pharetra enim eget tempor.',
		),
		array(
			'id' => 4,
			'Description' => 'Employee',
			'Notes' => 'Suspendisse potenti. <br /><br />Lorem ipsum dolor sit amet, consectetur adipiscing elit. In sagittis tincidunt tortor, non bibendum risus lobortis ac. Fusce at eros sed dolor aliquam vestibulum. Mauris eget ante sapien. Donec eu justo ac purus consectetur faucibus. Nullam consectetur scelerisque massa et scelerisque. Ut a eros justo, et pellentesque felis. Duis at mi erat, nec dapibus nulla. Nam laoreet, eros et tempor convallis, arcu ante fermentum eros, sit amet luctus tellus lorem id nisi. Phasellus tempus, tortor et lacinia fringilla, magna metus dignissim dolor, vel vulputate purus massa non turpis.',
		),
	);

	$table_name = EasyContactFormsDB::wptn('#wp__easycontactforms_contacttypes');
	$count = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $table_name . ';' );
	if ($count == 0) {
		foreach ($rows as $row) {
			$wpdb->insert($table_name, $row);
		}
	}


	$rows = array(
		array(
			'id' => 3,
			'Description' => 'Select',
			'CssClass' => 'ufo-customfield-select',
			'Settings' => '<form><ShowLabel>on</ShowLabel><Label>Select</Label><LabelPosition>left-align-left</LabelPosition><Advanced/><LabelCSSClass/><LabelCSSStyle/><ShowDescription>off</ShowDescription><Description/><DescriptionPosition>bottom</DescriptionPosition><DescriptionCSSClass>ufo-customfields-field-description</DescriptionCSSClass><DescriptionCSSStyle/><SetOptions>on</SetOptions><HasEmpty>off</HasEmpty><EmptyOption/><Options><option index="1">Option1</option><option index="2">Option2</option><option index="3">Option3</option></Options><Required>off</Required><RequiredMessage>This field is required</RequiredMessage><RequiredMessagePosition>right</RequiredMessagePosition><SetRequiredSuffix>on</SetRequiredSuffix><RequiredSuffix>*</RequiredSuffix><RequiredSuffixCSSClass>ufo-customfields-required-suffix</RequiredSuffixCSSClass><RequiredSuffixCSSStyle/><AbsolutePosition>on</AbsolutePosition><InvalidCSSClass/><RequiredMessageCSSClass/><RequiredMessageCSSStyle/><SetStyle>off</SetStyle><CSSClass/><CSSStyle/><RowCSSClass/><SetSize>off</SetSize><Width>230</Width><WidthUnit>px</WidthUnit></form>',
			'ListPosition' => 800,
			'ValueField' => 1,
		),
		array(
			'id' => 4,
			'Description' => 'Text',
			'CssClass' => 'ufo-customfield-text',
			'Settings' => '<form><ShowLabel>on</ShowLabel><Label>Text</Label><LabelPosition>left-align-left</LabelPosition><Advanced/><LabelCSSClass/><LabelCSSStyle/><ShowDescription>off</ShowDescription><Description/><DescriptionPosition>bottom</DescriptionPosition><DescriptionCSSClass>ufo-customfields-field-description</DescriptionCSSClass><DescriptionCSSStyle/><SetDefaultValue>off</SetDefaultValue><DefaultValue/><IsBlankValue>on</IsBlankValue><DefaultValueCSSClass/><Required>off</Required><RequiredMessage>This field is required</RequiredMessage><RequiredMessagePosition>right</RequiredMessagePosition><SetRequiredSuffix>on</SetRequiredSuffix><RequiredSuffix>*</RequiredSuffix><RequiredSuffixCSSClass>ufo-customfields-required-suffix</RequiredSuffixCSSClass><RequiredSuffixCSSStyle/><AbsolutePosition>on</AbsolutePosition><InvalidCSSClass/><RequiredMessageCSSClass/><RequiredMessageCSSStyle/><Validate>off</Validate><MinLength/><MaxLength/><SetValidMessage>off</SetValidMessage><ValidMessageAbsolutePosition>on</ValidMessageAbsolutePosition><ValidMessage/><ValidMessagePosition>right</ValidMessagePosition><ValidCSSClass/><ValidCSSStyle/><SetStyle>off</SetStyle><CSSClass/><CSSStyle/><RowCSSClass/><SetSize>off</SetSize><Width>230</Width><WidthUnit>px</WidthUnit><SetContactOptions>off</SetContactOptions><RegistredUsersOptions>none</RegistredUsersOptions><LinkToAppField/></form>',
			'ListPosition' => 300,
			'ValueField' => 1,
		),
		array(
			'id' => 5,
			'Description' => 'Email',
			'CssClass' => 'ufo-customfield-email',
			'Settings' => '<form><ShowLabel>on</ShowLabel><Label>Email</Label><LabelPosition>left-align-left</LabelPosition><Advanced/><LabelCSSClass/><LabelCSSStyle/><ShowDescription>off</ShowDescription><Description/><DescriptionPosition>bottom</DescriptionPosition><DescriptionCSSClass>ufo-customfields-field-description</DescriptionCSSClass><DescriptionCSSStyle/><SetDefaultValue>off</SetDefaultValue><DefaultValue>Your email</DefaultValue><IsBlankValue>on</IsBlankValue><DefaultValueCSSClass/><Required>on</Required><RequiredMessage>Please enter your email</RequiredMessage><RequiredMessagePosition>right</RequiredMessagePosition><SetRequiredSuffix>on</SetRequiredSuffix><RequiredSuffix>*</RequiredSuffix><RequiredSuffixCSSClass>ufo-customfields-required-suffix</RequiredSuffixCSSClass><RequiredSuffixCSSStyle/><AbsolutePosition>on</AbsolutePosition><InvalidCSSClass/><RequiredMessageCSSClass/><RequiredMessageCSSStyle/><Validate>on</Validate><SetValidMessage>on</SetValidMessage><ValidMessageAbsolutePosition>on</ValidMessageAbsolutePosition><ValidMessage/><ValidMessagePosition>right</ValidMessagePosition><ValidCSSClass/><ValidCSSStyle/><SetStyle>off</SetStyle><CSSClass/><CSSStyle/><RowCSSClass/><SetSize>off</SetSize><Width>230</Width><WidthUnit>px</WidthUnit><SetContactOptions>off</SetContactOptions><RegistredUsersOptions>none</RegistredUsersOptions><LinkToAppField/></form>',
			'ListPosition' => 500,
			'ValueField' => 1,
		),
		array(
			'id' => 6,
			'Description' => 'Submit button',
			'CssClass' => 'ufo-customfield-submitbutton',
			'Settings' => '<form><ShowLabel>on</ShowLabel><Label>Submit</Label><InputPosition>left</InputPosition><Advanced/><LabelCSSClass/><LabelCSSStyle/><SetStyle>off</SetStyle><CSSClass/><CSSStyle/><RowCSSClass/><WindowScroll/><SetSize>off</SetSize><Width>100</Width><WidthUnit>px</WidthUnit></form>',
			'ListPosition' => 1300,
			'ValueField' => 0,
		),
		array(
			'id' => 10,
			'Description' => 'Text Area',
			'CssClass' => 'ufo-customfield-textarea',
			'Settings' => '<form><ShowLabel>on</ShowLabel><Label>Text Area</Label><LabelPosition>left-align-left</LabelPosition><Advanced/><LabelCSSClass/><LabelCSSStyle/><ShowDescription>off</ShowDescription><Description/><DescriptionPosition>bottom</DescriptionPosition><DescriptionCSSClass>ufo-customfields-field-description</DescriptionCSSClass><DescriptionCSSStyle/><SetDefaultValue>off</SetDefaultValue><DefaultValue/><IsBlankValue>on</IsBlankValue><DefaultValueCSSClass/><Required>off</Required><RequiredMessage>This field is required</RequiredMessage><RequiredMessagePosition>right</RequiredMessagePosition><SetRequiredSuffix>on</SetRequiredSuffix><RequiredSuffix>*</RequiredSuffix><RequiredSuffixCSSClass>ufo-customfields-required-suffix</RequiredSuffixCSSClass><RequiredSuffixCSSStyle/><AbsolutePosition>on</AbsolutePosition><InvalidCSSClass/><RequiredMessageCSSClass/><RequiredMessageCSSStyle/><Validate>off</Validate><MinLength/><MaxLength/><SetValidMessage>off</SetValidMessage><ValidMessageAbsolutePosition>on</ValidMessageAbsolutePosition><ValidMessage/><ValidMessagePosition>right</ValidMessagePosition><ValidCSSClass/><ValidCSSStyle/><SetStyle>off</SetStyle><CSSClass/><CSSStyle/><RowCSSClass/><SetSize>on</SetSize><Width>230</Width><WidthUnit>px</WidthUnit><Height>100</Height><HeightUnit>px</HeightUnit></form>',
			'ListPosition' => 400,
			'ValueField' => 1,
		),
		array(
			'id' => 11,
			'Description' => 'Number',
			'CssClass' => 'ufo-customfield-number',
			'Settings' => '<form><ShowLabel>on</ShowLabel><Label>Number</Label><LabelPosition>left-align-left</LabelPosition><Advanced/><LabelCSSClass/><LabelCSSStyle/><ShowDescription>off</ShowDescription><Description/><DescriptionPosition>bottom</DescriptionPosition><DescriptionCSSClass>ufo-customfields-field-description</DescriptionCSSClass><DescriptionCSSStyle/><SetDefaultValue>off</SetDefaultValue><DefaultValue/><IsBlankValue>on</IsBlankValue><DefaultValueCSSClass/><Required>on</Required><RequiredMessage>Please enter a valid number</RequiredMessage><RequiredMessagePosition>right</RequiredMessagePosition><SetRequiredSuffix>on</SetRequiredSuffix><RequiredSuffix>*</RequiredSuffix><RequiredSuffixCSSClass>ufo-customfields-required-suffix</RequiredSuffixCSSClass><RequiredSuffixCSSStyle/><AbsolutePosition>on</AbsolutePosition><InvalidCSSClass/><RequiredMessageCSSClass/><RequiredMessageCSSStyle/><Validate>on</Validate><SetValidMessage>on</SetValidMessage><ValidMessageAbsolutePosition>on</ValidMessageAbsolutePosition><ValidMessage/><ValidMessagePosition>right</ValidMessagePosition><ValidCSSClass/><ValidCSSStyle/><SetStyle>off</SetStyle><CSSClass/><CSSStyle/><RowCSSClass/><SetSize>off</SetSize><Width>230</Width><WidthUnit>px</WidthUnit></form>',
			'ListPosition' => 600,
			'ValueField' => 1,
		),
		array(
			'id' => 12,
			'Description' => 'Radio Group',
			'CssClass' => 'ufo-customfield-radiogroup',
			'Settings' => '<form><ShowLabel>on</ShowLabel><Label>Radio Group</Label><LabelPosition>left-align-left</LabelPosition><Advanced/><LabelCSSClass/><LabelCSSStyle/><ShowDescription>off</ShowDescription><Description/><DescriptionPosition>bottom</DescriptionPosition><DescriptionCSSClass>ufo-customfields-field-description</DescriptionCSSClass><DescriptionCSSStyle/><SetOptions>on</SetOptions><Options><option index="1">Option1</option><option index="2">Option2</option><option index="3">Option3</option></Options><Required>off</Required><RequiredMessage>This field is required</RequiredMessage><RequiredMessagePosition>right</RequiredMessagePosition><SetRequiredSuffix>on</SetRequiredSuffix><RequiredSuffix>*</RequiredSuffix><RequiredSuffixCSSClass>ufo-customfields-required-suffix</RequiredSuffixCSSClass><RequiredSuffixCSSStyle/><AbsolutePosition>on</AbsolutePosition><InvalidCSSClass/><RequiredMessageCSSClass/><RequiredMessageCSSStyle/><SetStyle>on</SetStyle><CSSClass/><CSSStyle>float:left</CSSStyle><RowCSSClass/><SetSize>off</SetSize><Width>230</Width><WidthUnit>px</WidthUnit></form>',
			'ListPosition' => 900,
			'ValueField' => 1,
		),
		array(
			'id' => 9,
			'Description' => 'Checkbox',
			'CssClass' => 'ufo-customfield-checkbox',
			'Settings' => '<form><ShowLabel>on</ShowLabel><Label>Checkbox</Label><LabelPosition>right-align-left</LabelPosition><Advanced/><LabelCSSClass/><LabelCSSStyle>width:200px;</LabelCSSStyle><DisplayValueOn>on</DisplayValueOn><DisplayValueOff>off</DisplayValueOff><ShowDescription>off</ShowDescription><Description/><DescriptionPosition>bottom</DescriptionPosition><DescriptionCSSClass>ufo-customfields-field-description</DescriptionCSSClass><DescriptionCSSStyle/><Required>off</Required><RequiredMessage>This field is required</RequiredMessage><RequiredMessagePosition>right</RequiredMessagePosition><SetRequiredSuffix>on</SetRequiredSuffix><RequiredSuffix>*</RequiredSuffix><RequiredSuffixCSSClass>ufo-customfields-required-suffix</RequiredSuffixCSSClass><RequiredSuffixCSSStyle/><AbsolutePosition>on</AbsolutePosition><InvalidCSSClass/><RequiredMessageCSSClass/><RequiredMessageCSSStyle>width:200px;</RequiredMessageCSSStyle><SetStyle>off</SetStyle><CSSClass/><CSSStyle/><RowCSSClass/><SetSize>on</SetSize><Width>20</Width><WidthUnit>px</WidthUnit></form>',
			'ListPosition' => 700,
			'ValueField' => 1,
		),
		array(
			'id' => 13,
			'Description' => 'reCaptcha',
			'CssClass' => 'ufo-customfield-recaptcha',
			'Settings' => '<form><ShowLabel>off</ShowLabel><Label>ReCaptcha</Label><LabelPosition>left-align-left</LabelPosition><Advanced/><LabelCSSClass/><LabelCSSStyle/><ShowDescription>off</ShowDescription><Description/><DescriptionPosition>bottom</DescriptionPosition><DescriptionCSSClass>ufo-customfields-field-description</DescriptionCSSClass><DescriptionCSSStyle/><Required>off</Required><RequiredMessage>Please try again</RequiredMessage><RequiredMessagePosition>right</RequiredMessagePosition><SetRequiredSuffix>on</SetRequiredSuffix><RequiredSuffix>*</RequiredSuffix><RequiredSuffixCSSClass>ufo-customfields-required-suffix</RequiredSuffixCSSClass><RequiredSuffixCSSStyle/><AbsolutePosition>on</AbsolutePosition><InvalidCSSClass>none</InvalidCSSClass><RequiredMessageCSSClass/><RequiredMessageCSSStyle/><Validate>off</Validate><SetValidMessage>on</SetValidMessage><ValidMessageAbsolutePosition>on</ValidMessageAbsolutePosition><ValidMessage/><ValidMessagePosition>right</ValidMessagePosition><ValidCSSClass/><ValidCSSStyle/><SetStyle>off</SetStyle><CSSClass/><CSSStyle/><RowCSSClass/><SetSize>on</SetSize><Width>500</Width><WidthUnit>px</WidthUnit><SetReCaptchaOptions>off</SetReCaptchaOptions><ReCaptchaTheme>red</ReCaptchaTheme><ReCaptchaLanguage/><ReCaptchaPublicKey/><ReCaptchaPrivateKey/></form>',
			'ListPosition' => 1100,
			'ValueField' => 0,
		),
		array(
			'id' => 14,
			'Description' => 'Hidden',
			'CssClass' => 'ufo-customfield-hidden',
			'Settings' => '<form><ShowLabel>on</ShowLabel><Label>Last Name</Label><LabelPosition>left-align-left</LabelPosition><Advanced/><LabelCSSClass/><LabelCSSStyle/><ShowDescription>off</ShowDescription><Description/><DescriptionPosition>bottom</DescriptionPosition><DescriptionCSSClass>ufo-customfields-field-description</DescriptionCSSClass><DescriptionCSSStyle/><SetDefaultValue>off</SetDefaultValue><DefaultValue/><IsBlankValue>on</IsBlankValue><DefaultValueCSSClass/><Required>off</Required><RequiredMessage>This field is required</RequiredMessage><RequiredMessagePosition>right</RequiredMessagePosition><SetRequiredSuffix>on</SetRequiredSuffix><RequiredSuffix>*</RequiredSuffix><RequiredSuffixCSSClass>ufo-customfields-required-suffix</RequiredSuffixCSSClass><RequiredSuffixCSSStyle/><AbsolutePosition>on</AbsolutePosition><InvalidCSSClass/><RequiredMessageCSSClass/><RequiredMessageCSSStyle/><Validate>off</Validate><MinLength/><MaxLength/><SetValidMessage>off</SetValidMessage><ValidMessageAbsolutePosition>on</ValidMessageAbsolutePosition><ValidMessage/><ValidMessagePosition>right</ValidMessagePosition><ValidCSSClass/><ValidCSSStyle/><SetStyle>off</SetStyle><CSSClass/><CSSStyle/><RowCSSClass/><SetSize>off</SetSize><Width>230</Width><WidthUnit>px</WidthUnit></form>',
			'ListPosition' => 1000,
			'ValueField' => 0,
		),
		array(
			'id' => 16,
			'Description' => 'Phone Number',
			'CssClass' => 'ufo-customfield-phonenumber',
			'Settings' => '<form><ShowLabel>on</ShowLabel><Label>Phone Number</Label><LabelPosition>left-align-left</LabelPosition><Advanced/><LabelCSSClass/><LabelCSSStyle/><ShowDescription>off</ShowDescription><Description/><DescriptionPosition>bottom</DescriptionPosition><DescriptionCSSClass>ufo-customfields-field-description</DescriptionCSSClass><DescriptionCSSStyle/><SetDefaultValue>off</SetDefaultValue><DefaultValue/><IsBlankValue>on</IsBlankValue><DefaultValueCSSClass/><Required>on</Required><RequiredMessage>Please enter a valid phone number</RequiredMessage><RequiredMessagePosition>right</RequiredMessagePosition><SetRequiredSuffix>on</SetRequiredSuffix><RequiredSuffix>*</RequiredSuffix><RequiredSuffixCSSClass>ufo-customfields-required-suffix</RequiredSuffixCSSClass><RequiredSuffixCSSStyle/><AbsolutePosition>on</AbsolutePosition><InvalidCSSClass/><RequiredMessageCSSClass/><RequiredMessageCSSStyle/><Validate>on</Validate><SetValidMessage>on</SetValidMessage><ValidMessageAbsolutePosition>on</ValidMessageAbsolutePosition><ValidMessage/><ValidMessagePosition>right</ValidMessagePosition><ValidCSSClass/><ValidCSSStyle/><SetStyle>off</SetStyle><CSSClass/><CSSStyle/><RowCSSClass/><SetSize>off</SetSize><Width>230</Width><WidthUnit>px</WidthUnit></form>',
			'ListPosition' => 650,
			'ValueField' => 1,
		),
		array(
			'id' => 17,
			'Description' => 'Meeting Scheduler',
			'CssClass' => 'ufo-customfield-meetingscheduler',
			'Settings' => '<form><SetVCitaOptions>on</SetVCitaOptions><ShowLabel>on</ShowLabel><Label>Schedule a meeting with us</Label><ShowDescription>off</ShowDescription><Description/><DescriptionPosition>bottom</DescriptionPosition><Advanced/><DescriptionCSSClass>ufo-customfields-field-description</DescriptionCSSClass><DescriptionCSSStyle/><SetStyle>on</SetStyle><CSSClass>ufo-frontendbutton blue</CSSClass><CSSStyle/><RowCSSClass/><UseLink>off</UseLink></form>',
			'ListPosition' => 1250,
			'ValueField' => 0,
		),
		array(
			'id' => 22,
			'Description' => 'File Upload',
			'CssClass' => 'ufo-customfield-fileupload',
			'Settings' => '<form><FileSettings>on</FileSettings><ButtonText>Upload</ButtonText><UploadingText>Uploading...</UploadingText><OnlyAdminsCanDownload>on</OnlyAdminsCanDownload><AttachToConfirmation>off</AttachToConfirmation><AttachToNotification>off</AttachToNotification><ShowLabel>off</ShowLabel><Label>File Upload</Label><LabelPosition>left-align-left</LabelPosition><Advanced/><LabelCSSClass/><LabelCSSStyle/><ShowDescription>off</ShowDescription><Description/><DescriptionPosition>bottom</DescriptionPosition><DescriptionCSSClass>ufo-customfields-field-description</DescriptionCSSClass><DescriptionCSSStyle/><Required>off</Required><RequiredMessage>Please select a file</RequiredMessage><RequiredMessagePosition>right</RequiredMessagePosition><SetRequiredSuffix>on</SetRequiredSuffix><RequiredSuffix>*</RequiredSuffix><RequiredSuffixCSSClass>ufo-customfields-required-suffix</RequiredSuffixCSSClass><RequiredSuffixCSSStyle/><AbsolutePosition>on</AbsolutePosition><InvalidCSSClass/><RequiredMessageCSSClass/><RequiredMessageCSSStyle/><Validate>off</Validate><SetValidMessage>on</SetValidMessage><ValidMessageAbsolutePosition>on</ValidMessageAbsolutePosition><ValidMessage/><ValidMessagePosition>right</ValidMessagePosition><ValidCSSClass/><ValidCSSStyle/><SetStyle>on</SetStyle><CSSClass>ufo-frontendbutton blue</CSSClass><CSSStyle>width:130px;</CSSStyle><RowCSSClass/></form>',
			'ListPosition' => 925,
			'ValueField' => 1,
		),
		array(
			'id' => 1,
			'Description' => 'Fieldset',
			'CssClass' => 'ufo-customfield-fieldset',
			'Settings' => '<form><ShowLabel>on</ShowLabel><Label>Fieldset</Label><Advanced/><LabelCSSClass/><LabelCSSStyle/><ShowDescription>off</ShowDescription><Description/><DescriptionPosition>top</DescriptionPosition><DescriptionCSSClass>ufo-customfields-container-description</DescriptionCSSClass><DescriptionCSSStyle/><SetStyle>off</SetStyle><CSSClass/><CSSStyle/><AddCF>off</AddCF><SetSize>off</SetSize><Width>230</Width><WidthUnit>px</WidthUnit></form>',
			'ListPosition' => 100,
			'ValueField' => 0,
		),
		array(
			'id' => 2,
			'Description' => 'Section',
			'CssClass' => 'ufo-customfield-section',
			'Settings' => '<form><ShowLabel>on</ShowLabel><Label>Section</Label><LabelTagName>h3</LabelTagName><Advanced/><LabelCSSClass/><LabelCSSStyle/><ShowDescription>off</ShowDescription><Description/><DescriptionPosition>top</DescriptionPosition><DescriptionCSSClass>ufo-customfields-container-description</DescriptionCSSClass><DescriptionCSSStyle/><SetStyle>off</SetStyle><CSSClass/><CSSStyle/><AddCF>off</AddCF><SetSize>off</SetSize><Width>230</Width><WidthUnit>px</WidthUnit></form>',
			'ListPosition' => 200,
			'ValueField' => 0,
		),
	);

	$table_name = EasyContactFormsDB::wptn('#wp__easycontactforms_customformfieldtypes');
	$existent = $wpdb->get_col( 'SELECT id FROM ' . $table_name . ';' );
	foreach ($rows as $row) {
		$rid = $row['id'];
		if (in_array($rid, $existent)) {
			$wpdb->update($table_name, $row, array('id' => $rid));
		}
		else {
			$wpdb->insert($table_name, $row);
		}
	}


	$rows = array(
		array(
			'objtype' => 'CustomForms',
			'method' => 'val',
			'name' => 'main',
			'role' => 'Guest',
		),
		array(
			'objtype' => 'CustomFormEntryStatistics',
			'method' => 'viewDetailed',
			'name' => 'detailedMain',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomFormFields',
			'method' => 'deleteField',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'ApplicationSettings',
			'method' => 'show',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'Files',
			'method' => 'deletefile',
			'name' => 'main',
			'role' => 'Guest',
		),
		array(
			'objtype' => 'CustomFormsEntries',
			'method' => 'view',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomForms_MailingLists',
			'method' => 'view',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomFormFields',
			'method' => 'updateFieldData',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'DashBoard',
			'method' => 'show',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomForms',
			'method' => 'add',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomFormEntryFiles',
			'method' => 'view',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'DashBoard',
			'method' => 'getFormPageStatistics',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomFormsEntries',
			'method' => 'show',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'ContactTypes',
			'method' => 'view',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomForms',
			'method' => 'val',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomForms',
			'method' => 'new',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'ContactTypes',
			'method' => 'show',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomFormEntryStatistics',
			'method' => 'show',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomFormEntryStatistics',
			'method' => 'view',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomFormFields',
			'method' => 'moveFieldSet',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'Files',
			'method' => 'download',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomForms',
			'method' => 'refreshForm',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomForms',
			'method' => 'view',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomForms',
			'method' => 'preview',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomFormEntryStatistics',
			'method' => 'setFormPageStatisticsShowOnDashboard',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'Files',
			'method' => 'download',
			'name' => 'main',
			'role' => 'Guest',
		),
		array(
			'objtype' => 'Files',
			'method' => 'upload',
			'name' => 'main',
			'role' => 'Guest',
		),
		array(
			'objtype' => 'DashBoard',
			'method' => 'getUserStatistics',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomFormEntryStatistics',
			'method' => 'resetFormPageStatistics',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'Files',
			'method' => 'upload',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomFormEntryStatistics',
			'method' => 'new',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'Users',
			'method' => 'getEUserASList',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomForms',
			'method' => 'show',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomFormEntryFiles',
			'method' => 'viewDetailed',
			'name' => 'detailedMain',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomFormsEntries',
			'method' => 'viewDetailed',
			'name' => 'detailedMain',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomFormFields',
			'method' => 'updateOrder',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomFormFields',
			'method' => 'copyField',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomFormsEntries',
			'method' => 'processEntry',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomFormFields',
			'method' => 'addCustomField',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'DashBoard',
			'method' => 'getFormStatistics',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomFormFields',
			'method' => 'getSettingsForm',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'Users',
			'method' => 'view',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'DashBoard',
			'method' => 'getEntryStatistics',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomFormFields',
			'method' => 'viewDetailed',
			'name' => 'detailedMain',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomForms',
			'method' => 'add',
			'name' => 'main',
			'role' => 'Guest',
		),
		array(
			'objtype' => 'Users',
			'method' => 'getUserASList',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'ApplicationSettings',
			'method' => 'allowPBLink',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'Users',
			'method' => 'new',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'Users',
			'method' => 'show',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomForms',
			'method' => 'resetStatistics',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'ApplicationSettings',
			'method' => 'setOptionValue',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'Files',
			'method' => 'deletefile',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'CustomForms',
			'method' => 'copy',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
		array(
			'objtype' => 'ContactTypes',
			'method' => 'new',
			'name' => 'main',
			'role' => 'SuperAdmin',
		),
	);

	$table_name = EasyContactFormsDB::wptn('#wp__easycontactforms_acl');
	$wpdb->query('DELETE FROM ' . $table_name . ' WHERE 1;' );
	foreach ($rows as $row) {
		$wpdb->insert($table_name, $row);
	}


	$rows = array(
		array(
			'id' => 1,
			'Description' => 'AppSettings',
			'TinyMCEConfig' => '{theme_advanced_buttons4:"",mode:"exact",theme_advanced_statusbar_location:"",theme_advanced_toolbar_align:"left",theme_advanced_resizing:"true",plugins:"fullscreen",theme_advanced_toolbar_location:"top",theme_advanced_buttons1:"bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect",theme_advanced_buttons2:"bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,anchor,image,cleanup,|,forecolor,backcolor,|,fullscreen",theme_advanced_buttons3:"",theme:"advanced", relative_urls : false, remove_script_host: false}',
			'UseTinyMCE' => 1,
			'ApplicationWidth' => 900,
			'ApplicationWidth2' => 900,
			'DefaultStyle' => 'std2',
			'DefaultStyle2' => 'std2',
			'NotLoggenInText' => 'Please log in.',
			'FileFolder' => 'files',
			'FixJSLoading' => 0,
			'FixStatus0' => 0,
			'DateFormat' => 'Y-m-d^%Y-%m-%d^\d{4}-\d{2}-\d{2}$^2012-01-25',
			'DateTimeFormat' => 'Y-m-d H:i^%Y-%m-%d %H:%M^\d{4}-\d{1,2}-\d{1,2}\s\d{1,2}:\d{1,2}^Y-m-d hh:mm',
			'ShowPoweredBy' => 0,
		),
	);

	$table_name = EasyContactFormsDB::wptn('#wp__easycontactforms_applicationsettings');
	$count = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $table_name . ';' );
	if ($count == 0) {
		foreach ($rows as $row) {
			$wpdb->insert($table_name, $row);
		}
	}


	$rows = array(
		array(
			'id' => 1,
			'Description' => '2012-01-25',
			'Value' => 'Y-m-d^%Y-%m-%d^\d{4}-\d{2}-\d{2}$^2012-01-25',
			'OptionGroup' => 'dateformats',
		),
		array(
			'id' => 2,
			'Description' => 'Y-m-d hh:mm',
			'Value' => 'Y-m-d H:i^%Y-%m-%d %H:%M^\d{4}-\d{1,2}-\d{1,2}\s\d{1,2}:\d{1,2}^Y-m-d hh:mm',
			'OptionGroup' => 'datetimeformats',
		),
		array(
			'id' => 3,
			'Description' => '01/25/2012',
			'Value' => 'm/d/Y^%m/%d/%Y^\d{2}\/\d{2}\/\d{4}$^01/25/2012',
			'OptionGroup' => 'dateformats',
		),
		array(
			'id' => 4,
			'Description' => 'd.m.Y hh:mm',
			'Value' => 'd.m.Y H:i^%d.%m.%Y %H:%M^\d{1,2}\.\d{1,2}\.\d{4}\s\d{1,2}:\d{1,2}^d.m.Y hh:mm',
			'OptionGroup' => 'datetimeformats',
		),
		array(
			'id' => 5,
			'Description' => '25/01/2012',
			'Value' => 'd/m/Y^%d/%m/%Y^\d{2}\/\d{2}\/\d{4}$^25/01/2012',
			'OptionGroup' => 'dateformats',
		),
		array(
			'id' => 6,
			'Description' => '25.01.2012',
			'Value' => 'd.m.Y^%d.%m.%Y^\d{2}\.\d{2}\.\d{4}$^25.01.2012',
			'OptionGroup' => 'dateformats',
		),
		array(
			'id' => 7,
			'Description' => '25-01-2012',
			'Value' => 'd-m-Y^%d-%m-%Y^\d{2}-\d{2}-\d{4}$^25-01-2012',
			'OptionGroup' => 'dateformats',
		),
	);

	$table_name = EasyContactFormsDB::wptn('#wp__easycontactforms_options');
	$existent = $wpdb->get_col( 'SELECT Description FROM ' . $table_name . ';' );
	foreach ($rows as $row) {
		$rid = $row['Description'];
		if (!in_array($rid, $existent)) {
			$wpdb->insert($table_name, $row);
		}
	}


	$rows = array(
		array(
			'id' => 1,
			'Description' => 'SuperAdmin',
			'Admin' => 0,
			'Employee' => 0,
		),
		array(
			'id' => 4,
			'Description' => 'Guest',
			'Admin' => 0,
			'Employee' => 0,
		),
	);

	$table_name = EasyContactFormsDB::wptn('#wp__easycontactforms_roles');
	$wpdb->query('DELETE FROM ' . $table_name . ' WHERE 1;' );
	foreach ($rows as $row) {
		$wpdb->insert($table_name, $row);
	}

	require_once dirName(__FILE__) . DIRECTORY_SEPARATOR . 'easy-contact-forms-root.php';
	require_once dirName(__FILE__) . DIRECTORY_SEPARATOR . 'easy-contact-forms-applicationsettings.php';
	$as = EasyContactFormsApplicationSettings::getInstance();
	$as->set('ProductVersion', '1.4.2');
	$as->save();}


function easycontactforms_uninstall() {

	global $wpdb;
	$sqls[] = "DROP TABLE IF EXISTS #wp__easycontactforms_applicationsettings;";
	$sqls[] = "DROP TABLE IF EXISTS #wp__easycontactforms_contacttypes;";
	$sqls[] = "DROP TABLE IF EXISTS #wp__easycontactforms_customformentryfiles;";
	$sqls[] = "DROP TABLE IF EXISTS #wp__easycontactforms_customformentrystatistics;";
	$sqls[] = "DROP TABLE IF EXISTS #wp__easycontactforms_customformfields;";
	$sqls[] = "DROP TABLE IF EXISTS #wp__easycontactforms_customformfieldtypes;";
	$sqls[] = "DROP TABLE IF EXISTS #wp__easycontactforms_customforms;";
	$sqls[] = "DROP TABLE IF EXISTS #wp__easycontactforms_customforms_mailinglists;";
	$sqls[] = "DROP TABLE IF EXISTS #wp__easycontactforms_customformsentries;";
	$sqls[] = "DROP TABLE IF EXISTS #wp__easycontactforms_files;";
	$sqls[] = "DROP TABLE IF EXISTS #wp__easycontactforms_options;";
	$sqls[] = "DROP TABLE IF EXISTS #wp__easycontactforms_roles;";
	$sqls[] = "DROP TABLE IF EXISTS #wp__easycontactforms_users;";
	$sqls[] = "DROP TABLE IF EXISTS #wp__easycontactforms_acl;";
	$sqls[] = "DROP TABLE IF EXISTS #wp__easycontactforms_sessions;";

	require_once dirName(__FILE__) . DIRECTORY_SEPARATOR . 'easy-contact-forms-database.php';
	foreach ($sqls as $sql){
		$sql = EasyContactFormsDB::wptn($sql);
		$wpdb->query($sql);
	}
}
