<?php
/**
 * @brief		Rules conversions: Core
 * @package		Rules for IPS Social Suite
 * @since		20 Mar 2015
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\rules\extensions\rules\Conversions;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * @brief	Rules conversions extension: Core
 */
class _Core
{

	/**
	 * Global Arguments
	 *
	 * Let rules know about any global arguments that can be used
	 *
	 * @return 	array		Array of global argument definitions
	 */
	public function globalArguments()
	{
		return array
		(
			'site_settings' => array
			(
				'token' => 'site',
				'description' => 'the site settings',
				'argtype' => 'object',
				'class' => '\IPS\Settings',
				'getArg' => function()
				{
					return \IPS\Settings::i();
				},
			),
			'logged_in_member' => array
			(
				'token' => 'user',
				'description' => 'the currently logged in user',
				'argtype' => 'object',
				'class' => '\IPS\Member',
				'getArg' => function()
				{
					return \IPS\Member::loggedIn();
				},
			),
			'current_time' => array
			(
				'token' => 'time',
				'description' => 'the current time',
				'argtype' => 'object',
				'class' => '\IPS\DateTime',
				'getArg' => function()
				{
					return \IPS\DateTime::ts( time() );
				},
			),
			'request_url' => array
			(
				'token' => 'url',
				'description' => 'the request url',
				'argtype' => 'object',
				'class' => '\IPS\Http\Url',
				'getArg' => function()
				{
					return \IPS\Request::i()->url();
				},
			),
		);
	}

	/**
	 * Conversion Map
	 *
	 * Let's rules know how to convert objects into different types of arguments
	 *
	 * @return 	array		Array of conversion definitions
	 */
	public function conversionMap()
	{
		return array
		(
			'\IPS\Member' => array
			(
				'Name' => array
				(
					'token' => 'name',
					'description' => 'User name',
					'argtype' => 'string',
					'converter' => function( $member )
					{
						return $member->name;
					},
				),
				'Member Title' => array
				(
					'token' => 'title',
					'description' => 'User title',
					'argtype' => 'string',
					'converter' => function( $member )
					{
						return $member->member_title;
					},
				),
				'Content Count' => array
				(
					'token' => 'posts',
					'description' => 'Posts count',
					'argtype' => 'int',
					'converter' => function( $member )
					{
						return $member->member_posts;
					},
				),
				'Joined Date' => array
				(
					'argtype' => 'object',
					'class' => '\IPS\DateTime',
					'converter' => function( $member )
					{
						return \IPS\DateTime::ts( $member->joined );
					},
				),
				'Birthday' => array
				(
					'argtype' => 'object',
					'class' => '\IPS\DateTime',
					'nullable' => TRUE,
					'converter' => function( $member )
					{
						return $member->birthday;
					},
				),
				'Age' => array
				(
					'argtype' => 'int',
					'nullable' => TRUE,
					'converter' => function( $member )
					{
						return $member->age();
					},
				),
				'Member ID' => array
				(
					'token' => 'id',
					'description' => 'The member id',
					'argtype' => 'int',
					'converter' => function( $member )
					{
						return $member->member_id;
					},
				),
				'Url' => array
				(
					'argtype' => 'object',
					'class' => '\IPS\Http\Url',
					'converter' => function( $member )
					{
						return $member->url();
					},
				),
				'Followers' => array
				(
					'argtype' => 'array',
					'class' => '\IPS\Member',
					'converter' => function( $member )
					{
						$members = array();
						foreach ( $member->followers( 3, array( 'immediate', 'daily', 'weekly' ), NULL ) as $follower )
						{
							try
							{
								$members[] = \IPS\Member::load( $follower[ 'follow_member_id' ] );
							}
							catch( \OutOfRangeException $e ) {}
						}
						return $members;
					},
				),
				'Last Activity' => array
				(
					'argtype' => 'object',
					'class' => '\IPS\DateTime',
					'converter' => function( $member )
					{
						return \IPS\DateTime::ts( $member->last_activity );
					},
				),
			),
			'\IPS\Content' => array
			(
				'Title' => array
				(
					'token' => 'title',
					'description' => 'The content title',
					'argtype' => 'string',
					'nullable' => TRUE,
					'converter' => function( $content )
					{
						return $content->mapped( 'title' );
					},
				),
				'Content' => array
				(
					'token' => 'content',
					'description' => 'The content body',
					'argtype' => 'string',
					'nullable' => TRUE,
					'converter' => function( $content )
					{
						return $content->mapped( 'content' );
					},
				),
				'Created Date' => array
				(
					'argtype' => 'object',
					'class' => '\IPS\DateTime',
					'converter' => function( $content )
					{
						return \IPS\DateTime::ts( $content->mapped( 'date' ) );
					},
				),
				'Updated Date' => array
				(
					'argtype' => 'object',
					'class' => '\IPS\DateTime',
					'converter' => function( $content )
					{
						return \IPS\DateTime::ts( $content->mapped( 'updated' ) );
					},
				),
				'Tags' => array
				(
					'argtype' => 'array',
					'converter' => function( $content )
					{
						return (array) $content->tags();
					},
				),
				'Content ID' => array
				(
					'token' => 'id',
					'description' => 'The content ID',
					'argtype' => 'int',
					'converter' => function( $content )
					{
						$idField = $content::$databaseColumnId;
						return $content->$idField;
					},
				),
				'Author' => array
				(
					'argtype' => 'object',
					'class' => '\IPS\Member',
					'converter' => function( $content )
					{
						return $content->author();
					},
				),
				'Author Name' => array
				(
					'token' => 'author:name',
					'description' => 'The author name',
					'argtype' => 'string',
					'converter' => function( $content )
					{
						return $content->author()->name;
					},
				),
				'Author ID' => array
				(
					'token' => 'author:id',
					'description' => 'The author ID',
					'argtype' => 'int',
					'converter' => function( $content )
					{
						return $content->author()->member_id;
					},
				),
			),
			'\IPS\Content\Item' => array
			(
				'Container' => array
				(
					'argtype' => 'object',
					'class' => '\IPS\Node\Model',
					'nullable' => TRUE,
					'converter' => function( $item )
					{
						return $item->containerWrapper();
					},
				),
				'Comment Count' => array
				(
					'token' => 'comments',
					'description' => 'Item comment count',
					'argtype' => 'int',
					'converter' => function( $item )
					{
						return (int) $item->mapped( 'num_comments' );
					},
				),
				'Last Post Time' => array
				(
					'argtype' => 'object',
					'class' => '\IPS\DateTime',
					'converter' => function( $item )
					{
						return \IPS\DateTime::ts( max( $item->mapped( 'last_comment' ), $item->mapped( 'last_review' ) ) );
					},
				),
				'Views' => array
				(
					'token' => 'views',
					'description' => 'Item views count',
					'argtype' => 'int',
					'converter' => function( $item )
					{
						return (int) $item->mapped( 'views' );
					},
				),
				'Url' => array
				(
					'argtype' => 'object',
					'class' => '\IPS\Http\Url',
					'converter' => function( $item )
					{
						return $item->url();
					},
				),
				'Followers' => array
				(
					'argtype' => 'array',
					'class' => '\IPS\Member',
					'converter' => function( $item )
					{
						try
						{
							$members = array();
							foreach ( $item->followers( 3, array( 'none', 'immediate', 'daily', 'weekly' ), NULL ) as $follower )
							{
								try
								{
									$members[] = \IPS\Member::load( $follower[ 'follow_member_id' ] );
								}
								catch( \OutOfRangeException $e ) {}
							}
							return $members;
						}
						catch ( \BadMethodCallException $e )
						{
							return array();
						}
					},
				),
				'Author Followers' => array
				(
					'argtype' => 'array',
					'class' => '\IPS\Member',
					'converter' => function( $item )
					{
						$members = array();
						foreach ( $item->author()->followers( 3, array( 'immediate', 'daily', 'weekly' ), NULL ) as $follower )
						{
							try
							{
								$members[] = \IPS\Member::load( $follower[ 'follow_member_id' ] );
							}
							catch( \OutOfRangeException $e ) {}
						}
						return $members;
					},
				),
			),
			'\IPS\Node\Model' => array
			(
				'Parent' => array
				(
					'argtype' => 'object',
					'class' => '\IPS\Node\Model',
					'nullable' => TRUE,
					'converter' => function( $node )
					{
						return $node->parent();
					},
				),
				'Root Parent' => array
				(
					'argtype' => 'object',
					'class' => '\IPS\Node\Model',
					'nullable' => TRUE,
					'converter' => function( $node )
					{
						while( $parent = $node->parent() );
						return $parent;
					},
				),
				'Title' => array
				(
					'token' => 'title',
					'description' => 'The node title',
					'argtype' => 'string',
					'converter' => function( $node )
					{
						return $node->_title;
					},
				),
				'Content Count' => array
				(
					'token' => 'items',
					'description' => 'Total items count',
					'argtype' => 'int',
					'converter' => function( $node )
					{
						return (int) $node->_items;
					},
				),
				'Node ID' => array
				(
					'token' => 'id',
					'description' => 'The node ID',
					'argtype' => 'int',
					'converter' => function( $node )
					{
						return $node->_id;
					},
				),
				'Url' => array
				(
					'argtype' => 'object',
					'class' => '\IPS\Http\Url',
					'converter' => function( $node )
					{
						return $node->url();
					},
				),
			),
			'\IPS\DateTime' => array
			(
				'Date/Time' => array
				(
					'token' => 'datetime',
					'description' => 'The formatted date/time',
					'argtype' => 'string',
					'converter' => function( $date )
					{
						return (string) $date;
					},
				),
				'Timestamp' => array
				(
					'token' => 'timestamp',
					'argtype' => 'int',
					'converter' => function( $date )
					{
						return $date->getTimestamp();
					},
				),
				'Year' => array
				(
					'token' => 'year',
					'argtype' => 'int',
					'converter' => function( $date )
					{
						return $date->format( 'Y' );
					},
				),
				'Month' => array
				(
					'token' => 'month',
					'argtype' => 'int',
					'converter' => function( $date )
					{
						return $date->format( 'n' );
					},
				),
				'Day' => array
				(
					'token' => 'day',
					'argtype' => 'int',
					'converter' => function( $date )
					{
						return $date->format( 'j' );
					},
				),
				'Hour' => array
				(
					'token' => 'hour',
					'argtype' => 'int',
					'converter' => function( $date )
					{
						return $date->format( 'G' );
					},
				),
				'Minute' => array
				(
					'token' => 'minute',
					'argtype' => 'int',
					'converter' => function( $date )
					{
						return $date->format( 'i' );
					},
				),
			),
			'\IPS\Http\Url' => array
			(
				'Url' => array
				(
					'token' => 'url',
					'argtype' => 'string',
					'converter' => function( $url )
					{
						return (string) $url;
					},
				),
				'Params' => array
				(
					'argtype' => 'array',
					'converter' => function( $url )
					{
						return $url->queryString;
					},
				),
			),
			'\IPS\Settings' => array
			(
				'Site Name' => array
				(
					'token' => 'name',
					'argtype' => 'string',
					'converter' => function( $settings )
					{
						return $settings->board_name;
					},
				),
				'Site Url' => array
				(
					'token' => 'url',
					'argtype' => 'string',
					'converter' => function( $settings )
					{
						return $settings->base_url;
					},
				),
			),
		);
	}
	
}