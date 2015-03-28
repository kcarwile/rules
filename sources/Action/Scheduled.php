<?php
/**
 * @brief		IPS4 Rules
 * @author		Kevin Carwile (http://www.linkedin.com/in/kevincarwile)
 * @copyright		(c) 2014 - Kevin Carwile
 * @package		Rules
 * @since		6 Feb 2015
 */


namespace IPS\rules\Action;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Scheduled Action
 */
class _Scheduled extends \IPS\Patterns\ActiveRecord
{
	/**
	 * @brief	[ActiveRecord] Database Prefix
	 */
	public static $databasePrefix = 'schedule_';
	
	/**
	 * @brief	[ActiveRecord] ID Database Column
	 */
	public static $databaseColumnId = 'id';

	/**
	 * @brief	[ActiveRecord] Database table
	 * @note	This MUST be over-ridden
	 */
	public static $databaseTable	= 'rules_scheduled_actions';
		
	/**
	 * @brief	[ActiveRecord] Database ID Fields
	 */
	protected static $databaseIdFields = array();
	
	/**
	 * @brief	Bitwise keys
	 */
	protected static $bitOptions = array();

	/**
	 * @brief	[ActiveRecord] Multiton Store
	 * @note	This needs to be declared in any child classes as well, only declaring here for editor code-complete/error-check functionality
	 */
	protected static $multitons = array();
	
	/**
	 * Execute the Scheduled Action
	 *
	 * @param	bool	$deleteWhenDone		Delete the scheduled action when complete
	 */
	public function execute( $deleteWhenDone=TRUE )
	{
		$action_data = json_decode( $this->data, TRUE );

		$args = array();
		$event_args = array();

		foreach ( (array) $action_data[ 'args' ] as $arg )
		{
			$args[] = \IPS\rules\Application::restoreArg( $arg );
		}

		foreach ( (array) $action_data[ 'event_args' ] as $key => $arg )
		{
			$event_args[ $key ] = \IPS\rules\Application::restoreArg( $arg );
		}

		try
		{
			$action = \IPS\rules\Action::load( $this->action_id );
			$action->event()->thread = $this->thread;
			$action->event()->parentThread = $this->parent_thread;

			if ( isset( $action->definition[ 'callback' ] ) and is_callable( $action->definition[ 'callback' ] ) )
			{
				try
				{
					$result = call_user_func_array( $action->definition[ 'callback' ], array_merge( $args, array( $action->data[ 'configuration' ][ 'data' ], $event_args, $action ) ) );

					if ( $rule = $action->rule() and $rule->debug )
					{
						\IPS\rules\Application::rulesLog( $rule->event(), $rule, $action, $result, 'Evaluated'  );
					}
				}
				catch ( \Exception $e )
				{
					$event = $action->rule() ? $action->rule()->event() : NULL;
					\IPS\rules\Application::rulesLog( $event, $action->rule(), $action, $e->getMessage(), 'Error Exception', 1 );
				}
			}
			else
			{
				if ( $rule = $action->rule() )
				{
					\IPS\rules\Application::rulesLog( $rule->event(), $rule, $action, FALSE, 'Missing Callback', 1  );
				}
			}
		}
		catch ( \OutOfRangeException $e ) {


		}

		if ( $deleteWhenDone )
		{
			$this->delete();
		}
	}
	
}