<?php
/**
 * @package    Com_Timelog
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;

/**
 * Methods supporting a list of Timelog records.
 *
 * @since  1.0.0
 */
class TimelogModelActivities extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see        \Joomla\CMS\MVC\Model\BaseDatabaseModel
	 * @since      1.0.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.`id`',
				'activity_type_id', 'a.`activity_type_id`',
				'client', 'a.`client`',
				'client_id', 'a.`client_id`',
				'activity_note', 'a.`activity_note`',
				'created_date', 'a.`created_date`',
				'spent_time', 'a.`spent_time`',
				'state', 'a.`state`',
				'attachment', 'a.`attachment`',
				'created_by', 'a.`created_by`',
				'modified_date', 'a.`modified_date`',
				'modified_by', 'a.`modified_by`',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = Factory::getApplication();

		// List state information.
		parent::populateState('a.id', 'ASC');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   1.0.0
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.id');
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');
		$id .= ':' . $this->getState('filter.activity_type_id');
		$id .= ':' . $this->getState('filter.client');
		$id .= ':' . $this->getState('filter.client_id');
		$id .= ':' . $this->getState('filter.created_by');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   \Joomla\Database\DatabaseQuery
	 *
	 * @since    1.0.0
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from($db->qn('#__timelog_activities', 'a'));

		// Join over the user field 'created_by'
		$query->select('`uc`.name AS `created_by`');
		$query->join('LEFT', $db->qn('#__users', 'uc') . ' ON (' . $db->qn('uc.id') . ' = ' . $db->qn('a.created_by') . ')');

		// Join over the user field 'modified_by'
		$query->select('`um`.name AS `modified_by`');
		$query->join('LEFT', $db->qn('#__users', 'um') . ' ON (' . $db->qn('um.id') . ' = ' . $db->qn('a.modified_by') . ')');

		// Filter by published state
		$published = $this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$filter = new InputFilter;

			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( a.activity_note LIKE ' . $search . ' )');
			}
		}

		// Filter by client
		$client = $this->getState('filter.client');

		if (!empty($client))
		{
			$query->where($db->qn('a.client') . ' = ' . $db->quote($client));
		}

		// Filter by client_id
		$clientId = $this->getState('filter.client_id');

		if (!empty($clientId))
		{
			$query->where($db->qn('a.client_id') . ' = ' . (int) $clientId);
		}

		// Filter by activity_type_id
		$activityTypeId = $this->getState('filter.activity_type_id');

		if (!empty($activityTypeId))
		{
			$query->where($db->qn('a.activity_type_id') . ' = ' . (int) $activityTypeId);
		}

		// Filter by created_by
		$createdBy = $this->getState('filter.created_by');

		if (!empty($createdBy))
		{
			$query->where($db->qn('a.created_by') . ' = ' . (int) $createdBy);
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'a.id');
		$orderDirn = $this->state->get('list.direction', 'ASC');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return  mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();

		if ($items)
		{
			BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_timelog/models');

			foreach ($items as $oneItem)
			{
				$activityTypeModel = BaseDatabaseModel::getInstance('Activitytype', 'TimelogModel', array('ignore_request' => true));

				if (isset($oneItem->activity_type_id))
				{
					$activityTypes = $activityTypeModel->getItem($oneItem->activity_type_id);
					$oneItem->activity_type_id = isset($activityTypes->title) ? $activityTypes->title : $oneItem->activity_type_id;
				}
			}
		}

		return $items;
	}
}
