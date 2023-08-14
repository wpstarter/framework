<?php

namespace WpStarter\Wordpress\Plugins\Loader;

use WpStarter\Http\Request;
use WpStarter\Support\Arr;

class RulesCollection
{
    protected $rules=[];

    /**
     * Add Rule to collection
     * @param Rule $rule
     * @return Rule
     */
    public function add(Rule $rule)
    {
        $this->addToCollections($rule);

        return $rule;
    }

    /**
     * Add the given rule to the arrays of rules.
     *
     * @param  Rule  $rule
     * @return void
     */
    protected function addToCollections($rule)
    {
        $uri=$rule->uri();
        if($uri instanceof \Closure){
            $uri=spl_object_hash($uri);
        }
        $domainAndUri = $rule->getDomain().$uri;

        foreach ($rule->methods() as $method) {
            $this->rules[$method][$domainAndUri] = $rule;
        }

    }

    /**
     * Get all of the rules in the collection.
     *
     * @return Rule[]
     */
    public function getRules()
    {
        return array_values($this->rules);
    }

    /**
     * Get rules from the collection by method.
     *
     * @param  string|null  $method
     * @return Rule[]
     */
    public function get($method = null)
    {
        return is_null($method) ? $this->getRules() : Arr::get($this->rules, $method, []);
    }

    /**
     * Find the first rule matching a given request.
     * @return Rule|null
     */
    public function match(Request $request)
    {
        $rules = $this->get($request->getMethod());

        $rule = $this->matchAgainstRules($rules, $request);

        return $rule;
    }
    /**
     * Determine if a rule in the array matches the request.
     *
     */
    protected function matchAgainstRules(array $rules, $request, $includingMethod = true)
    {
        [$fallbacks, $rules] = ws_collect($rules)->partition(function ($rule) {
            return $rule->isFallback;
        });

        return $rules->merge($fallbacks)->first(function (Rule $rule) use ($request, $includingMethod) {
            return $rule->matches($request);
        });
    }
}
