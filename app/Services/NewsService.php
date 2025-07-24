<?php

namespace App\Services;

use App\Http\Controllers\API\LineController;
use App\Http\Resources\NewsCollection;
use App\Http\Resources\NewsResource;
use App\Http\Utility\LinePostUtility;
use App\Models\News;
use Illuminate\Support\Arr;

class NewsService
{
    protected $notificationService, $imageService;
    public function __construct(NotificationService $notificationService, ImageService $imageService)
    {
        $this->notificationService = $notificationService;
        $this->imageService = $imageService;
    }

    public function getList(array $filters = [], bool $shelter = false, bool $paginate = true, int $perPage = 20)
    {
        if (!$shelter) {
            $filters['center'] = true;
        }

        $query = News::with(['creator', 'editor', 'image'])->filter($filters);

        if (!isset($filters['sort'])) {
            $query->latest();
        }

        if (!$paginate) {
            $data = $query->get();
            return new NewsCollection($data);
        } else {
            $data = $query->paginate($perPage);
            $paginate = $data->setCollection(NewsResource::collection($data->items())->collection);
            return  $paginate;
        }
    }

    /**
     * Get a news by its id.
     */
    public function getById(int $id): News
    {
        return News::findOrFail($id);
    }

    /**
     * Create a news.
     */
    public function create(array $data): News
    {
        $news = News::create($data);
        $news->image = $this->imageService->uploadImage($data['image'] ?? null, News::IMAGE_FOLDER, News::class, $news->id);
        // Send a notification via socket and line
        LinePostUtility::line_oa_call(LineController::News_post, $news->toArray());
        $this->notify($news, 'created');
        return $news;
    }

    /**
     * Update a news.
     */
    public function update(News $news, array $data): News
    {
        $news->update($data);
        $news->refresh();
        $this->imageService->updateImage($data['image'] ?? null, News::IMAGE_FOLDER, News::class, $news->id);
        LinePostUtility::line_oa_call(LineController::News_update, $news->toArray());
        $this->notify($news, 'updated');
        return $news;
    }

    /**
     * Delete a news.
     */
    public function delete(News $news): bool
    {
        $this->imageService->deleteImage(News::IMAGE_FOLDER, News::class, $news->id);
        return $news->delete();
    }

    /**
     * Notification for news
     */
    public function notify(News $news, string $notiType): void
    {
        $template = match (true) {
            $notiType === 'created' && $news->shelter_id => 'shelter_news.created',
            $notiType === 'updated' && $news->shelter_id => 'shelter_news.updated',
            $notiType === 'created' => 'news.created',
            $notiType === 'updated' => 'news.updated',
            default => null,
        };

        if (!$template) return;

        $notification = $this->notificationService->createNotification(
            $template,
            ['title' => $news->title],
            $news->id,
            News::class,
            false
        );

        if ($news->shelter_id) {
            $this->notificationService->sendToStaff($notification, $news->id);
        } else {
            $this->notificationService->sendToAllUsers($notification);
        }
    }
}
