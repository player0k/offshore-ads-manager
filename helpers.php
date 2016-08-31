<?php if (!defined('FW')) die('Forbidden');

/**
 * Returns whether or not a post was built with the banner manager
 */
function fw_ext_banner_manager_is_supported($post_id = '') {
	return fw()->extensions->get('banner-manager')->is_supported($post_id);
}

/**
 * Returns all post types that can be integrated with the banner manager
 */
function fw_ext_banner_manager_get_supported_post_types() {
	$cache_key = fw()->extensions->get('banner-manager')->get_cache_key('/supported_post_types');

	try {
		return FW_Cache::get($cache_key);
	} catch (FW_Cache_Not_Found_Exception $e) {
		$post_types = get_post_types(array('public' => true), 'objects');

		$result = array();
		foreach ($post_types as $key => $post_type) {
				$result[$key] = $post_type->labels->name;
		}

		$result = apply_filters('fw_ext_banner_manager_supported_post_types', $result);

		FW_Cache::set($cache_key, $result);

		return $result;
	}
}
