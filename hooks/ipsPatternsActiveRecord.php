//<?php

abstract class rules_hook_ipsPatternsActiveRecord extends _HOOK_CLASS_
{

	/**
	 * @brief	Cache for Rules Data
	 */
	protected $rulesData = array();
	
	/**
	 * @brief	Cache for Raw Rules Data
	 */
	protected $rulesDataRaw = NULL;
	
	/**
	 * @brief	Track Loaded Data Keys
	 */
	protected $rulesLoadedKeys = array();
	
	/**
	 * @brief	Track if all data has been loaded
	 */
	protected $rulesAllKeysLoaded = FALSE;
	
	/**
	 * Save Changed Columns
	 *
	 * @return	void
	 */
	public function save()
	{
		if ( $this->_new )
		{
			$result = call_user_func_array( 'parent::save', func_get_args() );			
			\IPS\rules\Event::load( 'rules', 'System', 'record_updated' )->trigger( $this, $this->_data, TRUE );			
		}
		else
		{
			$changed = $this->changed;
			$result = call_user_func_array( 'parent::save', func_get_args() );
			
			if ( $changed )
			{
				\IPS\rules\Event::load( 'rules', 'System', 'record_updated' )->trigger( $this, $changed, FALSE );
			}
		}
		
		return $result;
	}

	/**
	 * [ActiveRecord] Delete Record
	 *
	 * @return	void
	 */
	public function delete()
	{
		$result = call_user_func_array( 'parent::delete', func_get_args() );
		\IPS\rules\Event::load( 'rules', 'System', 'record_deleted' )->trigger( $this );
		
		if ( $this->rulesTableExists() )
		{
			$idField = $this::$databaseColumnId;
			\IPS\Db::i()->delete( \IPS\rules\Data::getTableName( get_class( $this ) ), array( 'entity_id=?', $this->$idField ) );
		}
		
		return $result;
	}
	
	/**
	 * Rules Data Class
	 */
	public static function rulesDataClass()
	{
		return '-' . str_replace( '\\', '-', get_called_class() );
	}
	
	/**
	 * Get Raw Rules Data
	 */
	public function getRulesDataRaw()
	{
		if ( isset( $this->rulesDataRaw ) )
		{
			return $this->rulesDataRaw;
		}
	
		$idField = static::$databaseColumnId;
		
		try
		{
			$this->rulesDataRaw = \IPS\Db::i()->select( '*', \IPS\rules\Data::getTableName( get_class( $this ) ), array( 'entity_id=?', $this->$idField ) )->first();
		}
		catch ( \UnderflowException $e )
		{
			$this->rulesDataRaw = array();
		}
		
		return $this->rulesDataRaw;
	}
	
	/**
	 * Get Rules Data
	 *
	 * @param 	string|NULL	$key		The data to retrieve/set
	 * @return	array				Rules Data
	 */
	public function getRulesData( $key=NULL )
	{
		$idField 	= static::$databaseColumnId;
		$data_class 	= $this::rulesDataClass();
		
		if ( ! $this->$idField )
		{
			return $key ? NULL : array();
		}
		
		if ( isset( $key ) )
		{
			if ( isset( $this->rulesData[ $key ] ) or $this->rulesLoadedKeys[ $key ] )
			{
				return $this->rulesData[ $key ];
			}

			$where = array( 'data_class=? AND data_column_name=?', $data_class, $key );
		}
		else
		{
			if ( $this->rulesAllKeysLoaded )
			{
				return $this->rulesData;
			}
			
			$this->rulesAllKeysLoaded = TRUE;
			$where = array( 'data_class=?', $data_class );
		}

		if ( $this->rulesTableExists() and $data = $this->getRulesDataRaw() )
		{			
			foreach ( \IPS\rules\Data::roots( NULL, NULL, array( $where ) ) as $data_field )
			{
				if ( ! $this->rulesLoadedKeys[ $data_class->column_name ] )
				{
					$data_field_data = $data[ 'data_' . $data_field->column_name ];
					
					if ( ! isset ( $data_field_data ) )
					{
						$this->rulesData[ $data_field->column_name ] = NULL;
						continue;
					}
				
					switch ( $data_field->type )
					{
						case 'object':
						
							/**
							 * Specific object types are stored as an integer id
							 */
							if ( $data_field->type_class )
							{
								switch ( $data_field->type_class )
								{
									case '-IPS-DateTime':
									
										$data_field_data = \IPS\DateTime::ts( $data_field_data );
										break;
									
									default:
									
										try
										{
											$objClass = str_replace( '-', '\\', $data_field->type_class );
											$data_field_data = $objClass::load( $data_field_data );
										}
										catch ( \Exception $e )
										{
											$data_field_data = NULL;
										}
								}
							}
							
							/**
							 * Arbitrary objects are stored as json encoded arguments
							 */
							else
							{
								$data_field_data = \IPS\rules\Application::restoreArg( json_decode( $data_field_data, TRUE ) );
							}
							break;
						
						case 'array':
							
							/**
							 * Arrays of known object types are saved as comma separated lists
							 */
							if ( $data_field->type_class )
							{
								$data_field_data = explode( ',', $data_field_data );
								$_data_field_data = array();
								$objClass = str_replace( '-', '\\', $data_field->type_class );
								
								foreach ( $data_field_data as $_id )
								{
									switch ( $data_field->type_class )
									{
										case '-IPS-DateTime':
										
											$_data_field_data[ $_id ] = \IPS\DateTime::ts( $_id );
											break;
										
										default:
										
											try
											{
												$_data_field_data[ $_id ] = $objClass::load( $_id );
											}
											catch ( \Exception $e ) {}
									}
									
								}
								
								$data_field_data = $_data_field_data;
							}
							
							/**
							 * Arbitrary arrays are json_encoded
							 */
							else
							{
								$data_field_data = json_decode( $data_field_data, TRUE );
								$_data_field_data = array();
								if ( is_array ( $data_field_data ) )
								{
									foreach ( $data_field_data as $k => $value )
									{
										$result = \IPS\rules\Application::restoreArg( $value );
										if ( $result !== NULL )
										{
											$_data_field_data[ $k ] = $result;
										}
									}
									
									$data_field_data = $_data_field_data;
								}
								else
								{
									$data_field_data = array();
								}
							}
							break;
							
						case 'mixed':
						
							$data_field_data = \IPS\rules\Application::restoreArg( json_decode( $data_field_data, TRUE ) );
							break;
							
						case 'bool':
						
							$data_field_data = (bool) $data_field_data;
							break;
							
						case 'int':
							
							$data_field_data = (int) $data_field_data;
							break;
						
						case 'float':
						
							$data_field_data = (float) $data_field_data;
							break;
							
						case 'string':
						
							$data_field_data = (string) $data_field_data;
							break;

					}
					
					$this->rulesData[ $data_field->column_name ] = $data_field_data;
					$this->rulesLoadedKeys[ $data_field->column_name ] = TRUE;
				}
			}
		}
		
		return $key ? $this->rulesData[ $key ] : $this->rulesData;
	}
	
	/**
	 * Set Rules Data
	 *
	 * @param 	string|NULL	$key		The data to retrieve/set
	 * @param	mixed		$value		The value to set
	 * @return	array				Rules Data
	 */
	public function setRulesData( $key, $value )
	{
		$idField = static::$databaseColumnId;
		if ( ! $this->$idField )
		{
			return NULL;
		}
		
		if ( $this->rulesTableExists() )
		{
			if ( \IPS\Db::i()->checkForColumn( \IPS\rules\Data::getTableName( get_class( $this ) ), 'data_' . $key ) )
			{
				$save_value = NULL;
				$data_class = $this::rulesDataClass();
				$data_field = \IPS\rules\Data::load( $key, 'data_column_name', array( 'data_class=?', $data_class ) );
				
				if ( $value !== NULL )
				{											
					switch ( $data_field->type )
					{
						case 'object':
						
							if ( ! is_object( $value ) )
							{
								throw new \InvalidArgumentException( 'Value is expected to be an object' );
							}
						
							if ( $data_field->type_class )
							{
								$objClass = ltrim( str_replace( '-', '\\', $data_field->type_class ), '\\' );
								
								if ( get_class( $value ) != $objClass )
								{
									throw new \InvalidArgumentException( 'Object is expected to be of class: \\' . $objClass );
								}
								
								switch ( $objClass ) 
								{
									case 'IPS\DateTime':
									
										$save_value = $value->getTimestamp();
										break;
										
									default:
									
										$_idField = $value::$databaseColumnId;
										$save_value = $value->$_idField;
								}
							}
							else
							{
								$save_value = json_encode( \IPS\rules\Application::storeArg( $value ) );
							}
							break;
							
						case 'array':
						
							if ( ! is_array( $value ) )
							{
								throw new \InvalidArgumentException( 'Value is expected to be an array' );
							}
							
							if ( $data_field->type_class )
							{
								$ids = array();
								$new_value = array();
								$objClass = ltrim( str_replace( '-', '\\', $data_field->type_class ), '\\' );
								
								foreach ( $value as $k => $obj )
								{
									if ( ! is_object( $obj ) or get_class( $obj ) != $objClass )
									{
										continue;
									}
									
									switch ( $objClass ) 
									{
										case 'IPS\DateTime':
										
											$ts = $obj->getTimestamp();
											$ids[] = $ts;
											$new_value[ $ts ] = $obj;
											break;
											
										default: 
										
											$_idField = $obj::$databaseColumnId;
											$ids[] = $obj->$_idField;
											$new_value[ $obj->$_idField ] = $obj;
											break;
									}
								}
								$save_value = implode( ',', array_unique( $ids ) );
								$value = $new_value;
							}
							else
							{
								$save_value = array();
								$new_value = array();
								foreach ( $value as $k => $v )
								{
									$result = \IPS\rules\Application::storeArg( $v );
									if ( $result !== NULL )
									{
										$save_value[ $k ] = $result;
										$new_value[ $k ] = $v;
									}
								}
								
								$save_value = json_encode( $save_value );
								$value = $new_value;
							}
							break;
							
						case 'mixed':
						
							$save_value = json_encode( \IPS\rules\Application::storeArg( $value ) );
							break;
							
						case 'int':
						
							$save_value = (int) $value;
							break;
							
						case 'float':
						
							$save_value = (float) $value;
							break;
							
						case 'bool':
						
							$save_value = (bool) $value;
							break;
							
						case 'string':
						
							$save_value = (string) $value;
							break;
							
						default:
						
							$save_value = $value;
							break;
					}
				}
				
				/**
				 * Update or create the database record
				 */
				if ( \IPS\Db::i()->select( 'COUNT(*)', \IPS\rules\Data::getTableName( get_class( $this ) ), array( 'entity_id=?', $this->$idField ) )->first() )
				{
					\IPS\Db::i()->update( \IPS\rules\Data::getTableName( get_class( $this ) ), array( 'data_' . $key => $save_value ), array( 'entity_id=?', $this->$idField ) );
				}
				else
				{
					\IPS\Db::i()->insert( \IPS\rules\Data::getTableName( get_class( $this ) ), array( 'entity_id' => $this->$idField, 'data_' . $key => $save_value ) );
				}
				
				$this->rulesDataRaw = \IPS\Db::i()->select( '*', \IPS\rules\Data::getTableName( get_class( $this ) ), array( 'entity_id=?', $this->$idField ) )->first();
				$this->rulesData[ $key ] = $value;
				
				/* Trigger Event */
				$event_id = 'updated_' . $data_field->key;
				\IPS\rules\Event::load( 'rules', 'CustomData', $event_id )->trigger( $this, $value );
				
				return TRUE;
			}
			else
			{
				throw new \InvalidArgumentException( 'Data key doesnt exist' );
			}
		}
		
		return FALSE;
	}
	
	/**
	 * Check for data table
	 */
	protected function rulesTableExists()
	{
		static $tableExists = array();
		$table_name = \IPS\rules\Data::getTableName( get_class( $this ) );
		
		if ( ! isset ( $tableExists[ $table_name ] ) )
		{
			$tableExists[ $table_name ] = \IPS\Db::i()->checkForTable( $table_name );
		}
		
		return $tableExists[ $table_name ];
	}

}