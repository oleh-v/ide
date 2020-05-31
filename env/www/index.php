<!DOCTYPE html>
<html lang="en">
<head>
    <title>IDE</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>

    <style>
        .navbar .nav {
            margin: 0;
            display: flex;
            flex-direction: row;
            width: 85%;
        }

        .navbar .nav li {
            flex: 1;
        }

        .navbar .nav li a {
            font-weight: bold;
            text-align: center;
            flex: 1;
        }
    </style>
</head>
<body>
<?php
//echo '<h1>Hello World!</h1>';
//echo '<hr>';
chdir(__DIR__);
require_once 'autoload.php';
$config = 'config.json';
$env = new Env($config);
//echo '<pre>';
//print_r($_POST);
//echo '</pre>';
?>
<div class="container">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <ul class="nav navbar-nav">
                <li><a href="<?php echo $_SERVER['REQUEST_SCHEME'].':'.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.$env->hostname; ?>:9000" target="_blank">Portainer</a></li>
                <li><a href="<?php echo $_SERVER['REQUEST_SCHEME'].':'.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.$env->hostname; ?>:8080" target="_blank">Traefik</a></li>
                <li><a href="<?php echo $_SERVER['REQUEST_SCHEME'].':'.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.'pma.'.$env->hostname; ?>" target="_blank">PHPMyAdmin</a></li>
            </ul>
        </div>
    </nav>
    <h2>Integrated Development Environment</h2>
    <br>
    <h4>Create new project</h4>
    <form action="/index.php" method="POST">
    <table class="table">
        <thead>
        <tr>
            <th>Project Name</th>
            <th>Template</th>
            <th>Version</th>
            <th>Action</th>
        </tr>
        </thead>
            <tbody>
                <tr>
                    <td>
                        <input type="text" name="stack_name" value="" placeholder="Enter project name" class="form-control" />
                    </td>
                    <td>
                        <select name="template" class="form-control" id="sel1" onchange="giveSelection(this.value)" >
                            <option value="" >Select project template</option>
                            <?php foreach ($env->templates as $key){ ?>
                                <option value="<?php echo $key; ?>" ><?php echo pathinfo($key, PATHINFO_FILENAME); ?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <select name="version" class="form-control" id="sel2">
                            <!-- Joomla -->
                            <option data-option="joomla.yml" value="3.4-apache">3.4</option>
                            <option data-option="joomla.yml" value="3.5-apache">3.5</option>
                            <option data-option="joomla.yml" value="3.6-apache">3.6</option>
                            <option data-option="joomla.yml" value="3.7-apache">3.7</option>
                            <option data-option="joomla.yml" value="3.8-apache">3.8</option>
                            <option data-option="joomla.yml" value="3.9-apache">3.9</option>
                            <!-- Opencart -->
                            <option data-option="opencart.yml" value="2.0.0.0">2.0.0.0</option>
                            <option data-option="opencart.yml" value="2.0.1.1">2.0.1.1</option>
                            <option data-option="opencart.yml" value="2.0.2.0">2.0.2.0</option>
                            <option data-option="opencart.yml" value="2.0.3.1">2.0.3.1</option>
                            <option data-option="opencart.yml" value="2.1.0.1">2.1.0.1</option>
                            <option data-option="opencart.yml" value="2.1.0.2">2.1.0.2</option>
                            <option data-option="opencart.yml" value="2.2.0.0">2.2.0.0</option>
                            <option data-option="opencart.yml" value="2.3.0.2">2.3.0.2</option>
                            <option data-option="opencart.yml" value="3.0.0.0">3.0.0.0</option>
                            <option data-option="opencart.yml" value="3.0.1.1">3.0.1.1</option>
                            <option data-option="opencart.yml" value="3.0.1.2">3.0.1.2</option>
                            <option data-option="opencart.yml" value="3.0.2.0">3.0.2.0</option>
                            <option data-option="opencart.yml" value="3.0.3.0">3.0.3.0</option>
                            <option data-option="opencart.yml" value="3.0.3.1">3.0.3.1</option>
                            <option data-option="opencart.yml" value="3.0.3.2">3.0.3.2</option>
                            <option data-option="opencart.yml" value="3.0.3.3">3.0.3.3</option>
                            <!-- PHP App -->
                            <option data-option="php-custom-project.yml" value="5.2-apache">PHP 5.2</option>
                            <option data-option="php-custom-project.yml" value="5.3-apache">PHP 5.3</option>
                            <option data-option="php-custom-project.yml" value="5.4-apache">PHP 5.4</option>
                            <option data-option="php-custom-project.yml" value="5.5-apache">PHP 5.5</option>
                            <option data-option="php-custom-project.yml" value="5.6-apache">PHP 5.6</option>
                            <option data-option="php-custom-project.yml" value="7.0-apache">PHP 7.0</option>
                            <option data-option="php-custom-project.yml" value="7.1-apache">PHP 7.1</option>
                            <option data-option="php-custom-project.yml" value="7.2-apache">PHP 7.2</option>
                            <option data-option="php-custom-project.yml" value="7.3-apache">PHP 7.3</option>
                            <option data-option="php-custom-project.yml" value="7.4-apache">PHP 7.4</option>
                            <!-- Prestashop -->
                            <option data-option="prestashop.yml" value="1.6.1.24">1.6.1.24</option>
                            <option data-option="prestashop.yml" value="1.7.0.6">1.7.0.6</option>
                            <option data-option="prestashop.yml" value="1.7.1.2">1.7.1.2</option>
                            <option data-option="prestashop.yml" value="1.7.2.5">1.7.2.5</option>
                            <option data-option="prestashop.yml" value="1.7.3.4">1.7.3.4</option>
                            <option data-option="prestashop.yml" value="1.7.4.4">1.7.4.4</option>
                            <option data-option="prestashop.yml" value="1.7.5.1">1.7.5.1</option>
                            <option data-option="prestashop.yml" value="1.7.6.5">1.7.6.5</option>
                            <option data-option="prestashop.yml" value="1.7-7.2-apache">1.7-latest_php7.2_apache</option>
                            <!-- Wordpress -->
                            <option data-option="wordpress.yml" value="3.9">3.9</option>
                            <option data-option="wordpress.yml" value="4.0-apache">4.0</option>
                            <option data-option="wordpress.yml" value="4.1-apache">4.1</option>
                            <option data-option="wordpress.yml" value="4.2-apache">4.2</option>
                            <option data-option="wordpress.yml" value="4.3-apache">4.3</option>
                            <option data-option="wordpress.yml" value="4.4-apache">4.4</option>
                            <option data-option="wordpress.yml" value="4.5-apache">4.5</option>
                            <option data-option="wordpress.yml" value="4.6-php5.6-apache">4.6</option>
                            <option data-option="wordpress.yml" value="4.7-php5.6-apache">4.7</option>
                            <option data-option="wordpress.yml" value="4.8-php5.6-apache">4.8</option>
                            <option data-option="wordpress.yml" value="4.9-php5.6-apache">4.9</option>
                            <option data-option="wordpress.yml" value="5.0-php7.2-apache">5.0</option>
                            <option data-option="wordpress.yml" value="5.1-php7.2-apache">5.1</option>
                            <option data-option="wordpress.yml" value="5.2-php7.2-apache">5.2</option>
                            <option data-option="wordpress.yml" value="5.3-php7.2-apache">5.3</option>
                            <option data-option="wordpress.yml" value="5.4-php7.2-apache">5.4</option>
                        </select>
                    </td>
                    <td>
                        <button type="submit" name="submitNewProject" class="btn btn-success">Deploy</button>
                    </td>
                </tr>
            </tbody>

    </table>
    </form>

    <br>
    <h4>Existing projects list</h4>
    <table class="table">
        <thead>
        <tr>
            <th>Stack Name</th>
            <th>Back URL</th>
            <th>Front URL</th>
            <th>State</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
            <?php foreach ($env->projects as $key){ ?>
                <?php $state = (in_array($key, $env->stack))  ? "Runnig" : "Stopped" ; ?>
                <tr <?php echo (in_array($key, $env->stack))  ?'class="success"' : 'class="info"' ; ?>  >
                    <td><?php echo $key; ?></td>
                    <td><a href="<?php echo $_SERVER['REQUEST_SCHEME'].':'.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.$key.'.'.$env->hostname.DIRECTORY_SEPARATOR.$env->getAdminDir($key); ?>" target="_blank"><?php echo $_SERVER['REQUEST_SCHEME'].':'.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.$key.'.'.$env->hostname.DIRECTORY_SEPARATOR.$env->getAdminDir($key); ?></td>
                    <td><a href="<?php echo $_SERVER['REQUEST_SCHEME'].':'.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.$key.'.'.$env->hostname; ?>" target="_blank"><?php echo $_SERVER['REQUEST_SCHEME'].':'.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.$key.'.'.$env->hostname; ?></td>
                    <td><?php echo $state; ?></td>
                    <td>
                        <a href="<?php echo $_SERVER['REQUEST_SCHEME'].':'.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.$_SERVER['SERVER_NAME'].DIRECTORY_SEPARATOR ; ?>index.php?stack_name=<?php echo $key; ?>&submitStackStart" class="btn btn-primary">Start / Update</a>
                        <a href="<?php echo $_SERVER['REQUEST_SCHEME'].':'.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.$_SERVER['SERVER_NAME'].DIRECTORY_SEPARATOR ; ?>index.php?stack_name=<?php echo $key; ?>&submitStackStop" class="btn btn-warning">Stop</a>
                        <a href="<?php echo $_SERVER['REQUEST_SCHEME'].':'.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.$_SERVER['SERVER_NAME'].DIRECTORY_SEPARATOR ; ?>index.php?stack_name=<?php echo $key; ?>&submitStackDelete" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
            <?php } ?>
            <?php $env->caseSubmit(); ?>
        </tbody>
    </table>
</div>



</body>
</html>
<script>
    var sel1 = document.querySelector('#sel1');
    var sel2 = document.querySelector('#sel2');
    var options2 = sel2.querySelectorAll('option');

    function giveSelection(selValue) {
        sel2.innerHTML = '';
        for(var i = 0; i < options2.length; i++) {
            if(options2[i].dataset.option === selValue) {
                sel2.appendChild(options2[i]);
            }
        }
    }

    giveSelection(sel1.value);
</script>

