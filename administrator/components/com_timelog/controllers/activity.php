<?php
/**
 * @package    Com_Timelog
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;

/**
 * Activity controller class.
 *
 * @since  1.0.0
 */
class TimelogControllerActivity extends FormController
{
	/**
	 * @var    string  The prefix to use with controller messages.
	 * @since  1.0.0
	 */
	protected $text_prefix = 'COM_TIMELOG';

	/**
	 * @var    string  The view list.
	 * @since  1.0.0
	 */
	protected $view_list = 'activities';

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.0.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
}
