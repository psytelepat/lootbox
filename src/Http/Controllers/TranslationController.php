<?php

namespace Psytelepat\Lootbox\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use Log;
use Hash;
use Former;
use Illuminate\Support\Arr;

use Psytelepat\Lootbox\DotArray;
use Psytelepat\Lootbox\Util;
use Psytelepat\Lootbox\Http\Controllers\AbstractController as BaseController;

class TranslationController extends BaseController
{
    public function filePath(string $lang, string $file, string $folder = null)
    {
        return resource_path('lang/'.$lang.'/'.( $folder ? $folder.'/' : '' ).$file.'.php');
    }

    public function index(Request $request)
    {
        $locales = [];

        foreach (config('app.locales') as $localetid => $locale) {
            $files = [];

            if ($dir = opendir(resource_path('lang/'.$localetid))) {
                while ($file = readdir($dir)) {
                    if ($file == '.' || $file == '..') {
                        continue;
                    }
                    if (is_dir(resource_path('lang/'.$localetid.'/'.$file))) {
                        if ($dir2 = opendir(resource_path('lang/'.$localetid.'/'.$file))) {
                            while ($file2 = readdir($dir2)) {
                                if ($file2 == '.' || $file2 == '..') {
                                    continue;
                                }
                                if (preg_match('/\.php$/', $file2)) {
                                    $files[] = [
                                        'lang' => $localetid,
                                        'folder' => $file,
                                        'file' => preg_replace('/\.php$/', '', $file2),
                                    ];
                                }
                            }
                        }
                    } else {
                        if (preg_match('/\.php$/', $file)) {
                            $files[] = [
                                'lang' => $localetid,
                                'folder' => null,
                                'file' => preg_replace('/\.php$/', '', $file),
                            ];
                        }
                    }
                }
            }

            $filenames = array();
            foreach ($files as $key => $row) {
                $filenames[$key] = $row['file'];
            }
            array_multisort($filenames, SORT_ASC, $files);

            $locales[$localetid] = [
                'locale' => $locale,
                'files' => $files,
            ];
        }

        return $this->template->push('content', view('lootbox::translation.index', [
            'locales' => $locales,
        ]));
    }

    public function form(Request $request, string $lang, string $file, string $folder = null)
    {
        if ($request->isMethod('POST')) {
            $post = $request->all();
            $this->updateTranslation($post, $file, $folder);
        }

        $data = require($this->filePath($lang, $file, $folder));

        $form_path = route('lootbox.translation.form', [ 'lang' => $lang, 'file' => $file, 'folder' => $folder ]);
        $form_body = Former::open($form_path)->method('post')->data_form_mode('edit');
        $form_body .= $this->renderForm($data);
        $form_body .= Former::close();

        return $this->template->push('content', view('lootbox::translation.form', [
            'file' => $file,
            'folder' => $folder,
            'form_path_json' => route('lootbox.translation.update', [ 'lang' => $lang, 'file' => $file, 'folder' => $folder ]),
            'typo_path_json' => route('lootbox.translation.typo'),
            'form_body' => $form_body,
        ]));
    }

    public function update(Request $request, string $lang, string $file, string $folder = null)
    {
        $post = $request->all();
        $this->updateTranslation($post, $lang, $file, $folder);

        return [
            'success' => 1,
        ];
    }

    protected function updateTranslation(&$post, string $lang, string $file, string $folder = null)
    {

        $filePath = $this->filePath($lang, $file, $folder);

        $olddata = require($filePath);
        $newdata = new DotArray($olddata);
        
        unset($post['_token']);

        foreach ($post as $key => $val) {
            $newdata[str_replace('-', '.', $key)] = $val;
        }

        $this->save($newdata, $lang, $file, $folder);
    }

    public function renderForm($val, $parent_key = null, $key = null, $depth = 0)
    {
        $form_body = $depth > 0 ? '<div style="padding-left: 20px;">' : null;
        if (!is_array($val)) {
            $field_name = str_replace('.', '-', ( $parent_key ? $parent_key.'.' : null ).$key);
            $form_body .= '<div class="form-group row"><div class="col-2">'.$key.'</div><div class="col-8">'.
                Former::text($field_name)->label($key)->value($val)->render().'</div>'
                .'<div class="col-1"><button class="btn btn-default btn-sm js-'.$field_name.'-save">Сохранить</button></div>'
                .'<div class="col-1"><button class="btn btn-default btn-sm js-'.$field_name.'-typo">Типограф</button></div>'
                .'</div>';
        } else {
            $form_body .= '<div class="form-group row"><div class="col-2"><h3>'.$key.'</h3></div></div>';
            foreach ($val as $local_key => $local_val) {
                $form_body .= $this->renderForm($local_val, ( $parent_key ? $parent_key.'.' : null ) .$key, $local_key, $depth + 1);
            }
        }

        $form_body .= $depth > 0 ? '</div>' : null;

        return $form_body;
    }

    public function save(&$newdata, $lang, $file, $folder = null)
    {
        $filePath = $this->filePath($lang, $file, $folder);
        if ($config_file = fopen($filePath, 'w+')) {
            fputs($config_file, "<?php\nreturn ".$newdata->export().";\n?>");
            fclose($config_file);
        }
    }

    public function typo(Request $request)
    {
        $text = $request->post('text');

        $remoteTypograf = new \Psytelepat\Lootbox\RemoteTypograf();

        $remoteTypograf->htmlEntities();
        $remoteTypograf->br(false);
        $remoteTypograf->p(false);
        $remoteTypograf->nobr(3);
        $remoteTypograf->quotA('laquo raquo');
        $remoteTypograf->quotB('bdquo ldquo');

        return [
            'success' => 1,
            'source' => $text,
            'result' => $remoteTypograf->processText($text),
        ];
    }
}
