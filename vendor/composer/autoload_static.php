<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit36fb392c188c0dcb4196c4da48fabb3b
{
    public static $prefixLengthsPsr4 = array (
        'B' => 
        array (
            'BundleProductManager\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'BundleProductManager\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/php',
        ),
    );

    public static $classMap = array (
        'BundleProductManager\\Admin\\Notification\\BPM_Notification' => __DIR__ . '/../..' . '/src/php/Admin/Notification/BPM_Notification.php',
        'BundleProductManager\\BPM_Handlers' => __DIR__ . '/../..' . '/src/php/BPM_Handlers.php',
        'BundleProductManager\\BPM_Main' => __DIR__ . '/../..' . '/src/php/BPM_Main.php',
        'BundleProductManager\\BPM_Output' => __DIR__ . '/../..' . '/src/php/BPM_Output.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit36fb392c188c0dcb4196c4da48fabb3b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit36fb392c188c0dcb4196c4da48fabb3b::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit36fb392c188c0dcb4196c4da48fabb3b::$classMap;

        }, null, ClassLoader::class);
    }
}
