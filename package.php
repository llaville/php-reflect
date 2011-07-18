<?php
require_once 'PEAR/PackageFileManager2.php';

PEAR::setErrorHandling(PEAR_ERROR_DIE);

$p2 = new PEAR_PackageFileManager2();

$name        = 'PHP_Reflect';
$summary     = 'Adds the ability to reverse-engineer classes, interfaces, functions, constants, namespaces and more. ';
$description = 'PHP_Reflect adds the ability to reverse-engineer classes, interfaces, functions, constants, namespaces 
and more, by connecting php callbacks to tokens.
';
//$channel     = 'pear.php.net';
$channel     = 'bartlett.laurent-laville.org';

$release_state   = 'stable';
$release_version = '1.0.2';

$api_state       = 'stable';
$api_version     = '1.0.0';
$release_notes   = "
Additions and changes:
- none

Bug fixes:
- trimmed whitespace on PHP_Reflect_Token_FUNCTION::getSignature() method
";
$license = array('BSD License', 'http://www.opensource.org/licenses/bsd-license.php');

$p2->setOptions(
    array(
        'packagedirectory'  => dirname(__FILE__),
        'baseinstalldir'    => 'Bartlett',
        'filelistgenerator' => 'file',
        'simpleoutput'      => true,
        'clearcontents'     => false,
        'changelogoldtonew' => false,
        'ignore'            => array(basename(__FILE__),
            '.git', '*.log',
            'Thumbs.db', 'packageBeta*.xml', 'packageRC*.xml', 
            ),
        'exceptions'        => array(
            'LICENSE' => 'doc',
            'phpunit.xml' => 'test',
            ),
    )
);

$p2->setPackage($name);
$p2->setChannel($channel);
//$p2->setUri($uri);
$p2->setSummary($summary);
$p2->setDescription($description);

$p2->setPackageType('php');
$p2->setReleaseVersion($release_version);
$p2->setReleaseStability($release_state);
$p2->setAPIVersion($api_version);
$p2->setAPIStability($api_state);
$p2->setNotes($release_notes);
$p2->setLicense($license[0], $license[1]);

$p2->setPhpDep('5.2.0');
$p2->setPearinstallerDep('1.9.0');

$p2->addPackageDepWithChannel('optional',
                              'PHPUnit', 'pear.phpunit.de', '3.5.0');
$p2->addExtensionDep('required', 'tokenizer');
$p2->addExtensionDep('required', 'pcre');
$p2->addExtensionDep('required', 'spl');

$p2->addMaintainer('lead', 'farell', 'Laurent Laville', 'pear@laurent-laville.org');

$p2->addGlobalReplacement('package-info', '@package_version@', 'version');

$p2->generateContents();
$p2->addRelease();

if (isset($_GET['make'])
    || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    $p2->writePackageFile();
} else {
    $p2->debugPackageFile();
}
