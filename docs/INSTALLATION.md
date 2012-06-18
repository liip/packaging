====================================
General Informations about Packaging
====================================
A Package is basically the sources of a project you wan't to have on a live system, install scripts plus meta data.
Linux Distributions (Feodora, Redhat etc.) usually have a .rpm package manager. Debian and Ubuntu are using .deb packages
So depending on your deployment Server, you need different prerequisites. Check the INSTALLATION.md for more information.

=============
Prerequisites
=============
The packaging process depends on the following to external programs:

* fpm: A OS indepentent package manager. (Mac: $sudo gem install fpm) See https://github.com/jordansissel/fpm
* rsync: Remote Transfer/Sync Utility (this is usually installed on most \*nix like systems)
* rpmtools: Tools for building RPM Packages (only needed for rpm packages, easy to install on Linux with: $apt-get install rpmlibs, hard to install on Mac OS)

=============
Build Server / Workstation
==========================
You need a machine to build your packages. This could be your local development machine, or you can also set up a build server with all tools installed to avoid everybody installing individually. (@liipers: look in our wiki for build.liip.ch)