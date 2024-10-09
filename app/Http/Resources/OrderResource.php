<?php

namespace App\Http\Resources;

use App\Enums\UserRole;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [];

        if (AuthService::user_role(Auth::user()->id) == UserRole::DELIVERY->value) {
            $data['customer_name'] = $this->customer->name ?? '';
        } else if (AuthService::user_role(Auth::user()->id) == UserRole::CUSTOMER->value) {
            $data['delivery_name'] = $this->delivery->name ?? '';
        } else {
            $data['customer_name'] = $this->customer->name ?? '';
            $data['delivery_name'] = $this->delivery->name ?? '';
        }
        if ($this->delivery != null) {
        }
        return [
            'id' => $this->id,
            'price' => $this->price,
            'status' => $this->status,
            'delivery_date' => $this->delivery_date ?? '',
            ...$data
        ];
    }
}
