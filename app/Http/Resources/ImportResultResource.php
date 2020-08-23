<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImportResultResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'tradeflows' => TradeflowResource::collection(collect($this->getTradeflows())->sortBy('name')),
            'invalid_container_references' => $this->getInvalidContainerReferences(),
            'tradeflows_without_containers' => $this->getTradeflowsWithoutContainers(),
        ];
    }
}
