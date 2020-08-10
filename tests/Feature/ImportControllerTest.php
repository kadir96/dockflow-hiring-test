<?php

namespace Tests\Feature;

use App\Tradeflow;
use App\Container;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImportControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function test_import_successfully()
    {
        $this->assertEquals(0, Tradeflow::count());
        $this->assertEquals(0, Container::count());

        $response = $this->request($this->getStubPath('3valid.xlsx'));

        $response->assertStatus(200);
        $this->assertEquals(3, Tradeflow::count());
        $this->assertEquals(3, Container::count());

        // Tradeflows should be ordered by their name
        $response->assertJsonFragment([
            'tradeflows' => [
                [
                    'id' => 1,
                    'name' => '5700696000',
                    'containers' => [
                        [
                            'id' => 1,
                            'reference' => 'FCIU 658783-1',
                        ],
                    ],
                ],
                [
                    'id' => 3,
                    'name' => '5700697633',
                    'containers' => [
                        [
                            'id' => 3,
                            'reference' => 'FCIU 660212-9',
                        ],
                    ],
                ],
                [
                    'id' => 2,
                    'name' => '5700697684',
                    'containers' => [
                        [
                            'id' => 2,
                            'reference' => 'HASU 129740-9',
                        ],
                    ],
                ]
            ],
            'invalid_container_references' => [],
            'tradeflows_without_containers' => [],
        ]);
    }

    public function test_import_with_invalid_container_references()
    {
        $response = $this->request($this->getStubPath('3valid-1invalid.xlsx'));

        $response->assertStatus(200);
        $this->assertEquals(3, Tradeflow::count());
        $this->assertEquals(3, Container::count());

        // Tradeflows should be ordered by their name
        $response->assertJsonFragment([
            'tradeflows' => [
                [
                    'id' => 1,
                    'name' => '5700696000',
                    'containers' => [
                        [
                            'id' => 1,
                            'reference' => 'FCIU 658783-1',
                        ],
                    ],
                ],
                [
                    'id' => 3,
                    'name' => '5700697633',
                    'containers' => [
                        [
                            'id' => 3,
                            'reference' => 'FCIU 660212-9',
                        ],
                    ],
                ],
                [
                    'id' => 2,
                    'name' => '5700697684',
                    'containers' => [
                        [
                            'id' => 2,
                            'reference' => 'HASU 129740-9',
                        ],
                    ],
                ]
            ],
            'invalid_container_references' => [
                'FC 660212-9',
            ],
            'tradeflows_without_containers' => [],
        ]);
    }

    public function test_import_with_tradeflows_with_no_containers()
    {
        $response = $this->request($this->getStubPath('2invalid.xlsx'));

        $response->assertStatus(200);
        $this->assertEquals(0, Tradeflow::count());
        $this->assertEquals(0, Container::count());

        // Tradeflows should be ordered by their name
        $response->assertJsonFragment([
            'tradeflows' => [],
            'invalid_container_references' => [
                'FCIU 658783000-1',
                'HASU 129A740-9',
            ],
            'tradeflows_without_containers' => [
                '5700696000',
                '5700697684',
            ],
        ]);
    }

    public function test_file_is_required()
    {
        $response = $this->postJson('/import');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('file');
    }

    public function test_file_should_be_a_file()
    {
        $response = $this->postJson('/import', [
            'file' => 'as a string'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('file');
    }

    public function test_import_from_unsupported_file_type()
    {
        $response = $this->request($this->getStubPath('text-file'));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('file');
    }

    /**
     * Path of the file to upload.
     *
     * @param string $path
     * @return \Illuminate\Testing\TestResponse
     */
    private function request(string $path)
    {
        return $this->postJson('/import', [
            'file' => UploadedFile::fake()->createWithContent(basename($path), file_get_contents($path)),
        ]);
    }
}
