<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Detection\MobileDetect;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class EventResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'date' => $this->getDateAttribute(),
            'days' => $this->getDays(),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'hotel' => $this->hotel,
            'address' => $this->address,
            'map' => $this->map,
            'event_status' => $this->event_status,
            'logo' => asset('storage/images/event-' . $this->id . '-' . date('Y', strtotime($this->start_date)) . '/' . $this->logo),
            'banner' => $this->getBanner($this),
            'messages' => EventMessageResource::dataCollection($this->messages()->get())
        ];
    }

    public function getBanner($obj)
    {
        $detect = new MobileDetect();
        if ($detect->isMobile()) {
            return asset('storage/images/event-' . $obj->id . '-' . date('Y', strtotime($obj->end_date)) . '/mobile/' . $obj->banner);
        } else {
            return asset('storage/images/event-' . $obj->id . '-' . date('Y', strtotime($obj->end_date)) . '/' . $obj->banner);
        }

    }


}
