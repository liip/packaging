Packaging for php (and other) projects.

=============
How to use it
=============

1. cd to your project directory
2. git clone gitosis@git.liip.ch:public/packaging
3. cp packaging/packaging_config.php.sample packaging_config.php
4. edit the configuration file to suit your needs (see the section
   `Configuration options`_ below for more information)
5. ./packaging/configure
6. make

Now you should have the deb/rpm package in your `tmp/yourpackage` directory.

=============
Prerequisites
=============

The packaging process depends on the following to external programs:
* fpm: see https://github.com/jordansissel/fpm
* rsync: this is usually installed on most \*nix like systems

These two programs need to be installed before you can use the packaging
scripts.

.. _configuration_options:

=====================
Configuration options
=====================

Configuration options marked as optional mean they can be left blank but they
still must appear in the configuration array!

packagename
    The name of your package. You should use only lowercase letters and
    dashes for the package name. Avoid non-ASCII characters.

    Example: drupal-site-foo

version
    The version of your package.

    Example: 0.1.0

maintainer
    Usually your name.

    Example: Foo Bar <foo@bar.com>

description
    The description of your package.

    Example: Foo Drupal site

url (optional)
    The homepage of you package.

    Example: http://www.foo.bar

packagetype
    The type of package you're building. Choose `deb` or `rpm`.

    Example: deb

depends (optional)
    An array containing a set of package names your package depends on.

    Example: apache2, libapache2-mod-php, memcached, php5-memcached

tmpdir
    The directory that will be used to build the package and to store the
    resulting package as well. You can use a directory outside of your project
    directory, like `/tmp`.

    Example: tmp (this will put everything in a `tmp` directory inside your
    project directory)

templatedir
    The directory that holds files that must be templated. This must be relative
    to your project directory. Usually you then want to exclude this directory
    from the package (see 'filemapping' below).

    Example: templates (that means all your template files are in the templates
    directory)

postinst (optional)
    The path to the postinst script in your project directory. The script will
    be incorporated into the package. To prevent the script from ending up
    alongside your source code on target system you shoud exclude it (see
    'filemapping' below). The best way is to put it in the templatedir and
    exclude this directory.

    Example: templates/postinst.sh

preinst (optional)
    The path to the preinst script in your project directory. The script will
    be incorporated into the package. To prevent the script from ending up
    alongside your source code on target system you shoud exclude it (see
    'filemapping' below). The best way is to put it in the templatedir and
    exclude this directory.

    Example: templates/preinst.sh

postrm (optional)
    The path to the postrm script in your project directory. The script will
    be incorporated into the package. To prevent the script from ending up
    alongside your source code on target system you shoud exclude it (see
    'filemapping' below). The best way is to put it in the templatedir and
    exclude this directory.

    Example: templates/postrm.sh

prerm (optional)
    The path to the prerm script in your project directory. The script will
    be incorporated into the package. To prevent the script from ending up
    alongside your source code on target system you shoud exclude it (see
    'filemapping' below). The best way is to put it in the templatedir and
    exclude this directory.

    Example: templates/prerm.sh

filemapping
    The filemapping variable should hold the mapping between your project files
    and their location on the server (ie. where they'll get installed). Each
    entry of this array is represented by the destination of the file (the key)
    and the file in your project (the value). The destination (the array keys)
    should always be in **relative** notation (ie. they must never begin with a
    slash).

    The behaviour is different whether the value is an array or a single value.
    An array means that the destination is a directory and the contents of the
    array will be copied in this directory. If you just specify an empty array
    an empty directory is created.  A string means that the destination is a
    file.

    Note that you can use variables defined in your `$configure` array.

    Example::

        'var/www/@PACKAGENAME@' => array(
            'app/',
            'admin/',
        ),
        'etc/cron.d/@PACKAGENAME' => 'templates/cron.template'

    Additionally you can exclude specific files or even directories to prevent
    them from ending up in the package. To do this you need to prefix them
    with '- '.

    Example::

        'var/www/@PACKAGENAME@' => array(
            '*',
            '- /templates',
        ),

    This will prevent the top level 'templates' directory in your source tree
    from ending up in the package.

    RCS files (.git, .svn, .cvs) are already ignored in the Makefile so you
    don't need to exclude them here.

    You can find more info on the syntax you can use in the paths in the rsync
    manpage, section `FILTER RULES`.


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
