<?php

namespace App\Http\Resources\Meta;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Pagination",
    title: "Pagination",
    description: "Pagination",
    required: ["next_cursor", "prev_cursor", "has_more", "per_page", "count"],
    properties: [
        new OA\Property(property: "next_cursor", description: "Курсор для следующей страницы", type: "string", nullable: true),
        new OA\Property(property: "prev_cursor", description: "Курсор для предыдущей страницы", type: "string", nullable: true),
        new OA\Property(property: "has_more", description: "Есть ли еще страницы", type: "boolean"),
        new OA\Property(property: "per_page", description: "Количество элементов на странице", type: "integer"),
        new OA\Property(property: "count", description: "Количество элементов на текущей странице", type: "integer")
    ]
)]
class CursorPaginationResource
{
    //
}
