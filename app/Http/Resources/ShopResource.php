<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'shop_name' => $this->shop_name,
            'shop_description' => $this->shop_description,
            'shop_address' => $this->shop_address,
            'opening_time' => $this->opening_time,
            'closing_time' => $this->closing_time,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
