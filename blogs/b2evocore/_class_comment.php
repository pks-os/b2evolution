<?php
/**
 * This file implements comments
 *
 * b2evolution - {@link http://b2evolution.net/}
 *
 * Released under GNU GPL License - http://b2evolution.net/about/license.html
 *
 * @copyright (c)2003-2004 by Francois PLANQUE - {@link http://fplanque.net/}
 *
 * @package b2evocore
 */
require_once dirname(__FILE__).'/_class_dataobject.php';

class Comment extends DataObject
{
	var	$post_ID;
	var	$type;
	var	$status;
	var	$author;
	var	$author_email;
	var	$author_url;
	var	$author_IP;
	var	$date;
	var	$content;
	var	$karma;
	// Extra vars:
	var $post_title;
	var $blog_ID;
	var $blogparams;
	var $blog_name;

	/* 
	 * Comment::Comment(-)
	 *
	 * Constructor
	 */
	function Comment( $db_row = NULL )
	{
		global $tablecomments;
		
		// Call parent constructor:
		parent::DataObject( $tablecomments, 'comment_', 'comment_ID' );
	
		if( $db_row == NULL )
		{
			echo "null comment";
		}
		else
		{
			$this->ID = $db_row['comment_ID'];
			$this->post_ID = $db_row['comment_post_ID'];
			// echo 'post_ID=',$this->post_ID;
			$this->type = $db_row['comment_type'];
			$this->status = $db_row['comment_status'];
			$this->author = $db_row['comment_author'];
			$this->author_email = $db_row['comment_author_email'];
			$this->author_url = $db_row['comment_author_url'];
			$this->author_IP = $db_row['comment_author_IP'];
			$this->date = $db_row['comment_date'];
			$this->content = $db_row['comment_content'];
			$this->karma = $db_row['comment_karma'];
			// Extra vars:
			$this->post_title = $db_row['post_title'];
			$this->blog_ID = $db_row['blog_ID'];
			$this->blogparams = get_blogparams_by_ID($this->blog_ID);
			$this->blog_name = $db_row['blog_name'];
		}
	}	
	
	/* 
	 * Comment::set(-)
	 *
	 * Set param value
	 */
	function set( $parname, $parvalue )
	{
		switch( $parname )
		{
			case 'post_ID':
			case 'karma':
				parent::set_param( $parname, 'int', $parvalue );
			break;
			
			default:
				parent::set_param( $parname, 'string', $parvalue );
		}
	}

	/** 
	 * Get a member param by its name
	 *
	 * {@internal Comment::get(-) }}
	 *
	 * @param mixed Name of parameter
	 * @return mixed Value of parameter
	 */
	function get( $parname )
	{
		switch( $parname )
		{
			case 'post_link':
				// Link to original post:
				return gen_permalink( get_bloginfo( 'blogurl', $this->blogparams ), $this->post_ID, 
															'id', 'single' );
		}
		// Default:		
		return $this->$parname;	
	}

	// TEMP:
	function author_url_link($linktext='', $before='', $after='') 
	{
		$url = trim($this->author_url);
		$url = preg_replace('#&([^amp\;])#is', '&amp;$1', $url);
		$url = (!stristr($url, '://')) ? 'http://'.$url : $url;
		if ((!empty($url)) && ($url != 'http://') && ($url != 'http://url'))
		{
			$display = ($linktext != '') ? $linktext : stripslashes($url);
			echo $before;
			echo '<a href="'.stripslashes($url).'">'.$display.'</a>';
			echo $after;
		}
	}

	// TEMP:
	function text() 
	{
		global $use_textile;
	
		$comment = $this->content;
		$comment = str_replace('<trackback />', '', $comment);
		$comment = str_replace('<pingback />', '', $comment);
	
		if( $use_textile ) $comment = textile( $comment );
	
		$comment = format_to_output( $comment, 'htmlbody' );
		echo $comment;
	}

	function date($d='') 
	{
		if ($d == '') 
			echo mysql2date( locale_datefmt(), $this->date );
		else
			echo mysql2date( $d, $this->date );
	}

	function time($d='') 
	{
		if ($d == '')
			echo mysql2date( locale_timefmt(), $this->date );
		else
			echo mysql2date( $d, $this->date );
	}

}
?>
