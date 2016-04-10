<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
{
	$ssi = true;
	require_once(dirname(__FILE__) . '/SSI.php');
}
elseif (!defined('SMF'))
	exit('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

add_integration_function('integrate_pre_include', '$sourcedir/Subs-TeamList.php');
add_integration_function('integrate_pre_load', 'tl_pre_load');
add_integration_function('integrate_actions', 'tl_actions');
add_integration_function('integrate_menu_buttons', 'tl_menu_buttons');
add_integration_function('integrate_modify_modifications', 'tl_modify_modifications');
add_integration_function('integrate_admin_areas', 'tl_admin_areas');

updateSettings(array(
	'teamlist_enabled' => 1,
	'teamlist_groups' => serialize(array(1, 2)),
));

if (!empty($ssi))
	echo 'Database installation complete!';

?>