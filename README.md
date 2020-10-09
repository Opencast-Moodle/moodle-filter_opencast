# moodle-filter_opencastfilter

The opencast filter can be used to embed opencast videos. Tested opencast players are Paella or Theodul. If you find other players, please contact us to enhance the list.
The filter takes in the html pasted by the opencast repository and replaces it by an iframe, which serves an opencast player with the respective opencast event.
The filter itself has no influence on the embedded content, but simply takes the information created by the repository.
Look into the documentation of [repository_opencast](https://github.com/unirz-tu-ilmenau/moodle-repository_opencast) for details on that.

**Requirements**

- [tool_opencast](https://github.com/unirz-tu-ilmenau/moodle-tool_opencast)
- [repository_opencast](https://github.com/unirz-tu-ilmenau/moodle-repository_opencast)

**Configuration**

In order for the opencast players to work, they need to call multiple API endpoints, which requires an authenticated user.
The authentication is done using LTI. 
For this, the opencast installation has to be prepared for connecting an LMS via LTI.
You can find a documentation on that [here](https://docs.opencast.org/develop/admin/modules/ltimodule/). 
The Client ID and Client Secret of your opencast installation have to be set in the configuration of the filter.
Additionally, an alternative engage url can be set.
The engage url points to the server, against which the LTI authentication is performed.
In basic settings, this server is equivalent with the administration server, which is set in [tool_opencast](https://github.com/unirz-tu-ilmenau/moodle-tool_opencast).
For more advanced installations, you can separate engage and admin server and provide the engage server url here.

**Authentication**

In addition to the created iframe showing the opencast player,
the filter also create an LTI form in the background, which is prefilled with the users data,
and sends it. This way it creates a session for the user within the opencast system and enables the embedded video player to render properly.
The LTI connection also guarantees that only users, which meet the requirements set by the ACL rules will be able to view the respective video.
However, the links to the raw media files might still be extractable from the player code, which are unprotected and sharable among unauthorized users.