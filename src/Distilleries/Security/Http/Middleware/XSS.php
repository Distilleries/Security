<?php

namespace Distilleries\Security\Http\Middleware;

use Closure;
use Distilleries\Security\Helpers\Security;
use Illuminate\Http\Request;

class XSS
{
    public function handle(Request $request, Closure $next)
    {

        if (config('security.xss_enable') || config('security.html_purifier')) {
            $input = $request->all();


            $config = \HTMLPurifier_Config::createDefault();
            $config->set('AutoFormat.RemoveSpansWithoutAttributes', true);
            $config->set('AutoFormat.RemoveEmpty', true);
            $config->set('HTML.TidyLevel', 'heavy');
            $config->set('Cache.DefinitionImpl', null);
            //$config->set('HTML.SafeIframe', true);

            array_walk_recursive($input, function(&$input) use ($config) {
                if (config('security.html_purifier')) {
                    $input = (new \HTMLPurifier($config))->purify($input);
                }
                if (config('security.xss_enable')) {
                    $input = (new Security)->xss_clean($input);
                }

            });

            $request->merge($input);
        }

        return $next($request);


    }
}