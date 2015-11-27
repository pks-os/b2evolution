<?php
/**
 * This file implements the UI view for Emails > Campaigns > Edit > Plain Text
 *
 * This file is part of the evoCore framework - {@link http://evocore.net/}
 * See also {@link https://github.com/b2evolution/b2evolution}.
 *
 * @license GNU GPL v2 - {@link http://b2evolution.net/about/gnu-gpl-license}
 *
 * @copyright (c)2009-2015 by Francois Planque - {@link http://fplanque.com/}
 * Parts of this file are copyright (c)2009 by The Evo Factory - {@link http://www.evofactory.com/}.
 *
 * @package evocore
 */
if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );

global $admin_url, $tab;
global $edited_EmailCampaign;

$Form = new Form( NULL, 'campaign_form' );
$Form->begin_form( 'fform' );

$Form->add_crumb( 'campaign' );
$Form->hidden( 'ctrl', 'campaigns' );
$Form->hidden( 'current_tab', $tab );
$Form->hidden( 'ecmp_ID', $edited_EmailCampaign->ID );

$Form->begin_fieldset( T_('Plain-text message').get_manual_link( 'creating-an-email-campaign' ) );
	$Form->info( T_('Name'), $edited_EmailCampaign->get( 'name' ) );
	$Form->info( T_('Email title'), $edited_EmailCampaign->get( 'email_title' ) );
	$Form->info( T_('Campaign created'), mysql2localedatetime_spans( $edited_EmailCampaign->get( 'date_ts' ), 'M-d' ) );
	$Form->info( T_('Last sent'), $edited_EmailCampaign->get( 'sent_ts' ) ? mysql2localedatetime_spans( $edited_EmailCampaign->get( 'sent_ts' ), 'M-d' ) : T_('Not sent yet') );

	// Plain Text Message with button to extract text from html content
	if( $current_User->check_perm( 'emails', 'edit' ) )
	{ // User must has a permission to edit emails in order to extract text from html
		$Form->output = false;
		$button_to_extract = $Form->button( array( 'submit', 'actionArray[extract_html]', T_('Extract from HTML'), 'SmallButton' ) );
		$Form->output = true;
	}
	else
	{ // No permission
		$button_to_extract = '';
	}
	$Form->textarea_input( 'ecmp_email_text', $edited_EmailCampaign->get( 'email_text' ), 20, T_('Plain-text Message'), array( 'required' => true, 'input_prefix' => $button_to_extract ) );
$Form->end_fieldset();

$buttons = array();
if( $current_User->check_perm( 'emails', 'edit' ) )
{ // User must has a permission to edit emails
	$buttons[] = array( 'submit', 'actionArray[save]', T_('Save & continue').' >>', 'SaveButton' );
	$buttons[] = array( 'submit', 'actionArray[save_edit]', T_('Save & edit'), 'SaveButton' );
	$buttons[] = array( 'submit', 'actionArray[save_preview]', T_('Save & preview'), 'PreviewButton' );
}
$Form->end_form( $buttons );

?>