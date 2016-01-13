<?php

if (!(function_exists('is_pjax_request'))) {
    function is_pjax_request()
    {
        if (isset(Request::header('X-PJAX-CONTAINER'))) {
            return true;
        }

        return false;
    }
}
