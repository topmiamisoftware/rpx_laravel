<?php

namespace App\Http\Controllers;

use App\Models\AppVersion;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AppVersionController extends Controller
{
    // Allow the user to download the app if they have a business account.
    public function download()
    {
        $user = Auth::user();

        if (! is_null($user->business)) {
            $userCanDownload = true;
        }

        if ($userCanDownload) {
            return Storage::download(
                env('BUSINESS_APP_DOWNLOAD_URL'),
                'SB-Business',
                [
                    'Content-Type' => 'application/vnd.android.package-archive'
                ]
            );
        }

        return response()->noContent();
    }

    /**
     * Check if the user is on the correct version of the app.
     */
    public function check(Request $request)
    {
        $validatedData = $request->validate([
            'installedVersion' => 'required|string'
        ]);

        $user = Auth::user();
        $v = AppVersion::where('user_id', $user->id)->get();

        $currentVersion = '';
        if ( count($v) > 0 && $validatedData['installedVersion'] !== $v[0]->version) {
            $v[0]->version = $validatedData['installedVersion'];
            $v[0]->save();
            $v[0]->refresh();
            $currentVersion = $v[0]->version;
        } else {
            if ( count($v) === 0 ) {
                $k = new AppVersion();
                $k->user_id = $user->id;
                $k->version = $validatedData['installedVersion'];
                $k->save();
                $k->refresh();
                $currentVersion = $k->version;
            } else {
                $v[0]->save();
                $currentVersion = $v[0]->version;
            }
        }

        $needsUpdate = false;
        if ($currentVersion !== env('BUSINESS_FRONT_END_VERSION') ) {
            $needsUpdate = true;
        }

        return response([
            'currentVersion' => $currentVersion,
            'needsUpdate' => $needsUpdate
        ]);
    }
}
