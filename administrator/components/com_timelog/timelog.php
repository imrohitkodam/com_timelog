<?php
/**
 * @package    Com_Timelog
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_timelog'))
{
	throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
require_once JPATH_ADMINISTRATOR . '/components/com_timelog/includes/timelog.php';

// Execute the task.
$app  = Factory::getApplication();
$task = $app->getInput()->get('task', '');

// If task contains a dot (controller.task format), split it
if (strpos($task, '.') !== false)
{
	list($controllerName, $taskName) = explode('.', $task, 2);
	$app->getInput()->set('task', $taskName);
}
else
{
	// If no dot, treat the whole thing as a task
	$controllerName = '';
	$taskName = $task;
}

// Get the controller
if ($controllerName)
{
	// Load specific controller
	$controllerPath = JPATH_ADMINISTRATOR . '/components/com_timelog/controllers/' . strtolower($controllerName) . '.php';
	
	if (file_exists($controllerPath))
	{
		require_once $controllerPath;
		$controllerClass = 'TimelogController' . ucfirst($controllerName);
		
		if (class_exists($controllerClass))
		{
			$controller = new $controllerClass();
		}
		else
		{
			$controller = BaseController::getInstance('Timelog');
		}
	}
	else
	{
		$controller = BaseController::getInstance('Timelog');
	}
}
else
{
	// Use default controller
	$controller = BaseController::getInstance('Timelog');
}

$controller->execute($taskName);
$controller->redirect();
