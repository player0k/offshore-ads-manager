<?php if (!defined('FW')) {
	die('Forbidden');
}

class FW_Extension_Banner_Manager extends FW_Extension
{
	private $post_type_name = 'banner-manager-type';
	private $post_type_slug = 'banner-manager-slug';
	private $taxonomy_slug = 'banner-manager-tax';
	private $category_prefix = 'category-';
	private $banner_prefix = 'banner-';
	private $supports_feature_name = 'banner-manager';

	public function get_post_type()
	{
		return $this->post_type_name;
	}

	private function get_extended_positions()
	{
		$positions = array();
		$positions = apply_filters('fw-extension:banner-manager:extended_positions', $positions );
		return $positions;
	}

	public function _init()
	{
		$this->register_post_type();
		$this->register_taxonomy();

		if (is_admin()){
			add_filter('fw_ext_banner_manager_settings_options', array($this, '_filter_fw_extension_settings_page_options'));
			add_action( 'admin_enqueue_scripts', array($this, '_admin_action_enqueue_scripts_on_extension_settings_page') );
			add_filter('fw_post_options', array($this, '_filter_fw_post_options'), 10, 2);
		}
	}

	public function _filter_fw_post_options($post_options, $post_type)
	{
		if (!$this->is_supported()) {
			return $post_options;
		}

		$offshore_options = apply_filters(
			'fw_ext_offshore_ads_manager_options',
			array(
				'ads_manager_tab' => array(
					'title'   => 'Управление баннерами',
					'type'    => 'tab',
					'options' => $this->get_banners_multi_selects_options(true, $post_type)
				)
			)
		);

		if (isset($post_options['main']) && $post_options['main']['type'] === 'box') {
			$post_options['main']['options'][] = $offshore_options;
		} else {
			$post_options['main'] = array(
				'title'   => false,
				'desc'    => false,
				'type'    => 'box',
				'options' => $offshore_options
			);
		}

		return $post_options;
	}


	public function _admin_action_enqueue_scripts_on_extension_settings_page($hook)
	{
		$ext = FW_Request::GET('extension');
		if ($hook !== 'toplevel_page_fw-extensions' && $ext !== $this->get_name() ) {
			return;
		}

			wp_enqueue_script(
				'fw-extension-'.$this->get_name().'-main-js',
				$this->get_uri('/static/js/main.js'),
				array('fw-events', 'jquery', 'fw'),
				fw()->manifest->get_version()
			);

			wp_localize_script(
				'fw-extension-'.$this->get_name().'-main-js',
				'PhpVar',
				array(
					'alertMessage' => 'Проверьте вкладку "Выбор баннеров"',
					'bannerPostType' => $this->post_type_name
				)
			);

			wp_enqueue_style(
				'fw-extension-'.$this->get_name().'-main-css',
				$this->get_uri('/static/css/main.css'),
				array(),
				fw()->manifest->get_version()
			);
	}

	public function register_post_type()
	{
		$labels = array(
			'name'               => 'Управление баннерами',
			'singular_name'      => 'Баннер',
			'menu_name'          => 'Баннеры',
			'name_admin_bar'     => 'Баннеры',
			'add_new'            => 'Добавить баннер',
			'add_new_item'       => 'Добавить баннер',
			'new_item'           => 'Добавить баннер',
			'edit_item'          => 'Редактировать баннер',
			'view_item'          => 'Просмотреть баннер',
			'all_items'          => 'Все баннеры',
			'search_items'       => 'Найти баннер',
			'parent_item_colon'  => 'Родительский баннер',
			'not_found'          => 'Баннер не найден',
			'not_found_in_trash' => 'Баннер не найден в удаленных'
		);

		$args = array(
			'labels'             => $labels,
			'description'        => 'Описание',
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array('slug' => $this->post_type_slug),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array('title', 'editor')
		);

		register_post_type($this->post_type_name, $args);
	}

	public function register_taxonomy()
	{
		$labels = array(
			'name'              => 'Категории баннеров',
			'singular_name'     => 'Категория баннеров',
			'search_items'      => 'Поиск по категории баннеров',
			'all_items'         => 'Все категории баннеров',
			'parent_item'       => 'Родительская категория баннера',
			'parent_item_colon' => 'Родительская категория',
			'edit_item'         => 'Редактировать категорию',
			'update_item'       => 'Обновить категорию',
			'add_new_item'      => 'Добавить новую категорию',
			'new_item_name'     => 'Новая категория баннеров',
			'menu_name'         => 'Категории',
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array('slug' => $this->taxonomy_slug),
		);

		register_taxonomy($this->taxonomy_slug, array($this->post_type_name), $args);
	}

	public function get_taxonomy_slug()
	{
		return $this->taxonomy_slug;
	}

	private function get_categories_multi_selects_options()
	{
		$multi_selects = array();
		$extended_positions = $this->get_extended_positions();
		$all_positions = (array) $this->get_config('positions');

		foreach ($extended_positions as $positions) {
			$all_positions = array_merge($all_positions, $positions);
		}

		foreach ($all_positions as $position_key => $position) {
			$multi_selects[$this->category_prefix.$position_key] = array(
				'type'        => 'multi-select-extended',
				'label'       => $position['label'],
				'attr'        => array(
					'data-position' => $position_key,
					'data-type'     => 'category',
				),
				'help'        => $position['help'],
				'population'  => 'taxonomy',
				'source'      => $this->taxonomy_slug,
				'prepopulate' => 10,
				'limit'       => 1,
			);
		}

		return $multi_selects;
	}

	/**
	 * If empty post_type return all multi_select_options
	 * @return array
	 */
	private function get_banners_multi_selects_options($filter = false, $post_type = false)
	{
		$multi_selects = array();
		$extended_positions = $this->get_extended_positions();
		$all_positions = (array) $this->get_config('positions');

		if ($post_type && isset($extended_positions[$post_type])) {
			$all_positions = array_merge($all_positions, $extended_positions[$post_type]);
		} elseif (empty($post_type)) {
			foreach ($extended_positions as $positions) {
				$all_positions = array_merge($all_positions, $positions);
			}
		}

		foreach ($all_positions as $position_key => $position) {
			$val = fw_get_db_ext_settings_option($this->get_name(),$this->category_prefix.$position_key,array()) ;
			$term_id = empty($val) ? '' : array_shift($val);

			if($filter && empty($term_id)) {
				continue;
			}

			$multi_selects[$this->banner_prefix.$position_key] = array(
				'type'        => 'multi-select-extended',
				'label'       => $position['label'],
				'attr'        => array(
					'data-position' => $position_key,
					'data-type'     => 'banner',
					'data-term'     => $term_id,
				),
				'population'  => 'banner',
				'source'      => $this->post_type_name,
				'term'        => $term_id,
				'prepopulate' => 10,
				'limit'       => 1,
			);
		}

		if ($filter && empty($multi_selects)) {
			$multi_selects['message'] = array(
				'type'  => 'html',
				'label' => false,
				'desc'  => false,
				'html'  => 'Задайте пожалуйста категории для позиций баннеров на странице <a href="' . admin_url('themes.php?page=fw-settings') .'"><b>Настройки Темы</b></a> .',
			);
		}

		return $multi_selects;
	}

	/**
	 *
	 * Generate tabs with options on fw-settings page
	 *
	 * @param $options array
	 *
	 * @return mixed array of options
	 */
	public function _filter_fw_extension_settings_page_options($options)
	{
		$config = $this->get_config('positions');
		if (!is_array($config) || empty($config)) {
			return $options;
		}

		$options['offshore-ads-manager'] = array(
			'title'   => 'Управление баннерами',
			'type'    => 'tab',
			'options' => array(
				'sub_tab_1' => array(
					'title'   => 'Выбор категорий',
					'type'    => 'tab',
					'options' => array(
						'ads_categories_tab' => array(
							'title'   => 'Выберите категорию баннеров для позий',
							'type'    => 'box',
							'options' => $this->get_categories_multi_selects_options(),
						),
					),
				),
				'sub_tab_2' => array(
					'title'   => 'Выбор баннеров',
					'type'    => 'tab',
					'options' => array(
						'ads_banners_tab' => array(
							'title'   => 'Укажите на какой позиции должен быть баннер',
							'type'    => 'box',
							'options' => $this->get_banners_multi_selects_options()
						),
					),
				),
			),
		);

		return $options;
	}

	//@fixme: may be some issue if positions not unique
	public function render_banner($position, $post_id = null)
	{
		$val = fw_get_db_ext_settings_option($this->get_name(),$this->banner_prefix.$position, array());

		if (!empty($post_id)) {
			if ($this->is_supported($post_id)) {
				$post_val = fw_get_db_post_option($post_id, $this->banner_prefix.$position, array());
				if (!empty($post_val)){
					$val = $post_val;
				}
			}
		}

		if (empty($val)) {
			return false;
		}
		$banner_id = array_shift($val);
		if (empty($banner_id)) {
			return false;
		}

		$banner = get_post($banner_id);

		return htmlspecialchars_decode($banner->post_content);
	}

	/**
	 * Checks if a post is supported
	 *
	 * @param int $post_id
	 *
	 * @return bool
	 */
	public function is_supported( $post_id = null ) {
		if ( empty( $post_id ) ) {
			global $post;
		} else {
			$post = get_post( $post_id );
		}

		if ( ! $post ) {
			return false;
		}

		if ( post_type_supports( $post->post_type, $this->supports_feature_name ) ) {
			return true;
		} else {
			return false;
		}
	}

	public  function get_supports_feature_name()
	{
		return $this->supports_feature_name;
	}

}
