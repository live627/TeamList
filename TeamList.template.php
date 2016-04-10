<?php

function template_teamlist()
{
	global $context, $txt, $user_info, $memberContext, $context, $settings;

	echo '
	<div class="cat_bar">
		<h3 class="catbg">
			', $txt['teamlist'], '
		</h3>
	</div>
	<span class="upperframe"><span></span></span>
	<div class="roundframe">
	<table id="teamlist">
		<tbody class="content">';

	foreach ($context['teamlist'] as $id_group => $members)
	{
		echo '
			<tr><td colspan="2"><h3>',  $context['groups'][$id_group], '</h3></td></tr>
		';

	foreach ($members as $user_id => $buddy) {
		echo '
			<tr>
				<td width="40%" align="center">', $memberContext[$user_id]['avatar']['image'], '</td>
				<td>
					';

		echo '
					<table width="100%" cellspacing="8">';
		echo '
						<tr><td width="5%" align="center"><img src="', $settings['default_images_url'], ($memberContext[$user_id]['online']['is_online'] ? '/online.gif' : '/offline.gif'), '" alt="', $memberContext[$user_id]['online']['label'] , '" title="', $memberContext[$user_id]['online']['label'] , '" /></td><td><h4>', $buddy['link'], '</h4></td>';

		if (!empty($memberContext[$user_id]['title']))
			echo '
						<tr><td align="center"><img src="', $settings['default_images_url'], '/email_sm.gif" alt="" /></td><td>', $memberContext[$user_id]['title'], '</td></tr>';

		echo '
						<tr><td align="center"><img src="', $settings['default_images_url'], '/icons/package_installed.gif" alt="" /></td><td>', $buddy['lastlogin'], '</td></tr>';

		if (!empty($buddy['send_pm']))
			echo '
						<tr><td align="center"><img src="', $settings['default_images_url'], '/email_sm.gif" alt="" /></td><td>', $buddy['send_pm'], '</td></tr>';

		echo '
					</table>
				</td>
			</tr>';
		}
	}
	echo '
		</tbody>
	</table>
	</div>
	<span class="lowerframe"><span></span></span>';
}

?>