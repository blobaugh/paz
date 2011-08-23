<?php

/**
 * paz is a simple packaging and deployment tool for PHP projects on Windows Azure.
 * Given a simple application needing only a webrole, paz will automagically 
 * convert the application from a regular application folder to a Windows Azure
 * application package ready to upload or run in the local development environment
 * 
 * Want to contribute? Please keep all code in this single self contained file :) 
 * 
 * @license 100% free to use and modify. Contributors are not responsible for any damage. Please send back any improvements
 * 
 * @todo read custom .cscfg, .csdef, web.config, diagnostics.wadcfg files
 * @todo Add ability to deploy from command line
 */


$cmdparams = getCmdParams();

/*
 * Ensure all parameters needed are set to something
 */
checkParams();

/*
 * Create a project out of a default scaffolder
 */
echo "\nCreating temp build files...\n";
exec("scaffolder run -out=" . $cmdparams['tempBuild']);

/*
 * Copy the source project files to the scaffold project
 */
echo "\nCreating application...";
rcopy($cmdparams['in'], $cmdparams['tempBuild'] . "/PhpOnAzure.Web");

/*
 * If needed, copy in the Windows Azure SDK for PHP Microsoft folder
 */
if($cmdparams['noSDK'] == 'false') {
    echo "\nCopying Windows Azure SDK for PHP Microsoft folder to project";
    if(is_dir($cmdparams['sdkPath'] . "/trunk/library/Microsoft")) {
        $sdk = $cmdparams['sdkPath'] . "/trunk/library";
    } else {
        $sdk = $cmdparams['sdkPath'] . "/library";
    }
    rcopy($sdk, $cmdparams['tempBuild'] . "/PhpOnAzure.Web");
}

/*
 * Build the package
 */
echo "\nCreating the package...";
exec("package create -in={$cmdparams['tempBuild']} -out={$cmdparams['out']} -dev={$cmdparams['dev']}");

echo "\n\nPackage created in {$cmdparams['out']}\n\n";

/*
 * Clean up the temp build directory
 */
rrmdir($cmdparams['tempBuild']);


function displayHelp() {
    echo "\nSimple packaging and deployment tool for PHP project on Windows Azure";
    echo "\n\nOriginally developed by Ben Lobaugh 2011 <ben@lobaugh.net>";
    echo "\n\nParameters:";
    echo "\n\thelp - Display this menu";
    echo "\n\tin - Source of PHP project";
    echo "\n\tout - Output of Windows Azure package";
    echo "\n\tdev - If flag present local development environment will be used";
    echo "\n\tnoSDK - If present will not copy the Windows Azure SDK for PHP Microsoft folder to project";
    echo "\n\tsdkPath - Override default Windows Azure SDK for PHP path if not default install";
    echo "\n\ttempBuild - Override the temp build location";
    echo "\n\n\nSee ____ for documentation\n";
}

function checkParams() {
    global $cmdparams;
    
    if(isset($cmdparams['help'])) {
        displayHelp();
        exit();
    }
    /*
     * If the user did not specify a temp build directory one will be created
     */
    if(!isset($cmdparams['tempBuild'])) {
        if(strstr(strtolower(PHP_OS), "win") != false) {
            $cmdparams['tempBuild'] = "C:\\temp\\paz_build";
        } else {
            $cmdparams['tempBuild'] = "/tmp/paz_build";
        }
    }

    /*
     * Ensure the temp build directory exists and remove old builds if they exist
     */
    if(is_dir($cmdparams['tempBuild'])) {
        rrmdir($cmdparams['tempBuild']);
    }
    rmkdir($cmdparams['tempBuild']);

    /*
     * Figure out what directory will be used for source input
     */
    if(!isset($cmdparams['in'])) {
        $cmdparams['in'] = __DIR__;
    }

    /*
     * Ensure output directory exists and remove any old output
     * 
     * If no output is specified create an output dir in source
     */
    if(!isset($cmdparams['out'])) {
        $cmdparams['out'] = $cmdparams['in'] . "/paz_build";
    }
    if(is_dir($cmdparams['out'])) {
        rrmdir($cmdparams['out']);
    }
    rmkdir($cmdparams['out']);

    /*
     * Set build target to local dev or cloud
     */
    if(!isset($cmdparams['dev'])) {
        $cmdparams['dev'] = 'false';
    }
    
    /*
     * Figure out if there is a need to include the Windows Azure SDK for PHP Microsoft folder
     */
    if(!isset($cmdparams['noSDK'])) {
        $cmdparams['noSDK'] = 'false';
    }
    
    /*
     * Allow user to override the regular Windows Azure SDK for PHP path
     */
    if(!isset($cmdparams['sdkPath'])) {
        if(strstr(strtolower(PHP_OS), "win") != false) {
            $cmdparams['sdkPath'] = "C:\\Program Files\\Windows Azure SDK for PHP";
        } else {
            $cmdparams['sdkPath'] = "/usr/local/Windows Azure SDK for PHP";
        }
    }
    
    /*
     * If the user does not want local development then set for cloud package
     */
    if(!isset($cmdparams['dev'])) {
        $cmdparams['dev'] = 'false';
    }
}


/**
 * Recursively removes a folder along with all its files and directories
 * 
 * @param String $path 
 */
function rrmdir($path) {
     // Open the source directory to read in files
        $i = new DirectoryIterator($path);
        foreach($i as $f) {
            if($f->isFile()) {
                unlink($f->getRealPath());
            } else if(!$f->isDot() && $f->isDir()) {
                rrmdir($f->getRealPath());
                //rmdir($f->getRealPath());
            }
        }
        rmdir($path);
}

/**
 * Recursively creates a directory structure
 * 
 * @param String $path 
 */
function rmkdir($path) {
    $path = str_replace("\\", "/", $path);
    $path = explode("/", $path);
    
    $rebuild = '';
    foreach($path AS $p) {
        
        if(strstr($p, ":") != false) { 
            //echo "\nFound : in $p\n";
            $rebuild = $p;
            continue;
        }
        $rebuild .= "/$p";
        //echo "Checking: $rebuild\n";
        if(!is_dir($rebuild)) mkdir($rebuild);
    }
}



/**
 * Returns all the parameters passed by the command line as key/value pairs.
 * If a flag is used (param with no value) it will be set to true
 * 
 * @global Array $argv
 * @return Array 
 */
function getCmdParams() {
    global $argv;
   
    $params = array();
    for($i = 0; $i < count($argv); $i++) {
        if(substr($argv[$i], 0, 1) == '-') {
            if($i <= count($argv)-2 && substr($argv[($i + 1)], 0, 1) != '-') { 
                // Next item is flag
                $value = $argv[$i + 1];
            } else {
                $value = "true";
            }
            $key = str_replace("-", "", $argv[$i]);
            $params[$key] = $value;
        }
    }
    return $params;
}


/**
 * Recursively copy files from one directory to another
 *
 * @param String $src - Source of files being moved
 * @param String $dest - Destination of files being moved
 */
function rcopy($src, $dest){
 
    // If source is not a directory stop processing
    if(!is_dir($src)) return false;
 
    // If the destination directory does not exist create it
    if(!is_dir($dest)) {
        if(!mkdir($dest)) {
            // If the destination directory could not be created stop processing
            return false;
        }
    }
 
    // Open the source directory to read in files
    $i = new DirectoryIterator($src);
    foreach($i as $f) {
        if($f->isFile()) {
            copy($f->getRealPath(), "$dest/" . $f->getFilename());
        } else if(!$f->isDot() && $f->isDir()) {
            rcopy($f->getRealPath(), "$dest/$f");
        }
    }
}