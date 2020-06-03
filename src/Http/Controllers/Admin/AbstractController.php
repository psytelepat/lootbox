<?php

namespace Psytelepat\Lootbox\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Support\Renderable;

use Psytelepat\Lootbox\Http\Controllers\AbstractController as BaseController;
use Validation;
use Former;

/**
 * Абстрактный контроллер администрирования моделей в БД
 */
abstract class AbstractController extends BaseController
{
    protected $ADMIN_FORM = 'lootbox::admin.form';
    protected $ADMIN_DELETE = 'lootbox::admin.delete';

    protected $MODEL_CLASS;
    protected $ADMIN_ROUTE;
    protected $LANG_ROUTE;
    protected $FORM;
    
    /**
     * Возвращает массив рулзов для вадидации реквеста
     * @param  string $mode create|edit|delete
     * @param  mixed $model
     * @return array
     */
    public static function validationRules(string $mode, Model $model = null): array
    {
        return [];
    }

    /**
     * Колонки для LaravelTable
     * @param  mixed $list
     * @return void
     */
    public static function listColumns(&$list): void
    {
        $list->column('id')->sortable(true, 'asc');
    }
    
    /**
     * Метод обработки запроса
     *
     * @param  Request $request
     * @param  mixed $model
     * @param  string $mode create|edit|delete
     * @return bool
     */
    public static function handleRequest(Request &$request, Model &$model, string $mode): bool
    {
        return true;
    }

        
    /**
     * Сборщик входных данных запроса
     *
     * @param  string $mode create|edit|delete
     * @param  mixed $model
     * @return array
     */
    public static function gatherFormData(string $mode, Model $model = null): array
    {
        $data = [
            'form_action' => url()->current(),
            'form_mode' => $mode,
        ];
        if ($model) {
            $data['model'] = $model;
        }
        return $data;
    }


    /**
     * Возвращает вьюху с шапкой формы
     *
     * @param  string $mode create|edit|delete
     * @param  mixed $model
     * @return Renderable
     */
    private function pageHeading(string $mode, Model $model = null): Renderable
    {
        return view('lootbox::admin.page-heading', [
            'title' => $this->MODEL_CLASS::adminFormTitle($mode, $model),
            'breadcrumbs' => [
                [
                    'name' => __($this->LANG_ROUTE),
                    'route' => route('lootbox.'.$this->ADMIN_ROUTE.'.index'),
                ],
            ]
        ]);
    }
    
    /**
     * Возвращает список роутов модели для LaravelTable
     * @return array
     */
    private function listRoutes(): array
    {
        return [
            'index'     => [ 'name' => 'lootbox.'.$this->ADMIN_ROUTE.'.index' ],
            'create'    => [ 'name' => 'lootbox.'.$this->ADMIN_ROUTE.'.create' ],
            'edit'      => [ 'name' => 'lootbox.'.$this->ADMIN_ROUTE.'.edit' ],
            'destroy'   => [ 'name' => 'lootbox.'.$this->ADMIN_ROUTE.'.delete' ],
        ];
    }


    // CUD //
    
    /**
     * Список
     *
     * @param  Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $list = (new \Okipa\LaravelTable\Table)->model($this->MODEL_CLASS)->routes($this->listRoutes());
        static::listColumns($list);
        return $this->template->push('content', $list->render());
    }
    
    /**
     * Создание
     *
     * @param  Request $request
     * @return mixed
     */
    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate(static::validationRules(__FUNCTION__));

            $model = new $this->MODEL_CLASS;
            static::handleRequest($request, $model, __FUNCTION__);
            $model->save();

            return redirect()->to($this->MODEL_CLASS::adminRoute('edit', $model));
        } else {
            return $this->template->plugin(['tagsinput','icheck','datepicker','clockpicker',])->push('content', view($this->ADMIN_FORM, [
                'pageHeading' => $this->pageHeading(__FUNCTION__),
                'form' => view($this->FORM, $this->gatherFormData(__FUNCTION__)),
            ]));
        }
    }
    
    /**
     * Редактирование
     *
     * @param  Request $request
     * @param  int $id
     * @return midex
     */
    public function edit(Request $request, int $id)
    {
        $model = $this->MODEL_CLASS::findOrFail($id);

        if ($request->isMethod('post')) {
            $request->validate(static::validationRules(__FUNCTION__, $model));

            static::handleRequest($request, $model, __FUNCTION__);
            $model->save();

            if (isset($_POST['action_update'])) {
                return redirect()->to($this->MODEL_CLASS::adminRoute(__FUNCTION__, $model));
            } else {
                return redirect()->to($this->MODEL_CLASS::adminRoute());
            }
        } else {
            Former::populate($model);

            return $this->template->plugin(['tagsinput','icheck','datepicker','clockpicker',])->push('content', view($this->ADMIN_FORM, [
                'pageHeading' => $this->pageHeading(__FUNCTION__, $model),
                'form' => view($this->FORM, $this->gatherFormData(__FUNCTION__, $model)),
            ]));
        }
    }
    
    /**
     * Сортировка
     *
     * @param  Request $request
     * @param  int $id
     * @return mixed
     */
    public function repos(Request $request, int $id): array
    {
        $from = $this->MODEL_CLASS::find($id);
        $to = $this->MODEL_CLASS::find((int)$request->input('to'));
        if (!$from || !$to) {
            return [
                'error' => 1,
                'message' => 'Unable to load both models',
            ];
        }

        try {
            $from->reposTo($to);
            return [
                'success' => 1,
            ];
        } catch (Exception $e) {
            return [
                'error' => 1,
                'message' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * Удаление
     *
     * @param  Request $request
     * @param  int $id
     * @return mixed
     */
    public function delete(Request $request, int $id)
    {
        $model = $this->MODEL_CLASS::findOrFail($id);
        if ($request->isMethod('post')) {
            $model->delete();
            return redirect()->to($this->MODEL_CLASS::adminRoute());
        } else {
            return $this->template->push('content', view($this->ADMIN_DELETE, [
                'pageHeading' => $this->pageHeading(__FUNCTION__, $model),
                'model' => $model,
            ]));
        }
    }

        
    /**
     * Метод для формирования ссылки для LaravelTable на редактирование модели
     *
     * @param  mixed $model
     * @return void
     */
    public static function formatLinkEdit(Model $model): string
    {
        $class = get_class($model);
        return $class::adminRoute('edit', $model);
    }
 
    /**
     * Метод для форматирования колонки для перетаскивания элементов в таблице LaravelTable
     *
     * @param  mixed $model
     * @param  mixed $column
     * @return void
     */
    public static function formatPos(Model $model, $column): string
    {
        return '<i class="fa fa-sort repos-handle js-repos-handle" title="Зажмите и перетаскивайте элементы между собой"></i>';
    }
    
    /**
     * Метод для форматирования колонки статуса публикации модели
     *
     * @param  mixed $model
     * @param  mixed $column
     * @return void
     */
    public static function formatIsPublished(Model $model, $column): string
    {
        $column_name = $column->databaseDefaultColumn;
        return $model->$column_name ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    }
}
