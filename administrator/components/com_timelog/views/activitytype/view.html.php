<?php
/**
 * @package    Com_Timelog
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View to edit
 *
 * @since  1.0.0
 */
class TimelogViewActivitytype extends HtmlView
{
	/**
	 * The Form object
	 *
	 * @var  Form
	 */
	protected $form;

	/**
	 * The active item
	 *
	 * @var  object
	 */
	protected $item;

	/**
	 * The model state
	 *
	 * @var  object
	 */
	protected $state;

	/**
	 * The actions the user is authorised to perform
	 *
	 * @var  \stdClass
	 */
	protected $canDo;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws \Exception
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
		$this->form  = $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \Exception(implode("\n", $errors));
		}

		$this->canDo = ContentHelper::getActions('com_timelog', 'activitytype', $this->item->id);

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @throws \Exception
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->getInput()->set('hidemainmenu', true);

		$user  = Factory::getUser();
		$isNew = ($this->item->id == 0);

		if (isset($this->item->checked_out))
		{
			$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->id);
		}
		else
		{
			$checkedOut = false;
		}

		$canDo = $this->canDo;

		ToolbarHelper::title(Text::_('COM_TIMELOG_TITLE_ACTIVITYTYPE'), 'clock');

		// If not checked out, can save the item.
		if (!$checkedOut && (($canDo->core_edit ?? $canDo->get('core.edit')) || ($canDo->core_create ?? $canDo->get('core.create'))))
		{
			ToolbarHelper::apply('activitytype.apply', 'JTOOLBAR_APPLY');
			ToolbarHelper::save('activitytype.save', 'JTOOLBAR_SAVE');
		}

		if (!$checkedOut && ($canDo->core_create ?? $canDo->get('core.create')))
		{
			ToolbarHelper::save2new('activitytype.save2new', 'JTOOLBAR_SAVE_AND_NEW');
		}

		// If an existing item, can save to a copy.
		if (!$isNew && ($canDo->core_create ?? $canDo->get('core.create')))
		{
			ToolbarHelper::save2copy('activitytype.save2copy', 'JTOOLBAR_SAVE_AS_COPY');
		}

		// Button for version control
		if ($this->state->params->get('save_history', 1) && $user->authorise('core.edit'))
		{
			ToolbarHelper::versions('com_timelog.activitytype', $this->item->id);
		}

		if (empty($this->item->id))
		{
			ToolbarHelper::cancel('activitytype.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			ToolbarHelper::cancel('activitytype.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
