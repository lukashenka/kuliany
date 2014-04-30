<?php
/*
Plugin Name: Usermeta
Plugin URI: http://dev.wp-plugins.org/wiki/Usermeta
Description: Adds user meta tables and API
Author: James Ponder
Version: 0.4
Author URI: http://www.squish.net/
*/
/*
=== Usermeta ===
Tags: profiles, users, acl, categories
== What does this plug-in do? ==
This plug-in adds an API for WordPress 1.5, or provides an alternative API
for WP 2.0 so that other plug-ins can associate arbitrary meta information
to users very easily.
== Installation ==
1. Upload to your plugins folder, usually wp-content/plugins/
2. Activate the plugin on the plugin screen
3. Click "Create/Update usermeta tables" in Options -> Usermeta
== Examples ==
  === Get the Usermeta object ===

      $usermeta = get_usermeta_object();
  === Associate something with a user ===
      $usermeta->set($user_id, "myplugin_thingy", "myvalue");
 === Get the value for the author of a post (in the Loop) ===
      $val = $usermeta->get(get_the_author_ID(), "myplugin_thingy", true);
      if (is_null($val)) {
        do default action when no meta data
      } else {
        echo $val
      }
  === Adding more info to a post... ===
      <p>This post written by <?php the_author(); ?> who lives at
      <?php $usermeta = new Usermeta();
        echo $usermeta->get(get_the_author_ID(), "address", true); ?></p>
  === Add a few bits of information to the current user ===
      global $user_ID;
      get_currentuserinfo();
      $usermeta->add($user_ID, "myplugin_listofthings", "myval_1");
      $usermeta->add($user_ID, "myplugin_listofthings", "myval_2");
      $usermeta->add($user_ID, "myplugin_listofthings", "myval_3");
== API ==
  $bool = $usermeta->database_exists()
    Check to see if our tables exist, returns true if so
  $out = $usermeta->ensure_database()
    Ensure that the usermeta table has been created.  Returns an array
    of messages of actions taken, if any.  (Comes from Wordpress' dbDelta
    function.)
  $bool = $usermeta->add($user_id, $key, $value)
  $bool = $usermeta->add($user_id, $key, $value, $unique)
    Adds in the given value for this user/key when unique is false (or not
    passed in).  If unique is true, a check is made to see if the user/key
    already exists and if it does, no new entry is added.  If anything was
    added true is returned, false otherwise.
  $bool = $usermeta->delete($user_id, $key)
  $bool = $usermeta->delete($user_id, $key, $value)
    If no value is given, deletes all values for the given user/key.  If a
    value is given, only tries to delete that value.  If anything was
    deleted, returns true.
  $bool = $usermeta->get($user_id, $key, $single)
    If single is false, returns an array of all values for this user/key,
    however if single is true, returns the first found entry for this
    user/key or NULL if there are none.
  $bool = $usermeta->set($user_id, $key, $value)
    Sets all occurances of user_id/key to the given value.  If no such
    user_id/key exists, a new entry is added. Always true on success.
  $bool = $usermeta->update($user_id, $key, $value)
  $bool = $usermeta->update($user_id, $key, $value, $prev_value)
    Updates all occurances of this user/key with the new value.  If optional
    fourth param is passed in, then only matches with that value are changed.
    Returns true if any items were changed, false otherwise.
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
== Upgrading from WordPress 1.5 to 2.0 ===
If you used Usermeta 0.1 or 0.2 you must run this before you upgrade to
WP 2.0:
  ALTER TABLE wp_usermeta CHANGE meta_id umeta_id bigint(20)
    NOT NULL auto_increment
You do not need to do this with Usermeta 0.3 which is compatible with both
versions.
=== Failed upgrades to WP 2.0 ===
Unfortunately due to some interaction between WP 2.0 and the tables which
were used up in Usermeta 0.1 if you upgrade to WP 2.0 having used an old
version of Usermeta it will fail to correctly create the necessary data in
wp_usermeta.  You may see the error:
  WordPress database error: [Key column 'umeta_id' doesn't exist in table]
The simplest fix is to restore your database and then follow the
instructions above.  The problem any other way is that you need to go
through the upgrade process again to populate the wp_usermeta table with WP
2.0 parameters (if you really want to try, see upgrade_160() in WordPress
API.
*/
function get_usermeta_object() {
	static $usermeta;
	if (!isset($usermeta))
		$usermeta = new Usermeta;
	return $usermeta;
}
class Usermeta {
	var $table;
	var $cache;
	var $types;
	/* Usermeta - class initialiser */
	function Usermeta() {
		global $table_prefix;
		$this->table = "${table_prefix}usermeta";
	}
	/* database_exists() - check to see if our tables exist, returns true
		 if so */
	function database_exists() {
		global $wpdb;
		if ($wpdb->get_var("SHOW TABLES LIKE '$this->table'") != $this->table)
			return false;
		return true;
	}

	/* ensure_database() - ensures that the necessary table has been
		 created */
	function ensure_database() {
		require_once(ABSPATH.'wp-admin/upgrade-functions.php');
		return dbDelta("CREATE TABLE $this->table (
    umeta_id bigint(20) NOT NULL auto_increment,
    user_id bigint(20) NOT NULL default '0',
    meta_key varchar(255) default NULL,
    meta_value longtext,
    PRIMARY KEY  (umeta_id),
    KEY user_id (user_id),
    KEY meta_key (meta_key)
    ) TYPE=MyISAM;");
	}
	/* add(user_id, key, value, unique) - adds in the given value for this
		 user/key when unique is false.  If unique is true, a check is made to
		 see if the user/key already exists and if it does, no new entry is
		 added.  If anything was added true is returned, false otherwise. */
	function add($user_id, $key, $value, $unique = false) {
		global $wpdb;
		if ($unique) {
			if( $wpdb->get_var("SELECT meta_key FROM $this->table
                          WHERE meta_key='$key' AND user_id = '$user_id'") ) {
				return false;
			}
		}
		$wpdb->query("INSERT INTO $this->table (user_id,meta_key,meta_value)
                  VALUES ('$user_id','$key','$value')");
		return true;
	}
	/* delete(user_id, key, value) - if no value is given, deletes all values
		 for the given user/key.  If a value is given, only tries to delete that
		 value.  If anything was deleted, returns true. */
	function delete($user_id, $key, $value = NULL) {
		global $wpdb;
		if (is_null($value)) {
			$wpdb->query("DELETE FROM $this->table WHERE user_id='$user_id'
                    AND meta_key='$key'");
		} else {
			$wpdb->query("DELETE FROM $this->table WHERE user_id='$user_id'
                    AND meta_key='$key' AND meta_value='$value'");
		}
		if ($wpdb->rows_affected < 1)
			return false;
		$this->update_cache($user_id, $key);
		return true;
	}
	/* get(user_id, key, single) - if single is false, returns an array of all
		 values for this user/key, however if single is true, returns the first
		 found entry for this user/key or NULL if there are none */

	function get($user_id, $key, $single = false) {
		if (isset($this->cache[$user_id][$key])) {
			$values = $this->cache[$user_id][$key];
			if (is_null($values))
				return NULL;
			return $single ? $values[0] : $values;
		}
		$values = $this->update_cache($user_id, $key, $single);
		if ($single)
			return is_null($values) ? NULL : $values[0];
		return $values;
	}
	/* set(user_id, key, value) - sets all occurances of user_id/key to the
		 given value.  If no such user_id/key exists, a new entry is added.
		 Always true on success. */
	function set($user_id, $key, $value) {
		if (is_null($this->get($user_id, $key, true))) {
			$this->add($user_id, $key, $value, false);
			return true;
		}
		$this->update($user_id, $key, $value);
		return true;
	}
	/* update_cache(user_id, key) - updates the cache and returns an array of
		 current values, or NULL if there are no values */
	function update_cache($user_id, $key) {
		global $wpdb;
		$metalist = $wpdb->get_results("SELECT meta_value FROM $this->table
                                    WHERE user_id='$user_id'
                                    AND meta_key='$key'", ARRAY_N);
		$values = NULL;
		if ($metalist) {
			$values = array();
			foreach ($metalist as $metarow)
				$values[] = $metarow[0];
		}
		$this->cache[$user_id][$key] = $values;
		return $values;
	}
	/* update(user_id, key, value[, prev_value]) - updates all occurances of
		 this user/key with the new value.  If optional fourth param is passed in,
		 then only matches with that value are changed.  Returns true if any
		 items were changed, false otherwise. */

	function update($user_id, $key, $value, $prev_value = NULL) {
		global $wpdb;
		if (is_null($prev_value)) {
			$wpdb->query("UPDATE $this->table SET meta_value='$value'
                    WHERE meta_key='$key' AND user_id='$user_id'");
		} else {
			$wpdb->query("UPDATE $this->table SET meta_value = '$value'
                    WHERE meta_key = '$key' AND user_id = '$user_id'
                    AND meta_value = '$prev_value'");
		}
		if ($wpdb->rows_affected < 1)
			return false;
		$this->update_cache($user_id, $key);
		return true;
	}
	/* get_fieldinfo($field_name) - return information on the field named.
		 Returns an array with the keys:
		   description => a description of the field
		   type => the type of the field: Text, Text Box, Toggle, Option List
		   options => the options
		   default => the default for this field
		   useredit => No or Yes
		 Note that this only returns information for a field which has been
		 defined by the user in the main Usermeta options page.
	  */
	function display_updated($text) {
		echo '<div class="updated"><p>';
		echo $this->T($text, 'usermeta');
		echo '</p></div>';
	}
	function T($text) {
		return __($text, 'usermeta');
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
	function options_usermeta() {
		$this->options_usermeta_checkaction();
		$this->options_usermeta_display();
	}
	function options_usermeta_checkaction() {
		switch ($_REQUEST['action']) {
			case 'ensure_database':
				/* make sure database exists */
				$out = $this->ensure_database();
				foreach ($out as $message) {
					$this->display_updated($message);
				}
				if (count($out) == 0)
					$this->display_updated('Tables up to date, nothing to do.');
				break;
		}
	}
	function options_usermeta_display() {
		?>
		<div class="wrap">
			<h2><?php $this->EHT('Database management') ?></h2>
			<form method="post">
				<input type="hidden" name="action" value="ensure_database" />
				<fieldset class="options">
					<legend><?php $this->EHT('Maintenance') ?></legend>
					<?php if ($this->database_exists() == false) {
						echo '<p><strong>';
						echo $this->H("WARNING: Table $this->table not found.");
						$this->EHT('Please click button below to create '.
							'necessary tables.');
						echo '</strong></p>';
					} else {
						echo '<p>';
						$this->EHT('Usermeta tables are installed.');
						echo '</p>';
					} ?>
					<div class="submit"><input type="submit" value="<?php
						echo $this->H($this->T('Create/Update usermeta tables')).
							' &raquo;' ?>" /></div>
				</fieldset>
			</form>
		</div>
	<?
	}
	function action_admin_menu() {
		add_options_page($this->T('User attributes'),
			$this->T('Usermeta'),
			8, basename(__FILE__), array(&$this, 'options_usermeta'));
	}
}
global $user_level;
load_plugin_textdomain('usermeta');
$usermeta = get_usermeta_object();
add_action('admin_menu', array(&$usermeta, 'action_admin_menu'));
?>