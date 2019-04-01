<?php

$appname = null;
$resourceGroup = null;
$pathtoenv = ".env";

if (!isset($argc) || ($argc <=1 ))
{
	echo PHP_EOL."PHP arguments aren't working, this script can't be run.".PHP_EOL;
    exit;	
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
	echo PHP_EOL."Call this script with the following parameters:".PHP_EOL;
	echo " env2Azure.php -appname=<appname> -rg=<resourcegroupname> [optional: -pathtoenv=<path>]".PHP_EOL.PHP_EOL;
	echo "EXAMPLE:".PHP_EOL;
	echo " env2Azure.php -appname=myawesomeweb -rg=myresourcegroup -pathtoenv=../projectx/.env";
    exit;	
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
echo "az webapp config appsettings set --name ".$appname." --resource-group ". $resourceGroup. " --settings SCM_REPOSITORY_PATH=..\repository" .PHP_EOL; 
echo "az webapp config appsettings set --name ".$appname." --resource-group ". $resourceGroup. " --settings SCM_TARGET_PATH=.." .PHP_EOL; 



fclose($file);

echo "BE SURE TO SET APP_DEBUG=false when this goes in azure or any error will reveal all ENV values!";
echo "BE SURE TO SET APP_ENV = 'production'";
echo "I HAD ISSUES WITH THE AZURE_SECRET VALUE, check that it's not truncated in any way";
