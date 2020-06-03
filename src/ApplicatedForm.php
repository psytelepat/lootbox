<?php

namespace Psytelepat\Lootbox;

use Illuminate\Database\Eloquent\Model;

class ApplicatedForm extends Model implements \Psytelepat\Lootbox\AdminableModelInterface
{
    protected $table = 'applicated_forms';
    protected $fillable = [
        'form_id',
        'payload',
        'sent_at',
    ];

    // AdminableModelInterface

    public static function adminFormTitle(string $mode = null, $model = null): string
    {
        switch ($mode) {
            case 'create':
                return __('lootbox::common.applicated-form_new');
            break;
            case 'delete':
                return __('lootbox::common.applicated-form_delete', [ 'title' =>  __('applicated-form.id.'.$model->form_id) ]);
            break;
            default:
                return  __('applicated-form.id.'.$model->form_id);
            break;
        }
    }

    public static function adminRoute(string $mode = 'index', $model = null): string
    {
        switch ($mode) {
            case 'create':
                return route('lootbox.applicated-form.'.$mode);
            break;
            case 'edit':
                return route('lootbox.applicated-form.'.$mode, $model);
            break;
            case 'delete':
                return route('lootbox.applicated-form.'.$mode, $model);
            break;
            case 'index':
                return route('lootbox.applicated-form.'.$mode);
            break;
            default:
                throw new \InvalidArgumetException('Invalid $mode');
            break;
        }
    }
}
