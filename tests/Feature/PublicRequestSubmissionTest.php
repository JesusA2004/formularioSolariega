<?php

use App\Mail\NewRequestNotification;
use App\Models\Request as BuzonRequest;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

test('the public form page loads', function () {
    $this->get(route('reportar.create'))->assertOk();
});

test('a collaborator can submit a message with an attachment and receives a folio', function () {
    Storage::fake('local');
    Mail::fake();

    $file = UploadedFile::fake()->image('evidencia.jpg');

    $response = $this->post(route('reportar.store'), [
        'full_name' => 'Juan Pérez',
        'department' => 'sistemas',
        'request_type' => 'queja',
        'description' => 'Esta es una descripción de prueba con más de veinte caracteres.',
        'involved_people' => '',
        'accepted_terms' => '1',
        'attachments' => [$file],
    ]);

    $request = BuzonRequest::firstOrFail();

    $response->assertRedirect(route('reportar.exito', ['folio' => $request->folio]));

    expect($request->folio)->toMatch('/^BZ-\d{4}-\d{6}$/');
    expect($request->full_name)->toBe('Juan Pérez');
    expect($request->has_evidence)->toBeTrue();
    expect($request->attachments)->toHaveCount(1);

    Storage::disk('local')->assertExists($request->attachments->first()->file_path);

    Mail::assertQueued(NewRequestNotification::class, fn ($mail) => $mail->request->is($request));
});

test('the full name is required', function () {
    Mail::fake();

    $response = $this->post(route('reportar.store'), [
        'department' => 'operaciones',
        'request_type' => 'sugerencia',
        'description' => 'Esta es una descripción de prueba con más de veinte caracteres.',
        'accepted_terms' => '1',
    ]);

    $response->assertSessionHasErrors('full_name');
    expect(BuzonRequest::count())->toBe(0);
});

test('the success page shows the folio without exposing sensitive data', function () {
    Mail::fake();

    $this->post(route('reportar.store'), [
        'full_name' => 'Ana López',
        'department' => 'otro',
        'request_type' => 'otro',
        'description' => 'Esta es una descripción de prueba con más de veinte caracteres.',
        'accepted_terms' => '1',
    ]);

    $request = BuzonRequest::firstOrFail();

    $response = $this->get(route('reportar.exito', ['folio' => $request->folio]));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('public/Gracias')
        ->where('folio', $request->folio)
    );
});

test('the public form is rate limited', function () {
    Mail::fake();

    $payload = [
        'full_name' => 'Colaborador de prueba',
        'department' => 'sistemas',
        'request_type' => 'queja',
        'description' => 'Esta es una descripción de prueba con más de veinte caracteres.',
        'accepted_terms' => '1',
    ];

    for ($i = 0; $i < 5; $i++) {
        $this->post(route('reportar.store'), $payload)->assertRedirect();
    }

    $this->post(route('reportar.store'), $payload)->assertStatus(429);
});

test('only admins can manage users', function () {
    $supervisor = User::factory()->create();

    $this->actingAs($supervisor)
        ->get(route('usuarios.index'))
        ->assertForbidden();

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('usuarios.index'))
        ->assertOk();
});

test('inactive users cannot access the admin panel', function () {
    $user = User::factory()->inactive()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertForbidden();
});
