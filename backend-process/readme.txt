This package contains program and perl scripts to process data files

STAR genome browser: http://tabit.ucsd.edu/sdec/
--------------------------------------------------------------------------------

Requirements:
* GNU C++
* PERL
* PHP
* Berkeley DB
* Apache web server

--------------------------------------------------------------------------------

Suppose all of the above packages have beed installed and configured successfully, 
The install instructions of this package will be:
cd src &
vim Makefile

CXXFLAGS += -I/usr/local/BerkeleyDB.4.6/include/
LDFLAGS := -L/usr/local/BerkeleyDB.4.6/lib/ -ldb_cxx -lpthread

find the above two lines in Makefile and replace "/usr/local/BerkeleyDB.4.6/" with 
your Berkeley DB installation destination(please find the installation instructions 
of Berkeley DB in system configuration documentation).

make &
make install &

Note: If the program was successfully compiled and installed, you will find 
ngsproc in your work directory. 

ngsproc is used to process data track files and then deposit them into Berkeley
database, you can also use it separately.
