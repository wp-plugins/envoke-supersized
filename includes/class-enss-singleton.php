<?php
/**
 * Implements the base singleton class
 *
 * @since 2.0.0
 *
 * @package Envoke_Supersized
 */

/**
 * Envoke Supersized Singleton Class.
 *
 * @since 2.0.0
 */
abstract class ENSS_Singleton {
	/**
	 * Holds instantiated classes.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @var array $_instance Holds instantiated classes.
	 */
	protected static $_instance = array();

	/**
	 * Prevents direct creation.
	 *
	 * @since 2.0.0
	 * @access protected
	 */
	protected function  __construct() { }

	/**
	 * Prevent cloning.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	final private function  __clone() { }

	/**
	 * Returns new or existing class instance.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return ENSS_Singleton The requested class.
	 */
	final public static function get_instance() {
		$class = get_called_class();
		if( ! isset( static::$_instance[ $class ] ) ) {
			self::$_instance[ $class ] = new $class();
			self::$_instance[ $class ]->_init();
		}
		return self::$_instance[ $class ];
	}

	/**
	 * Called when class initialized.
	 *
	 * @since 2.0.0
	 * @access protected
	 */
	protected function _init() { }
}