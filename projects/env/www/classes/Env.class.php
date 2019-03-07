<?php

class Env
{
    public $dir_templates;

    public $dir_projects;

    public $stack;

    public $templates;

    public $projects;

    public function __construct($params)
    {
        $this->getConfig($params);
        $this->stack = $this->getStacks();
        $this->templates = $this->getTemplates($this->dir_templates);
        $this->projects = $this->getProjects($this->dir_projects);
    }

    public function getConfig($params)
    {
        $config = json_decode(file_get_contents($params), true);
        $this->dir_templates = $config['templates'];
        $this->dir_projects = $config['projects'];
    }

    public function getStacks()
    {
        $data = array_filter(explode("\n", `docker stack ls | awk '{if(NR>1)print $1}'`));

        return $data;
    }

    public function getTemplates($params)
    {
        if ($templates = glob($params, GLOB_BRACE)) {
            foreach ($templates as $key => $template) {
                $data[] = basename($template);
            }
        } else {
            $data[] = '';
        }

        return $data;
    }

    public function getProjects($params)
    {
        if ($projects = glob($params, GLOB_BRACE)) {
            foreach ($projects as $key => $project) {
                $data[] = basename(dirname($project));
            }
        } else {
            $data[] = '';
        }

        return $data;
    }

    public function render()
    {

    }

    public function createNewProject()
    {

    }

    public function stackStart()
    {

    }

    public function stackStop()
    {

    }

    public function stackDelete()
    {

    }

    public static function isSubmit($submit)
    {
        return (
            isset($_POST[$submit]) || isset($_POST[$submit.'_x']) || isset($_POST[$submit.'_y'])
            || isset($_GET[$submit]) || isset($_GET[$submit.'_x']) || isset($_GET[$submit.'_y'])
        );
    }

    protected function caseSubmit()
    {
        if ($this->isSubmit('submitNewProject')) {
            echo "submitNew";
        } elseif ($this->isSubmit('submitStackStart')) {
            echo "submitStart";
        } elseif ($this->isSubmit('submitStackStop')) {
            echo "submitStop";
        } elseif ($this->isSubmit('submitStackDelete')) {
            echo "submitDelete";
        }

    }

}