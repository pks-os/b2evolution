<?php
/**
 * This file implements the UI controller for settings management.
 *
 * This file is part of the evoCore framework - {@link http://evocore.net/}
 * See also {@link http://sourceforge.net/projects/evocms/}.
 *
 * @copyright (c)2003-2009 by Francois PLANQUE - {@link http://fplanque.net/}
 * Parts of this file are copyright (c)2004-2006 by Daniel HAHLER - {@link http://thequod.de/contact}.
 *
 * {@internal License choice
 * - If you have received this file as part of a package, please find the license.txt file in
 *   the same folder or the closest folder above for complete license terms.
 * - If you have received this file individually (e-g: from http://evocms.cvs.sourceforge.net/)
 *   then you must choose one of the following licenses before using the file:
 *   - GNU General Public License 2 (GPL) - http://www.opensource.org/licenses/gpl-license.php
 *   - Mozilla Public License 1.1 (MPL) - http://www.opensource.org/licenses/mozilla1.1.php
 * }}
 *
 * {@internal Open Source relicensing agreement:
 * Daniel HAHLER grants Francois PLANQUE the right to license
 * Daniel HAHLER's contributions to this file and the b2evolution project
 * under any OSI approved OSS license (http://www.opensource.org/licenses/).
 * }}
 *
 * @package admin
 *
 * {@internal Below is a list of authors who have contributed to design/coding of this file: }}
 * @author blueyed: Daniel HAHLER
 * @author fplanque: Francois PLANQUE
 *
 * @version $Id$
 */
if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );


// Check minimum permission:
$current_User->check_perm( 'options', 'view', true );


$AdminUI->set_path( 'options', 'general' );

param( 'action', 'string' );
param( 'edit_locale', 'string' );
param( 'loc_transinfo', 'integer', 0 );

if( in_array( $action, array( 'update', 'reset', 'updatelocale', 'createlocale', 'deletelocale', 'extract', 'prioup', 'priodown' )) )
{ // We have an action to do..
	// Check permission:
	$current_User->check_perm( 'options', 'edit', true );

	// clear settings cache
	$cache_settings = '';

	// UPDATE general settings:

	if( param( 'default_blog_ID', 'integer', NULL ) !== NULL )
	{
		$Settings->set( 'default_blog_ID', $default_blog_ID );
	}

	// Session timeout
	$timeout_sessions = param_duration( 'timeout_sessions' );

	if( $timeout_sessions < 300 )
	{ // lower than 5 minutes: not allowed
		param_error( 'timeout_sessions', sprintf( T_( 'You cannot set a session timeout below %d seconds.' ), 300 ) );
	}
	elseif( $timeout_sessions < 86400 )
	{ // lower than 1 day: notice/warning
		$Messages->add( sprintf( T_( 'Warning: your session timeout is just %d seconds. Your users may have to re-login often!' ), $timeout_sessions ), 'note' );
	}
	$Settings->set( 'timeout_sessions', $timeout_sessions );

	// Reload page timeout
	$reloadpage_timeout = param_duration( 'reloadpage_timeout' );

	if( $reloadpage_timeout > 99999 )
	{
		param_error( 'reloadpage_timeout', sprintf( T_( 'Reload-page timeout must be between %d and %d seconds.' ), 0, 99999 ) );
	}
	$Settings->set( 'reloadpage_timeout', $reloadpage_timeout );

	$new_cache_status = param( 'general_cache_enabled', 'integer', 0 );
	$old_cache_status = $Settings->get('general_cache_enabled');

	load_class( '_core/model/_pagecache.class.php', 'PageCache' );
	$PageCache = & new PageCache();

	if( $old_cache_status == false && $new_cache_status == true )
	{ // Caching has been turned ON:
		if( $PageCache->cache_create() )
		{
			$Messages->add( T_('General caching has been enabled.'), 'success' );
		}
		else
		{
			$Messages->add( T_('General caching could not be enabled. Check /cache/ folder file permissions.'), 'error' );
			$new_cache_status = 0;
		}
	}
	elseif( $old_cache_status == true && $new_cache_status == false )
	{ // Caching has been turned OFF:
		$PageCache->cache_delete();
		$Messages->add( T_('General caching has been disabled. All general cache contents have been purged.'), 'note' );
	}

	$Settings->set( 'general_cache_enabled', $new_cache_status );

	if( ! $Messages->count('error') )
	{
		if( $Settings->dbupdate() )
		{
			$Messages->add( T_('General settings updated.'), 'success' );
		}
	}

}


$AdminUI->breadcrumbpath_init();
$AdminUI->breadcrumbpath_add( T_('Global settings'), '?ctrl=settings',
		T_('Global settings are shared between all blogs; see Blog settings for more granular settings.') );
$AdminUI->breadcrumbpath_add( T_('General'), '?ctrl=settings' );


// Display <html><head>...</head> section! (Note: should be done early if actions do not redirect)
$AdminUI->disp_html_head();

// Display title, menu, messages, etc. (Note: messages MUST be displayed AFTER the actions)
$AdminUI->disp_body_top();

// Begin payload block:
$AdminUI->disp_payload_begin();

// Display VIEW:
$AdminUI->disp_view( 'settings/views/_general.form.php' );

// End payload block:
$AdminUI->disp_payload_end();

// Display body bottom, debug info and close </html>:
$AdminUI->disp_global_footer();

/*
 * $Log$
 * Revision 1.18  2009/12/06 22:55:21  fplanque
 * Started breadcrumbs feature in admin.
 * Work in progress. Help welcome ;)
 * Also move file settings to Files tab and made FM always enabled
 *
 * Revision 1.17  2009/10/28 10:56:34  efy-maxim
 * param_duration
 *
 * Revision 1.16  2009/10/27 23:06:46  fplanque
 * doc
 *
 * Revision 1.15  2009/10/27 13:27:49  efy-maxim
 * 1. months and seconds fields in duration field
 * 2. duration fields instead simple text fields
 *
 * Revision 1.14  2009/09/26 12:00:43  tblue246
 * Minor/coding style
 *
 * Revision 1.13  2009/09/16 05:35:47  efy-bogdan
 * Require country checkbox added
 *
 * Revision 1.12  2009/09/15 22:33:20  efy-bogdan
 * Require country checkbox added
 *
 * Revision 1.11  2009/09/15 09:20:47  efy-bogdan
 * Moved the "email validation" and the "security options" blocks to the Users -> Registration tab
 *
 * Revision 1.10  2009/09/14 13:41:44  efy-arrin
 * Included the ClassName in load_class() call with proper UpperCase
 *
 * Revision 1.9  2009/09/14 11:54:21  efy-bogdan
 * Moved Default user permissions under a new tab
 *
 * Revision 1.8  2009/09/03 15:51:52  tblue246
 * Doc, "refix", use "0" instead of an empty string for the "No blog" option.
 *
 * Revision 1.7  2009/09/02 23:27:20  fplanque
 * != works, doesn't it?
 * Tblue> No, it does _not_. If you select "No blog", the setting does
 * not get set to 0 because comparing 0 and NULL using the != operator
 * gives false.
 *
 * Revision 1.6  2009/09/02 18:01:51  tblue246
 * minor
 *
 * Revision 1.5  2009/09/02 17:47:25  fplanque
 * doc/minor
 *
 * Revision 1.4  2009/03/08 23:57:45  fplanque
 * 2009
 *
 * Revision 1.3  2008/09/28 08:06:07  fplanque
 * Refactoring / extended page level caching
 *
 * Revision 1.2  2008/01/21 09:35:34  fplanque
 * (c) 2008
 *
 * Revision 1.1  2007/06/25 11:01:18  fplanque
 * MODULES (refactored MVC)
 *
 * Revision 1.16  2007/04/26 00:11:14  fplanque
 * (c) 2007
 *
 * Revision 1.15  2007/03/25 13:20:52  fplanque
 * cleaned up blog base urls
 * needs extensive testing...
 *
 * Revision 1.14  2007/03/24 20:41:16  fplanque
 * Refactored a lot of the link junk.
 * Made options blog specific.
 * Some junk still needs to be cleaned out. Will do asap.
 *
 * Revision 1.13  2006/12/15 22:54:14  fplanque
 * allow disabling of password hashing
 *
 * Revision 1.12  2006/12/07 00:55:52  fplanque
 * reorganized some settings
 *
 * Revision 1.11  2006/12/04 19:41:11  fplanque
 * Each blog can now have its own "archive mode" settings
 *
 * Revision 1.10  2006/12/04 18:16:50  fplanque
 * Each blog can now have its own "number of page/days to display" settings
 *
 * Revision 1.9  2006/11/24 18:27:23  blueyed
 * Fixed link to b2evo CVS browsing interface in file docblocks
 */
?>