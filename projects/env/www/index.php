<?php
echo '<h1>Hello World!</h1>';
echo '<hr>';
$output = shell_exec('docker stack ls');
echo "<pre>$output</pre>";

echo '<hr>';

var_dump($output);

echo '<hr>';

$a = `docker stack ls | awk '{if(NR>1)print $1}'`;
echo "<pre>$a</pre>";
echo '<hr>';
$array  =   array_filter(explode("\n", $a));
//$array  =   explode("\n", $a);
echo "<pre>";
print_r($array);
echo "</pre>";
echo '<hr>';

//echo `pwd`;
//$c = `docker stack deploy -c /host/projects/ex2/docker-compose.yml ex2`;
//echo "<pre>$c</pre>";
//$out = exec('docker service update ex2_env');
//$out = `ls -l /`;
//echo "<pre>$out</pre>";

	$scriptName = "docker stack deploy -c /host/projects/ex2/docker-compose.yml ex2";
	exec($scriptName,$out);
	foreach($out as $key => $value)
	{
	    echo $key." ".$value."<br>";
	}

?>