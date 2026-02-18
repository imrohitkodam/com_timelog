<?php
/**
 * @package    Com_Timelog
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die();

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Table\Table;

$helperPath = JPATH_SITE . '/components/com_timelog/helpers/main.php';

if (!class_exists('TimelogMainHelper'))
{
	if (file_exists($helperPath))
	{
		require_once $helperPath;
	}
}

// Load Global language constants to in .js file
if (class_exists('TimelogMainHelper'))
{
	TimelogMainHelper::getLanguageConstant();
}

/**
 * Timelog factory class.
 *
 * This class perform the helpful operation for truck app
 *
 * @since  __DEPLOY_VERSION__
 */
class TimelogFactory
{
	/**
	 * Retrieves a table from the table folder
	 *
	 * @param   string  $name    The table file name
	 *
	 * @param   string  $prefix  The table class name prefix
	 *
	 * @param   array   $config  The table file name
	 *
	 * @return	object|boolean Table object or false
	 *
	 * @since 	__DEPLOY_VERSION__
	 **/
	public static function table($name, $prefix = 'TimelogTable', $config = array())
	{
		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_timelog/tables');

		return Table::getInstance($name, $prefix, $config);
	}

	/**
	 * Retrieves a model from the model folder
	 *
	 * @param   string  $name    The model name to instantiate
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return	object|boolean Model object or false
	 *
	 * @since 	__DEPLOY_VERSION__
	 **/
	public static function model($name, $config = array())
	{
		BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_timelog/models');

		return BaseDatabaseModel::getInstance($name, 'TimelogModel', $config);
	}
}
