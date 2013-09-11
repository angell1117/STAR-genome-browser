STAR-genome-browser
===================
Easily visualization of complex data features is a necessary step to conduct studies on next generation sequencing (NGS) data.STAR is an integrated web application that enables online management, visualization, and track-based analysis of next generation sequencing data. 

To use our STAR system, please visit the following URL:
http://wanglab.ucsd.edu/star/browser/
to view data tracks and get details from manual.
 

If you want to process your own data and set up environment on your computer, you
need to install some software packages before using STAR genome browser:

--------------------------------------------------------------------------------
Required software packages:

* GNU C++ 	http://gcc.gnu.org/
* PERL 		http://www.perl.org/ 
* FREETYPE 	http://www.freetype.org/
* ZLIB 		http://www.zlib.org
* JPEG 		http://www.ijg.org/
* GD 		http://www.libgd.org/
* LIBPNG 	http://www.libpng.org/
* PHP 		http://www.php.net/
* Berkeley DB 	http://www.oracle.com/technetwork/database/berkeleydb/downloads/index.html
* Apache 	http://httpd.apache.org/

Normally GNU C++, PERL and APACHE have been installed in most Linux OS distributions. If they were installed already, just skip those steps.JPEG, GD and LIBPNG are only required when you want to use graphical functions and modules in PHP. Below sections give some installation instructions. Please find specific help in each module: 

System environment and module installations:
--------------------------------------------------------------------------------
1. GNU C++ (required, may be installed defaultly)
	(1) download gcc-x.x.x.tar.gz from http://gcc.gnu.org/
	(2) tar xzvf gcc-x.x.x.tar.gz
	(3) mkdir ${destdir}/gcc-x.x.x
	(4) gcc-x.x.x/configure --prefix=${destdir}/gcc-x.x.x --enable-threads=posix 
		--disable-checking --enable--long-long --host=i386-redhat-linux 
		--with-system-zlib --enable-languages=c,c++,java
	(5) make
	(6) make instal
	(7) add ${destdir}/gcc-x.x.x/bin to PATH environment variable
	(8) add ${destdir}/gcc-x.x.x/lib to LD_LIBRARY_PATH environment variable
--------------------------------------------------------------------------------
2. PERL (required, may be installed defaultly)
	(1) download perl-x.x.x.tar.gz from  http://www.perl.org/
	(2) tar xzvf perl-x.x.x.tar.gz
	(3) perl-x.x.x/configure
	(4) make
	(5) make test
	(6) make install
--------------------------------------------------------------------------------
3. FreeType (only required when using PHP graphical features)
	(1) download freetype-x.x.x.tar.gz from  http://www.freetype.org/
	(2) tar xzvf freetype-x.x.x.tar.gz
	(3) mkdir ${destdir}/freetype-x.x.x
	(4) freetype-x.x.x/configure --prefix=${destdir}/freetype-x.x.x
	(5) make
	(6) make install
--------------------------------------------------------------------------------
4. ZLIB (required, many packges need it)
	(1) download zlib-x.x.x.tar.gz from  http://www.zlib.org
	(2) tar xzvf zlib-x.x.x.tar.gz
	(3) mkdir ${destdir}/zlib-x.x.x
	(4) zlib-x.x.x/configure --prefix=${destdir}/zlib-x.x.x
	(5) make
	(6) make install
--------------------------------------------------------------------------------
5. JPEG (only required when using PHP graphical features)
	(1) download jpeg.x.tar.gz from  http://www.ijg.org/
	(2) tar xzvf jpeg.x.tar.gz
	(3) mkdir ${destdir}/jpeg
	(4) jpeg.x/configure --prefix=${destdir}/jpeg
	(5) make
	(6) make install
--------------------------------------------------------------------------------
6. GD (only required when using PHP graphical features)
	(1) download gd-x.x.x.tar.gz from http://www.libgd.org/ 
	(2) tar xzvf gd-x.x.x.tar.gz
	(3) mkdir ${destdir}/gd-x.x.x
	(4) gd-x.x.x/configure --prefix=${destdir}/gd-x.x.x --with-freetype
		=${destdri}/freetype-x.x.x --with-jpeg=${destdir}/jpeg
	(5) make
	(6) make install
--------------------------------------------------------------------------------
7. LIBPNG (only required when using PHP graphical features)
	(1) download from http://www.libpng.org/ 
	(2) cp scripts/makefile.linux makefile
	(3) make
	(4) make install
--------------------------------------------------------------------------------
8. APACHE (required, may be installed defaultly)
	(1) download httpd-x.x.x.tar.gz from  http://httpd.apache.org/
	(2) tar xzvf httpd-x.x.x.tar.gz
	(3) mkdir ${destdir}/apache2
	(4) cd {src}/srclib/apr
	(5) ./configure --prefix=/usr/local/apr-httpd/
	(6) make
	(7) make install
	(8) cd ../apr-util
	(9) ./configure --prefix=/usr/local/apr-util-httpd/ --with-apr=/usr/local/apr-httpd/
	(10) make
	(11) make install
	(12) cd {src}
	(13) ./configure --prefix=${destdir}/apache2 --enable-cgi --enable-ssl --enable-modules=most 
	--enable-so --enable-proxy --enable-rewrite --sysconfdir=/etc --with-apr=/usr/local/apr-httpd
	--with-apr-util=/usr/local/apr-util-httpd
	(14) make
	(15) make install
	(16) modify httpd.conf to set up web server
--------------------------------------------------------------------------------
9. PHP (required)
	(1) download php-x.x.x.tar.gz from  http://www.php.net/
	(2) tar xzvf php-x.x.x.tar.gz
	(3) mkdir ${destdir}/php-x.x.x
	(4) php-x.x.x/configure --prefix=/usr --exec-prefix=/usr --bindir=/usr/bin 
		--sbindir=/usr/sbin --sysconfdir=/etc --datadir=/usr/share 
		--includedir=/usr/include --libdir=/usr/lib64 --with-libdir=lib 
		--libexecdir=/usr/libexec --localstatedir=/var --sharedstatedir=/usr/com 
		--mandir=/usr/share/man --infodir=/usr/share/info --with-config-file-path=/etc 
		--with-config-file-scan-dir=/etc/php.d --disable-debug --with-pic --disable-rpath 
		--without-pear --with-bz2 --with-curl --with-exec-dir=/usr/bin 
		--with-freetype-dir=${destdir}/freetype-x.x.x --with-png-dir=/usr 
		--enable-gd-native-ttf --without-gdbm --with-gettext --with-gmp --with-iconv 
		--with-jpeg-dir=${destdir}/jpeg --with-openssl --with-pspell  --with-pcre-regex=/usr 
		--with-zlib=${destdir}/zlib --with-layout=GNU --enable-exif --enable-ftp 
		--enable-magic-quotes --enable-sockets --enable-sysvsem --enable-sysvshm 
		--enable-sysvmsg  --enable-wddx --with-kerberos --enable-ucd-snmp-hack 
		--with-unixODBC=shared,/usr  --enable-shmop --enable-calendar 
		--with-mime-magic=/usr/share/file/magic.mime --without-sqlite --with-libxml-dir=/usr 
		--with-apxs2=/usr/sbin/apxs --with-gd=${destdir}/gd-x.x.x --enable-shared
	(5) make
	(6) make test
	(7) make install
	(8) sed -i 's/DirectoryIndex index.html/DirectoryIndex index.html index.php/g' httpd.conf 
	    sed -i '/AddType application\/x-tar .tgz/a\ AddType application\/x-httpd-php .php' httpd.conf
	    sed -i '/AddType application\/x-tar .tgz/a\ AddType application\/x-httpd-source .phps' httpd.conf
--------------------------------------------------------------------------------
10. Berkeley DB (required)
	(1) download db-x.x.x.tar.gz from  http://www.oracle.com/technetwork/database/berkeleydb/downloads/index.html
	    or just using db-4.6.18.tar.gz in this package.
	(2) tar xzvf db-x.x.x.tar.gz

    install C++ API:
	(3) cd db-x.x.x/build_unix
	(4) ../dist/configure --enable-cxx --enable-static --disable-shared
	(5) make
	(6) make install
	defaultly, the objects will be installed to /usr/local/BerkeleyDBx.x/. When you compile chipseq_proc,
	you should replace "/usr/local/BerkeleyDB4.6" to this destination directory.

    install PHP module:
	(7) cd db-x.x.x/php_db*
	(8) phpize
	(9) ./configure --with-db4=${destdir}/db-x.x.x
	(10) make
	(11) make install
	(12) Then in your php.ini file add: extension=db4.so
	(13) mkdir ${your-db-dir}

--------------------------------------------------------------------------------
11. Install Proxy rewrite rules
	(1) In your httpd.conf, find the section:"AllowOverride None" and modify it to "AllowOverride All".
	This will allow apache server to support additional .htaccess file to rewrite url.
	(2) Create a .htaccess file and put it in the document root directory. Add the RewriteRules in your
	.htaccess file. Please find a .htaccess example file in this package.

Note: Due to security problem, the program does not support retrieving data from crossdomain. Using the apache rewrite and proxy modules can help to solve this problem. 
--------------------------------------------------------------------------------
12. Final step to test your system
	(1) put simple_counter.php to your web document root
	(2) visit http://yourdomain/simple_counter.php
