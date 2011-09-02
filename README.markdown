Introducing paz
===============

paz is a simple packaging and deployment tool for PHP projects on Windows Azure.

Given a simple application needing only a webrole, paz will automagically convert the application from a regular application folder to a Windows Azure application package ready to upload or run in the local development environment

__Very Beta Project!__ 
Understand that this project is in a very early development phase. Please report all bugs and/or feature requests on the Github project issue tracker

Requirements
------------
paz relies on the Windows Azure SDK for PHP being installed correctly. See
http://azurephp.interoperabilitybridges.com/articles/setup-the-windows-azure-sdk-for-php

Contributing
------------
Want to contribute? Please keep all code in this single self contained file :) 


Parameters
----------
       help - Display this menu
       in - Source of PHP project
       out - Output of Windows Azure package. If not specified the project directory from -in will be used
       dev - If flag present local development environment will be used
       noSDK - If present will not copy the Windows Azure SDK for PHP Microsoft folder to project
       sdkPath - Override default Windows Azure SDK for PHP path if not default install
       tempBuild - Override the temp build location


Installation
------------

Windows:
    - Download paz
    - Extract the files to C:\Program Files\paz
    - Add C:\Program Files\paz to your path
    - Begin using paz from the command line :)


Demo on Windows
---------------

For a quick demo I will be using a simple pre-built PHP CMS application that does not require a database, Pluck CMS (http://www.pluck-cms.org)

    - Install paz, see above if this is not already done
    - Download Pluck CMS from http://www.pluck-cms.org/?file=download
    - Extract the Pluck CMS archive to C:\temp\pluck
    - Open a command prompt
    - Run: paz -in "C:\temp\pluck" -dev -noSDK
        -- The -dev flag signals paz to build a package for local development. If this flag is not present a package for the Windows Azure Portal will be created
        -- This particular application does not require the Windows Azure SDK for PHP libraries to be included (for connecting to Windows Azure services) so the -noSDK flag has been used
    - After paz processes the PHP application and builds a package the default web browser will open and display your application
    - When satisfied with the development version you will run the following command to create a package for deployment to Windows Azure
        -- paz -in "C:\temp\pluck" -noSDK

That's It!