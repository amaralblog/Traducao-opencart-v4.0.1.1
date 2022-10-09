<?php
namespace Opencart\Admin\Controller\Extension\LanguageGerman\Language;
class German extends \Opencart\System\Engine\Controller {

	public function index(): void {
		$this->load->language('extension/language_german/language/german');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=language')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/language_german/language/german', 'user_token=' . $this->session->data['user_token'])
		];

		$data['save'] = $this->url->link('extension/language_german/language/german|save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=language');

		$data['language_german_status'] = $this->config->get('language_german_status');

		$this->load->model('localisation/language');

		$language_info = $this->model_localisation_language->getLanguageByCode('de-de');

		if ($language_info) {
			$data['entry_language_de_name'] = $language_info['name'];
			$data['language_german_language_de_status'] = $language_info['status'];
		}

		$language_info = $this->model_localisation_language->getLanguageByCode('de-at');

		if ($language_info) {
			$data['entry_language_at_name'] = $language_info['name'];
			$data['language_german_language_at_status'] = $language_info['status'];
		}

		$language_info = $this->model_localisation_language->getLanguageByCode('de-ch');

		if ($language_info) {
			$data['entry_language_ch_name'] = $language_info['name'];
			$data['language_german_language_ch_status'] = $language_info['status'];
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/language_german/language/german', $data));
	}

	public function save(): void {
		$this->load->language('extension/language_german/language/german');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/language_german/language/german')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('language_german', $this->request->post);

			// Update languages
			$this->load->model('localisation/language');

			// German common (DE)
			$language_info = $this->model_localisation_language-> getLanguageByCode('de-de');

			$language_info['status'] = (empty($this->request->post['language_german_language_de_status']) ? '0' : '1');

			$this->model_localisation_language->editLanguage($language_info['language_id'], $language_info);

			// Austria (AT)
			$language_info = $this->model_localisation_language-> getLanguageByCode('de-at');

			$language_info['status'] = (empty($this->request->post['language_german_language_at_status']) ? '0' : '1');

			$this->model_localisation_language->editLanguage($language_info['language_id'], $language_info);

			// Switzerland (CH)
			$language_info = $this->model_localisation_language-> getLanguageByCode('de-ch');

			$language_info['status'] = (empty($this->request->post['language_german_language_ch_status']) ? '0' : '1');

			$this->model_localisation_language->editLanguage($language_info['language_id'], $language_info);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function install(): void {
		// v1.0 if ($this->user->hasPermission('modify', 'extension/language_german/language/german')) {
		if ($this->user->hasPermission('modify', 'extension/language')) {
			// Add language
			$language_data = [
				'name'       => 'Deutsch (DE)',
				'code'       => 'de-de',
				'locale'     => 'de,de-de,de_DE.UTF-8,de_DE,de-DE,deutsch',
				'extension'  => 'language_german',
				'status'     => 1,
				'sort_order' => 2
			];

			$this->load->model('localisation/language');

			$this->model_localisation_language->addLanguage($language_data);

			$language_data = [
				'name'       => 'Deutsch (AT)',
				'code'       => 'de-at',
				'locale'     => 'at,de-at,de_AT.UTF-8,de_AT,de-AT',
				'extension'  => 'language_german',
				'status'     => 1,
				'sort_order' => 3
			];

			$this->load->model('localisation/language');

			$this->model_localisation_language->addLanguage($language_data);

			$language_data = [
				'name'       => 'Deutsch (CH)',
				'code'       => 'de-ch',
				'locale'     => 'ch,de-ch,de_CH.UTF-8,de_CH,de-CH',
				'extension'  => 'language_german',
				'status'     => 1,
				'sort_order' => 3
			];

			$this->load->model('localisation/language');

			$this->model_localisation_language->addLanguage($language_data);

			$this->load->model('setting/startup');

			// Add startup to catalog
			$startup_data = [
				'code'        => 'language_german',
				'description' => 'German catalog language translation',
				'action'      => 'catalog/extension/language_german/startup/german',
				'status'      => 1,
				'sort_order'  => 2
			];

		// startup no longer needed
		//	$this->model_setting_startup->addStartup($startup_data);

			// Add startup for admin

			$startup_data = [
				'code'        => 'language_german',
				'description' => 'German admin language translation',
				'action'      => 'admin/extension/language_german/startup/german',
				'status'      => 1,
				'sort_order'  => 2
			];

		//	$this->model_setting_startup->addStartup($startup_data);

		// copy translation files for standard opencart extension due to a OpenCart 4.0.1.1 error
			$extension_folder = implode('', glob(DIR_EXTENSION));

			if (is_dir($extension_folder . 'language_german/extension/opencart/')) {

				$src = $extension_folder . '/language_german/extension/opencart/';
				$dst = $extension_folder . '/opencart/';

				$this->custom_copy($src, $dst);
			} else {
				$this->log->write('Source (' . $src . ') is not present');
			}
		}
	}

	public function uninstall(): void {
		// v1.0 if ($this->user->hasPermission('modify', 'extension/language_german/language/german')) {
		if ($this->user->hasPermission('modify', 'extension/language')) {
			$this->load->model('localisation/language');

			// Germany (de-de)
			$language_info = $this->model_localisation_language->getLanguageByCode('de-de');

			if ($language_info) {
				$this->model_localisation_language->deleteLanguage($language_info['language_id']);
			}

			// Austria (de-at)
			$language_info = $this->model_localisation_language->getLanguageByCode('de-at');

			if ($language_info) {
				$this->model_localisation_language->deleteLanguage($language_info['language_id']);
			}

			// Switzerland (de-ch)
			$language_info = $this->model_localisation_language->getLanguageByCode('de-ch');

			if ($language_info) {
				$this->model_localisation_language->deleteLanguage($language_info['language_id']);
			}

			// Remove startup entries

			$this->load->model('setting/startup');

			$this->model_setting_startup->deleteStartupByCode('language_german');
		}
	}

	private function custom_copy($src, $dst) : void { 
		// open the source directory
		$dir = opendir($src); 
	  
		// Make the destination directory if not exist
		//@mkdir($dst); 
		if(!is_dir($dst))
		{
			mkdir($dst, 0755);
		}

		// Loop through the files in source directory
		while( $file = readdir($dir) ) { 

			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					// Recursively calling custom copy function for sub directory 
					$this->custom_copy($src . '/' . $file, $dst . '/' . $file); 
				} else { 
					copy($src . '/' . $file, $dst . '/' . $file); 
				}
			}
		}

		closedir($dir);
	}
}
