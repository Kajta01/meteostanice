<?php
	
	/**
	 * Helpers tools
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 09.11.2017, Webcraftic
	 * @version 1.0
	 */
	class WINP_Helper {

		private static $meta_options = array();

		/**
		 * Get meta option
		 *
		 * @param int $post_id
		 * @param string $option_name
		 * @param mixed $default
		 * @return bool|int
		 */
		public static function getMetaOption($post_id, $option_name, $default = null)
		{
			$post_id = (int)$post_id;

			if( !isset(self::$meta_options[$post_id]) || empty(self::$meta_options[$post_id]) ) {
				$meta_vals = get_post_meta($post_id, '', true);

				foreach($meta_vals as $name => $val) {
					self::$meta_options[$post_id][$name] = $val[0];
				}
			}

			return isset(self::$meta_options[$post_id][WINP_Plugin::app()->getPrefix() . $option_name])
				? self::$meta_options[$post_id][WINP_Plugin::app()->getPrefix() . $option_name]
				: $default;
		}

		/**
		 * Udpdate meta option
		 *
		 * @param int $post_id
		 * @param string $option_name
		 * @param mixed $option_value
		 * @return bool|int
		 */
		public static function updateMetaOption($post_id, $option_name, $option_value)
		{
			$post_id = (int)$post_id;

			return update_post_meta($post_id, WINP_Plugin::app()->getPrefix() . $option_name, $option_value);
		}

		/**
		 * Remove meta option
		 *
		 * @param int $post_id
		 * @param string $option_name
		 * @return bool|int
		 */
		public static function removeMetaOption($post_id, $option_name)
		{
			$post_id = (int)$post_id;

			return delete_post_meta($post_id, WINP_Plugin::app()->getPrefix() . $option_name);
		}
	}