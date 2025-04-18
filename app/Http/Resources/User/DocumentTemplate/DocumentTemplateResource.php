<?php

declare(strict_types=1);

namespace App\Http\Resources\User\DocumentTemplate;

use App\Http\Resources\User\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $file_orig_name
 * @property string file_ext
 * @property string file_size
 * @property string file_path
 * @property string created_at
 * @property string updated_at
 * @property User $user
 */
class DocumentTemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'file_orig_name' => $this->file_orig_name,
            'file_ext' => $this->file_ext,
            'file_size' => Number::fileSize((float) ($this->file_size), 2),
            'file_path' => $this->file_path,
            'created_at' => Carbon::parse($this->created_at)->toDateTimeString(),
            'updated_at' => Carbon::parse($this->created_at)->toDateTimeString(),
            'user' => auth()->user()->getAuthIdentifier() === $this->user_id
                ? new UserResource(auth()->user())
                : new UserResource($this->whenLoaded('user')),
        ];
    }
}
