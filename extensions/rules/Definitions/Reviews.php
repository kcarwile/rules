<?php
/**
 * @brief		Rules extension: Reviews
 * @package		Rules for IPS Social Suite
 * @since		26 Feb 2015
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\rules\extensions\rules\Definitions;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * @brief	Rules definitions extension: Reviews
 */
class _Reviews
{

	/**
	 * @brief	Group events and actions in this extension with other extensions by group name
	 */
	public $group = 'Reviews';

	/**
	 * Triggerable Events
	 *
	 * Define the events that can be triggered by your application
	 *
	 * @return 	array		Array of event definitions
	 */
	public function events()
	{
		$events = array
		(
			/**
			 * Event Key
			 *
			 * Each of your events is triggered using an event key.
			 * Event keys only need to be unique to this class.
			 */
			'event_id' => array
			( 
				/**
				 * Event Arguments
				 *
				 * Define the arguments that are sent to your event trigger
				 */
				'arguments' => array
				( 
					/**
					 * Argument/Variable Names
					 *
					 * Arguments are defined in the order that they are given
					 * in the event trigger.
					 *
					 * The names you define will be available as PHP variables
					 * when using PHP code in the rules configurations.
					 * eg: $variable1
					 */
					'variable1' => array
					(
						/**
						 * Argument Type
						 *
						 * Define the type of this argument as it will be passed to the event
						 * trigger. This allows actions to determine whether this argument
						 * is compatible with the action callback.
						 *
						 * string: this argument will always be a string ( or possibly null )
						 * int: this argument will always be an integer ( or possibly null )
						 * bool: this argument will always be boolean ( or possibly null )
						 * float: this argument will always be a floating point number ( or possibly null )
						 * array: this argument will always be an array ( or possibly null )
						 * object: this argument will always be an object ( or possibly null )
						 * mixed: this argument will contain mixed values ( or possibly null )
						 */
						'argtype' 	=> 'int',
						
						/**
						 * Class Association
						 *
						 * This is used to help rules determine whether this argument can be used 
						 * with certain conditions/actions or not. For example, if your argument
						 * is a member object, (or associated with a member object ), you can define 
						 * it's class as \IPS\Member, which allows rules to make it available as an
						 * event argument to operations that specifically need a member object, or 
						 * an argument associated with a member object.
						 *
						 * It's good to be specific here. But if your argument is simply arbitrary in
						 * nature, then you can omit this setting.
						 */
						'class'		=> '\IPS\Member',
						
						/**
						 * Property Association
						 *
						 * If this argument can be associated specifically with a particular object
						 * property, then it's good to define that here because certain condition or
						 * action arguments may only be compatible with specific object properties.
						 */
						'property'	=> 'member_id',
						
						/**
						 * NULLABLE
						 *
						 * Set to TRUE if this argument may be NULL when the event is triggered.
						 * This gives the user the opportunity to set a default value for it when
						 * they create their rule.
						 *
						 * If the argument will always have a value that is not null, you 
						 * can omit this setting or leave it set it to FALSE
						 */
						'nullable'	=> FALSE,
					),
				),
			),
		);
		
		return array(); // $events;
	}
	
	/**
	 * Conditional Operations
	 *
	 * You can define your own conditional operations which can be
	 * added to rules as conditions.
	 *
	 * @return 	array		Array of conditions definitions
	 */
	public function conditions()
	{
		$conditions = array
		(
			/**
			 * Condition Key
			 *
			 * Any condition you provide is invoked using a condition key.
			 * Condition keys only need to be unique to this class.
			 */
			'condition_key' => array
			(
				/**
				 * Condition Callback
				 *
				 * The condition callback is the function that will be executed
				 * when your condition is invoked from rules.
				 */
				'callback' 	=> array( $this, 'operationCallback' ),
				
				/**
				 * Condition Configuration
				 * 
				 * You can create custom configuration options for your condition.
				 */
				'configuration' => array
				(
					/**
					 * Form Builder
					 *
					 * Create custom form fields for your condition. The values will be saved
					 * automatically, and passed as the final argument to your condition callback
					 * when it is invoked.
					 *
					 * @param	\IPS\Helpers\Form 	$form		The condition editing form 
					 * @param	array			$values		An array of any previously saved form values
					 * @param	\IPS\rules\Condition	$condition		The condition object
					 * @return	void
					 */
					'form' => function( $form, $values, $condition )
					{
						
					},
				),
				
				/**
				 * Condition Arguments
				 *
				 * If your callback function for your condition has any arguments, 
				 * you need to define them so rules know how to invoke your callback.
				 */
				'arguments'	=> array
				(
					/**
					 * Argument Names
					 *
					 * Arguments are defined in the order that they will be
					 * passed to your callback function.
					 */
					'arg1' => array
					(
						/**
						 * When rules are triggered by events, the event will usually have
						 * variables associated with it that may be usable by your condition.
						 *
						 * You can define which types of variables that are compatible with
						 * your argument, and also provide a converter function that will
						 * translate the passed variable into the correct state for your
						 * callback.
						 */
						'argtypes' => array
						(
							/**
							 * The following argtypes are valid:
							 *
							 * string: allows rules to pass you a string
							 * int: allows rules to pass you an integer
							 * bool: allows rules to pass you a boolean value
							 * float: allows rules to pass you a floating point value
							 * array: allows rules to pass you an array
							 * object: allows rules to pass you an object
							 * mixed: allows rules to pass you mixed variables
							 */
							'int' => array
							(
								/**
								 * Description
								 *
								 * Describe what you expect for this argtype. This description
								 * helps users who create rules using php code to know what
								 * to supply your converter/callback function.
								 */
								'description' => 'Member ID',
								
								/**
								 * Converter Function
								 *
								 * If you define a converter function, you can process an argument
								 * from the event before it gets sent to your condition callback.
								 *
								 * @param	mixed	$val	The value being sent as an argument to your condition callback
								 * @param	array	$values	The saved configuration values from the condition
								 */
								'converter' => function( $val, $values ) 
								{
									return \IPS\Member::load( $val );
								},
								
								/**
								 * When events are defined, the arguments they receive when triggered
								 * can be associated with specific classes.
								 *
								 * If you define classes here for an argtype, only variables that have
								 * been associated with those class(es) in the event definition will be
								 * passed to your converter.
								 */
								'class' => array( '\IPS\Member' ),
								
								/**
								 * When events are defined, the arguments they receive when triggered
								 * can also be associated with specific properties.
								 *
								 * If you define properties for an argtype, only variables that have
								 * been associated with those property(ies) in the event definition will be
								 * passed to your converter.
								 */
								'property' => array( 'member_id' ),
								
							),						
						),
						
						/**
						 * Define whether this argument is required by your condition callback
						 */
						'required'	=> TRUE,
						
						/**
						 * Configuration
						 *
						 * You should create form elements on the condition editing form for users to 
						 * manually define the argument which is passed to your condition callback.
						 */
						'configuration' => array
						(
							/**
							 * Form Builder
							 *
							 * Rules will automatically hide your form elements if manual configuration is not selected
							 * by the user, but in order to do that, you must return an array of the HTML id's of the
							 * elements that you've added to the form ( the last argument in the form element construct )
							 *
							 * @param	\IPS\Helpers\Form 	$form		The condition editing form 
							 * @param	array			$values		An array of any previously saved form values
							 * @param	\IPS\rules\Condition	$condition		The condition object
							 * @return	array			An array of the form element id's that you created
							 */
							'form'		=> function( $form, $values, $condition ) 
							{
								$form->add( new \IPS\Helpers\Form\Text( 'custom_arg_value', $values[ 'custom_arg_value' ], FALSE, array(), NULL, NULL, NULL, 'custom_arg_value' ) );
								return array( 'custom_arg_value' );
							},
							
							/**
							 * Process Form Values
							 *
							 * Form values are saved automatically, however, you may need to modify them
							 * before they are saved (such as turning an array of member objects into an
							 * array of member id's).
							 *
							 * @param	array			$values		The values from the form
							 * @param	\IPS\rules\Condition	$condition		The condition object
							 * @return 	void
							 */
							'saveValues'	=> function( &$values, $condition ) 
							{
							
							},
							
							/**
							 * Get Callback Argument
							 *
							 * When your condition is invoked using a manually defined argument (via the form builder),
							 * the saved values are first passed to this function so that you can take any necessary
							 * steps to create the argument for your callback function.
							 * 
							 * @param	array			$values		The saved form values
							 * @param	\IPS\rules\Condition	$condition		The condition object
							 * @return	mixed			The value to be sent to your condition callback
							 */
							'getArg'	=> function( $values, $condition )
							{
								return $values[ 'custom_arg_value' ];
							},
						),
					),	
				),				
			),
		);
		
		return array(); // $conditions;
	}

	/**
	 * Triggerable Actions
	 *
	 * @return 	array		Array of action definitions
	 */
	public function actions()
	{
		$actions = array
		(
			/**
			 * Action Key
			 *
			 * Each of your actions is invoked using an action key.
			 * Actions keys only need to be unique to this class.
			 */
			'action_key' => array
			(
				/**
				 * Action Callback
				 *
				 * The action callback is the function that will be executed
				 * when your action is invoked from rules.
				 */
				'callback' 	=> array( $this, 'operationCallback' ),
				
				/**
				 * Action Configuration
				 * 
				 * You can create custom configuration options for your action.
				 */
				'configuration' => array
				(
					/**
					 * Form Builder
					 *
					 * Create custom form fields for your action. The values will be saved
					 * automatically, and passed as the final argument to your action callback
					 * when it is invoked.
					 *
					 * @param	\IPS\Helpers\Form 	$form		The action editing form 
					 * @param	array			$values		An array of any previously saved form values
					 * @param	\IPS\rules\Action	$action		The action object
					 * @return	void
					 */
					'form' => function( $form, $values, $action )
					{
						
					},
				),
				
				/**
				 * Action Arguments
				 *
				 * If your callback function for your action has any arguments, 
				 * you need to define them so rules know how to invoke your callback.
				 */
				'arguments'	=> array
				(
					/**
					 * Argument Names
					 *
					 * Arguments are defined in the order that they will be
					 * passed to your callback function.
					 */
					'arg1' => array
					(
						/**
						 * When rules are triggered by events, the event will usually have
						 * variables associated with it that may be usable by your action.
						 *
						 * You can define which types of variables that are compatible with
						 * your argument, and also provide a converter function that will
						 * translate the passed variable into the correct state for your
						 * callback.
						 */
						'argtypes' => array
						(
							/**
							 * The following argtypes are valid:
							 *
							 * string: allows rules to pass you a string
							 * int: allows rules to pass you an integer
							 * bool: allows rules to pass you a boolean value
							 * float: allows rules to pass you a floating point value
							 * array: allows rules to pass you an array
							 * object: allows rules to pass you an object
							 * mixed: allows rules to pass you mixed variables
							 */
							'int' => array
							(
								/**
								 * Description
								 *
								 * Describe what you expect for this argtype. This description
								 * helps users who create rules using php code to know what
								 * to supply your converter/callback function.
								 */
								'description' => 'Member ID',
								
								/**
								 * Converter Function
								 *
								 * If you define a converter function, you can process an argument
								 * from the event before it gets sent to your action callback.
								 *
								 * @param	mixed	$val	The value being sent as an argument to your action callback
								 * @param	array	$values	The saved configuration values from the action
								 */
								'converter' => function( $val, $values ) 
								{
									return \IPS\Member::load( $val );
								},
								
								/**
								 * When events are defined, the arguments they receive when triggered
								 * can be associated with specific classes.
								 *
								 * If you define classes here for an argtype, only variables that have
								 * been associated with those class(es) in the event definition will be
								 * passed to your converter.
								 */
								'class' => array( '\IPS\Member' ),
								
								/**
								 * When events are defined, the arguments they receive when triggered
								 * can also be associated with specific properties.
								 *
								 * If you define properties for an argtype, only variables that have
								 * been associated with those property(ies) in the event definition will be
								 * passed to your converter.
								 */
								'property' => array( 'member_id' ),
								
							),						
						),
						
						/**
						 * Define whether this argument is required by your action callback
						 */
						'required'	=> TRUE,
						
						/**
						 * Configuration
						 *
						 * You should create form elements on the action editing form for users to 
						 * manually define the argument which is passed to your action callback.
						 */
						'configuration' => array
						(
							/**
							 * Form Builder
							 *
							 * Rules will automatically hide your form elements if manual configuration is not selected
							 * by the user, but in order to do that, you must return an array of the HTML id's of the
							 * elements that you've added to the form ( the last argument in the form element construct )
							 *
							 * @param	\IPS\Helpers\Form 	$form		The action editing form 
							 * @param	array			$values		An array of any previously saved form values
							 * @param	\IPS\rules\Action	$action		The action object
							 * @return	array			An array of the form element id's that you created
							 */
							'form'		=> function( $form, $values, $action ) 
							{
								$form->add( new \IPS\Helpers\Form\Text( 'custom_arg_value', $values[ 'custom_arg_value' ], FALSE, array(), NULL, NULL, NULL, 'custom_arg_value' ) );
								return array( 'custom_arg_value' );
							},
							
							/**
							 * Process Form Values
							 *
							 * Form values are saved automatically, however, you may need to modify them
							 * before they are saved (such as turning an array of member objects into an
							 * array of member id's).
							 *
							 * @param	array			$values		The values from the form
							 * @param	\IPS\rules\Action	$action		The action object
							 * @return 	void
							 */
							'saveValues'	=> function( &$values, $action ) 
							{
							
							},
							
							/**
							 * Get Callback Argument
							 *
							 * When your action is invoked using a manually defined argument (via the form builder),
							 * the saved values are first passed to this function so that you can take any necessary
							 * steps to create the argument for your callback function.
							 * 
							 * @param	array			$values		The saved form values
							 * @param	\IPS\rules\Action	$action		The action object
							 * @return	mixed			The value to be sent to your action callback
							 */
							'getArg'	=> function( $values, $action )
							{
								return $values[ 'custom_arg_value' ];
							},
						),
					),	
				),				
			),
		);
		
		return array(); // $actions;
	}
	
	/**
	 * Example Operation Callback
	 *
	 * Your operation callback will recieve all of the arguments defined in your
	 * action/condition definition. If an argument is not required, and not provided 
	 * by the user, then it will be NULL.
	 *
	 * Your operation callback will also recieve three additional arguments at the end of
	 * your regularly defined arguments.
	 *
	 * @extraArg1	array				$values		An array of the existing saved values from the configuration form
	 * @extraArg2	array				$arg_map	A keyed array of the arguments from the event
	 * @extraArg3	object	\IPS\rules\Action	$operation	The operation object (Action or Condition) which is invoking the callback
	 *			\IPS\rules\Condition
	 */
	public function operationCallback( $arg1, $values, $arg_map, $operation )
	{
	
	}
	
}