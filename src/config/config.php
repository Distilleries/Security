<?php

return [
    'xss_enable'=> env('SECURITY_XSS_ENABLE',true),
    'html_purifier'=> env('SECURITY_HTML_PURIFIER_ENABLE',true)
];