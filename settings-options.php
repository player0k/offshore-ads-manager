<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = array(
	'general-tab' => array(
		'title'   => 'Главные настройки',
		'type'    => 'tab',
		'options' => array(
			'main-box' => array(
				'title'   => 'Выбор страниц на которых будет возможно переопределять глобальные настройки банеров',
				'type'    => 'box',
				'options' => array(
					'post_types' => array(
						'label'   => 'Активировать для',
						'type'    => 'checkboxes',
						'choices' => fw_ext_banner_manager_get_supported_post_types(),
						'value'   => apply_filters(
							'fw_ext_banner_manager_settings_options_post_types_default_value',
							array( 'page' => true, 'post' => true)
						),
						'desc'    => 'Выберите посты для которых необходимо активировать это расширение'
					),
				)
			)
		)
	)
);

$options = apply_filters('fw_ext_banner_manager_settings_options', $options);
