#
#<?php die('Forbidden.'); ?>
#Date: 2026-02-18 07:41:11 UTC
#Software: Joomla! 6.0.0 Stable [ Kuimarisha ] 14-October-2025 16:00 UTC

#Fields: datetime	priority clientip	category	message
2026-02-18T07:41:11+00:00	CRITICAL 127.0.0.1	error	Uncaught Throwable of type InvalidArgumentException thrown with message "Invalid controller class: display". Stack trace: #0 [ROOT]/libraries/src/Dispatcher/ComponentDispatcher.php(142): Joomla\CMS\Dispatcher\ComponentDispatcher->getController()
#1 [ROOT]/libraries/src/Component/ComponentHelper.php(361): Joomla\CMS\Dispatcher\ComponentDispatcher->dispatch()
#2 [ROOT]/libraries/src/Application/AdministratorApplication.php(150): Joomla\CMS\Component\ComponentHelper::renderComponent()
#3 [ROOT]/libraries/src/Application/AdministratorApplication.php(205): Joomla\CMS\Application\AdministratorApplication->dispatch()
#4 [ROOT]/libraries/src/Application/CMSApplication.php(320): Joomla\CMS\Application\AdministratorApplication->doExecute()
#5 [ROOT]/administrator/includes/app.php(58): Joomla\CMS\Application\CMSApplication->execute()
#6 [ROOT]/administrator/index.php(32): require_once('...')
#7 {main}
