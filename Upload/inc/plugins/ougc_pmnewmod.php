<?php

/***************************************************************************
 *
 *	OUGC PM New Moderators plugin (/inc/plugins/ougc_pmnewmod.php)
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

// Die if IN_MYBB is not defined, for security reasons.
defined('IN_MYBB') or die('Direct initialization of this file is not allowed.');

// PLUGINLIBRARY
defined('PLUGINLIBRARY') or define('PLUGINLIBRARY', MYBB_ROOT.'inc/plugins/pluginlibrary.php');

// Plugin API
function ougc_pmnewmod_info()
{
	global $ougc_pmnewmod;

	return $ougc_pmnewmod->_info();
}

// _activate() routine
function ougc_pmnewmod_activate()
{
	global $ougc_pmnewmod;

	return $ougc_pmnewmod->_activate();
}

// _is_installed() routine
function ougc_pmnewmod_is_installed()
{
	global $ougc_pmnewmod;

	return $ougc_pmnewmod->_is_installed();
}

// _uninstall() routine
function ougc_pmnewmod_uninstall()
{
	global $ougc_pmnewmod;

	return $ougc_pmnewmod->_uninstall();
}

// Plugin class
class OUGC_PMNewMod
{
	function __construct()
	{
		global $plugins;

		// Tell MyBB when to run the hook
		if(defined('IN_ADMINCP'))
		{
			$plugins->add_hook('admin_config_settings_begin', array($this, 'load_language'));
			$plugins->add_hook('admin_forum_management_start_moderators_commit', array($this, 'hook_admin_forum_management_start_moderators_commit'));
		}
	}

	// Plugin API:_info() routine
	function _info()
	{
		global $lang;

		$this->load_language();

		$this->_info = array(
			'name'					=> 'OUGC PM New Moderators',
			'description'			=> $lang->setting_group_ougc_pmnewmod_desc,
			'website'				=> 'http://omarg.me',
			'author'				=> 'Omar G.',
			'authorsite'			=> 'http://omarg.me',
			'version'				=> '1.0',
			'versioncode'			=> 1000,
			'compatibility'			=> '18*',
			'codename'				=> 'ougc_pmnewmod',
			'pl'			=> array(
				'version'	=> 12,
				'url'		=> 'https://community.mybb.com/mods.php?action=view&pid=573'
			)
		);

		return $this->_info;
	}

	// Plugin API:_activate() routine
	function _activate()
	{
		global $PL, $lang, $mybb;
		$this->load_pluginlibrary();

		$PL->settings('ougc_pmnewmod', $lang->setting_group_ougc_pmnewmod, $lang->setting_group_ougc_pmnewmod_desc, array(
			'title'			=> array(
				'title'			=> $lang->setting_ougc_pmnewmod_title,
				'description'	=> $lang->setting_ougc_pmnewmod_title_desc,
				'optionscode'	=> 'text',
				'value'			=> $lang->ougc_pmnewmod_pm_title
			),
			'message'			=> array(
				'title'			=> $lang->setting_ougc_pmnewmod_message,
				'description'	=> $lang->setting_ougc_pmnewmod_message_desc,
				'optionscode'	=> 'textarea',
				'value'			=> $lang->ougc_pmnewmod_pm_message
			),
		));

		// Insert/update version into cache
		$plugins = $mybb->cache->read('ougc_plugins');
		if(!$plugins)
		{
			$plugins = array();
		}

		if(!isset($plugins['pmnewmod']))
		{
			$plugins['pmnewmod'] = $this->_info['versioncode'];
		}

		/*~*~* RUN UPDATES START *~*~*/

		/*~*~* RUN UPDATES END *~*~*/

		$plugins['pmnewmod'] = $this->_info['versioncode'];
		$mybb->cache->update('ougc_plugins', $plugins);
	}

	// Plugin API:_is_installed() routine
	function _is_installed()
	{
		global $cache;

		$plugins = $cache->read('ougc_plugins');

		return isset($plugins['pmnewmod']);
	}

	// Plugin API:_uninstall() routine
	function _uninstall()
	{
		global $PL, $cache;
		$this->load_pluginlibrary();

		// Delete settings
		$PL->settings_delete('ougc_pmnewmod');

		// Delete version from cache
		$plugins = (array)$cache->read('ougc_plugins');

		if(isset($plugins['pmnewmod']))
		{
			unset($plugins['pmnewmod']);
		}

		if(!empty($plugins))
		{
			$cache->update('ougc_plugins', $plugins);
		}
		else
		{
			$PL->cache_delete('ougc_plugins');
		}
	}

	// Load language file
	function load_language($isdatahandler=false)
	{
		global $lang;

		isset($lang->setting_group_ougc_pmnewmod) or $lang->load('ougc_pmnewmod', $isdatahandler);
	}

	// PluginLibrary requirement check
	function load_pluginlibrary()
	{
		global $lang;
		$this->load_language();

		if(!file_exists(PLUGINLIBRARY))
		{
			flash_message($lang->sprintf($lang->ougc_pmnewmod_pluginlibrary_required, $this->_info['pl']['ulr'], $this->_info['pl']['version']), 'error');
			admin_redirect('index.php?module=config-plugins');
		}

		global $PL;
		$PL or require_once PLUGINLIBRARY;

		if($PL->version < $this->_info['pl']['version'])
		{
			global $lang;

			flash_message($lang->sprintf($lang->ougc_pmnewmod_pluginlibrary_old, $PL->version, $this->_info['pl']['version'], $this->_info['pl']['ulr']), 'error');
			admin_redirect('index.php?module=config-plugins');
		}
	}

	// Hook: editpost_end/datahandler_post_update
	function hook_admin_forum_management_start_moderators_commit(&$dh)
	{
		global $mybb, $lang, $db, $newmod, $new_mod, $isgroup, $forum;
		$this->load_language(true);

		$title = $mybb->settings['ougc_pmnewmod_title'];
		$message = $mybb->settings['ougc_pmnewmod_message'];

		!empty($title) or $title = $lang->ougc_pmnewmod_pm_title;
		!empty($message) or $message = $lang->ougc_pmnewmod_pm_message;

		if(empty($title) || empty($message))
		{
			return;
		}

		$id = (int)$new_mod['id'];

		$users = array();
		if($isgroup)
		{
			switch($db->type)
			{
				case 'sqlite':
				case 'pgsql':
					$where = "','||additionalgroups||',' LIKE '%,{$id},%'";
					break;
				default:
					$where = "CONCAT(',',additionalgroups,',') LIKE '%,{$id},%'";
			}

			$query = $db->simple_select('users', 'uid,username,usergroup,additionalgroups,displaygroup,receivepms,language', "usergroup='{$id}' OR {$where}");

			while($user = $db->fetch_array($query))
			{
				$users[(int)$user['uid']] = $user;
			}
		}
		else
		{
			$query = $db->simple_select('users', 'uid,username,usergroup,additionalgroups,displaygroup,receivepms,language', "uid='{$id}'", array('limit' => 1));
			$user = $db->fetch_array($query);

			$users[(int)$user['uid']] = $user;
		}

		unset($user);

		foreach($users as $uid => $user)
		{
			$this->send_pm(array(
				'subject'		=> $lang->sprintf($title, htmlspecialchars_uni($user['username']), strip_tags($forum['name'])),
				'message'		=> $lang->sprintf($message, htmlspecialchars_uni($user['username']), strip_tags($forum['name']), $mybb->settings['bbname']),
				'touid'			=> $uid
			), -1, true);
		}
	}

	// Send a Private Message to a user  (Copied from MyBB 1.7)
	function send_pm($pm, $fromid=0, $admin_override=false)
	{
		global $mybb;

		if(!$pm['subject'] || !$pm['message'] || (!$pm['receivepms'] && !$admin_override))
		{
			return false;
		}

		require_once MYBB_ROOT.'inc/datahandlers/pm.php';
		$pmhandler = new PMDataHandler();

		// Build our final PM array
		$pm = array(
			'subject'		=> $pm['subject'],
			'message'		=> $pm['message'],
			'icon'			=> -1,
			'fromid'		=> ($fromid == 0 ? (int)$mybb->user['uid'] : ($fromid < 0 ? 0 : $fromid)),
			'toid'			=> array($pm['touid']),
			'bccid'			=> array(),
			'do'			=> '',
			'pmid'			=> '',
			'saveasdraft'	=> 0,
			'options'	=> array(
				'signature'			=> 0,
				'disablesmilies'	=> 0,
				'savecopy'			=> 0,
				'readreceipt'		=> 0
			)
		);
		// should we use the bccid field?

		if(isset($mybb->session)) // apparently always false in acp
		{
			$pm['ipaddress'] = $mybb->session->packedip;
		}

		// Admin override
		$pmhandler->admin_override = (int)$admin_override;
		$pmhandler->set_data($pm);

		if($pmhandler->validate_pm())
		{
			$pmhandler->insert_pm();
			return true;
		}

		return false;
	}
}

global $ougc_pmnewmod;

$ougc_pmnewmod = new OUGC_PMNewMod;