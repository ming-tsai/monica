<?php

namespace Tests\Unit\Services\Account;

use Tests\TestCase;
use App\Models\User\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Services\Account\Settings\ExportAccount;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExportAccountTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_exports_account_information()
    {
        Storage::fake('local');

        $user = factory(User::class)->create([]);

        $request = [
            'account_id' => $user->account->id,
            'user_id' => $user->id,
        ];

        $filename = app(ExportAccount::class)->execute($request);

        $this->assertStringStartsWith('temp/', $filename);
        $this->assertStringEndsWith('.sql', $filename);
        Storage::disk('local')->assertExists($filename);
    }

    public function test_it_fails_if_wrong_parameters_are_given()
    {
        $request = [];

        $this->expectException(ValidationException::class);
        app(ExportAccount::class)->execute($request);
    }
}