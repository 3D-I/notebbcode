<?php
/**
*
* @package phpBB Extension - notebbcode
* @copyright (c) 2016, 2017 3Di (Marco T.)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/


if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ERROR_MSG_3111_321_MISTMATCH'	=>	'Minimum 3.1.11 but less than 3.2.0@dev OR greater than 3.2.1 but less than 3.3.0@dev',
));
