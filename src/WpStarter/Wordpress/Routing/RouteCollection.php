<?php

namespace WpStarter\Wordpress\Routing;

use WpStarter\Http\Request;
use WpStarter\Routing\Matching\MethodValidator;
use WpStarter\Routing\RouteCollection as BaseRouteCollection;

class RouteCollection extends BaseRouteCollection
{
    protected function matchAgainstRoutes(array $routes, $request, $includingMethod = true)
    {
        $methodValidator=new MethodValidator();
        if (is_singular()) {
            if ($post = get_post()) {
                foreach ($routes as $route) {
                    if($includingMethod && !$methodValidator->matches($route,$request)){
                        continue;
                    }
                    if ($this->hasShortcode($post->post_content, $route->uri())) {
                        return $route;
                    }
                }
            }
        }
    }

    /**
     * Better way to check if page has shortcode than WordPress function
     * @param $content
     * @param $tag
     * @return boolean
     */
    protected function hasShortcode($content,$tag){
        if ( false === strpos( $content, '[' ) ) {
            return false;
        }

        preg_match_all( '/' . get_shortcode_regex([$tag]) . '/', $content, $matches, PREG_SET_ORDER );
        if ( empty( $matches ) ) {
            return false;
        }

        foreach ( $matches as $shortcode ) {
            if ( $tag === $shortcode[2] ) {
                return true;
            } elseif ( ! empty( $shortcode[5] ) && has_shortcode( $shortcode[5], $tag ) ) {
                return true;
            }
        }
        return false;
    }
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
