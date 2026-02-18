<?php
/**
 * @package    Com_Timelog
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

require_once JPATH_ADMINISTRATOR . '/components/com_timelog/includes/timelog.php';

/**
 * TimelogActivity class
 *
 * @since  1.0.0
 */
class TimelogActivity
{
	/**
	 * Activity id
	 *
	 * @var    integer
	 * @since  1.0.0
	 */
	public $id = 0;

	/**
	 * Activity type id
	 *
	 * @var    integer
	 * @since  1.0.0
	 */
	public $activity_type_id = 0;

	/**
	 * Client
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $client = '';

	/**
	 * Client id
	 *
	 * @var    integer
	 * @since  1.0.0
	 */
	public $client_id = 0;

	/**
	 * Activity note
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $activity_note = '';

	/**
	 * Created date
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $created_date = '';

	/**
	 * Spent time
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $spent_time = '';

	/**
	 * State
	 *
	 * @var    integer
	 * @since  1.0.0
	 */
	public $state = 1;

	/**
	 * Attachment
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $attachment = '';

	/**
	 * Created by
	 *
	 * @var    integer
	 * @since  1.0.0
	 */
	public $created_by = 0;

	/**
	 * Modified date
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $modified_date = '';

	/**
	 * Modified by
	 *
	 * @var    integer
	 * @since  1.0.0
	 */
	public $modified_by = 0;

	/**
	 * License id
	 *
	 * @var    integer
	 * @since  1.0.0
	 */
	public $license_id = 0;

	/**
	 * Timelog (time spent)
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $timelog = '';

	/**
	 * Last error message
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $lastError = '';

	/**
	 * Instances container
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	private static $instances = array();

	/**
	 * Constructor
	 *
	 * @since  1.0.0
	 */
	public function __construct()
	{
	}

	/**
	 * Returns a reference to the global TimelogActivity object, only creating it if it doesn't already exist.
	 *
	 * @param   integer  $id  The id of the activity to load
	 *
	 * @return  TimelogActivity  A TimelogActivity object.
	 *
	 * @since   1.0.0
	 */
	public static function getInstance($id = 0)
	{
		if (!isset(self::$instances[$id]))
		{
			$activity = new TimelogActivity;

			if ($id)
			{
				$activity->load($id);
			}

			self::$instances[$id] = $activity;
		}

		return self::$instances[$id];
	}

	/**
	 * Load the activity data from the database
	 *
	 * @param   integer  $id  The id of the activity to load
	 *
	 * @return  boolean  True on success
	 *
	 * @since   1.0.0
	 */
	public function load($id)
	{
		$table = TimelogFactory::table('Activity');

		if (!$table->load($id))
		{
			return false;
		}

		$properties = $table->getProperties();

		foreach ($properties as $key => $value)
		{
			if (property_exists($this, $key))
			{
				$this->$key = $value;
			}
		}

		return true;
	}

	/**
	 * Bind the data to the activity object
	 *
	 * @param   array  $data  The data to bind
	 *
	 * @return  boolean  True on success
	 *
	 * @since   1.0.0
	 */
	public function bind($data)
	{
		if (is_array($data))
		{
			foreach ($data as $key => $value)
			{
				if (property_exists($this, $key))
				{
					$this->$key = $value;
				}
			}
		}
		elseif (is_object($data))
		{
			foreach (get_object_vars($data) as $key => $value)
			{
				if (property_exists($this, $key))
				{
					$this->$key = $value;
				}
			}
		}

		return true;
	}

	/**
	 * Save the activity to the database
	 *
	 * @return  boolean  True on success
	 *
	 * @since   1.0.0
	 */
	public function save()
	{
		$table = TimelogFactory::table('Activity');

		$data = array();

		foreach (get_object_vars($this) as $key => $value)
		{
			if ($key !== 'lastError' && substr($key, 0, 1) !== '_')
			{
				$data[$key] = $value;
			}
		}

		if (!$table->bind($data))
		{
			$this->lastError = $table->getError();

			return false;
		}

		if (!$table->check())
		{
			$this->lastError = $table->getError();

			return false;
		}

		if (!$table->store())
		{
			$this->lastError = $table->getError();

			return false;
		}

		$this->id = $table->id;

		return true;
	}

	/**
	 * Delete the activity from the database
	 *
	 * @param   integer  $id  The id of the activity to delete
	 *
	 * @return  boolean  True on success
	 *
	 * @since   1.0.0
	 */
	public function delete($id = null)
	{
		if (empty($id))
		{
			$id = $this->id;
		}

		$table = TimelogFactory::table('Activity');

		if (!$table->delete($id))
		{
			$this->lastError = $table->getError();

			return false;
		}

		return true;
	}

	/**
	 * Get the last error message
	 *
	 * @return  string  The last error message
	 *
	 * @since   1.0.0
	 */
	public function getLastError()
	{
		return $this->lastError;
	}

	/**
	 * Get a property value
	 *
	 * @param   string  $property  The property name
	 * @param   mixed   $default   The default value
	 *
	 * @return  mixed  The property value
	 *
	 * @since   1.0.0
	 */
	public function get($property, $default = null)
	{
		if (property_exists($this, $property))
		{
			return $this->$property;
		}

		return $default;
	}

	/**
	 * Set a property value
	 *
	 * @param   string  $property  The property name
	 * @param   mixed   $value     The value to set
	 *
	 * @return  mixed  The previous value
	 *
	 * @since   1.0.0
	 */
	public function set($property, $value = null)
	{
		$previous = $this->get($property);
		$this->$property = $value;

		return $previous;
	}
}
