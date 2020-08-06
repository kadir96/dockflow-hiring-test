<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TradeflowResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'containers' => ContainerResource::collection($this->containers()->orderBy('reference')->get()),
        ];
    }
}
