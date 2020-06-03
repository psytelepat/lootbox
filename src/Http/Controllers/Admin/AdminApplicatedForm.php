<?php

namespace Psytelepat\Lootbox\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

use Psytelepat\Lootbox\Http\Controllers\Admin\AbstractController as AdminController;

/**
 * Контроллер просмотра форм, заполняемых юзерами на сайте
 */
class AdminApplicatedForm extends AdminController
{
    protected $MODEL_CLASS = 'Psytelepat\Lootbox\ApplicatedForm';
    protected $FORM = 'lootbox::applicated-form.form';
    protected $ADMIN_ROUTE = 'applicated-form';
    protected $LANG_ROUTE = 'lootbox::common.applicated-forms';

    private function listRoutes(): array
    {
        return [
            'index'     => [ 'name' => 'lootbox.'.$this->ADMIN_ROUTE.'.index' ],
            'edit'      => [ 'name' => 'lootbox.'.$this->ADMIN_ROUTE.'.edit' ],
        ];
    }

    public static function listColumns(&$list): void
    {
        $link = \Closure::fromCallable(static::class.'::formatLinkEdit');
        $list->column('id')->sortable()->link($link);
        $list->column('form_id')->sortable()->link($link)->html(function ($model, $column) {
            return '<a href="'.get_class($model)::adminRoute('edit', $model).'">'.__('applicated-form.id.'.$model->form_id).'</a>';
        });
        $list->column('payload')->searchable()->html(function ($model, $column) {
            $data = json_decode($model->payload, true);
            $lines = [];
            foreach ($data as $key => $val) {
                if (is_string($val)) {
                    $lines[] = __('validation.attributes.'.$key) . ': <b>' . e($val) . '</b>';
                } else {
                    $lines[] = __('validation.attributes.'.$key) . ': <b>' . e(var_export($val, true)) . '</b>';
                }
            }
            return implode('<br>', $lines);
        });
        $list->column('created_at')->sortable(true, 'desc');
        $list->column('sent_at')->sortable();
    }
}
