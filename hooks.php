<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Call add_post_type_support('{post-type}', 'offshore-ads-manager')
 * for post types checked on Offshore ADS Manager Settings page.
 */
function _action_fw_ext_banner_manager_add_support() {
	$feature_name = fw_ext('banner-manager')->get_supports_feature_name();

	foreach (
		array_keys(fw_get_db_ext_settings_option('banner-manager', 'post_types'))
		as $slug
	) {
		add_post_type_support($slug, $feature_name);
	}
}
add_action( 'init', '_action_fw_ext_banner_manager_add_support',
	9999
);
