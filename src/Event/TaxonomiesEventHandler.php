<?php

namespace Croogo\Taxonomy\Event;

use Cake\Event\EventListenerInterface;
use Croogo\Croogo\CroogoNav;

/**
 * Taxonomy Event Handler
 *
 * @category Event
 * @package  Croogo.Taxonomy.Event
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TaxonomiesEventHandler implements EventListenerInterface {

/**
 * implementedEvents
 */
	public function implementedEvents() {
		return array(
			'Croogo.setupAdminData' => array(
				'callable' => 'onSetupAdminData',
			),
			'Controller.Links.setupLinkChooser' => array(
				'callable' => 'onSetupLinkChooser',
			),
		);
	}

/**
 * Setup admin data
 */
	public function onSetupAdminData($event) {
		$View = $event->subject;

		if (empty($View->viewVars['vocabularies_for_admin_layout'])) {
			$vocabularies = array();
		} else {
			$vocabularies = $View->viewVars['vocabularies_for_admin_layout'];
		}
		foreach ($vocabularies as $v) {
			$weight = 9999 + $v->weight;
			CroogoNav::add('sidebar', 'content.children.taxonomy.children.' . $v->alias, array(
				'title' => $v->title,
				'url' => array(
					'prefix' => 'admin',
					'plugin' => 'Croogo/Taxonomy',
					'controller' => 'Terms',
					'action' => 'index',
					$v->id,
				),
				'weight' => $weight,
			));
		};
	}

/**
 * Setup Link chooser values
 *
 * @return void
 */
	public function onSetupLinkChooser($event) {
		$this->Vocabulary = ClassRegistry::init('Taxonomy.Vocabulary');
		$vocabularies = $this->Vocabulary->find('all', array(
			'joins' => array(
				array(
					'table' => 'types_vocabularies',
					'alias' => 'TypesVocabulary',
					'conditions' => 'Vocabulary.id = TypesVocabulary.vocabulary_id'
				),
				array(
					'table' => 'types',
					'alias' => 'Type',
					'conditions' => 'TypesVocabulary.type_id = Type.id',
				),
			),
		));

		$linkChoosers = array();
		foreach ($vocabularies as $vocabulary) {
			foreach ($vocabulary['Type'] as $type) {
				$title = $type['title'] . ' ' . $vocabulary['Vocabulary']['title'];
				$linkChoosers[$title] = array(
					'description' => $vocabulary['Vocabulary']['description'],
					'url' => array(
						'plugin' => 'taxonomy',
						'controller' => 'terms',
						'action' => 'index',
						$vocabulary['Vocabulary']['id'],
						'?' => array(
							'type' => $type['alias'],
							'chooser' => 1,
							'KeepThis' => true,
							'TB_iframe' => true,
							'height' => 400,
							'width' => 600,
						),
					),
				);
			}
		}
		Croogo::mergeConfig('Menus.linkChoosers', $linkChoosers);
	}

}
