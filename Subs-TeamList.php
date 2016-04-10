<?php

function tl_pre_load()
{
	loadLanguage('TeamList');
}

function tl_menu_buttons($menu_buttons)
{
	global $txt, $context, $modSettings, $scripturl;

	$new_button = array(
		'title' => $txt['teamlist'],
		'href' => $scripturl . '?action=teamlist',
		'show' => !empty($modSettings['teamlist_enabled']),
		'active_button' => false,
	);

	$new_menu_buttons = array();
	foreach ($menu_buttons as $area => $info)
	{
		$new_menu_buttons[$area] = $info;
		if ($area == 'mlist')
			$new_menu_buttons['teamlist'] = $new_button;
	}

	$menu_buttons = $new_menu_buttons;
}

function tl_actions(&$action_array)
{
	$action_array['teamlist'] = array('TeamList.php', 'TeamList');
}

function tl_admin_areas(&$admin_areas)
{
	global $txt;

	$admin_areas['config']['areas']['modsettings']['subsections']['teamlist'] = array($txt['teamlist']);
}

function tl_modify_modifications(&$sub_actions)
{
	$sub_actions['teamlist'] = 'ModifyTLModSettings';
}

function ModifyTLModSettings($return_config = false)
{
	global $txt, $context, $scripturl, $smcFunc;

	$request = $smcFunc['db_query']('', '
		SELECT
			mg.id_group, mg.group_name, mg.description, mg.group_type, mg.online_color, mg.hidden
		FROM {db_prefix}membergroups AS mg
		WHERE mg.min_posts = {int:min_posts}
			AND mg.id_group != {int:moderator_group}
		ORDER BY group_name',
		array(
			'min_posts' => -1,
			'moderator_group' => 3,
		)
	);

	while ($row = $smcFunc['db_fetch_assoc']($request))
		if ($row['hidden'] != 2)
			$context['groups'][$row['id_group']] = $row['group_name'];

	$smcFunc['db_free_result']($request);

	$config_vars = array(
		array('check', 'teamlist_enabled'),
		array('select', 'teamlist_groups', $context['groups'], 'subtext' => $txt['teamlist_groups_desc'], 'multiple' => true),
		array('check', 'teamlist_additional_groups'),
	);


	if ($return_config)
		return $config_vars;

	if (isset($_GET['save']))
	{
		checkSession();

		saveDBSettings($config_vars);
		writeLog();

		redirectexit('action=admin;area=modsettings;sa=teamlist');
	}

	$context['post_url'] = $scripturl . '?action=admin;area=modsettings;save;sa=teamlist';
	$context['settings_title'] = $txt['teamlist'];

	prepareDBSettingContext($config_vars);
}

?>