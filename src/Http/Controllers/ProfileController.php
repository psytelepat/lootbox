<?php

namespace Psytelepat\Lootbox\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use Log;
use Hash;
use Former;

use Psytelepat\Lootbox\Util;
use Psytelepat\Lootbox\Http\Controllers\AbstractController as BaseController;

use Psytelepat\Lootbox\User\Avatar;

class ProfileController extends BaseController
{
    public function index(Request $request)
    {
        if ($request->isMethod('POST')) {
            $user = auth()->user();
            if ($current_password = $request->input('current_password')) {
                if (Hash::check($current_password, $user->password)) {
                    $new_password = $request->input('new_password');
                    $confirm_new_password = $request->input('confirm_new_password');
                    if ($new_password) {
                        if ($new_password == $confirm_new_password) {
                            $user->password = Hash::make($new_password);
                        } else {
                        }
                    } else {
                    }
                } else {
                }
            }

            $name = $request->input('name');
            if (strlen($name) && ( $user->name != $name )) {
                $user->name = $name;
            }

            $user->save();
        }

        Former::populate(auth()->user());

        return $this->template->push('content', view('lootbox::admin.profile', [
            'model' => auth()->user(),
        ]));
    }
}
