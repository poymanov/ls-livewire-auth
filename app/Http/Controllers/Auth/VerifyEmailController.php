<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\VerifyEmailService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class VerifyEmailController extends Controller
{
    /** @var VerifyEmailService */
    private VerifyEmailService $verifyEmailService;

    /**
     * @param VerifyEmailService $verifyEmailService
     */
    public function __construct(VerifyEmailService $verifyEmailService)
    {
        $this->verifyEmailService = $verifyEmailService;
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Request $request)
    {
        $userId    = $request->route('id');
        $emailHash = $request->route('hash');

        try {
            $this->verifyEmailService->verify((int) $userId, $emailHash);
            session()->flash('alert.success', Lang::get('mail.verification.successful'));
        } catch (\Throwable $e) {
            session()->flash('alert.error', $e->getMessage());
        }

        return redirect((route('home')));
    }
}
