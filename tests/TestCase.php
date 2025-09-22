<?php

namespace Tests;

use App\Models\News;
use App\Models\Shelter;
use Database\Seeders\TestSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    //
    protected $seeder = TestSeeder::class;

    protected $shelter;
    protected $news;

    // protected function setUp(): void
    // {
    //     parent::setUp();

    //     // เตรียมข้อมูล factory
    //     $this->shelter = Shelter::first();
    //     $this->news = News::first();
    // }
}
