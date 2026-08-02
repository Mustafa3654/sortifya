<?php

/**
 * Sends visitors to the real entry point.
 *
 * The application is served from public/. Everything beside it is denied by
 * .htaccess, and without this file the project folder answers a bare 403 —
 * which is what anyone typing http://host/sortifya/ hits, including from
 * another machine on the network.
 *
 * The redirect is relative on purpose, so the same file works unchanged on
 * localhost, on a LAN address, and through a tunnel.
 *
 * None of this is needed once DocumentRoot points straight at public/, which
 * is the arrangement the README recommends.
 */

header('Location: public/', true, 302);
exit;
