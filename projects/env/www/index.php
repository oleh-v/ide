<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bootstrap Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
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
    <h2>Contextual Classes</h2>
    <p>Contextual classes can be used to color table rows or table cells. The classes that can be used are: .active, .success, .info, .warning, and .danger.</p>
    <form action="/index.php" method="POST">
    <table class="table">
        <thead>
        <tr>
            <th>New Project Name</th>
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
                                <option value="<?php echo $key; ?>" ><?php echo $key; ?></option>
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
    <h4>Contextual Classes</h4>
    <table class="table">
        <thead>
        <tr>
            <th>Stack Name</th>
            <th>State</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
            <?php foreach ($env->projects as $key){ ?>
                <?php $state = (in_array($key, $env->stack))  ? "Runnig" : "Stopped" ; ?>
                <tr <?php echo (in_array($key, $env->stack))  ?'class="success"' : 'class="info"' ; ?>  >
                    <td><?php echo $key; ?></td>
                    <td><?php echo $state; ?></td>
                    <td>
                        <a href="http://env.ide/index.php?stack_name=<?php echo $key; ?>&submitStackStart" class="btn btn-primary">Start / Update</a>
                        <a href="http://env.ide/index.php?stack_name=<?php echo $key; ?>&submitStackStop" class="btn btn-warning">Stop</a>
                        <a href="http://env.ide/index.php?stack_name=<?php echo $key; ?>&submitStackDelete" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
            <?php } ?>
            <?php $env->caseSubmit(); ?>
        </tbody>
    </table>
</div>



</body>
</html>
