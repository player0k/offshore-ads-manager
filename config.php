<?php if (!defined('FW')) {
	die('Forbidden');
}

$cfg = array();

$uri = get_template_directory_uri() . '/images/default.jpg';

$cfg['positions'] = array(
	'carusel_1' => array('label' => 'Первая позиция в каруселе', 'help' => '<img width="80" height="80" src="'. $uri .'"/>'),
	'carusel_2' => array('label' => 'Вторая позиция в каруселе', 'help' => '<img width="80" height="80" src="'. $uri .'"/>'),
	'carusel_3' => array('label' => 'Третья позиция в каруселе', 'help' => '<img width="80" height="80" src="'. $uri .'"/>'),
	'carusel_4' => array('label' => 'Четвертая позиция в каруселе', 'help' => '<img width="80" height="80" src="'. $uri .'"/>'),
	'sidebar1_1'  => array('label' => 'Первая позиция в сайдбаре 1', 'help' => '<img width="80" height="80" src="'. $uri .'"/>'),
	'sidebar1_2' => array('label' => 'Вторая позиция в сайдбаре 1', 'help' => '<img width="80" height="80" src="'. $uri .'"/>'),
	'sidebar1_3'  => array('label' => 'Третья позиция в сайдбаре 1', 'help' => '<img width="80" height="80" src="'. $uri .'"/>'),
	'sidebar2_1'  => array('label' => 'Первая позиция в сайдбаре 2', 'help' => '<img width="80" height="80" src="'. $uri .'"/>'),
);