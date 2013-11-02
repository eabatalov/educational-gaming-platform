SETTING UP PHPUNIT

If you use Linux with apt package manager execute:
sudo apt-get install php-pear
sudo pear install pear.phpunit.de/PHPUnit
sudo pear install phpunit/PHPUnit_SkeletonGenerator

Also your IDE may ask you about phpunit/skeleton generator path.
On my Ubuntu distribution they are located here:
/usr/bin/phpunit
/usr/bin/phpunit-skelgen

Once NetBeans asks you abouth the paths, it shows a button to search for them
automatically.

If you use Windows or other OS web development environment you use should provide
phpunit and skeleton generator in some way.