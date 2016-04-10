<?php

if (!defined('SMF'))
	die('Hacking attempt...');

function TeamList()
{
	global $context, $memberContext, $modSettings, $scripturl, $smcFunc, $txt, $user_profile;

	loadTemplate('TeamList');
	loadLanguage('Profile');
	$context['page_title'] = $txt['teamlist'];
	$context['sub_template'] = 'teamlist';
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=teamlist',
		'name' => $txt['teamlist']
	);
	$context['html_headers'] .= '
	<style type="text/css">
		#teamlist h3
		{
			text-align: right;
			padding-bottom:  0.5em;
			border-bottom: dashed 1px grey;
			mrgin-bottom: 0.5em;
		}
		#teamlist h3:not(:first-child)
		{
			margin-top: 0.5em;
		}
	</style>';

	$context['teamlist'] = cache_get_data('teamlist', 3600);
	if ($context['teamlist'] === null)
	{
		$members = array();
		$groups = unserialize($modSettings['teamlist_groups']);
		$other = allowedTo('profile_view_any');
		$can_pm = allowedTo('pm_send');
		foreach ($groups as $group)
		{
			$request = $smcFunc['db_query']('', '
				SELECT
					mem.id_member
				FROM {db_prefix}members AS mem
				WHERE mem.id_group = {int:id_group}' . (!empty($modSettings['teamlist_additional_groups']) ? '
					OR FIND_IN_SET({int:id_group}, mem.additional_groups)' : '') . '
				ORDER BY mem.real_name ASC',
				array(
					'id_group' => $group,
				)
			);

			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$members[] = $row['id_member'];
				$member_groups[$group][] = $row['id_member'];
			}
		}
		loadMemberData($members);
		foreach ($groups as $group)
			foreach ($members as $member)
			{
				if (in_array($member, $member_groups[$group]))
				{
					loadMemberContext($member);
					$context['teamlist'][$group][$member] = array(
						'link' => ($other) ? $memberContext[$member]['link'] : $user_profile[$member]['real_name'],
						'lastlogin' => $txt['lastLoggedIn'] . ': ' . timeformat($user_profile[$member]['last_login']),
						'send_pm' => ($can_pm) ? '<a href="' . $scripturl . '?action=pm;sa=send;u=' . $member . '">' . $txt['profileSendIm'] . '</a>' : '',
					);
				}
			}

		cache_put_data('teamlist', $context['teamlist'], 3600);
	}

	$request = $smcFunc['db_query']('', '
		SELECT
			mg.id_group, mg.group_name, mg.hidden
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
}

?>