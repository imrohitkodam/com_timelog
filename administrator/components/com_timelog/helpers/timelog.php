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

/**
 * Timelog component helper.
 *
 * @since  1.0.0
 */
class TimelogHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return void
	 *
	 * @since  1.0.0
	 */
	public static function addSubmenu($vName = '')
	{
		// In Joomla 6, sidebar/submenu is handled via the component's XML manifest
		// or via the Joomla admin sidebar API. The JHtmlSidebar class was removed.
		// Submenu items are now defined in the manifest XML <submenu> section.
	}
}
