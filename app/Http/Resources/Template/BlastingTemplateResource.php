<?php

namespace App\Http\Resources\Template;

use Illuminate\Http\Resources\Json\JsonResource;

class BlastingTemplateResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'subject' => $this->subject,
            'wording' => $this->wording,

            'button_type' => $this->button_type,
            'button_text' => $this->button_text,
            'url_type' => $this->url_type,
            'url' => $this->url,
            'params' => $this->params,

            'is_active' => (bool) $this->is_active,
            'created_by' => $this->created_by,
            'created_at' => optional($this->created_at)->toDateTimeString(),
        ];
    }
}
