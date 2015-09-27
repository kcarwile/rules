<?php
/**
 * @brief		IPS4 Rules
 * @author		Kevin Carwile (http://www.linkedin.com/in/kevincarwile)
 * @copyright		(c) 2014 - Kevin Carwile
 * @package		Rules
 * @since		6 Feb 2015
 */


namespace IPS\rules\Log;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Node
 */
class _Custom extends \IPS\Node\Model implements \IPS\Node\Permissions
{
	/**
	 * @brief	[ActiveRecord] Multiton Store
	 */
	protected static $multitons;
	
	/**
	 * @brief	[ActiveRecord] Database Table
	 */
	public static $databaseTable = 'rules_custom_logs';
	
	/**
	 * @brief	[ActiveRecord] Database Prefix
	 */
	public static $databasePrefix = 'custom_log_';
		
	/**
	 * @brief	[Node] Order Database Column
	 */
	public static $databaseColumnOrder = 'weight';
	
	/**
	 * @brief	[ActiveRecord] Database ID Fields
	 */
	protected static $databaseIdFields = array( 'custom_log_key' );
	
	/**
	 * @brief	[Node] Parent ID Database Column
	 */
	public static $databaseColumnParent = NULL;
	
	/**
	 * @brief	Sub Node Class
	 */
	public static $subnodeClass = '\IPS\rules\Log\Argument';
	
	/**
	 * @brief	[Node] Node Title
	 */
	public static $nodeTitle = 'custom_logs';
	
	/**
	 * @brief	[Node] App for permission index
	 */
	public static $permApp = 'rules';
	
	/**
	 * @brief	[Node] Type for permission index
	 */
	public static $permType = 'custom_log';
	
	/**
	 * @brief	The map of permission columns
	 */
	public static $permissionMap = array
	(
		'view'			=> 'view',
		'delete'		=> 2,
	);
	
	/**
	 * @brief	[Node] Prefix string that is automatically prepended to permission matrix language strings
	 */
	public static $permissionLangPrefix = 'rules_log_';

	/**
	 * @brief	Use Modal Forms?
	 */
	public static $modalForms = FALSE;
	
	/**
	 *  Disable Copy Button
	 */	
	public $noCopyButton = TRUE;
	
	/**
	 *  Get Title
	 */
	public function get__title()
	{
		return $this->title;
	}
	
	/**
	 * Set Title
	 */
	public function set__title( $val )
	{
		$this->title = $val;
	}
	
	/**
	 * Get Node Description
	 */
	public function get__description()
	{
		return $this->description;
	}
	
	/**
	 * Get Description
	 */
	public function get_description()
	{
		return $this->_data[ 'description' ];
	}
	
	/**
	 * Set Description
	 */
	public function set_description( $val )
	{
		$this->_data[ 'description' ] = $val;
	}
	
	/**
	 * Get Data
	 */ 
	public $data = array();
	
	/**
	 * [Node] Get whether or not this node is enabled
	 *
	 * @note	Return value NULL indicates the node cannot be enabled/disabled
	 * @return	bool|null
	 */
	protected function get__enabled()
	{
		return $this->enabled;
	}

	/**
	 * [Node] Set whether or not this node is enabled
	 *
	 * @param	bool|int	$enabled	Whether to set it enabled or disabled
	 * @return	void
	 */
	protected function set__enabled( $enabled )
	{
		$this->enabled = $enabled;
	}
	
	/**
	 * @brief	Action Definition
	 */
	public $definition = NULL;
		
	/**
	 * Init
	 *
	 * @return	void
	 */
	public function init()
	{

	}
	
	/**
	 * [Node] Get buttons to display in tree
	 * Example code explains return value
	 *
	 * @param	string	$url		Base URL
	 * @param	bool	$subnode	Is this a subnode?
	 * @return	array
	 */
	public function getButtons( $url, $subnode=FALSE )
	{
		$buttons = parent::getButtons( $url, $subnode );
		
		return $buttons;
	}
	
	/**
	 * [Node] Add/Edit Form
	 *
	 * @param	\IPS\Helpers\Form	$form	The form
	 * @return	void
	 */
	public function form( &$form )
	{
		$lang = \IPS\Member::loggedIn()->language();
		$wrap_chosen_prefix	= "<div data-controller='rules.admin.ui.chosen'>";
		$wrap_chosen_suffix	= "</div>";

		/**
		 * Basic Object Classes
		 */
		$object_classes = array
		(
			// None
		);
		$object_classes_toggles = array();
		$object_classes_containers = array();
		
		$core_key = $lang->get( '__app_core' );
		
		/**
		 * Add additional content types
		 */
		foreach ( \IPS\Application::allExtensions( 'core', 'ContentRouter' ) as $router )
		{
			$appname = '';
			$_object_classes = array();
			foreach ( $router->classes as $contentItemClass )
			{
				if ( is_subclass_of( $contentItemClass, '\IPS\Content\Item' ) )
				{
					/* Set Appname */
					$appname = $appname ?: $lang->addToStack( '__app_' . $contentItemClass::$application );
					if ( $contentItemClass::$application == 'core' )
					{
						$core_key = $appname;
					}
					
					/* Add the content class */
					$_object_classes[ '-' . str_replace( '\\', '-', $contentItemClass ) ] =  ucwords( $lang->checkKeyExists( $contentItemClass::$title ) ? $lang->get( $contentItemClass::$title ) : '' ) . ' ( ' . $contentItemClass . ' )';
					
					/* Add node class */
					if ( isset( $contentItemClass::$containerNodeClass ) and $nodeClass = $contentItemClass::$containerNodeClass )
					{
						$_object_classes[ '-' . str_replace( '\\', '-', $nodeClass ) ] = $lang->addToStack( $nodeClass::$nodeTitle ) . ' ( ' . $nodeClass . ' )';
						
						$lang->words[ 'containers-' . str_replace( '\\', '-', $nodeClass ) ] = $lang->get( $nodeClass::$nodeTitle );
						$object_classes_containers[] = new \IPS\Helpers\Form\Node( 'containers-' . str_replace( '\\', '-', $nodeClass ), isset( $configuration[ 'containers-' . str_replace( '\\', '-', $nodeClass ) ] ) ? $configuration[ 'containers-' . str_replace( '\\', '-', $nodeClass ) ] : 0, FALSE, array( 'class' => $nodeClass, 'multiple' => TRUE, 'subnodes' => FALSE, 'zeroVal' => 'All' ), NULL, NULL, NULL, 'containers-' . str_replace( '\\', '-', $nodeClass ) );
						$object_classes_toggles[ '-' . str_replace( '\\', '-', $contentItemClass ) ] = array( 'containers-' . str_replace( '\\', '-', $nodeClass ) );
						$object_classes_toggles[ '-' . str_replace( '\\', '-', $nodeClass ) ] = array( 'containers-' . str_replace( '\\', '-', $nodeClass ) );
					}
				}
			}
			
			$object_classes[ $appname ] = $_object_classes;
		}
		
		$data_classes = array
		(
			$core_key => array
			(
				'-IPS-Member'			=> 'Member ( IPS\Member )',
			),
		);				
		
		$data_classes = array_replace_recursive( $data_classes, $object_classes );

		$form->add( new \IPS\Helpers\Form\Text( 'custom_log_title', $this->title, TRUE ) );
		$form->add( new \IPS\Helpers\Form\Select( 'custom_log_class', $this->class ?: '-IPS-Member', FALSE, array( 'options' => $data_classes, 'disabled' => $this->class !== NULL, 'toggles' => $object_classes_toggles ), NULL, $wrap_chosen_prefix, $wrap_chosen_suffix, 'data_class' ) );
		$form->add( new \IPS\Helpers\Form\TextArea( 'custom_log_description', $this->description, FALSE ) );
		
		$form->addHeader( 'custom_log_options' );
		
		$form->add( new \IPS\Helpers\Form\Number( 'custom_log_max_logs', $this->max_logs ?: 0, TRUE, array( 'unlimited' => 0 ) ) );
		$form->add( new \IPS\Helpers\Form\Number( 'custom_log_entity_max', $this->entity_max ?: 0, TRUE, array( 'unlimited' => 0 ) ) );
		$form->add( new \IPS\Helpers\Form\Number( 'custom_log_max_age', $this->max_age ?: 0, TRUE, array( 'unlimited' => 0 ) ) );

		parent::form( $form );
	}
	
	/**
	 * Form to delete or move content
	 *
	 * @param	bool	$showMoveToChildren	If TRUE, will show "move to children" even if there are no children
	 * @return	\IPS\Helpers\Form
	 */
	public function deleteOrMoveForm( $showMoveToChildren=FALSE )
	{
		$form = new \IPS\Helpers\Form( 'delete_custom_action', 'rules_confirm_delete' );
		$form->hiddenValues[ 'node_move_children' ] = 0;
		return $form;
	}

	/**
	 * [Node] Save Add/Edit Form
	 *
	 * @param	array	$values	Values from the form
	 * @return	void
	 */
	public function saveForm( $values )
	{
		/**
		 * Prevent changing of the log association.
		 */
		if ( $this->id )
		{
			unset( $values[ 'custom_log_class' ] );
		}
		
		parent::saveForm( $values );
	}
	
	/**
	 * [ActiveRecord] Save 
	 */
	public function save()
	{
		/**
		 * To link custom actions with rules that use them
		 * after export/import, we need to use a static
		 * sync key since ID's will change from system to
		 * system.
		 */
		if ( ! $this->key )
		{
			$this->key = md5( uniqid() . mt_rand() );
		}
		
		/**
		 * Create a data table if we don't already have one
		 */
		if ( ! \IPS\Db::i()->checkForTable( $this::getTableName( $this->class ) ) )
		{
			\IPS\Db::i()->createTable( $this::tableDefinition( $this->class ) );
		}
		
		parent::save();
	}
		
	/**
	 * Get Table Name
	 */
	public static function getTableName( $class )
	{
		$class = str_replace( '\\', '-', $class );
		$class = trim( $class, '-' );
		$table_suffix = mb_strtolower( $class );
		$table_suffix = str_replace( 'ips-', '', $table_suffix );
		$table_suffix = str_replace( '-', '_', $table_suffix );
		
		return 'rules_logs_' . $table_suffix;
	}

	/**
	 * Get Table Definition
	 */
	public static function tableDefinition( $class )
	{
		$table_name = static::getTableName( $class );
		
		return array
		(
			'name' 		=> $table_name,
			'columns' 	=> array
			(
				'id' => array
				(
					'name' => 'id',
					'type' => 'int',
					'allow_null' => FALSE,
					'auto_increment' => TRUE,
					'binary' => FALSE,
					'comment' => '',
					'decimals' => NULL,
					'default' => NULL,
					'length' => 20,
					'unsigned' => FALSE,
					'values' => array(),
					'zerofill' => FALSE,
				),
				'log_id' => array
				(
					'name' => 'log_id',
					'type' => 'int',
					'allow_null' => FALSE,
					'auto_increment' => FALSE,
					'binary' => FALSE,
					'comment' => '',
					'decimals' => NULL,
					'default' => NULL,
					'length' => 20,
					'unsigned' => FALSE,
					'values' => array(),
					'zerofill' => FALSE,
				),
				'entity_id' => array
				(
					'name' => 'entity_id',
					'type' => 'int',
					'allow_null' => FALSE,
					'auto_increment' => FALSE,
					'binary' => FALSE,
					'comment' => '',
					'decimals' => NULL,
					'default' => 0,
					'length' => 20,
					'unsigned' => FALSE,
					'values' => array(),
					'zerofill' => FALSE,
				),
				'logtime' => array
				(
					'name' => 'logtime',
					'type' => 'int',
					'allow_null' => FALSE,
					'auto_increment' => FALSE,
					'binary' => FALSE,
					'comment' => '',
					'decimals' => NULL,
					'default' => NULL,
					'length' => 11,
					'unsigned' => FALSE,
					'values' => array(),
					'zerofill' => FALSE,
				),
				'message' => array
				(
					'name' => 'message',
					'type' => 'varchar',
					'allow_null' => FALSE,
					'auto_increment' => FALSE,
					'binary' => FALSE,
					'comment' => '',
					'decimals' => NULL,
					'default' => NULL,
					'length' => 2048,
					'unsigned' => FALSE,
					'values' => array(),
					'zerofill' => FALSE,
				),
			),
			'indexes' 	=> array
			(
				'PRIMARY' => array
				(
					'type' => 'primary',
					'name' => 'PRIMARY',
					'length' => array( NULL ),
					'columns' => array( 'id' ),
				),
				'LOG' => array
				(
					'type' => 'key',
					'name' => 'LOG',
					'length' => array( NULL ),
					'columns' => array( 'log_id' ),
				),
				'ENTITY' => array
				(
					'type' => 'key',
					'name' => 'ENTITY',
					'length' => array( NULL ),
					'columns' => array( 'log_id', 'entity_id' ),
				),
			),
		);	
	}

	/**
	 * Clone
	 */
	public function __clone()
	{
		$this->key = md5( uniqid() . mt_rand() );
		parent::__clone();
	}
	
	/**
	 * [ActiveRecord] Delete Record
	 *
	 * @return	void
	 */
	public function delete()
	{		
		foreach ( $this->children() as $argument )
		{
			$argument->delete();
		}
		
		/* Delete existing logs */
		\IPS\Db::i()->delete( $this::getTableName( $this->class ), array( 'log_id=?', $this->_id ) );
		
		$result = parent::delete();
		
		/* If there are no custom logs left, drop the table too */
		if ( ! \IPS\Db::i()->select( 'COUNT(*)', 'rules_custom_logs', array( 'custom_log_class=?', $this->class ) )->first() )
		{
			try
			{
				\IPS\Db::i()->dropTable( $this::getTableName( $this->class ) );
			}
			catch ( \IPS\Db\Exception $e ) { }
		}

		return $result;
	}
	
	/** 
	 * Add Log Entry
	 *
	 * @param	object		$entity		The entity the log is associated with
	 * @param	string		$message	The message to log
	 * @param	array		$data		Additional data to log
	 * @return 	bool				Returns TRUE if log is created
	 * @throws	\InvalidArgumentException
	 */
	public function createEntry( $entity, $message, $data=array() )
	{
		
		if ( ! is_object( $entity ) or get_class( $entity ) !== ltrim( str_replace( '-', '\\', $this->class ), '\\' ) )
		{
			throw new \InvalidArgumentException( 'Invalid log association. Type: ' . ( is_object( $entity ) ? get_class( $entity ) : gettype( $entity ) ) );
		}
		
		/* Create basic log entry */
		$logEntry = array
		(
			'log_id' => $this->_id, 
			'logtime' => time(), 
			'entity_id' => $entity->activeid, 
			'message' => $message,
		);
		
		/* Attach the custom log data */
		foreach( $data as $key => $value )
		{
			$logValue = $value;
			
			if ( is_object( $value ) )
			{
				if ( \IPS\rules\Data::isConcreteRecord( get_class( $value ) ) )
				{
					$logValue = $value->activeid;
				}
				else
				{
					$logValue = json_encode( \IPS\rules\Application::storeArg( $value ) );
				}
			}
			
			if ( is_array( $value ) )
			{
				$logValue = json_encode( \IPS\rules\Application::storeArg( $value ) );
			}
			
			$logEntry[ 'data_' . $key ] = $logValue;
		}
		
		\IPS\Db::i()->insert( static::getTableName( $this->class ), $logEntry );
		
		/* Prune old logs */
		$this->prune( $entity );
		
		return TRUE;
	}
	
	/**
	 * Prune Logs
	 *
	 * @param 	object		$entity		The entity to prune logs for
	 * @return 	void
	 */
	public function prune( $entity=NULL )
	{
		/* Prune overflow */
		if ( $this->max_logs )
		{
			try
			{
				$cutoff = \IPS\Db::i()->select( 'id', static::getTableName( $this->class ), array( 'log_id=?', $this->id ), 'id DESC', array( $this->max_logs, 1 ) )->first();
				\IPS\Db::i()->delete( static::getTableName( $this->class ), array( 'id<=? AND log_id=?', $cutoff, $this->id ) );
			}
			catch( \UnderflowException $e ) { }
		}
		
		/* Prune old logs */
		if ( $this->max_age )
		{
			$oldTimer = $this->max_age * ( 60 * 60 * 24 );
			\IPS\Db::i()->delete( static::getTableName( $this->class ), array( 'log_id=? AND logtime<?', $this->id, $oldTimer ) );
		}
		
		if ( $entity )
		{
			/* Prune entity logs */
			if ( $this->entity_max )
			{
				try
				{
					$cutoff = \IPS\Db::i()->select( 'id', static::getTableName( $this->class ), array( 'log_id=? AND entity_id=?', $this->id, $entity->activeid ), 'id DESC', array( $this->entity_max, 1 ) )->first();
					\IPS\Db::i()->delete( static::getTableName( $this->class ), array( 'id<=? AND log_id=? AND entity_id=?', $cutoff, $this->id, $entity->activeid ) );
				}
				catch( \UnderflowException $e ) { }
			}
		}
	}
	
	/**
	 * Check for logs
	 *
	 * @param	object		$entity		The entity to count logs for
	 */
	public function logCount( $entity )
	{
		return \IPS\Db::i()->select( 'COUNT(*)', static::getTableName( $this->class ), array( 'log_id=? AND entity_id=?', $this->id, $entity->activeid ) )->first();
	}
	
	/**
	 * Get logs for an entity
	 *
	 * @param	object		$entity		The entity to get the log table for	 
	 */
	public function logsTable( $entity, $limit=NULL )
	{
		$sortBy = \IPS\Request::i()->sortby ?: 'id';
		$sortDirection = \IPS\Request::i()->sortdirection ?: 'desc';
		$page = \IPS\Request::i()->page ?: 1;
	
		/**
		 * Process log table requests
		 */
		if ( \IPS\Request::i()->getlog )
		{
			/* Ignore ajax requests not targeted at this log */
			if ( $this->id != \IPS\Request::i()->getlog and \IPS\Request::i()->isAjax() )
			{
				return '';
			}
			
			/* This log is targeted */
			if ( $this->id == \IPS\Request::i()->getlog )
			{
				/**
				 * Process Commands
				 */
				switch( \IPS\Request::i()->logdo )
				{
					case 'delete':
					
						if ( $this->can( 'delete' ) and \IPS\Request::i()->logid )
						{
							\IPS\Db::i()->delete( static::getTableName( $this->class ), array( 'id=?', \IPS\Request::i()->logid ) );
							if ( \IPS\Request::i()->isAjax() )
							{
								\IPS\Output::i()->json( 'OK' );
							}
						}
				}
			}
			
			/* This log is NOT targeted */
			else
			{
				$sortBy = 'id';
				$sortDirection = 'desc';
				$page = 1;
			}
		}
		
		/**
		 * Build the table
		 */
		$self 		= $this;
		$lang		= \IPS\Member::loggedIn()->language();
		$controllerUrl 	= \IPS\Request::i()->url()->setQueryString( 'getlog', $this->id )->stripQueryString( 'logdo' )->stripQueryString( 'logid' );
		$table 		= new \IPS\Helpers\Table\Db( static::getTableName( $this->class ), $controllerUrl, array( 'log_id=? AND entity_id=?', $this->id, $entity->activeid ) );
		
		$table->tableTemplate = array( \IPS\Theme::i()->getTemplate( 'tables', 'core', 'admin' ), 'table' );
		$table->rowsTemplate = array( \IPS\Theme::i()->getTemplate( 'tables', 'core', 'admin' ), 'rows' );
		$table->include 	= array( 'logtime', 'message' );
		$table->langPrefix 	= 'rules_custom_logs_table_';
		$table->noSort 		= array( 'message' );
		$table->sortBy 		= $sortBy;
		$table->sortDirection 	= $sortDirection;
		$table->page		= $page;
		
		if ( $limit )
		{
			$table->limit = $limit;
		}
		
		$table->parsers 	= array
		(
			'logtime' => function( $val, $row )
			{
				return (string) \IPS\DateTime::ts( $val );
			},
		);
		
		foreach( $this->children() as $data )
		{
			if ( $data->can( 'view' ) )
			{
				$lang->words[ 'rules_custom_logs_table_data_' . $data->varname ] = $data->name;
				$table->include[] = 'data_' . $data->varname;
				$table->parsers[ 'data_' . $data->varname ] = function( $val, $row ) use ( $data )
				{
					if ( $data->type == 'object' and \IPS\rules\Data::isConcreteRecord( $data->class ) )
					{
						$objClass = str_replace( '-', '\\', $data->class );
						try
						{
							$obj = $objClass::load( $val );
							return \IPS\rules\Data::dataDisplayValue( $obj );
						}
						catch( \OutOfRangeException $e ) 
						{ 
							return NULL; 
						}
					}
					else if ( $data->type == 'object' )
					{
						$obj = \IPS\rules\Application::restoreArg( json_decode( $val, TRUE ) );
						return \IPS\rules\Data::dataDisplayValue( $obj );
					}
					else if ( $data->type == 'array' )
					{
						$array = \IPS\rules\Application::restoreArg( json_decode( $val, TRUE ) );
						return \IPS\rules\Data::dataDisplayValue( $array );
					}
					
					return $val;
				};
				
				if ( ! in_array( $data->type, array( 'int', 'float' ) ) )
				{
					$table->noSort[] = 'data_' . $data->varname;
				}
			}
		}
		
		$table->rowButtons = function( $row ) use ( $controllerUrl, $self )
		{	
			$buttons = array();
			
			if ( $self->can( 'delete' ) )
			{
				$buttons[ 'delete' ] = array(
					'icon'		=> 'trash',
					'title'		=> 'delete',
					'id'		=> "{$row['id']}-delete",
					'link'		=> $controllerUrl->setQueryString( array( 'logdo' => 'delete', 'logid' => $row[ 'id' ] ) ),
					'data'		=> array( 'delete' => '' ),
				);
			}
			
			return $buttons;
		};
		
		return $table;
	}
	
	/**
	 * Get a tabbed output of all logs for an entity
	 *
	 * @param	object		$entity		The entity to count logs for
	 */
	public static function allLogs( $entity, $limit=NULL )
	{
		$output = new \IPS\Helpers\Form;
		$output->actionButtons = array();
		$logs = array();
		
		foreach( \IPS\rules\Log\Custom::roots( 'view', NULL, array( array( 'custom_log_class=?', $entity::rulesDataClass() ) ) ) as $log )
		{
			if ( $log->logCount( $entity ) )
			{
				$tab_title = 'custom_log_' . $log->id;
				\IPS\Member::loggedIn()->language()->words[ 'custom_log_' . $log->id ] = $log->title;
			
				$output->addTab( $tab_title );
				$output->addHtml( $logs[] = $log->logsTable( $entity, $limit ) );
			}
		}
		
		return $logs ? $output : NULL;		
	}
	
}