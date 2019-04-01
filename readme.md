### intro

This script will create a bunch of AZ commands you can copy from the shell window where you run this script, into a shell window in azure.

Its purpose is to cut down the amount of time it takes to move a laravel site from development to production.

In Azure, instead of using a .env file, The azure portal UI provides a place for application settings, and these are used at run time.

All that's needed is a simple way to configure those settings - which of course can be done one by one by hand in the azure portal.

This script provides a shortcut - creating lines you can easily copy/paste. 

This script  does not log into the portal, so it needs no credentials.

To use it, start the script with the required command line options, then copy the output you see on screen.
Then go to portal.azure.com and find your Azure Web App running on windows (they handle linux differently)
Open a command shell from the header bar in azure, then paste in what you copied.
