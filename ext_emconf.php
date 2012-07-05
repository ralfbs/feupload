<?php

########################################################################
# Extension Manager/Repository config file for ext "feupload".
#
# Auto generated 01-07-2012 09:53
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Frontend User Upload',
	'description' => 'File upload capabilities brougth to frontend users',
	'category' => 'fe',
	'shy' => 0,
	'version' => '0.5.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => 'uploads/feupload/',
	'modify_tables' => 'pages',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Pascal Duersteler @ Keel Martkideen, Ralf Schneider @ hr-interactive',
	'author_email' => 'pascal.duersteler@gmail.com, ralf@hr-interactive.com',
	'author_company' => 'Keel Marktideen, hr-interactive',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.2.0-0.0.0',
			'typo3' => '4.5.0-0.0.0',
			'extbase' => '1.3.0-0.0.0',
			'fluid' => '1.3.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:30:{s:21:"ext_conf_template.txt";s:4:"ee88";s:12:"ext_icon.gif";s:4:"d178";s:17:"ext_localconf.php";s:4:"114e";s:14:"ext_tables.php";s:4:"97c9";s:14:"ext_tables.sql";s:4:"7ffe";s:41:"Classes/Controller/DownloadController.php";s:4:"c5dd";s:39:"Classes/Controller/UploadController.php";s:4:"70d9";s:29:"Classes/Domain/Model/File.php";s:4:"868e";s:37:"Classes/Domain/Model/FrontendUser.php";s:4:"e987";s:42:"Classes/Domain/Model/FrontendUserGroup.php";s:4:"86fa";s:44:"Classes/Domain/Repository/FileRepository.php";s:4:"c5c4";s:57:"Classes/Domain/Repository/FrontendUserGroupRepository.php";s:4:"73c1";s:52:"Classes/Domain/Repository/FrontendUserRepository.php";s:4:"c0c6";s:48:"Classes/Domain/Validator/FileUploadValidator.php";s:4:"d5f8";s:42:"Classes/ViewHelpers/DownloadViewHelper.php";s:4:"94ef";s:35:"Configuration/FlexForm/download.xml";s:4:"2050";s:26:"Configuration/TCA/File.php";s:4:"46a4";s:46:"Configuration/TypoScript/default/constants.txt";s:4:"db3e";s:42:"Configuration/TypoScript/default/setup.txt";s:4:"bbc6";s:41:"Documentation/whereIsTheDocumentation.txt";s:4:"d9fd";s:35:"Resources/Private/Icons/default.png";s:4:"55a6";s:39:"Resources/Private/Language/flexform.xml";s:4:"c31e";s:40:"Resources/Private/Language/locallang.xml";s:4:"18ab";s:34:"Resources/Private/Language/tca.xml";s:4:"2e7f";s:39:"Resources/Private/Layouts/download.html";s:4:"c366";s:37:"Resources/Private/Layouts/upload.html";s:4:"f17d";s:42:"Resources/Private/Partials/FormErrors.html";s:4:"414d";s:47:"Resources/Private/Templates/Download/index.html";s:4:"1335";s:43:"Resources/Private/Templates/Upload/new.html";s:4:"302e";s:14:"doc/manual.sxw";s:4:"5e90";}',
);

?>