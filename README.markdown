Introducing paz
===============

paz is a simple packaging and deployment tool for PHP projects on Windows Azure.

Given a simple application needing only a webrole, paz will automagically convert the application from a regular application folder to a Windows Azure application package ready to upload or run in the local development environment

__Very Beta Project!__ 
Understand that this project is in a very early development phase. Please report all bugs and/or feature requests on the Github project issue tracker

Contributing
------------
Want to contribute? Please keep all code in this single self contained file :) 

Usage
-----
Parameters:
       help - Display this menu
       in - Source of PHP project
       out - Output of Windows Azure package
       dev - If flag present local development environment will be used
       noSDK - If present will not copy the Windows Azure SDK for PHP Microsoft folder to project
       sdkPath - Override default Windows Azure SDK for PHP path if not default install
       tempBuild - Override the temp build location
