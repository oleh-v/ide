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
echo '<h1>Hello World!</h1>';
echo '<hr>';

chdir(__DIR__);
require_once 'autoload.php';

$config = 'config.json';

$env = new Env($config);

echo '<pre>';
print_r($env->projects);
echo '<br>';
echo '</pre>';
?>

<div class="container">
    <h2>Contextual Classes</h2>
    <p>Contextual classes can be used to color table rows or table cells. The classes that can be used are: .active, .success, .info, .warning, and .danger.</p>
    <table class="table">
        <thead>
        <tr>
            <th>Stack</th>
            <th>State</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>


            <?php
            foreach ($env->projects as $key){
                echo '<tr>';
                echo '<td>'.$key.'</td>';
                echo '<td>Status</td>';
                echo '<td>Update / Stop / Delete</td>';
                echo '</tr>';
            }
            ?>


        <tr class="success">
            <td>Success</td>
            <td>Doe</td>
            <td>john@example.com</td>
        </tr>
        <tr class="danger">
            <td>Danger</td>
            <td>Moe</td>
            <td>mary@example.com</td>
        </tr>
        <tr class="info">
            <td>Info</td>
            <td>Dooley</td>
            <td>july@example.com</td>
        </tr>
        <tr class="warning">
            <td>Warning</td>
            <td>Refs</td>
            <td>bo@example.com</td>
        </tr>
        <tr class="active">
            <td>Active</td>
            <td>Activeson</td>
            <td>act@example.com</td>
        </tr>
        </tbody>
    </table>
</div>



</body>
</html>
