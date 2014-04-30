<?php
/*
Plugin Name: Userextra
Plugin URI: http://dev.wp-plugins.org/wiki/Userextra
Description: Extends user profiles to include admin-defined attributes, and provides for category access controls with user-level granularity
Author: James Ponder
Version: 0.3
Author URI: http://www.squish.net/
*/
/*
=== Userextra ===
Tags: profiles, users, acl, categories
Requires: Usermeta
== What does this plug-in do? ==
This plug-in does two things:
1. It adds the ability to associate extra information to users
    - can be a variety of types: Text, Text Box, Option List or Toggle
    - optionally each field can be editable by your users
    - can be displayed in your theme (see Usermeta plug-in for details)
2. Category access controls.  Firstly there's an option in Userextra to
   define the categories which are restricted (both visability and posting
   rights).  Then, on a per-user level you can modify:
    - categories_allow - allow a previously denied category, for this user
    - categories_deny  - deny a normally allowed category, for this user
It provides three screens:
Otions -> Userextra (main options window)
Users -> Your Extended Profile (does not appear until some attributes exist)
Manage -> Extended User Profiles
== Installation ==
1. Install "Usermeta" plug-in which this plug-in depends on.
   Remember to click on "Create/Update usermeta tables" when you do
   this if you're using WP 1.5 (see its install instructions).
2. Upload to your plugins folder, usually wp-content/plugins/
3. Activate the plugin on the plugin screen
If you're installing this plug-in for category access control, then:
4a. Define the list of restricted categories in the options screen
4b. Go to Manage -> Extra User Data and for each user who should have access
    add the category(s), comma separated, in the "Allow these locked
    categories" field.
If you're installing this plug-in to have extra user attributes/fields, then:
4a. Choose a name for your field and a description and enter it in the
    options window.
4b. Choose a type and select it, and fill in "Options" as specified:
      - Text is a normal text field.  The Options specifies the width.
      - Text Box is a textarea field.  The Options indicates width, height.
      - Toggle is a checkbox.  The options indicate state: e.g. No, Yes.
      - Option List is a drop-down.  Place the options in a list, e.g. a,b,c.
4c. Optionally specify a default, which is used to automatically populate the
    user's extra profile.
4d. Edit each user in Manage -> Extra User Data, and/or
4e. Log-in as a user and go to Users -> Extended User Profiles
== Category access control ==
- Works by filtering posts
- User level 10 is special and is never restricted
- Extended User Profile window won't appear for users unless attributes defined
== License ==
Copyright (c) 2005, 2006 James Ponder <james@squish.net>
Permission to use, copy, modify, and distribute this software for any
purpose with or without fee is hereby granted, provided that the above
copyright notice and this permission notice appear in all copies.
THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
*/
function get_userextra() {
	static $userextra;
	if (!isset($userextra))
		$userextra = new Userextra;
	return $userextra;
}
class Userextra {
	var $types;
	var $usermeta;
	/* Userextra - class initialiser */
	function Userextra() {
		$this->types = "Text, Text Box, Toggle, Option List";
	}
	function formitem_display($type, $options, $name, $current,
	                          $attrs = array()) {
		$disabled = $attrs['disabled'] == 1 ? 'disabled="true"' : '';
		$h_name = $this->H($name);
		$h_current = $this->H($current);
		$o = $this->options_split($options);
		switch ($type) {
			case 'Text':
				return "<input type='text' name='$h_name' value='$h_current' ".
				"size='$o[0]' $disabled />";
			case 'Text Box':
				return "<textarea name='$h_name' cols='$o[0]' rows='$o[1]' $disabled>".
				"$h_current</textarea>";
			case 'Toggle':
				return "<input type='checkbox' name='$h_name' value='1' ".
				($current == $o[1] ? "checked='true'" : '')." $disabled />";
			case 'Option List':
				$txt = "<select name='$h_name' $disabled>";
				foreach ($o as $item) {
					$h_item = $this->H($item);
					if ($item == $current)
						$txt.= "<option value='$h_item' selected='true'>$h_item</option>";
					else
						$txt.= "<option value='$h_item'>$h_item</option>";
				}
				$txt.= "</select>";
				return $txt;
		}
	}
	function formitem_usertovalue($type, $options, $value) {
		$o = $this->options_split($options);
		switch ($type) {
			case 'Text':
				return $value;
			case 'Text Box':
				return preg_replace('/\r\n?/', '\n', $value); /* clean of CRs */
			case 'Toggle':
				return $value == 1 ? $o[1] : $o[0];
			case 'Option List':
				if (in_array($value, $o, true) == true)
					return $value;
				return count($o) > 0 ? $o[0] : ''; /* not configured? you get empty */
		}
	}
	/* get_fieldinfo($field_name) - return information on the field named.
		 Returns an array with the keys:
		   description => a description of the field
		   type => the type of the field: Text, Text Box, Toggle, Option List
		   options => the options
		   default => the default for this field
		   useredit => No or Yes
		 Note that this only returns information for a field which has been
		 defined by the user in the main Userextra options page.
	  */
	function get_fieldinfo($field_name) {
		$fields = get_settings('userextra_fields');
		if (isset($fields[$field_name]))
			return $fields[$field_name];
		return NULL;
	}
	function display_updated($text) {
		echo '<div class="updated"><p>';
		echo $this->T($text, 'userextra');
		echo '</p></div>';
	}
	function T($text) {
		return __($text, 'userextra');
	}
	function H($text) {
		return htmlspecialchars($text, ENT_QUOTES);
	}
	function HT($text) {
		return htmlspecialchars($this->T($text), ENT_QUOTES);
	}
	function EHT($text) {
		echo $this->HT($text);
	}
	function options_split($text) {
		return preg_split('/\s*,+\s*/', $text, -1, PREG_SPLIT_NO_EMPTY);
	}
	function options_userextra() {
		$this->options_userextra_checkaction();
		$this->options_userextra_display();
	}

	function options_userextra_checkaction() {
		switch ($_REQUEST['action']) {
			case 'update_options':
				/* update options */
				update_option('userextra_adminlevel', $_REQUEST['adminlevel']);
				update_option('userextra_lockedcategories_view',
					$_REQUEST['lockedcategories_view']);
				update_option('userextra_lockedcategories_post',
					$_REQUEST['lockedcategories_post']);
				update_option('userextra_orderuserlist', $_REQUEST['orderuserlist']);
				update_option('userextra_adjudication', $_REQUEST['adjudication']);
				$this->display_updated('Options updated.');
				break;
			case 'add':
			case 'update':
				/* add or update meta field */
				$newinfo = array(
					'description' =>
						$this->formitem_usertovalue('Text', 'n/a',
							$_REQUEST['description']),
					'type' =>
						$this->formitem_usertovalue('Option List', $this->types,
							$_REQUEST['type']),
					'default' =>
						$this->formitem_usertovalue('Text', 'n/a', $_REQUEST['default']),
					'options' =>
						$this->formitem_usertovalue('Text', 'n/a', $_REQUEST['options']),
					'useredit' =>
						$this->formitem_usertovalue('Toggle', 'No, Yes',
							$_REQUEST['useredit']),
				);
				if (!in_array($newinfo['type'], preg_split('/\s*,+\s*/', $this->types),
						true) || strlen($newinfo['description']) < 1) {
					$this->display_updated('Incomplete attribute definition.');
					break;
				}
				if ($newinfo['type'] == 'Option List') {
					$o = $this->options_split($newinfo['options']);
					if (count($o) < 1) {
						$this->display_updated('Option Lists must have at least '.
							'one option.');
						break;
					}
					if (!in_array($newinfo['default'], $o)) {
						$this->display_updated('Option Lists must have a default, '.
							'using first option.');
						$newinfo['default'] = $o[0];
					}
				}
				if ($newinfo['type'] == 'Toggle') {
					$o = $this->options_split($newinfo['options']);
					if (count($o) != 2) {
						$this->display_updated('Toggles must have two options, '.
							'defaulting to "No, Yes".');
						$newinfo['options'] = 'No, Yes';
						$o = $this->options_split($newinfo['options']);
					}
					if (!in_array($newinfo['default'], $o)) {
						$this->display_updated('Toggles must have a default, '.
							'using first option.');
						$newinfo['default'] = $o[0];
					}
				}
				$fields = get_settings('userextra_fields');
				if ($fields == false)
					$fields = array();
				if (isset($_REQUEST['name']) && $_REQUEST['name'])
					$fields[$_REQUEST['name']] = $newinfo;
				update_option('userextra_fields', $fields);
				switch ($_REQUEST['action']) {
					case 'add': $this->display_updated('Added new field.'); break;
					case 'update': $this->display_updated('Updated field.'); break;
				}
				break;
			case 'delete':
				/* delete a meta field */
				$fields = get_settings('userextra_fields');
				if ($fields == false)
					$fields = array();
				unset($fields[$_REQUEST['name']]);
				update_option('userextra_fields', $fields);
				$this->display_updated('Deleted field.');
				break;
		}
	}

	function options_userextra_display() {
		/* Display options page */
		$adminlevel = get_settings('userextra_adminlevel');
		$lockedcategories_view = get_settings('userextra_lockedcategories_view');
		$lockedcategories_post = get_settings('userextra_lockedcategories_post');
		$orderuserlist = get_settings('userextra_orderuserlist');
		$fields = get_settings('userextra_fields');
		$adjudication = get_settings('userextra_adjudication');
		?>
		<div class="wrap">
			<h2><?php $this->EHT('Userextra Fields') ?></h2>
			<table class="editform">
				<tr>
					<th><?php $this->EHT('Name') ?></th>
					<th><?php $this->EHT('Description') ?></th>
					<th><?php $this->EHT('Type') ?></th>
					<th><?php $this->EHT('Options') ?></th>
					<th><?php $this->EHT('Default') ?></th>
					<th><?php $this->EHT('User editable?') ?></th>
				</tr>
				<?php
				$alternate = '';
				foreach ($fields as $name => $f) {
					$disabled = array();
					if ($name == "categories_allow" || $name == "categories_deny")
						$disabled['disabled'] = 1;
					?><form method="post">
					<input type="hidden" name="action" value="update" />
					<input type="hidden" name="name" value="<?php
					echo $this->H($name) ?>" />
					<tr<?php echo $alternate ?>>
						<td><?php echo $this->H($name) ?></td>
						<td><?php echo $this->formitem_display('Text', '32',
								'description', $f['description']) ?></td>
						<td><?php echo $this->formitem_display('Option List',
								$this->types, 'type', $f['type'],
								$disabled) ?></td>
						<td><?php echo $this->formitem_display('Text', '12',
								'options', $f['options']) ?></td>
						<td><?php echo $this->formitem_display('Text', '12',
								'default', $f['default']) ?></td>
						<td><?php echo $this->formitem_display('Toggle', 'No, Yes',
								'useredit', $f['useredit']) ?></td>
						<td><?php
							if (!$disabled['disabled']) {
								?><a href="<?php echo $this->H(add_query_arg(
									array('action' => 'delete', 'name' => $name),
									$_SERVER['REQUEST_URI']))
								?>" onClick="return confirm('<?php
								echo $this->H($this->T('Are you sure '.
										'you want to delete this entry?')." ($name) ".
									$this->H($this->T('Note: This will not delete the '.
										'user data from the database - '.
										'only this definition for display '.
										'and input purposes.')))
								?>');"><?php $this->EHT('Delete') ?></a>
							<?php
							} ?>
						</td>
						<td><input type="submit" value="<?php
							echo $this->H($this->T('Modify')).' &raquo;' ?>" /></td>
					</tr>
					</form>
					<?php
					$alternate = $alternate == '' ? ' class="alternate"' : '';
				} ?>
				<form method="post">
					<input type="hidden" name="action" value="add" />
					<tr<?php echo $alternate ?>>
						<td><?php echo $this->formitem_display('Text', '20',
								'name', '') ?></td>
						<td><?php echo $this->formitem_display('Text', '32',
								'description', '') ?></td>
						<td><?php echo $this->formitem_display('Option List',
								$this->types, 'type', '') ?></td>
						<td><?php echo $this->formitem_display('Text', '12',
								'options', '') ?></td>
						<td><?php echo $this->formitem_display('Text', '12',
								'default', '') ?></td>
						<td><?php echo $this->formitem_display('Toggle', 'No, Yes',
								'useredit', '') ?></td>
						<td></td>
						<td><input type="submit" value="<?php
							echo $this->H($this->T('Add')).' &raquo;' ?>" /></td>
					</tr>
				</form>
			</table>
			<p>Options:<ul>
				<li>Text - Width (e.g. "24")</li>
				<li>Text Box - Width, Height (e.g. "30, 10")</li>
				<li>Toggle - Value when not selected, Value when selected
					(e.g. "No, Yes")</li>
				<li>Option List - Item 1[, Item 2]... (e.g. "Low, Medium, High")</li>
			</ul></p>
		</div>
		<div class="wrap">
			<h2><?php $this->EHT('Userextra Options') ?></h2>
			<form method="post">
				<input type="hidden" name="action" value="update_options" />
				<fieldset class="options">
					<legend><?php $this->EHT('Main options') ?></legend>
					<table class="editform">
						<tr>
							<td><?php echo $this->HT("Administration level to edit extended ".
										"user profiles").": " ?></td>
							<td><input type="text" size="2" name="adminlevel" value="<?php
								$this->EHT($adminlevel) ?>"></td>
						</tr>
						<tr>
							<td><?php echo $this->HT("Order of user list in admin page").
									": " ?></td>
							<td><?php echo $this->formitem_display('Option List',
									'ID, user_login',
									'orderuserlist', $orderuserlist) ?></td>
						</tr>
						<tr>
							<td><?php echo $this->HT("List of category names ".
										" not visable by default").": " ?></td>
							<td><?php echo $this->formitem_display('Text', '40',
									'lockedcategories_view', $lockedcategories_view) ?></td>
						</tr>
						<tr>
							<td><?php echo $this->HT("List of category names ".
										" users can't post to by default").": " ?></td>
							<td><?php echo $this->formitem_display('Text', '40',
									'lockedcategories_post', $lockedcategories_post) ?></td>
						</tr>
						<tr>
							<td><?php echo $this->HT("Adjudication style for posts in ").
									"both allow and deny categories ".
									"(decides visibility): " ?></td>
							<td><?php echo $this->formitem_display('Option List',
									'Unanimous Permit, Any Permit', 'adjudication',
									$adjudication) ?></td>
						</tr>
					</table>
					<div class="submit"><input type="submit" value="<?php
						echo $this->H($this->T('Update options')).' &raquo;' ?>" /></div>
				</fieldset>
			</form>
		</div>
	<?
	}
	function options_userdata() {
		global $user_level;
		$adminlevel = get_settings('userextra_adminlevel');
		get_currentuserinfo();
		if ($user_level < $adminlevel) {
			die($this->T('You do not have permission to edit this user.'));
		}
		$id = (int) $_REQUEST['id'];
		$page = $this->options_userdata_checkaction($id);
		switch($page) {
			case 'list':
				$this->options_userdata_display_list();
				break;
			case 'edit':
				$this->options_userdata_display_user($id);
				break;
		}
	}
	function options_myuserdata() {
		global $user_ID;
		get_currentuserinfo();
		$page = $this->options_userdata_checkaction($user_ID, true);
		$this->options_userdata_display_user($user_ID, true);
	}
	function options_userdata_checkaction($id, $only_useredit = false) {
		switch ($_REQUEST['action']) {
			case 'edit':
				$fields = get_settings('userextra_fields');
				foreach ($fields as $name => $f) {
					if ($only_useredit == true && $f['useredit'] != 'Yes')
						continue;
					$value = $this->formitem_usertovalue($f['type'], $f['options'],
						$_REQUEST[$name]);
					$this->usermeta->set($id, $name, $value);
				}
				$this->display_updated('User updated.');
				return "list";
			default:
				if ($id)
					return "edit";
				return 'list';
		}
	}
	function options_userdata_display_list() {
		/* Display Extra User Data page */
		global $wpdb;
		$orderuserlist = get_settings('userextra_orderuserlist');
		$users = $wpdb->get_results(
			"SELECT ID FROM $wpdb->users ORDER BY $orderuserlist");
		?>
		<div class="wrap">
			<h2><?php $this->EHT('All Users') ?></h2>
			<p>Select the user below to edit category access control and additional
				fields you have added in <em>Userextra</em> options page.</p>
			<table class="editform" style='width: 100%'>
				<tr>
					<th style='text-align: left'><?php $this->EHT('ID') ?></th>
					<th style='text-align: center'><?php $this->EHT('Nickname') ?></th>
					<th style='text-align: center'><?php $this->EHT('Name') ?></th>
					<th style='text-align: center'><?php $this->EHT('E-mail') ?></th>
					<th style='text-align: center'><?php $this->EHT('Website') ?></th>
					<th></th>
				</tr>
				<?php
				foreach ($users as $user) {
					$alternate = $alternate == '' ? ' class="alternate"' : '';
					$user_data = get_userdata($user->ID);
					if (($url = $user_data->url) == null)
						$url = $user_data->user_url;
					if (($nickname = $user_data->nickname) == null)
						$nickname = $user_data->user_nickname;
					if (($email = $user_data->email) == null)
						$email = $user_data->user_email;
					if (($firstname = $user_data->first_name) == null)
						$firstname = $user_data->user_firstname;
					if (($lastname = $user_data->last_name) == null)
						$lastname = $user_data->user_lastname;
					$short_url = str_replace('http://', '', $url);
					$short_url = str_replace('www.', '', $short_url);
					if ('/' == substr($short_url, -1))
						$short_url = substr($short_url, 0, -1);
					if (strlen($short_url) > 35)
						$short_url =  substr($short_url, 0, 32).'...';
					?><form method="post">
					<tr<?php echo $alternate ?>>
						<td><?php echo $this->H($user_data->ID) ?></td>
						<td><strong><?php
								echo $this->H($nickname) ?></strong></td>
						<td><?php echo $this->H("$firstname $lastname") ?></td>
						<td><?php echo $this->H($email) ?></td>
						<td><?php echo $this->H($short_url) ?></td>
						<td><a href="<?php echo $this->H(add_query_arg(
								array('id' => $user_data->ID),
								$_SERVER['REQUEST_URI'])) ?>" class="edit"><?php
								$this->EHT('Edit') ?></a></td>
					</tr>
					</form>
				<?php
				} ?>
			</table>
		</div>
	<?
	}
	function options_userdata_display_user($id, $only_useredit = false) {
		/* Display Your Extended Profile page */
		$edituser = get_userdata($id);
		$anyeditables = 0;
		?>
		<div class="wrap">
			<h2><?php $this->EHT('User details') ?></h2>
			<form method="post">
				<input type="hidden" name="id" value="<?php echo $id ?>" />
				<input type="hidden" name="action" value="edit" />
				<table class="editform" style='width: 100%'>
					<tr style='padding-bottom: 0.5em'>
						<th style='width: 33%' scope="row"><?php $this->EHT('Username:')
							?></th>
						<td style='width: 67%'><?php
							echo $edituser->user_login ?></td>
					</tr>
					<?php
					$fields = get_settings('userextra_fields');
					$alternate = '';
					foreach ($fields as $name => $f) {
						if ($only_useredit == true && $f['useredit'] != 'Yes')
							continue;
						$alternate = $alternate == '' ? ' class="alternate"' : '';
						$star = $only_useredit == false && $f['useredit'] == 'Yes' ? 1 : 0;
						if ($star)
							$anyeditables = 1;
						$um = $this->usermeta;
						$value = $um->get($id, $name, true);
						if (is_null($value))
							$value = $f['default'];
						?>
						<tr<?php echo $alternate ?>>
							<th style='width: 33%; vertical-align: top; padding-top: 0.4em'
							    scope="row"><?php
								echo $this->H($f['description']. /* " ($name)". */ ": ").
									($star ? '*' : '') ?></th>
							<td style='width: 67%'><?php
								echo $this->formitem_display($f['type'], $f['options'], $name,
									$value) ?></td>
						</tr>
					<?php
					} ?>
				</table>
				<div class="submit"><input type="submit" value="<?php
					echo $this->H($this->T('Update user')).' &raquo;' ?>" /></div>
				<?php if ($anyeditables) {
					echo '<p>* This item is editable by the user.</p>';
				} ?>
			</form>
		</div>
	<?
	}
	function action_init() {
		if (function_exists('get_usermeta_object'))
			$this->usermeta = get_usermeta_object();
	}
	function action_admin_menu() {
		add_options_page($this->T('User attributes'),
			$this->T('Userextra'),
			8, basename(__FILE__), array(&$this, 'options_userextra'));
		add_submenu_page("edit.php", $this->T('Extra User Data'),
			$this->T('Extra User Data'),
			8, basename(__FILE__), array(&$this, 'options_userdata'));
		$fields = get_settings('userextra_fields');
		foreach ($fields as $name => $f) {
			if ($f['useredit'] == 'Yes') {
				add_submenu_page("profile.php", $this->T('Profile (extended)'),
					$this->T('Your Extended Profile'),
					0, basename(__FILE__), array(&$this,
						'options_myuserdata'));
				break;
			}
		}
	}
	function is_current_user_allowed($post) {
		global $user_ID;
		$locked = get_settings('userextra_lockedcategories_view');
		$locked_a = $this->options_split($locked);
		$allow = $this->usermeta->get($user_ID, "categories_allow", true);
		$allow_a = $this->options_split($allow);
		$deny = $this->usermeta->get($user_ID, "categories_deny", true);
		$deny_a = $this->options_split($deny);
		$post_cats = wp_get_post_cats(1, $post->ID);
		$seen_allow = false;
		$seen_deny = false;
		foreach ($post_cats as $post_cat) {
			$name = get_cat_name($post_cat);
			if (in_array($name, $allow_a) ||
				(!(in_array($name, $locked_a)) && !(in_array($name, $deny_a)))) {
				$seen_allow = true;
			} else {
				$seen_deny = true;
			}
		}
		if ($seen_deny == false)
			return true;
		if ($seen_allow == false)
			return false;
		$adjudication = get_settings('userextra_adjudication');
		if ($adjudication == 'Any Permit')
			return true;
		return false; /* not unanimous */
	}
	function filter_error() {
		die("Userextra: Permission Denied.\n");
	}
	function filter_hide() {
		return "filter_hide";
	}
	function filter_posts($posts) {
		global $user_level;
		if ($user_level == 10)
			return $posts;
		$ok = array();
		foreach ($posts as $post) {
			if ($this->is_current_user_allowed($post))
				$ok[] = $post;
		}
		return $ok;
	}
	function filter_content_save_pre($in) {
		/* for some reason, WP doesn't call category_save_pre on edits */
		global $user_ID;
		$locked = get_settings('userextra_lockedcategories_post');
		$locked_a = $this->options_split($locked);
		$allow = $this->usermeta->get($user_ID, "categories_allow", true);
		$allow_a = $this->options_split($allow);
		$deny = $this->usermeta->get($user_ID, "categories_deny", true);
		$deny_a = $this->options_split($deny);
		if ($_REQUEST['post_category'] == null)
			return $in;
		foreach ($_REQUEST['post_category'] as $catid) {
			$name = get_catname($catid);
			if (!in_array($name, $allow_a) &&
				(in_array($name, $locked_a) || in_array($name, $deny_a))) {
				die($this->T('You cannot post to that category as this user.'));
			}
		}
		return $in;
	}

	function action_admin_head($in) {
		global $user_level;
		get_currentuserinfo();
		if ($user_level < 10) {
			if(preg_match('#/wp-admin/post\.php#',
				$_SERVER['REQUEST_URI'])) {
				ob_start(array($this, 'postpage_output'));
			}
		}
		return $in;
	}
	function postpage_output($page) {
		return preg_replace_callback(
			'#<label for="category-(.*?)</label>.*?<span .*?</span>#sim',
			array($this, 'modify_field'), $page);
	}
	function modify_field($matches) {
		global $user_ID;
		$locked = get_settings('userextra_lockedcategories_post');
		$locked_a = $this->options_split($locked);
		$allow = $this->usermeta->get($user_ID, "categories_allow", true);
		$allow_a = $this->options_split($allow);
		$deny = $this->usermeta->get($user_ID, "categories_deny", true);
		$deny_a = $this->options_split($deny);
		if (preg_match('#<input value="(\d+)" #i', $matches[0], $r) > 0) {
			$catid = $r[1];
			$name = get_catname($catid);
			if (!in_array($name, $allow_a) &&
				(in_array($name, $locked_a) || in_array($name, $deny_a))) {
				return "";
			}
		} else {
			return "";
		}
		return $matches[0];
	}
}
global $user_level;
load_plugin_textdomain('userextra');
$userextra = get_userextra();
add_option('userextra_adminlevel', '8',
	$userextra->T('Administration level to edit extended user '.
		'profiles'));
add_option('userextra_lockedcategories_view', '',
	$userextra->T('List of category names not visable by default'));
add_option('userextra_lockedcategories_post', '',
	$userextra->T("List of category names users can't post to by ".
		"default"));
add_option('userextra_orderuserlist', 'ID',
	$userextra->T('Ordering of user list'));
add_option('userextra_adjudication', 'Unanimous Permit',
	$userextra->T('Adjudication style for posts in '.
		'multiple categories'));
add_option('userextra_fields', array(
	'categories_allow' => array(
		'description' => 'Allow these locked categories',
		'type' => 'Text',
		'options' => '32',
		'default' => ''),
	'categories_deny' => array(
		'description' => 'Deny these categories',
		'type' => 'Text',
		'options' => '32',
		'default' => ''),
));
add_action('admin_menu', array(&$userextra, 'action_admin_menu'));
add_action('init', array(&$userextra, 'action_init'));
add_filter('the_posts', array(&$userextra, 'filter_posts'));
add_filter('content_save_pre', array(&$userextra,
	'filter_content_save_pre'));
add_action('admin_head', array(&$userextra, 'action_admin_head'));
?>