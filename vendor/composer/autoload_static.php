<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit80708e116fe8aeea84d0fe35c2b2e0ea
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Akill\\Payment\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Akill\\Payment\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit80708e116fe8aeea84d0fe35c2b2e0ea::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit80708e116fe8aeea84d0fe35c2b2e0ea::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
