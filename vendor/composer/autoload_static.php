<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit702692486baf17986f845ebd9e2f934a
{
    public static $files = array (
        'decc78cc4436b1292c6c0d151b19445c' => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'p' => 
        array (
            'phpseclib3\\' => 11,
        ),
        'P' => 
        array (
            'ParagonIE\\ConstantTime\\' => 23,
        ),
        'D' => 
        array (
            'Drola\\' => 6,
        ),
        'A' => 
        array (
            'AcfExportManager\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'phpseclib3\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib',
        ),
        'ParagonIE\\ConstantTime\\' => 
        array (
            0 => __DIR__ . '/..' . '/paragonie/constant_time_encoding/src',
        ),
        'Drola\\' => 
        array (
            0 => __DIR__ . '/..' . '/helsingborg-stad/coordinate-transformation-library/src',
        ),
        'AcfExportManager\\' => 
        array (
            0 => __DIR__ . '/..' . '/helsingborg-stad/acf-export-manager/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit702692486baf17986f845ebd9e2f934a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit702692486baf17986f845ebd9e2f934a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit702692486baf17986f845ebd9e2f934a::$classMap;

        }, null, ClassLoader::class);
    }
}
