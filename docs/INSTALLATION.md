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