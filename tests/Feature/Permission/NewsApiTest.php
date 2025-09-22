<?php

use App\Models\News;
use App\Models\Shelter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

pest()->use(RefreshDatabase::class);

beforeEach(function () {
    $this->shelter = Shelter::first();
    $this->news = News::first();
});
/*
|--------------------------------------------------------------------------
| Guest Permission : คนทั่วไป
|--------------------------------------------------------------------------
*/
test('guest can view centralized news', function () {
    $response = $this->getJson('/api/news');
    $response->assertOk();
});

test('guest cannot view shelter-specific news', function () {
    $response = $this->getJson('/api/news?shelter_id=' . $this->shelter->id);
    $response->assertUnauthorized();
});

test('guest cannot create news', function () {
    $payload = ['title' => 'Breaking News', 'description' => 'This is the content'];
    $response = $this->postJson('/api/new', $payload);
    $response->assertUnauthorized();
});

test('guest cannot update news', function () {
    $payload = ['title' => 'Updated News', 'description' => 'This is updated'];
    $response = $this->postJson('/api/new/' . $this->news->id, $payload);
    $response->assertUnauthorized();
});

test('guest cannot delete news', function () {
    $response = $this->deleteJson('/api/new/' . $this->news->id);
    $response->assertUnauthorized();
});

/*
|--------------------------------------------------------------------------
| Admin Permission : เจ้าหน้าที่ส่วนกลาง
|--------------------------------------------------------------------------
*/