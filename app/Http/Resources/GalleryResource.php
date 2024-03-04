<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GalleryResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->name,
            'logo' => asset('storage/images/event-' . $this->id . '-' . date('Y', strtotime($this->start_date)) . '/' . $this->logo),
            'images' => $this->gallery($this)

        ];
    }

    public function gallery($obj): array
    {
        $images = $obj->galleries()->get();
        $list = [];
        foreach ($images as $image) {
            $list[] = [
                'id' => $image->id,
                'url' => asset('storage/images/event-' . $obj->id . '-' . date('Y', strtotime($obj->start_date)) . '/gallery/' . $image->url)
            ];
        }
        return $list;
    }
}
