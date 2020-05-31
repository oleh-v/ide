<?php

class Env
{
    public $dir;

    public $dir_templates;

    public $dir_projects;

    public $stack;

    public $hostname;

    protected $mysql;

    public $templates;

    public $projects;

    public function __construct($params)
    {
        $this->getConfig($params);
        $this->stack = $this->getStacks();
        $this->templates = $this->getTemplates($this->dir_templates);
        $this->projects = $this->getProjects($this->dir_projects);
        $this->mysql = new mysqli("mysql", "root", "root");
    }

    public function getConfig($params)
    {
        $config = json_decode(file_get_contents($params), true);

        $this->dir = $config['documentRoot'];

        $this->dir_templates = $config['documentRoot'].DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'templates';

        $this->dir_projects = $config['documentRoot'].DIRECTORY_SEPARATOR.'projects';

        $this->hostname = str_replace('env.', '', $_SERVER['SERVER_NAME']);
    }

    public function getStacks()
    {
        $data = array_filter(explode("\n", `docker stack ls | awk '{if(NR>1)print $1}'`));

        return $data;
    }

    public function getTemplates($params)
    {
        $data = array();
        if ($templates = glob($params .DIRECTORY_SEPARATOR.'*.{yml,YML}', GLOB_BRACE)) {
            foreach ($templates as $key => $template) {
                $data[] = basename($template);
            }
        }

        return $data;
    }

    public function getProjects($params)
    {
        $data = array();
        if ($projects = glob($params.DIRECTORY_SEPARATOR.'*'.DIRECTORY_SEPARATOR.'docker-compose.yml', GLOB_BRACE)) {
            foreach ($projects as $key => $project) {
                $data[] = basename(dirname($project));
            }
        }

        return $data;
    }

    public function getAdminDir($params)
    {
        $adminDir = '';
        if ($data = glob($this->dir_projects.DIRECTORY_SEPARATOR.$params.DIRECTORY_SEPARATOR.'www'.DIRECTORY_SEPARATOR.'*admin*', GLOB_ONLYDIR)) {
            $adminDir = basename($data[0]);
        }

        return $adminDir;
    }

    public function getModules($params)
    {
        $data = array();
        if ($projects = glob($params.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR)) {
            foreach ($projects as $key => $project) {
                //$data[] = basename(dirname($project));
                $data[] = basename($project);
            }
        }

        return $data;
    }

/*
    public function createNewProject($params)
    {
        $stack_name =  $params['project'] . '-' . pathinfo($params['template'], PATHINFO_FILENAME);
        $dir_project = $this->dir_projects . DIRECTORY_SEPARATOR . $stack_name;
        $dir_www = $dir_project . DIRECTORY_SEPARATOR . 'www';
        `mkdir -m 777 -p $dir_project`;
        `mkdir -m 777 -p $dir_www/modules`;

        $template = $this->dir_templates . DIRECTORY_SEPARATOR . $params['template'];
        $file = $dir_project. DIRECTORY_SEPARATOR . 'docker-compose.yml';

        file_put_contents($file, str_replace(array('{{stack_name}}','{{hostname}}','{{dir}}'), array($stack_name, $this->hostname, $this->dir), file_get_contents($template)));

        file_put_contents($dir_www.DIRECTORY_SEPARATOR.'index.html', str_replace(array('{{stack_name}}','{{hostname}}'), array($stack_name, $this->hostname), file_get_contents($this->dir.DIRECTORY_SEPARATOR.'env'.DIRECTORY_SEPARATOR.'index.html')));

        if (substr($params['template'], 0, strlen("ps")) === "ps") {
            $platform = "prestashop";
            $modules = $this->getModules($this->dir.DIRECTORY_SEPARATOR.'code'.DIRECTORY_SEPARATOR.$platform.DIRECTORY_SEPARATOR.'modules');

            foreach ($modules as $module) {
                `ln -sfn $this->dir/code/$platform/modules/$module $dir_www/modules/$module`;
            }

        }

        $this->mysql->query("CREATE DATABASE `$stack_name`");
        $this->stackStart($stack_name);
    }
*/

    public function createNewProjectPrestashop($params)
    {
        $platform = pathinfo($params['template'], PATHINFO_FILENAME);
        $version = $params['version'];
        $stack_name =  $params['project'] . '-' . pathinfo($params['template'], PATHINFO_FILENAME) . preg_replace("/[^0-9]/", '', $version);
        $dir_project = $this->dir_projects . DIRECTORY_SEPARATOR . $stack_name;
        $dir_www = $dir_project . DIRECTORY_SEPARATOR . 'www';
        `mkdir -m 777 -p $dir_project`;
        `mkdir -m 777 -p $dir_www/modules`;

        $template = $this->dir_templates . DIRECTORY_SEPARATOR . $params['template'];
        $compose = $dir_project. DIRECTORY_SEPARATOR . 'docker-compose.yml';
        file_put_contents($compose, str_replace(array('{{stack_name}}','{{hostname}}','{{dir}}','{{platform}}', '{{version}}'), array($stack_name, $this->hostname, $this->dir, $platform, $version), file_get_contents($template)));

        file_put_contents($dir_project . DIRECTORY_SEPARATOR . 'php.ini', file_get_contents($this->dir_templates . DIRECTORY_SEPARATOR . $platform . DIRECTORY_SEPARATOR . 'php.ini'));

        // Dockerfile (install memcached)
        $dockerfile = $dir_project. DIRECTORY_SEPARATOR . 'Dockerfile';
        file_put_contents($dockerfile, str_replace(array('{{version}}'), array($version), file_get_contents($this->dir_templates . DIRECTORY_SEPARATOR . $platform . DIRECTORY_SEPARATOR . 'Dockerfile')));

        `docker build -t prestashop_prestashop:$version - < $dockerfile || true`;

        $modules = $this->getModules($this->dir.DIRECTORY_SEPARATOR.'code'.DIRECTORY_SEPARATOR.$platform.DIRECTORY_SEPARATOR.'modules');
        foreach ($modules as $module) {
            `ln -sfn $this->dir/code/$platform/modules/$module $dir_www/modules/$module`;
        }

        $this->mysql->query("CREATE DATABASE `$stack_name`");

        $this->stackStart($stack_name);
    }

    public function createNewProjectOpencart($params)
    {
        $platform = pathinfo($params['template'], PATHINFO_FILENAME);
        $version = $params['version'];
        $version_number = preg_replace("/[^0-9]/", '', $version);
        $upload_path = "/tmp/upload/*";
        if ($version_number < 2200) {
            $oc_url = "https://github.com/opencart/opencart/archive/" . $version . ".zip";
            $upload_path = "/tmp/opencart-" . $version . "/upload/*";
        } elseif ($version_number >= 2200 && $version_number <= 3011 ) {
            $oc_url = "https://github.com/opencart/opencart/releases/download/".$version."/" . $version . "-compiled.zip";
            if ($version_number == 2200) {
                $upload_path = "/tmp/" . $version . "-compiled/upload/*";
            }
        } elseif ($version_number == 3012) {
            $oc_url = "https://github.com/opencart/opencart/releases/download/".$version."/" . $version . "-opencart.zip";
        } elseif ($version_number == 3020) {
            $oc_url = "https://github.com/opencart/opencart/releases/download/".$version."/" . $version . "-OpenCart.zip";
        } elseif ($version_number > 3020) {
            $oc_url = "https://github.com/opencart/opencart/releases/download/".$version."/opencart-".$version.".zip";
            $upload_path = "/tmp/upload/*";
        }
        $stack_name =  $params['project'] . '-' . pathinfo($params['template'], PATHINFO_FILENAME) . preg_replace("/[^0-9]/", '', $version);
        $dir_project = $this->dir_projects . DIRECTORY_SEPARATOR . $stack_name;
        `mkdir -m 777 -p $dir_project/www`;

        $template = $this->dir_templates . DIRECTORY_SEPARATOR . $params['template'];
        $compose = $dir_project. DIRECTORY_SEPARATOR . 'docker-compose.yml';
        file_put_contents($compose, str_replace(array('{{stack_name}}','{{hostname}}','{{dir}}','{{platform}}', '{{version}}'), array($stack_name, $this->hostname, $this->dir, $platform, $version), file_get_contents($template)));

        file_put_contents($dir_project . DIRECTORY_SEPARATOR . 'php.ini', file_get_contents($this->dir_templates . DIRECTORY_SEPARATOR . $platform . DIRECTORY_SEPARATOR . 'php.ini'));

        $inittemplate = $this->dir_templates . DIRECTORY_SEPARATOR . $platform . DIRECTORY_SEPARATOR . 'init.sh';
        $init = $dir_project. DIRECTORY_SEPARATOR . 'init.sh';
        file_put_contents($init, str_replace(array('{{oc_url}}','{{upload_path}}'), array($oc_url, $upload_path), file_get_contents($inittemplate)));

        $dockertemplate = $this->dir_templates . DIRECTORY_SEPARATOR . $platform . DIRECTORY_SEPARATOR . 'Dockerfile';
        $dockerfile = $dir_project. DIRECTORY_SEPARATOR . 'Dockerfile';
        file_put_contents($dockerfile, str_replace(array('{{version}}'), array($version), file_get_contents($dockertemplate)));

        `docker build -t env-opencart:$version -f $dockerfile $dir_project`;

        $this->mysql->query("CREATE DATABASE `$stack_name`");

        $this->stackStart($stack_name);
    }

    public function createNewProjectPhpCustomProject($params)
    {
        $platform = pathinfo($params['template'], PATHINFO_FILENAME);
        $version = $params['version'];
        $stack_name =  $params['project'] . '-' . pathinfo($params['template'], PATHINFO_FILENAME) . preg_replace("/[^0-9]/", '', $version);
        $dir_project = $this->dir_projects . DIRECTORY_SEPARATOR . $stack_name;
        `mkdir -m 777 -p $dir_project/www`;

        $template = $this->dir_templates . DIRECTORY_SEPARATOR . $params['template'];
        $compose = $dir_project. DIRECTORY_SEPARATOR . 'docker-compose.yml';
        file_put_contents($compose, str_replace(array('{{stack_name}}','{{hostname}}','{{dir}}','{{platform}}', '{{version}}'), array($stack_name, $this->hostname, $this->dir, $platform, $version), file_get_contents($template)));

        file_put_contents($dir_project . DIRECTORY_SEPARATOR . 'php.ini', file_get_contents($this->dir_templates . DIRECTORY_SEPARATOR . $platform . DIRECTORY_SEPARATOR . 'php.ini'));

        file_put_contents($dir_project . DIRECTORY_SEPARATOR . 'www' . DIRECTORY_SEPARATOR . 'index.php', file_get_contents($this->dir_templates . DIRECTORY_SEPARATOR . $platform . DIRECTORY_SEPARATOR . 'index.php'));

        $this->mysql->query("CREATE DATABASE `$stack_name`");

        $this->stackStart($stack_name);
    }


    public function createNewProjectWordpress($params)
    {
        $platform = pathinfo($params['template'], PATHINFO_FILENAME);
        $version = $params['version'];
        $stack_name =  $params['project'] . '-' . pathinfo($params['template'], PATHINFO_FILENAME) . preg_replace("/[^0-9]/", '', $version);
        $dir_project = $this->dir_projects . DIRECTORY_SEPARATOR . $stack_name;
        `mkdir -m 777 -p $dir_project/www`;

        $template = $this->dir_templates . DIRECTORY_SEPARATOR . $params['template'];
        $compose = $dir_project. DIRECTORY_SEPARATOR . 'docker-compose.yml';
        file_put_contents($compose, str_replace(array('{{stack_name}}','{{hostname}}','{{dir}}','{{platform}}', '{{version}}'), array($stack_name, $this->hostname, $this->dir, $platform, $version), file_get_contents($template)));

        $this->mysql->query("CREATE DATABASE `$stack_name`");

        $this->stackStart($stack_name);
    }

    public function createNewProjectJoomla($params)
    {
        $platform = pathinfo($params['template'], PATHINFO_FILENAME);
        $version = $params['version'];
        $stack_name =  $params['project'] . '-' . pathinfo($params['template'], PATHINFO_FILENAME) . preg_replace("/[^0-9]/", '', $version);
        $dir_project = $this->dir_projects . DIRECTORY_SEPARATOR . $stack_name;
        `mkdir -m 777 -p $dir_project/www`;

        $template = $this->dir_templates . DIRECTORY_SEPARATOR . $params['template'];
        $compose = $dir_project. DIRECTORY_SEPARATOR . 'docker-compose.yml';
        file_put_contents($compose, str_replace(array('{{stack_name}}','{{hostname}}','{{dir}}','{{platform}}', '{{version}}'), array($stack_name, $this->hostname, $this->dir, $platform, $version), file_get_contents($template)));

        $this->mysql->query("CREATE DATABASE `$stack_name`");

        $this->stackStart($stack_name);
    }


    public function stackStart($params)
    {
        $docker_compose_path = $this->dir_projects . DIRECTORY_SEPARATOR . $params . DIRECTORY_SEPARATOR . 'docker-compose.yml';
        `docker stack deploy -c $docker_compose_path $params`;

    }

    public function stackStop($params)
    {
        `docker stack rm $params`;
    }

    public function stackDelete($params)
    {
        $this->stackStop($params);
        $project_path = $this->dir_projects . DIRECTORY_SEPARATOR . $params;
        `rm -rf $project_path`;
        $this->mysql->query("DROP DATABASE `$params`");
    }

    public static function isSubmit($submit)
    {
        return (
            isset($_POST[$submit]) || isset($_POST[$submit.'_x']) || isset($_POST[$submit.'_y'])
            || isset($_GET[$submit]) || isset($_GET[$submit.'_x']) || isset($_GET[$submit.'_y'])
        );
    }

    public static function getValue($key, $default_value = false)
    {
        if (!isset($key) || empty($key) || !is_string($key)) {
            return false;
        }

        $value = (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $default_value));

        if (is_string($value)) {
            return stripslashes(urldecode(preg_replace('/((\%5C0+)|(\%00+))/i', '', urlencode($value))));
        }

        return $value;
    }

    public static function redirect($url)
    {
        echo '<META HTTP-EQUIV=REFRESH CONTENT="1; '.$url.'">';
    }

    public static function url()
    {
        $url = $_SERVER['REQUEST_SCHEME'].':'.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.$_SERVER['SERVER_NAME'];

        return $url;
    }

    public function caseSubmit()
    {
        if ($this->isSubmit('submitNewProject')) {

            $params['template'] = $this->getValue('template');
            $params['project'] = $this->getValue('stack_name');
            $params['version'] = $this->getValue('version');
            if ($params['template'] == 'prestashop.yml') {
                $this->createNewProjectPrestashop($params);
            } elseif ($params['template'] == 'opencart.yml') {
                $this->createNewProjectOpencart($params);
            } elseif ($params['template'] == 'wordpress.yml') {
                $this->createNewProjectWordpress($params);
            } elseif ($params['template'] == 'php-custom-project.yml') {
                $this->createNewProjectPhpCustomProject($params);
            } elseif ($params['template'] == 'joomla.yml') {
                $this->createNewProjectJoomla($params);
            }
            $this->redirect($this->url());

        } elseif ($this->isSubmit('submitStackStart')) {

            $stack_name = $this->getValue('stack_name');
            $this->stackStart($stack_name);
            $this->redirect($this->url());

        } elseif ($this->isSubmit('submitStackStop')) {

            $stack_name = $this->getValue('stack_name');
            $this->stackStop($stack_name);
            $this->redirect($this->url());

        } elseif ($this->isSubmit('submitStackDelete')) {

            $stack_name = $this->getValue('stack_name');
            $this->stackDelete($stack_name);
            $this->redirect($this->url());

        }
    }



}