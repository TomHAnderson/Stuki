#Assets Module

Matches URLs of the form `assets/[ module ]/[ asset ]` and serves that file 
under a module's `public` folder if it exists.

URLs of the form above are routed to the `indexAction` of the 
`Assets\Controller\IndexController` class. It tests for the presence of the
class `[Module]\Module`. The dirname of that class is obtained, then the
existence of the path `[dir]/public/[asset]` is tested. If the file exists, its
contents are added to the response and the `Content-type` header is set
accordingly. If any of the previous tests failed, a 404 response is sent.

Whenever you need an asset in your module view scripts, use 
`$this->baseUrl('/assets/[module]/[filename.ext]')` to trigger this module.

##Hooks
Certain prefixes of the `asset` route param triggers special processing before
serving the file. At the moment, only css minification is implemented.


##TODO:
- look for the `If-Modified-Since` header and send a 304 if the file modtime is
  earlier.
- cache to the actual public directory so that the web server can handle the
  next requests to the same URL


