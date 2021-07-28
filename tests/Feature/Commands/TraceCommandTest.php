<?php

namespace Tests\Feature\Commands;

use Blueprint\Blueprint;
use Blueprint\Builder;
use Blueprint\Commands\TraceCommand;
use Blueprint\Tracer;
use Illuminate\Support\Facades\File;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\TestCase;

/**
 * @covers \Blueprint\Commands\TraceCommand
 */
class TraceCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function it_shows_error_if_no_model_found()
    {
        $tracer = $this->mock(Tracer::class);

        $tracer->shouldReceive('execute')
            ->with(resolve(Blueprint::class), $this->files, '')
            ->andReturn([]);

        $this->artisan('blueprint:trace')
            ->assertExitCode(0)
            ->expectsOutput('No models found');
    }

    /** @test */
    public function it_shows_the_number_of_traced_models()
    {
        $tracer = $this->mock(Tracer::class);

        $tracer->shouldReceive('execute')
            ->with(resolve(Blueprint::class), $this->files, '')
            ->andReturn([
                "Model" => [],
                "OtherModel" => [],
            ]);

        $this->artisan('blueprint:trace')
            ->assertExitCode(0)
            ->expectsOutput('Traced 2 models');
    }

    /** @test */
    public function it_passes_the_command_path_to_tracer()
    {
        $this->filesystem->shouldReceive('exists')
            ->with('test.yml')
            ->andReturnTrue();

        $builder = $this->mock(Builder::class);

        $builder->shouldReceive('execute')
            ->with(resolve(Blueprint::class), $this->files, 'vendor/package/src/app/Models')
            ->andReturn([]);

        $this->artisan('blueprint:trace --path=vendor/package/src/app/Models')
            ->assertExitCode(0);
    }
}
