<?php
/**
 * @package    Sla
 * @author     Techjoomla <contact@techjoomla.com>
 * @copyright  Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Unauthorized Access');
use Joomla\CMS\Language\Text;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Table\Table;

/**
 * Sla activity class.  Handles all application interaction with a Sla Activity
 *
 * @since  1.0.0
 */
class SlaSlaActivity
{
	public $id = null;

	public $sla_activity_type_id = 0;

	public $sla_id = 0;

	public $sla_service_id = 0;

	public $cluster_id = 0;

	public $todo_id = 0;

	public $license_id = 0;

	public $ordering = 0;

	public $state = 1;

	public $checked_out = null;

	public $checked_out_time = null;

	public $created_on = null;

	public $created_by = 0;

	public $modified_on = null;

	public $modified_by = 0;

	protected static $slaActivityObj = array();

	/**
	 * Constructor activating the default information of the Sla activity
	 *
	 * @param   int  $id  The unique event key to load.
	 *
	 * @since   1.0.0
	 */
	public function __construct($id = 0)
	{
		if (!empty($id))
		{
			$this->load($id);
		}

		$db = Factory::getDbo();

		$this->checked_out_time = $this->created_on = $this->modified_on = $db->getNullDate();
	}

	/**
	 * Returns the global sla activity object
	 *
	 * @param   integer  $id  The primary key of the sla activity id to load (optional).
	 *
	 * @return  Object  Sla activity object.
	 *
	 * @since   1.0.0
	 */
	public static function getInstance($id = 0)
	{
		// @Todo- Check the comments for this function
		if (!$id)
		{
			return new SlaSlaActivity;
		}

		if (empty(self::$slaActivityObj[$id]))
		{
			$slaActivity = new SlaSlaActivity($id);
			self::$slaActivityObj[$id] = $slaActivity;
		}

		return self::$slaActivityObj[$id];
	}

	/**
	 * Method to load a sla activity object by sla activity id
	 *
	 * @param   int  $id  The sla activity id
	 *
	 * @return  boolean  True on success
	 *
	 * @since 1.0.0
	 */
	public function load($id)
	{
		$table = SlaFactory::table("slaactivities");

		if (!$table->load($id))
		{
			return false;
		}

		$this->setProperties($table->getProperties());

		return true;
	}

	/**
	 * Method to save the Sla activity object to the database
	 *
	 * @return  boolean  True on success
	 *
	 * @since 1.0.0
	 * @throws  \RuntimeException
	 */
	public function save()
	{
		// Create the sla activity table object
		$table = SlaFactory::table("slaactivities");
		$table->bind($this->getProperties());

		$currentDateTime = Factory::getDate()->toSql();

		$user = Factory::getUser();

		// Check and store the object.
		if (!$table->check())
		{
			throw new \RuntimeException($table->getError());
		}

		// Check if new record
		$isNew = empty($this->id);

		if ($isNew)
		{
			$table->created_on = $currentDateTime;
			$table->created_by = $user->id;
		}
		else
		{
			$table->modified_on = $currentDateTime;
			$table->modified_by = $user->id;
		}

		// Store the user data in the database
		if (!($table->store()))
		{
			throw new \RuntimeException($table->getError());
		}

		$this->id = $table->id;

		// Fire the onSlaActivityAfterSave event.

		Factory::getApplication()->triggerEvent('onSlaActivityAfterSave', array($isNew, $this));

		return true;
	}

	/**
	 * Method to bind an associative array of data to a sla activity object
	 *
	 * @param   array  &$array  The associative array to bind to the object
	 *
	 * @return  boolean  True on success
	 *
	 * @since 1.0.0
	 */
	public function bind(&$array)
	{
		if (empty ($array))
		{
			throw new \InvalidArgumentException(Text::_('COM_SLA_EMPTY_DATA'));
		}

		// Bind the array
		if (!$this->setProperties($array))
		{
			throw new \RuntimeException(Text::_('COM_SLA_BINDING_ERROR'));
		}

		// Make sure its an integer
		$this->id = (int) $this->id;

		return true;
	}

	/**
	 * Method to get Sla details
	 *
	 * @return  object  Sla details
	 *
	 * @since 1.0.0
	 */
	public function getSlaDetails()
	{
		return SlaSla::getInstance($this->sla_id);
	}

	/**
	 * Method to get Sla service details
	 *
	 * @return  object  SlaService details
	 *
	 * @since 1.0.0
	 */
	public function getSlaServiceDetails()
	{
		return SlaSlaService::getInstance($this->sla_service_id);
	}

	/**
	 * Method to get Sla activity's cluster
	 *
	 * @return  object  Sla activity's cluster object
	 *
	 * @since 1.0.0
	 */
	public function getSlaActivityCluster()
	{
		\JLoader::import("/components/com_cluster/includes/cluster", JPATH_ADMINISTRATOR);

		$clusterModel = ClusterFactory::model('Cluster');

		return $clusterModel->getClusterByClient('com_multiagency', $this->cluster_id);
	}

	/**
	 * Method to get Sla activity Jlike Todo's
	 *
	 * @return  object  Jlike Todo Table object
	 *
	 * @since 1.0.0
	 */
	public function getSlaActivityTodo()
	{
		JLoader::import('components.com_jlike.tables.todos', JPATH_ADMINISTRATOR);
		$table = Factory::getApplication()->bootComponent('com_jlike')->getMVCFactory()->createTable('Todos', 'Administrator');
		$table->load($this->todo_id);

		return $table;
	}


	/**
	 * Set the object properties based on a named array/hash.
	 *
	 * @param   mixed  $properties  Either an associative array or another object.
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 */
	public function setProperties($properties)
	{
		if (is_array($properties) || is_object($properties))
		{
			foreach ((array) $properties as $k => $v)
			{
				$this->$k = $v;
			}

			return true;
		}

		return false;
	}

	/**
	 * Returns an associative array of object properties.
	 *
	 * @param   boolean  $public  If true, returns only the public properties.
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	public function getProperties($public = true)
	{
		$vars = get_object_vars($this);

		if ($public)
		{
			foreach ($vars as $key => $value)
			{
				if ('_' == substr($key, 0, 1))
				{
					unset($vars[$key]);
				}
			}
		}

		return $vars;
	}
}
