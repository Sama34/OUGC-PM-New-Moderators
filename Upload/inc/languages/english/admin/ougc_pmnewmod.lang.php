<?php

/***************************************************************************
 *
 *	OUGC PM New Moderators plugin (/inc/languages/english/admin/ougc_pmnewmod.lang.php)
 *	Author: Omar Gonzalez
 *	Copyright: © 2018 Omar Gonzalez
 *
 *	Website: http://omarg.me
 *
 *	PM new moderators once being assigned to a forum.
 *
 ***************************************************************************

****************************************************************************
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
****************************************************************************/

// Plugin API
$l['setting_group_ougc_pmnewmod'] = 'OUGC PM New Moderators';
$l['setting_group_ougc_pmnewmod_desc'] = 'PM new moderators once being assigned to a forum.';

// Settings
$l['setting_ougc_pmnewmod_title'] = 'Private Message Title';
$l['setting_ougc_pmnewmod_title_desc'] = 'Insert here the private message title. Will use the language system if empty.
<br />{1} = User name with no formatting.
<br />{2} = Forum name with no formatting.';
$l['setting_ougc_pmnewmod_message'] = 'Private Message Content';
$l['setting_ougc_pmnewmod_message_desc'] = 'Insert here the message body. Will use the language system if empty.
<br />{1} = User name with no formatting.
<br />{2} = Forum name with no formatting.
<br />{3} = Forum board name.';

// PluginLibrary
$l['ougc_pmnewmod_pluginlibrary_required'] = 'This plugin requires <a href="{1}">PluginLibrary</a> version {2} or later to be uploaded to your forum.';
$l['ougc_pmnewmod_pluginlibrary_old'] = 'This plugin requires PluginLibrary version {2} or later, whereas your current version is {1}. Please do update <a href="{3}">PluginLibrary</a>.';