<?php
/**
 * @brief		latestProducts Widget
 * @author		<a href='https://www.invisioncommunity.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) Invision Power Services, Inc.
 * @license		https://www.invisioncommunity.com/legal/standards/
 * @package		Invision Community
 * @subpackage	nexus
 * @since		16 Jul 2018
 */

namespace IPS\nexus\widgets;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !\defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * latestProducts Widget
 */
class _latestProducts extends \IPS\Widget\PermissionCache
{
	/**
	 * @brief	Widget Key
	 */
	public $key = 'latestProducts';
	
	/**
	 * @brief	App
	 */
	public $app = 'nexus';
		
	/**
	 * @brief	Plugin
	 */
	public $plugin = '';

	/**
	 * Initialise this widget
	 *
	 * @return void
	 */ 
	public function init()
	{
		\IPS\Output::i()->cssFiles = array_merge( \IPS\Output::i()->cssFiles, \IPS\Theme::i()->css( 'widgets.css', 'nexus' ) );
		parent::init();
	}

	/**
	 * Specify widget configuration
	 *
	 * @param	null|\IPS\Helpers\Form	$form	Form object
	 * @return	null|\IPS\Helpers\Form
	 */
	public function configuration( &$form=null )
	{
		$form = parent::configuration( $form );

		$form->add( new \IPS\Helpers\Form\Number( 'number_to_show', isset( $this->configuration['number_to_show'] ) ? $this->configuration['number_to_show'] : 5, TRUE ) );

		return $form;
 	} 
 	
 	 /**
 	 * Ran before saving widget configuration
 	 *
 	 * @param	array	$values	Values from form
 	 * @return	array
 	 */
 	public function preConfig( $values )
 	{
 		return $values;
 	}

	/**
	 * Render a widget
	 *
	 * @return	string
	 */
	public function render()
	{
		//Load the products
		$select = \IPS\Db::i()->select( '*', 'nexus_packages', array(
			array( 'p_store=1' ),
			array( "( p_member_groups='*' OR " . \IPS\Db::i()->findInSet( 'p_member_groups', \IPS\Member::loggedIn()->groups ) . ' )' )
		), "p_date_added DESC", array( 0, ( isset( $this->configuration['number_to_show'] ) AND $this->configuration['number_to_show'] > 0 ) ? $this->configuration['number_to_show'] : 5 ) );

		if ( !\count( $select ) )
		{
			return "";
		}

		$packages = new \IPS\Patterns\ActiveRecordIterator( $select, 'IPS\nexus\Package' );

		return $this->output( $packages );
	}
}