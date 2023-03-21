<?php

namespace WpStarter\Tests\Integration\Http\Fixtures;

use WpStarter\Http\Resources\Json\JsonResource;

class PostResourceWithOptionalAppendedAttributes extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first' => $this->whenAppended('is_published'),
            'second' => $this->whenAppended('is_published', 'override value'),
            'third' => $this->whenAppended('is_published', function () {
                return 'override value';
            }),
            'fourth' => $this->whenAppended('is_published', $this->is_published, 'default'),
            'fifth' => $this->whenAppended('is_published', $this->is_published, function () {
                return 'default';
            }),
        ];
    }
}
