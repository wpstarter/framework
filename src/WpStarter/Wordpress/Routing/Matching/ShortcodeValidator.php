<?php

namespace WpStarter\Wordpress\Routing\Matching;

use WpStarter\Http\Request;
use WpStarter\Routing\Matching\ValidatorInterface;
use WpStarter\Routing\Route;

class ShortcodeValidator implements ValidatorInterface
{
    /**
     * Validate a given rule against a route and request.
     *
     * @param  \WpStarter\Routing\Route  $route
     * @param  \WpStarter\Http\Request  $request
     * @return bool
     */
    public function matches(Route $route, Request $request)
    {
        if (is_singular()) {
            if ($post = get_post()) {
                if ($this->hasShortcode($post->post_content, $route->uri())) {
                    return true;
                }
            }
        }
        return false;
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
            } elseif ( ! empty( $shortcode[5] ) && $this->hasShortcode( $shortcode[5], $tag ) ) {
                return true;
            }
        }
        return false;
    }
}
