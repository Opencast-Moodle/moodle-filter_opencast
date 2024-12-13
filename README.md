moodle-filter_opencast
=====================
The opencast filter can be used to embed opencast videos in Moodle.
Mainly it is used in combination with the opencast repository plugin to play videos in course text fields.


Description
-----------
The filter takes the HTML pasted by the opencast repository and replaces it by an iframe, which loads the [Paella Player 7](https://paellaplayer.upv.es/) with the respective opencast event.
The filter itself has no influence on the embedded content, but simply takes the information created by the repository.
Look into the documentation of [repository_opencast](https://github.com/Opencast-Moodle/moodle-repository_opencast) for details on that.

<img width="800" alt="Filter opencast demonstration" src="https://user-images.githubusercontent.com/28386141/137904963-968fd449-602d-40c8-99ad-c56a40fd03f0.png">


Installation
------------

* Copy the module code directly to the filter/opencast directory.

* Log into Moodle as administrator.

* Open the administration area (http://your-moodle-site/admin) to start the installation
  automatically.


Admin Settings
--------------

View the documentation of the plugin settings [here](https://moodle.docs.opencast.org/#filter/settings/).


## Documentation ##

The full documentation of the plugin can be found [here](https://moodle.docs.opencast.org/#filter/about/).


Bug Reports / Support
---------------------

We try our best to deliver bug-free plugins, but we can not test the plugin for every platform,
database, PHP and Moodle version. If you find any bug please report it on
[GitHub](https://github.com/Opencast-Moodle/moodle-filter_opencast/issues). Please
provide a detailed bug description, including the plugin and Moodle version and, if applicable, a
screenshot.

You may also file a request for enhancement on GitHub.


## License ##

This plugin is developed in cooperation with the WWU Münster.

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <http://www.gnu.org/licenses/>.
