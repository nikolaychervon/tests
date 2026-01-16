<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VideoPost\StoreVideoPostRequest;
use App\Http\Resources\VideoPost\VideoPostResource;
use App\Services\VideoPost\VideoPostCreatingService;
use App\Services\VideoPost\VideoPostListService;
use App\Services\VideoPost\VideoPostShowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class VideoPostController extends Controller
{
    public function __construct(
        private VideoPostCreatingService $videoPostCreatingService,
        private VideoPostShowService $videoPostShowService,
        private VideoPostListService $videoPostListService,
    ) {
    }

    #[OA\Get(
        path: "/video-posts",
        description: "Возвращает список видео постов с курсорной пагинацией",
        summary: "Список видео постов",
        tags: ["VideoPosts"],
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
                            items: new OA\Items(ref: "#/components/schemas/VideoPost")
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
        $videoPosts = $this->videoPostListService->getPaginatedList($perPage);

        return response()->json([
            'success' => true,
            'data' => VideoPostResource::collection($videoPosts->items()),
            'meta' => [
                'next_cursor' => $videoPosts->nextCursor()?->encode(),
                'prev_cursor' => $videoPosts->previousCursor()?->encode(),
                'has_more' => $videoPosts->hasMorePages(),
                'per_page' => $perPage,
                'count' => count($videoPosts->items()),
            ],
        ]);
    }

    #[OA\Post(
        path: "/video-posts",
        description: "Создает видео пост",
        summary: "Создание видео поста",
        requestBody: new OA\RequestBody(
            description: "Данные для создания видео поста",
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(ref: "#/components/schemas/StoreVideoPost")
            )
        ),
        tags: ["VideoPosts"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Видео пост успешно создан.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(
                            property: "data",
                            ref: "#/components/schemas/VideoPost",
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
    public function store(StoreVideoPostRequest $request): JsonResponse
    {
        $videoPost = $this->videoPostCreatingService->create($request->getTitle(), $request->getVideo());
        return response()->json([
            'success' => true,
            'data' => new VideoPostResource($videoPost)
        ]);
    }

    #[OA\Get(
        path: "/video-posts/{id}",
        description: "Показывает видео пост по ID",
        summary: "Показывает конкретный видео пост по ID",
        tags: ["VideoPosts"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID видео поста.",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Видео пост успешно получен.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(
                            property: "data",
                            ref: "#/components/schemas/VideoPost"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Видео пост не найден",
                content: new OA\JsonContent(ref: "#/components/schemas/BaseException")
            )
        ]
    )]
    public function show(int $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new VideoPostResource($this->videoPostShowService->get($id))
        ]);
    }
}
