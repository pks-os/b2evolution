<?php
/**
 * This file implements the linkblog_Widget class.
 *
 * This file is part of the evoCore framework - {@link http://evocore.net/}
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2003-2016 by Francois Planque - {@link http://fplanque.com/}
 *
 * @package evocore
 */
if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );

load_class( 'widgets/widgets/_coll_item_list.widget.php', 'coll_item_list_Widget' );

/**
 * linkblog_widget class
 *
 * This widget displays another blog as a linkblog.
 *
 * @package evocore
 */
class linkblog_Widget extends coll_item_list_Widget
{
	/**
	 * Constructor
	 */
	function __construct( $db_row = NULL )
	{
		// Call parent constructor:
		ComponentWidget::__construct( $db_row, 'core', 'linkblog' );
	}


	/**
	 * Get definitions for editable params
	 *
	 * @see Plugin::GetDefaultSettings()
	 * @param local params like 'for_editing' => true
	 */
	function get_param_definitions( $params )
	{
		$ItemTypeCache = & get_ItemTypeCache();
		$ItemTypeCache->clear();
		$ItemTypeCache->load_where( 'ityp_usage = "special"' ); // Load only special item types
		$item_type_cache_load_all = $ItemTypeCache->load_all; // Save original value
		$ItemTypeCache->load_all = false; // Force to don't load all item types in get_option_array() below
		$special_item_type_options =
			array(
				''  => T_('All'),
			) + $ItemTypeCache->get_option_array();
		// Revert back to original value:
		$ItemTypeCache->load_all = $item_type_cache_load_all;

		// This is derived from coll_post_list_Widget, so we DO NOT ADD ANY param here!
		$r = parent::get_param_definitions( $params );
		// We only change the defaults and hide some params.
		$r['title']['defaultvalue'] = T_('Linkblog');
		$r['title_link']['no_edit'] = true;
		$r['item_type_usage']['no_edit'] = true;
		$r['follow_mainlist']['no_edit'] = true;
		$r['cat_IDs']['no_edit'] = true;
		$r['item_group_by']['defaultvalue'] = 'chapter';
		$r['item_title_link_type']['no_edit'] = true;
		$r['item_title_link_type']['defaultvalue'] = 'auto';
		$r['disp_excerpt']['no_edit'] = true;
		$r['disp_teaser']['no_edit'] = true;
		$r['disp_teaser_maxwords']['no_edit'] = true;
		$r['widget_css_class']['no_edit'] = true;
		$r['widget_ID']['no_edit'] = true;

		// Allow to select what special item type to display:
		$r['item_type'] = array(
				'label' => T_('Post type'),
				'note' => T_('What kind of items do you want to list?'),
				'type' => 'select',
				'options' => $special_item_type_options,
				'defaultvalue' => '',
			);

		return $r;
	}


	/**
	 * Get help URL
	 *
	 * @return string URL
	 */
	function get_help_url()
	{
		return get_manual_url( 'simple-linkblog-links-list-widget' );
	}


	/**
	 * Get name of widget
	 */
	function get_name()
	{
		return T_('Simple Linkblog Links list');
	}


	/**
	 * Get a very short desc. Used in the widget list.
	 */
	function get_short_desc()
	{
		return format_to_output($this->disp_params['title']);
	}


	/**
	 * Get short description
	 */
	function get_desc()
	{
		return T_('Simplified Item list for listing links from another blog.');
	}


	/**
	 * Prepare display params
	 *
	 * @param array MUST contain at least the basic display params
	 */
	function init_display( $params )
	{
		global $Blog;

		// Force some params (because this is a simplified widget):
		if( $this->disp_params['blog_ID'] == 0 )
		{	// We want to try and use previous defaults:
			if( !empty( $this->disp_params['linkblog_ID'] ) )
			{
				$params['blog_ID'] = $this->disp_params['linkblog_ID'];
			}
			else
			{ // Recycle the previous value from deprecated links_blog_ID param. We will eventually drop that field from the database.
				$params['blog_ID'] = $Blog->get('links_blog_ID');
			}
		}

		// Force some params (because this is a simplified widget):
		$params['item_type_usage'] = 'special';	// Use post types usage "special" only

		parent::init_display( $params );
	}

}

?>