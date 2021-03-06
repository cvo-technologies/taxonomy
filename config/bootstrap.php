<?php

use Cake\Core\Configure;
use Croogo\Croogo\Cache\CroogoCache;
use Croogo\Croogo\Croogo;

$cacheConfig = array_merge(
	Configure::read('Croogo.Cache.defaultConfig'),
	array('groups' => array('taxonomy'))
);
CroogoCache::config('croogo_types', $cacheConfig);
CroogoCache::config('croogo_vocabularies', $cacheConfig);

Croogo::hookComponent('*', 'Croogo/Taxonomy.Taxonomies');

Croogo::hookHelper('*', 'Croogo/Taxonomy.Taxonomies');

Croogo::mergeConfig('Translate.models.Term', array(
	'fields' => array(
		'title' => 'titleTranslation',
		'description' => 'descriptionTranslation',
	),
	'translateModel' => 'Taxonomy.Term',
));
