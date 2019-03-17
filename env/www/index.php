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
//print_r($env->getAdminDir('promo-ps1751'));
//echo '</pre>';
?>
<div class="container">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <ul class="nav navbar-nav">
                <li><a href="<?php echo $_SERVER['REQUEST_SCHEME'].':'.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.$key.'elk.'.$env->hostname; ?>" target="_blank">ELK</a></li>
                <li><a href="<?php echo $_SERVER['REQUEST_SCHEME'].':'.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.$key.$env->hostname; ?>:9000" target="_blank">Portainer</a></li>
                <li><a href="<?php echo $_SERVER['REQUEST_SCHEME'].':'.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.$key.$env->hostname; ?>:8080" target="_blank">Traefik</a></li>
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
            <th>Actiction</th>
        </tr>
        </thead>
            <tbody>
                <tr>
                    <td>
                        <input type="text" name="stack_name" value="" placeholder="Enter stack name" class="form-control" />
                    </td>
                    <td>
                        <select name="template" class="form-control">
                            <option value="" >Select project template</option>
                            <?php foreach ($env->templates as $key){ ?>
                                <option value="<?php echo $key; ?>" ><?php echo pathinfo($key, PATHINFO_FILENAME); ?></option>
                            <?php } ?>
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
