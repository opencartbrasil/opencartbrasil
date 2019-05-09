<?php
class ControllerToolUpdate extends Controller {
    public function index() {
        $this->load->language('tool/update');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('tool/update', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['user_token'] = $this->session->data['user_token'];

        $data['version'] = OPENCART_BRASIL;
        $data['update'] = true;

        $curl = curl_init('https://api.github.com/repos/opencartbrasil/opencartbrasil/releases');

        curl_setopt($curl, CURLOPT_USERAGENT, 'OpenCart Brasil ' . OPENCART_BRASIL);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($curl);

        curl_close($curl);

        if ($response) {
            $releases = json_decode($response, true);

            $release = $releases[0];
            $version = $release['tag_name'];
            $release_local = str_replace("v", "", OPENCART_BRASIL);
            $release_atual = str_replace("v", "", $version);

            $this->session->data['version'] = $version;

            $data['version'] = $version;
            $data['log'] = $release['body'];

            $data['text_change'] = sprintf($this->language->get('text_change'), $release_atual);

            if (version_compare($release_local, $release_atual, '>=')) {
                $data['button_update'] = $this->language->get('button_again');
                $data['success'] = sprintf($this->language->get('text_success'), $release_atual);
            } else {
                $data['button_update'] = sprintf($this->language->get('button_start'), $release_atual);
                $data['warning'] = sprintf($this->language->get('error_version'), $release_atual);
            }
        } else {
            $data['update'] = false;
            $data['warning'] = $this->language->get('error_connection');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('tool/update', $data));
    }

    public function download() {
        $this->load->language('tool/update');

        $this->maintenance_on();

        $json = array();

        if (!$this->user->hasPermission('modify', 'tool/update')) {
            $json['error'] = $this->language->get('error_permission');
        }

        if (!$json && isset($this->session->data['version'])) {
            set_time_limit(0);

            $file = DIR_DOWNLOAD . 'opencartbrasil.zip';
            if (is_file($file)) {
                @unlink($file);
            }

            $handle = fopen(DIR_DOWNLOAD . 'opencartbrasil.zip', 'w');

            $curl = curl_init('https://github.com/opencartbrasil/opencartbrasil/releases/download/'.$this->session->data['version'].'/opencartbrasil.zip');

            curl_setopt($curl, CURLOPT_USERAGENT, 'OpenCart Brasil ' . OPENCART_BRASIL);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
            curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 300);
            curl_setopt($curl, CURLOPT_FILE, $handle);

            curl_exec($curl);

            fclose($handle);

            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);

            if ($status == 200) {
                $json['text'] = $this->language->get('text_unzip');

                $json['next'] = str_replace('&amp;', '&', $this->url->link('tool/update/unzip', 'user_token=' . $this->session->data['user_token'], true));
            } else {
                $this->maintenance_off();

                $json['error'] = $this->language->get('error_download');
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function unzip() {
        $this->load->language('tool/update');

        $json = array();

        if (!$this->user->hasPermission('modify', 'tool/update')) {
            $json['error'] = $this->language->get('error_permission');
        }

        $file = DIR_DOWNLOAD . 'opencartbrasil.zip';
        if (!is_file($file)) {
            $this->maintenance_off();

            $json['error'] = $this->language->get('error_file');
        }

        if (!$json) {
            $zip = new ZipArchive();

            if ($zip->open($file)) {
                $zip->extractTo(DIR_DOWNLOAD . 'opencartbrasil/');
                $zip->close();

                $this->remove();

                $json['text'] = $this->language->get('text_move');

                $json['next'] = str_replace('&amp;', '&', $this->url->link('tool/update/move', 'user_token=' . $this->session->data['user_token'], true));
            } else {
                $json['error'] = $this->language->get('error_unzip');
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function move() {
        $this->load->language('tool/update');

        $json = array();

        if (!$this->user->hasPermission('modify', 'tool/update')) {
            $json['error'] = $this->language->get('error_permission');
        }

        $directory = DIR_DOWNLOAD . 'opencartbrasil/';

        if (!is_dir($directory)) {
            $this->maintenance_off();

            $json['error'] = $this->language->get('error_directory');
        }

        if (!$json) {
            $files = array();

            $path = array($directory . '/*');

            while (count($path) != 0) {
                $next = array_shift($path);

                foreach ((array)glob($next) as $file) {
                    if (is_dir($file)) {
                        $path[] = $file . '/*';
                    }

                    $files[] = $file;
                }
            }

            foreach ($files as $file) {
                $destination = str_replace('\\', '/', substr($file, strlen($directory . '/')));

                $path = str_replace('\\', '/', realpath(DIR_CATALOG . '../')) . '/' . $destination;

                if (substr($destination, 0, 5) == 'admin') {
                    $path = DIR_APPLICATION . substr($destination, 6);
                }

                if (substr($destination, 0, 7) == 'catalog') {
                    $path = DIR_CATALOG . substr($destination, 8);
                }

                if (substr($destination, 0, 5) == 'image') {
                    $path = DIR_IMAGE . substr($destination, 6);
                }

                if (substr($destination, 0, 6) == 'system') {
                    $path = DIR_SYSTEM . substr($destination, 7);
                }

                if (substr($destination, 0, 7) == 'storage') {
                    $path = DIR_STORAGE . substr($destination, 8);
                }

                if (is_dir($file) && !is_dir($path)) {
                    if (!mkdir($path, 0777)) {
                        $json['error'] = sprintf($this->language->get('error_directory'), $destination);
                    }
                }

                if (is_file($file)) {
                    if (!rename($file, $path)) {
                        $json['error'] = sprintf($this->language->get('error_file'), $destination);
                    }
                }
            }

            $files = array();
            $path = array(DIR_MODIFICATION . '*');
            while (count($path) != 0) {
                $next = array_shift($path);
                foreach (glob($next) as $file) {
                    if (is_dir($file)) {
                        $path[] = $file . '/*';
                    }
                    $files[] = $file;
                }
            }
            rsort($files);
            foreach ($files as $file) {
                if ($file != DIR_MODIFICATION . 'index.html') {
                    if (is_file($file)) {
                        @unlink($file);
                    } elseif (is_dir($file)) {
                        @rmdir($file);
                    }
                }
            }

            $json['text'] = $this->language->get('text_db');

            $json['next'] = str_replace('&amp;', '&', $this->url->link('tool/update/db', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function db() {
        $this->load->language('tool/update');

        $json = array();

        if (!$this->user->hasPermission('modify', 'tool/update')) {
            $json['error'] = $this->language->get('error_permission');
        }

        if (!$json) {
            $files = glob(DIR_APPLICATION .  'model/update/*.php');

            if ($files) {
                foreach ($files as $file) {
                    $update = basename($file, '.php');

                    $this->load->model('update/' . $update);

                    $this->{'model_update_' . $update}->update();
                }
            }

            $json['text'] = $this->language->get('text_clear');

            $json['next'] = str_replace('&amp;', '&', $this->url->link('tool/update/clear', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function clear() {
        $this->load->language('tool/update');

        $json = array();

        if (!$this->user->hasPermission('modify', 'tool/update')) {
            $json['error'] = $this->language->get('error_permission');
        }

        if (!$json) {
            $this->cache_update();

            $this->cache_ocmod();

            $this->cache_sass();

            $this->cache_twig();

            $json['success'] = sprintf($this->language->get('text_success'), str_replace("v", "", $this->session->data['version']));

            $this->maintenance_off();

            unset($this->session->data['version']);
            unset($this->session->data['maintenance']);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    private function maintenance_on() {
        $this->session->data['maintenance'] = $this->config->get('config_maintenance');
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSettingValue('config', 'config_maintenance', true);
    }

    private function maintenance_off() {
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSettingValue('config', 'config_maintenance', $this->session->data['maintenance']);
    }

    private function remove() {
        $directory = DIR_DOWNLOAD . 'opencartbrasil/install/';

        if (is_dir($directory)) {
            $files = array();

            $path = array($directory);

            while (count($path) != 0) {
                $next = array_shift($path);

                foreach (array_diff(scandir($next), array('.', '..')) as $file) {
                    $file = $next . '/' . $file;

                    if (is_dir($file)) {
                        $path[] = $file;
                    }

                    $files[] = $file;
                }
            }

            rsort($files);

            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                } elseif (is_dir($file)) {
                    @rmdir($file);
                }
            }

            if (is_dir($directory)) {
                @rmdir($directory);
            }
        }

        $files = array('admin/config-dist.php','config-dist.php','php.ini','.htaccess');

        foreach ($files as $file) {
            $file = DIR_DOWNLOAD . 'opencartbrasil/'.$file;
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }

    private function cache_update() {
        $directory = DIR_DOWNLOAD . 'opencartbrasil/';

        if (is_dir($directory)) {
            $files = array();

            $path = array($directory);
            while (count($path) != 0) {
                $next = array_shift($path);

                foreach (array_diff(scandir($next), array('.', '..')) as $file) {
                    $file = $next . '/' . $file;

                    if (is_dir($file)) {
                        $path[] = $file;
                    }

                    $files[] = $file;
                }
            }

            rsort($files);

            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                } elseif (is_dir($file)) {
                    @rmdir($file);
                }
            }

            if (is_dir($directory)) {
                @rmdir($directory);
            }
        }

        $file = DIR_DOWNLOAD . 'opencartbrasil.zip';
        if (is_file($file)) {
            @unlink($file);
        }
   }

    private function cache_ocmod() {
        $files = array();
        $path = array(DIR_MODIFICATION . '*');
        while (count($path) != 0) {
            $next = array_shift($path);
            foreach (glob($next) as $file) {
                if (is_dir($file)) {
                    $path[] = $file . '/*';
                }
                $files[] = $file;
            }
        }
        rsort($files);
        foreach ($files as $file) {
            if ($file != DIR_MODIFICATION . 'index.html') {
                if (is_file($file)) {
                    @unlink($file);
                } elseif (is_dir($file)) {
                    @rmdir($file);
                }
            }
        }

        $log = array();
        $log_error = array();

        $xml = array();
        $xml[] = file_get_contents(DIR_SYSTEM . 'modification.xml');

        $files = glob(DIR_SYSTEM . '*.ocmod.xml');

        if ($files) {
            foreach ($files as $file) {
                $xml[] = file_get_contents($file);
            }
        }

        $this->load->model('setting/modification');
        $results = $this->model_setting_modification->getModifications();
        foreach ($results as $result) {
            if ($result['status']) {
                $xml[] = $result['xml'];
            }
        }

        $modification = array();

        foreach ($xml as $xml) {
            if (empty($xml)){
                continue;
            }
            
            $dom = new DOMDocument('1.0', 'UTF-8');
            $dom->preserveWhiteSpace = false;
            $dom->loadXml($xml);

            $log[] = 'MOD: ' . $dom->getElementsByTagName('name')->item(0)->textContent;

            $log_mod = 'MOD: ' . $dom->getElementsByTagName('name')->item(0)->textContent;

            $recovery = array();

            if (isset($modification)) {
                $recovery = $modification;
            }

            $files = $dom->getElementsByTagName('modification')->item(0)->getElementsByTagName('file');
            foreach ($files as $file) {
                $operations = $file->getElementsByTagName('operation');

                $error_file = $file->getAttribute('error');

                $files = explode('|', $file->getAttribute('path'));
                foreach ($files as $file) {
                    $path = '';

                    if (substr($file, 0, 7) == 'catalog') {
                        $path = DIR_CATALOG . substr($file, 8);
                    }

                    if (substr($file, 0, 5) == 'admin') {
                        $path = DIR_APPLICATION . substr($file, 6);
                    }

                    if (substr($file, 0, 6) == 'system') {
                        $path = DIR_SYSTEM . substr($file, 7);
                    }

                    if ($path) {
                        $files = glob($path, GLOB_BRACE);
                        if ($files) {
                            foreach ($files as $file) {
                                if (substr($file, 0, strlen(DIR_CATALOG)) == DIR_CATALOG) {
                                    $key = 'catalog/' . substr($file, strlen(DIR_CATALOG));
                                }

                                if (substr($file, 0, strlen(DIR_APPLICATION)) == DIR_APPLICATION) {
                                    $key = 'admin/' . substr($file, strlen(DIR_APPLICATION));
                                }

                                if (substr($file, 0, strlen(DIR_SYSTEM)) == DIR_SYSTEM) {
                                    $key = 'system/' . substr($file, strlen(DIR_SYSTEM));
                                }

                                if (!isset($modification[$key])) {
                                    $content = file_get_contents($file);
                                    $modification[$key] = preg_replace('~\r?\n~', "\n", $content);
                                    $original[$key] = preg_replace('~\r?\n~', "\n", $content);

                                    $log[] = PHP_EOL . 'FILE: ' . $key;
                                }

                                foreach ($operations as $operation) {
                                    $error = $operation->getAttribute('error');

                                    $ignoreif = $operation->getElementsByTagName('ignoreif')->item(0);
                                    if ($ignoreif) {
                                        if ($ignoreif->getAttribute('regex') != 'true') {
                                            if (strpos($modification[$key], $ignoreif->textContent) !== false) {
                                                continue;
                                            }
                                        } else {
                                            if (preg_match($ignoreif->textContent, $modification[$key])) {
                                                continue;
                                            }
                                        }
                                    }

                                    $status = false;

                                    if ($operation->getElementsByTagName('search')->item(0)->getAttribute('regex') != 'true') {
                                        $search = $operation->getElementsByTagName('search')->item(0)->textContent;
                                        $trim = $operation->getElementsByTagName('search')->item(0)->getAttribute('trim');
                                        $index = $operation->getElementsByTagName('search')->item(0)->getAttribute('index');

                                        if (!$trim || $trim == 'true') {
                                            $search = trim($search);
                                        }

                                        $add = $operation->getElementsByTagName('add')->item(0)->textContent;
                                        $trim = $operation->getElementsByTagName('add')->item(0)->getAttribute('trim');
                                        $position = $operation->getElementsByTagName('add')->item(0)->getAttribute('position');
                                        $offset = $operation->getElementsByTagName('add')->item(0)->getAttribute('offset');

                                        if ($offset == '') {
                                            $offset = 0;
                                        }

                                        if ($trim == 'true') {
                                            $add = trim($add);
                                        }

                                        $log[] = 'CODE: ' . $search;

                                        if ($index !== '') {
                                            $indexes = explode(',', $index);
                                        } else {
                                            $indexes = array();
                                        }

                                        $i = 0;
                                        $lines = explode("\n", $modification[$key]);
                                        for ($line_id = 0; $line_id < count($lines); $line_id++) {
                                            $line = $lines[$line_id];
                                            $match = false;
                                            if (stripos($line, $search) !== false) {
                                                if (!$indexes) {
                                                    $match = true;
                                                } elseif (in_array($i, $indexes)) {
                                                    $match = true;
                                                }
                                                $i++;
                                            }

                                            if ($match) {
                                                switch ($position) {
                                                    default:
                                                    case 'replace':
                                                        $new_lines = explode("\n", $add);

                                                        if ($offset < 0) {
                                                            array_splice($lines, $line_id + $offset, abs($offset) + 1, array(str_replace($search, $add, $line)));
                                                            $line_id -= $offset;
                                                        } else {
                                                            array_splice($lines, $line_id, $offset + 1, array(str_replace($search, $add, $line)));
                                                        }
                                                        break;
                                                    case 'before':
                                                        $new_lines = explode("\n", $add);
                                                        array_splice($lines, $line_id - $offset, 0, $new_lines);
                                                        $line_id += count($new_lines);
                                                        break;
                                                    case 'after':
                                                        $new_lines = explode("\n", $add);
                                                        array_splice($lines, ($line_id + 1) + $offset, 0, $new_lines);
                                                        $line_id += count($new_lines);
                                                        break;
                                                }

                                                $log[] = 'LINE: ' . $line_id;
                                                $status = true;
                                            }
                                        }

                                        $modification[$key] = implode("\n", $lines);
                                    } else {
                                        $search = trim($operation->getElementsByTagName('search')->item(0)->textContent);
                                        $limit = $operation->getElementsByTagName('search')->item(0)->getAttribute('limit');
                                        $replace = trim($operation->getElementsByTagName('add')->item(0)->textContent);

                                        if (!$limit) {
                                            $limit = -1;
                                        }

                                        $match = array();
                                        preg_match_all($search, $modification[$key], $match, PREG_OFFSET_CAPTURE);

                                        if ($limit > 0) {
                                            $match[0] = array_slice($match[0], 0, $limit);
                                        }

                                        if ($match[0]) {
                                            $log[] = 'REGEX: ' . $search;
                                            for ($i = 0; $i < count($match[0]); $i++) {
                                                $log[] = 'LINE: ' . (substr_count(substr($modification[$key], 0, $match[0][$i][1]), "\n") + 1);
                                            }
                                            $status = true;
                                        }

                                        $modification[$key] = preg_replace($search, $replace, $modification[$key], $limit);
                                    }

                                    if (!$status) {
                                        if ($error != 'skip') {
                                            $log_error[] = PHP_EOL . 'ERROR: CODE NOT FOUND!';
                                            $log_error[] = 'FILE: ' . $key;
                                            $log_error[] = 'CODE: ' . $search;
                                            $log_error[] = $log_mod;
                                        }

                                        if ($error == 'abort') {
                                            $modification = $recovery;
                                            $log[] = 'NOT FOUND - ABORTING!';
                                            break 5;
                                        } elseif ($error == 'skip') {
                                            $log[] = 'NOT FOUND - OPERATION SKIPPED!';
                                            continue;
                                        } else {
                                            $log[] = 'NOT FOUND - OPERATIONS ABORTED!';
                                             break;
                                        }
                                    }
                                }
                            }
                        } else {
                            if ($error_file != 'skip') {
                                $log_error[] = PHP_EOL . 'ERROR: FILE NOT FOUND!';
                                $log_error[] = 'FILE: ' . $path;
                                $log_error[] = $log_mod;
                            }
                        }
                    }
                }
            }

            $log[] = '----------------------------------------------------------------';
        }

        $ocmod = new Log('ocmod.log');
        $ocmod->write(implode("\n", $log));

        $log_error_path = DIR_LOGS . 'error_ocmod.log';
        if (file_exists($log_error_path)) {
            @unlink($log_error_path);
        }

        if ($log_error) {
            $ocmod = new Log('error_ocmod.log');
            $ocmod->write(implode("\n", $log_error));
        }

        foreach ($modification as $key => $value) {
            if ($original[$key] != $value) {
                $path = '';

                $directories = explode('/', dirname($key));
                foreach ($directories as $directory) {
                    $path = $path . '/' . $directory;

                    if (!is_dir(DIR_MODIFICATION . $path)) {
                        @mkdir(DIR_MODIFICATION . $path, 0777);
                    }
                }

                $handle = @fopen(DIR_MODIFICATION . $key, 'w');
                fwrite($handle, $value);
                fclose($handle);
            }
        }
    }

    private function cache_sass() {
        $file = DIR_APPLICATION  . 'view/stylesheet/bootstrap.css';

        if (is_file($file) && is_file(DIR_APPLICATION . 'view/stylesheet/sass/_bootstrap.scss')) {
            @unlink($file);
        }

        $files = glob(DIR_CATALOG  . 'view/theme/*/stylesheet/sass/_bootstrap.scss');

        foreach ($files as $file) {
            $file = substr($file, 0, -21) . '/bootstrap.css';

            if (is_file($file)) {
                @unlink($file);
            }
        }
    }

    private function cache_twig() {
        $directories = glob(DIR_CACHE . '*', GLOB_ONLYDIR);

        if ($directories) {
            foreach ($directories as $directory) {
                $files = glob($directory . '/*');

                foreach ($files as $file) {
                    if (is_file($file)) {
                        @unlink($file);
                    }
                }

                if (is_dir($directory)) {
                    @rmdir($directory);
                }
            }
        }
    }
}
