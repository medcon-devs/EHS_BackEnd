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
            'images1' => $this->gallery($this, 'event-2-2023'),
            'images2' => $this->gallery($this, 'event-1-2024'),
        ];
    }

    /**
     * Get gallery images based on folder year.
     *
     * @param $obj
     * @param string $yearFolder
     * @return array
     */
    public function gallery($obj, string $yearFolder): array
    {
        $images = $obj->galleries()->get();
        $list = [];
        foreach ($images as $image) {
            $list[] = [
                'id' => $image->id,
                'url' => asset('storage/images/' . $yearFolder . '/gallery/' . $image->url)
            ];
        }
        return $list;
    }
}
