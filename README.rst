Packaging for php (and other) projects.

=============
How to use it
=============

1. cd to your project directory
2. git clone git://github.com/liip/packaging.git
3. cp packaging/packaging_config.php.sample packaging_config.php
4. edit the configuration file to suit your needs (see the section
   `Configuration options`_ below for more information)
5. ./packaging/configure
6. make

Now you should have the deb/rpm package in your `tmp/yourpackage` directory.

Hint:
You can pass an optional argument 'ENV' to the 'make' command:
make ENV=prod

This way you can distinguish between different installation environments
(i.e production and staging). You can then use this variable in you
package_config.php to distinguish between files that should be used in a
production environment or in a staging environment.

Example::

    $filemapping = array(
        'var/www/@PACKAGENAME@' => array(
            '*',
            '- /packaging_files',
        ),
        'etc/packaged-site/@PACKAGENAME@' => array(
            // This file is used as a template file that holds environment-dependent
            // information
            'packaging_files/config.$(ENV).m4',
        ),
    );


================
How does it work
================

Files
-----

* configure: its role is to create the Makefile
* template: its role is to replace placeholders in a file by actual
  configuration values
* Makefile.in: skeleton file of the final Makefile
* common.php: holds various functions used by the packaging/templating process

Process
-------

First, the `configure` script will take the values defined in the
`packaging_config.php` file and use them to generate the `Makefile`, based on the
`Makefile.in` file.

The resulting `Makefile` will be called when the user invokes the `make`
command. The `Makefile` will do in order:

1. Create the basic build structure in a temporary directory defined by the
   `tmpdir` configuration option
2. Copy all the project files in this temporary directory, excluding all files
   that have been explicitly excluded, plus RCS and packaging files
3. Template the files that are in the directory defined by the `templatedir`
   configuration option
4. Create the directory structure as it will be on the server
5. Copy each file to its directory structure as defined by the `filemapping`
   configuration option
6. Call `fpm` on this final directory

================
Additional Hints
================

File permissions
----------------

If you want to change file permissions on the target system you can do that in
the postinst script.

If for example you have a dedicated directory where your web application will
write data this directory needs to be writeable by the webserver:

Example::

    #!/bin/sh
    chown -R www-data:www-data /var/lib/sitedata/@PACKAGENAME@

Database setup
--------------

Unfortunately it is not possible to setup a database interactively during the
installation of the package. One workaround is to create a script that guides
you through the configuration of a database and to put that script into the
package (it could be put into '/usr/share/doc/@PACKAGENAME@/' for example).
The person installing the package would then be responsible to run this script
and adapt the application configuration.
