moodle-filter_opencast
=====================
The opencast filter can be used to embed opencast videos.
The filter takes in the html pasted by the opencast repository and replaces it by an iframe, which loads the  <a href="https://github.com/polimediaupv/paella">Paella player</a> with the respective opencast event.
The filter itself has no influence on the embedded content, but simply takes the information created by the repository.
Look into the documentation of [repository_opencast](https://github.com/Opencast-Moodle/moodle-repository_opencast) for details on that.

<img width="800" alt="Filter opencast demonstration" src="https://user-images.githubusercontent.com/28386141/137904963-968fd449-602d-40c8-99ad-c56a40fd03f0.png">

## Requirements

- [tool_opencast](https://github.com/Opencast-Moodle/moodle-tool_opencast)
- [mod_opencast](https://github.com/Opencast-Moodle/moodle-mod_opencast)
- [repository_opencast](https://github.com/Opencast-Moodle/moodle-repository_opencast): The filter can be installed without the repository 
  but it is necessary to enable teachers to insert opencast videos.

## Configuration
The filter has two global configurations that can be modified by the administrator.

<img width="800" alt="Filter opencast configuration" src="https://user-images.githubusercontent.com/28386141/137904968-cacaf48f-35c0-4d15-b6d7-001ded49afc9.png">

The first configuration "URL template for filtering" specifies the URL type that is replaced by the filter. This URL should correspond to the URLs inserted by the repository plugin. In the config, you must use the placeholder [EPISODEID] to indicate where the episode id is contained in the link, e.g. `http://stable.opencast.de/play/[EPISODEID]`.

The second configuration "URL to Paella config.json" specifies the path to the Paella player config. This config can be adapted if you want to modify the look or behavior of the Paella player.

Notice: Make sure to configure [mod_opencast](https://github.com/Opencast-Moodle/moodle-mod_opencast) before using the filter. If [mod_opencast](https://github.com/Opencast-Moodle/moodle-mod_opencast) isn't configured correctly, the paella player won't be able to display the video.

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
