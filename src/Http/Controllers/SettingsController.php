<?php

namespace Psytelepat\Lootbox\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use Log;
use Hash;
use Former;
use Storage;
use Illuminate\Support\Arr;

use Psytelepat\Lootbox\Util;
use Psytelepat\Lootbox\Http\Controllers\AbstractController as BaseController;

class SettingsController extends BaseController
{
    public function index(Request $request)
    {
        $cfg = config('lootbox.settings');
        $list = Arr::get($cfg, 'list');

        if (is_array($list) && !empty($list)) {
            if ($request->isMethod('POST')) {
                foreach ($list as $section_tid => $section) {
                    $fields = Arr::get($section, 'fields');
                    if (is_array($fields) && !empty($fields)) {
                        foreach ($fields as $field_tid => $field) {
                            switch (Arr::get($field, 'type')) {
                                case 'file':
                                    if ($file = $request->file($field_tid)) {
                                        // delete previous

                                        $filename = Util::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                                        $file->move(storage_path('app/public/' . Arr::get($field, 'path')), $filename);
                                        config([ 'site-settings.'.$field_tid => $filename, ]);
                                        config([ 'site-settings.'.$field_tid . '_filesize' => filesize(storage_path('app/public/' . Arr::get($field, 'path')) . $filename), ]);
                                    } elseif ($request->input('delete_'.$field_tid) && $file = config('site-settings.'.$field_tid)) {
                                        Storage::disk('public')->delete('app/public/' . Arr::get($field, 'path') . $file);
                                        config([ 'site-settings.'.$field_tid => null, ]);
                                        config([ 'site-settings.'.$field_tid . '_filesize' => null, ]);
                                    }

                                    break;
                                default:
                                    config([ 'site-settings.'.$field_tid => $request->input($field_tid) ]);
                                    break;
                            }
                        }
                    }
                }
                self::save();
            }
        }

        $settings = config('site-settings');
        Former::populate($settings);

        return $this->template->push('content', view('lootbox::admin.settings', [
            'list' => $list,
            'model' => $settings,
        ]));
    }

    public static function save()
    {
        if ($config_file = fopen(config_path('site-settings.php'), 'w+')) {
            fputs($config_file, "<?php\nreturn ".var_export(config('site-settings'), true).";\n?>");
            fclose($config_file);
        }
    }
}
