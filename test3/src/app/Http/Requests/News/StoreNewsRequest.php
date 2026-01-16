<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "StoreNews",
    title: "Create news",
    description: "Create news",
    required: ["title", "content"],
    properties: [
        new OA\Property(property: "title", description: "Заголовок", type: "string", example: "Example title"),
        new OA\Property(property: "content", description: "Контент", type: "string", example: "Example content"),
    ]
)]
class StoreNewsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:100',
            'content' => 'required|string|max:500',
        ];
    }

    public function getTitle(): string
    {
        return $this->get('title');
    }

    public function getContentField(): string
    {
        return $this->get('content');
    }
}
