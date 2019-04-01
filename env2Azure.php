<?php

$appname = null;
$resourceGroup = null;
$pathtoenv = ".env";

if (!isset($argc) || ($argc <=1 ))
{
	showhelp();
}

//parse command line arguments
for ($i = 0; $i < $argc; $i++) {
		//echo "Argument #" . $i . " - " . $argv[$i] . "\n";
		//echo "$i appname check" .stripos($argv[$i],"-appname").PHP_EOL;
		//echo "$i appname check" .stripos($argv[$i],"-rg").PHP_EOL;
		if (stripos($argv[$i],"-appname") === 0)
		{
			$appname = substr($argv[$i], 9);
		}
		
		if (stripos($argv[$i],"-rg") === 0)
		{
			$resourceGroup = substr($argv[$i], 4);
		}
		
		if (stripos($argv[$i],"-pathtoenv") === 0)
		{
			$pathtoenv = substr($argv[$i], 11);
		}
}



//confirm we have both parameters before continuing.
if ($appname == null || $resourceGroup == null)
{
	showhelp();
}


echo PHP_EOL."appname [$appname], rg [$resourceGroup], pathtoenv [$pathtoenv]".PHP_EOL;


$file = fopen($pathtoenv, "r") or die ("unable to open .env file: $pathtoenv");

while (!feof($file))
{
    $oneline = trim(fgets($file));
    if (strpos($oneline,'='))
    {
      

        $array = explode('=',$oneline,2);
        
        if (strlen($array[1]) ==0 || strtolower($array[1]) == 'null' )
        {
           # echo "    this line is empty!".PHP_EOL;
        } else {
          #  echo "We have a valid line $oneline".PHP_EOL;

            $value = $array[1];
            $value = str_replace ("\"","",$value);
            $value = str_replace ("'","",$value);
			
			#replace common variable types so we don't expose these values by mistake on the public internet
			if ($array[0] == 'APP_ENV')   { $value = 'production'; }
		    if ($array[0] == 'APP_DEBUG') { $value = 'false'; }

            echo "az webapp config appsettings set --name ".$appname." --resource-group ". $resourceGroup. " --settings ".$array[0]."=\"".$value."\"".PHP_EOL; 
        }
    }
}

//write two specific settings for using with Azure App service on windows:
echo "az webapp config appsettings set --name ".$appname." --resource-group ". $resourceGroup. " --settings SCM_REPOSITORY_PATH=..\\repository" .PHP_EOL; 
echo "az webapp config appsettings set --name ".$appname." --resource-group ". $resourceGroup. " --settings SCM_TARGET_PATH=.." .PHP_EOL; 



fclose($file);

echo PHP_EOL."NOTE APP_DEBUG has been set to false so you don't expose these variables on an error page in azure!".PHP_EOL;
echo "NOTE APP_ENV has been set to 'production'".PHP_EOL;
echo "IF YOU'RE USING AN AZURE SECRET FOR AUTHENTICATION check that it's not truncated in any way".PHP_EOL;


function showhelp()
{
	echo PHP_EOL."Call this script with the following parameters:".PHP_EOL;
	echo " env2Azure.php -appname=<appname> -rg=<resourcegroupname> [optional: -pathtoenv=<path>]".PHP_EOL.PHP_EOL;
	echo "EXAMPLE:".PHP_EOL;
	echo " env2Azure.php -appname=myawesomeweb -rg=myresourcegroup -pathtoenv=../projectx/.env".PHP_EOL.PHP_EOL;
    exit;	
}
