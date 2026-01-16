<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\News\StoreNewsRequest;
use App\Http\Resources\News\NewsResource;
use App\Services\News\NewsCreatingService;
use App\Services\News\NewsListService;
use App\Services\News\NewsShowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class NewsController extends Controller
{
    public function __construct(
        private NewsCreatingService $newsCreatingService,
        private NewsShowService $newsShowService,
        private NewsListService $newsListService,
    ) {
    }

    #[OA\Get(
        path: "/news",
        description: "Возвращает список новостей с курсорной пагинацией",
        summary: "Список новостей",
        tags: ["News"],
        parameters: [
            new OA\Parameter(
                name: "cursor",
                description: "Курсор для пагинации (опционально)",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "per_page",
                description: "Количество элементов на странице (по умолчанию: 10)",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", default: 10, maximum: 20, minimum: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешно",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "success",
                            type: "boolean",
                            example: true
                        ),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(ref: "#/components/schemas/News")
                        ),
                        new OA\Property(
                            property: "meta",
                            ref: "#/components/schemas/Pagination",
                        )
                    ]
                )
            )
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $news = $this->newsListService->getPaginatedList($perPage);

        return response()->json([
            'success' => true,
            'data' => NewsResource::collection($news->items()),
            'meta' => [
                'next_cursor' => $news->nextCursor()?->encode(),
                'prev_cursor' => $news->previousCursor()?->encode(),
                'has_more' => $news->hasMorePages(),
                'per_page' => $perPage,
                'count' => count($news->items()),
            ],
        ]);
    }

    #[OA\Post(
        path: "/news",
        description: "Создает новость",
        summary: "Создание новости",
        requestBody: new OA\RequestBody(
            description: "Данные для создания новости",
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/StoreNews")
        ),
        tags: ["News"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Новость успешно создана.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(
                            property: "data",
                            ref: "#/components/schemas/News"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Ошибка валидации",
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationError")
            )
        ]
    )]
    public function store(StoreNewsRequest $request): JsonResponse
    {
        $news = $this->newsCreatingService->create($request->getTitle(), $request->getContentField());
        return response()->json([
            'success' => true,
            'data' => new NewsResource($news)
        ]);
    }

    #[OA\Get(
        path: "/news/{id}",
        description: "Показывает новость по ID",
        summary: "Показывает конкретную новость по ID",
        tags: ["News"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID новости",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Новость успешно получена.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(
                            property: "data",
                            ref: "#/components/schemas/News"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Новость не найдена",
                content: new OA\JsonContent(ref: "#/components/schemas/BaseException")
            )
        ]
    )]
    public function show(int $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new NewsResource($this->newsShowService->get($id))
        ]);
    }
}
