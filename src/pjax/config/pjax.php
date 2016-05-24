<?php
/**
 * laravel-pjax configuration
 */

return [

    //
    // See https://github.com/defunkt/jquery-pjax#usage
    //
    'valid_containers' => [
        '#pjax-container', '.content'
    ],

    //
    // Set the layout version to force a hard reload of the requested page
    // when a client is on an old version of the layout.
    //
    // bumping the version will force clients to do a full reload the next
    // request getting the new layout and assets
    //
    // Set the version to something like 'v1'. Also, you will need to add
    // a meta tag like this to the base layout:
    //
    //      <meta http-equiv="x-pjax-version" content="v123">
    //
    //'layout_version' => 'v1'

];
