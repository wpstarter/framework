<?php

namespace WpStarter\Wordpress\Routing;

use WpStarter\Http\Request;
use WpStarter\Routing\Matching\MethodValidator;
use WpStarter\Routing\RouteCollection as BaseRouteCollection;

class RouteCollection extends BaseRouteCollection
{
    protected function handleMatchedRoute(Request $request, $route)
    {
        $route= parent::handleMatchedRoute($request, $route);
        if($post=get_post()){
            $attrs=$this->getShortcodeAtts($post->post_content,[$route->uri()]);
            foreach ($attrs as $key=>$value){
                $route->setParameter($key,$value);
            }
        }
        return $route;
    }

    protected function getShortcodeAtts($content,$shortcode){
        preg_match_all( '/' . get_shortcode_regex($shortcode) . '/', $content, $matches, PREG_SET_ORDER );
        $atts=[];
        if ( ! empty( $matches ) ) {
            $atts = shortcode_parse_atts($matches[0][3]??'');
        }
        if(!is_array($atts)){
            $atts=[];
        }
        return $atts;
    }
}
