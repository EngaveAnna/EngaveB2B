<?php

// autoload_real.php @generated by Composer
class ComposerAutoloaderInit17bf89217da4fa1d81bb1533010bfb64 {
	private static $loader;
	public static function loadClassLoader($class) {
		if ('Composer\Autoload\ClassLoader' === $class) {
			require __DIR__ . '/ClassLoader.php';
		}
	}
	public static function getLoader() {
		if (null !== self::$loader) {
			return self::$loader;
		}
		
		spl_autoload_register ( array (
				'ComposerAutoloaderInit17bf89217da4fa1d81bb1533010bfb64',
				'loadClassLoader' 
		), true, true );
		self::$loader = $loader = new \Composer\Autoload\ClassLoader ();
		spl_autoload_unregister ( array (
				'ComposerAutoloaderInit17bf89217da4fa1d81bb1533010bfb64',
				'loadClassLoader' 
		) );
		
		$vendorDir = dirname ( __DIR__ );
		$baseDir = dirname ( $vendorDir );
		
		$map = require __DIR__ . '/autoload_namespaces.php';
		foreach ( $map as $namespace => $path ) {
			$loader->set ( $namespace, $path );
		}
		
		$map = require __DIR__ . '/autoload_psr4.php';
		foreach ( $map as $namespace => $path ) {
			$loader->setPsr4 ( $namespace, $path );
		}
		
		$classMap = require __DIR__ . '/autoload_classmap.php';
		if ($classMap) {
			$loader->addClassMap ( $classMap );
		}
		
		$loader->register ( true );
		
		return $loader;
	}
}
function composerRequire17bf89217da4fa1d81bb1533010bfb64($file) {
	require $file;
}
