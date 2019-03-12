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
        $this->mysql = new mysqli("mysql", "root", "");
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

    public function createNewProject($params)
    {
        $stack_name =  $params['project'] . '-' . pathinfo($params['template'], PATHINFO_FILENAME);
        $dir_project = $this->dir_projects . DIRECTORY_SEPARATOR . $stack_name;
        $dir_www = $dir_project . DIRECTORY_SEPARATOR . 'www';
        `mkdir -m 777 -p $dir_project`;
        `mkdir -m 777 -p $dir_www`;

        $template = $this->dir_templates . DIRECTORY_SEPARATOR . $params['template'];
        $file = $dir_project. DIRECTORY_SEPARATOR . 'docker-compose.yml';

        file_put_contents($file, str_replace(array('{{stack_name}}','{{hostname}}'), array($stack_name, $this->hostname), file_get_contents($template)));

        file_put_contents($dir_www.DIRECTORY_SEPARATOR.'index.php', str_replace(array('{{stack_name}}','{{hostname}}'), array($stack_name, $this->hostname), file_get_contents($this->dir.DIRECTORY_SEPARATOR.'env'.DIRECTORY_SEPARATOR.'index.php')));

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
            $this->createNewProject($params);
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