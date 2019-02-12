<?php

namespace Crystoline\LaraRestApi;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Rest Api trait for Api Controller. Provide CRUD.
 */
trait RestApiTrait
{
    /**
     * Status Codes response ok.
     * @var int
     */
    public static $STATUS_CODE_DONE = 200;
    /**
     * Status Codes response created.
     * @var int
     */
    public static $STATUS_CODE_CREATED = 201;
    /**
     * Status Codes response deleted.
     * @var int
     */
    public static $STATUS_CODE_REMOVED = 204;
    /**
     * Status Codes invalid response.
     * @var int
     */
    public static $STATUS_CODE_NOT_VALID = 400;
    /**
     * Status Codes response not allowed.
     * @var int
     */
    public static $STATUS_CODE_NOT_ALLOWED = 405;
    /**
     * Status Codes response not created.
     * @var int
     */
    public static $STATUS_CODE_NOT_CREATED = 406;
    /**
     * Status Codes response not found.
     * @var int
     */
    public static $STATUS_CODE_NOT_FOUND = 404;
    /**
     * Status Codes response duplicate.
     * @var int
     */
    public static $STATUS_CODE_CONFLICT = 409;
    /**
     * Status Codes response Unauthorized.
     * @var int
     */
    public static $STATUS_CODE_PERMISSION = 401;
    /**
     * Status Codes response Access Denied.
     * @var int
     */
    public static $STATUS_CODE_FORBIDDEN = 403;
    /**
     * Status Codes response Server Error.
     * @var int
     */
    public static $STATUS_CODE_SERVER_ERROR = 500;
    /**
     * Status Codes response no data.
     * @var int
     */
    public static $STATUS_CODE_NO_RECORD = 407;
    protected $statusCodes = [
        'done' => 200,
        'created' => 201,
        'removed' => 204,
        'not_valid' => 400,
        'not_found' => 404,
        'not_record' => 407,
        'conflict' => 409,
        'permissions' => 401,
        'server_error' => 500,
    ];


    /**
     * @var int Number of pages
     */
    protected $pages = 50;

    /**
     * List Objects.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        /** @var Model $m */
        $m = self::getModel();
        $data = $m::query();
        $pages = self::getPages();
        $searchables = self::searchable();
        $orderBy = self::orderBy() ?: [];
        self::filter($request, $data);
        self::doSearch($request, $data, $searchables);
        self::doOrderBy($request, $data, $orderBy);
        $data = self::paginate($request, $data, $pages);

        if ($data instanceof Builder) {
            $data = $data->get();
        }

        $this->beforeList($data);

        return response()->json($data, self::$STATUS_CODE_DONE);
    }

    /**
     * get The Model name used. with full namespace
     * @return string
     */
    public static function getModel(): string
    {
        return Model::class;
    }

    /**
     * return number pages for pagination
     * @return int
     */
    public static function getPages(): int
    {
        return 50;
    }

    /**
     * Return array of searchable fields
     * @return array
     */
    public static function searchable(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public static function orderBy(): array
    {
        return [
        ];
    }

    /**
     * Filter data using request
     * @param Request $request
     * @param $query
     */
    public static function filter(Request $request, $query)
    {
    }

    /**
     * Perform wild-card search
     * @param Request $request
     * @param Builder $builder
     * @param $searchables
     * return none, Builder passed by reference
     */
    public static function doSearch(Request $request, Builder $builder, $searchables) /*:Builder*/
    {
        $builder->where(function (Builder $builder) use ($request, $searchables) {
            if ($search = $request->input('search')) {
                $keywords = explode(' ', trim($search));
                if ($searchables) {
                    $i = 0;
                    foreach ($searchables as $searchable) {
                        foreach ($keywords as $keyword) {
                            $builder->orWhere($searchable, 'like', "%{$keyword}%");
                        }
                    }
                }
            }
            if ($search = $request->input('qsearch')) {
                if ($searchables) {
                    $i = 0;
                    foreach ($searchables as $searchable) {
                        $builder->orWhere($searchable, 'like', "%{$search}%");
                    }
                }
            }
        });

        //return $builder;
    }

    /**
     * Order Data
     * @param Request $request
     * @param Builder $builder
     * @param array $orderBy
     */
    public static function doOrderBy(Request $request, Builder $builder, array $orderBy)
    {
        if ($orderBy) {
            foreach ($orderBy as $field => $direction) {
                $builder->orderBy($field, $direction);
            }
        }
    }

    /**
     * Paginate Data
     * @param Request $request
     * @param Builder $data
     * @param int $pages
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|Builder
     */
    public static function paginate(Request $request, $data, $pages = 50)
    {
        $should_paginate = $request->input('paginate', 'yes');

        if ('yes' == $should_paginate) {
            $data = $data->paginate($request->input('pages', $pages));
        }

        return $data;
    }

    /**
     * Perform action before data list
     * @param $data
     */
    public function beforeList($data)
    {
    }

    /**
     * Show records.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $m = self::getModel();
        $data = $m::find($id);

        if (is_null($data)) {
            return response()->json(['message' => 'Record was not found'], self::$STATUS_CODE_NOT_FOUND);
        }
        $this->beforeShow($data);

        return response()->json($data, self::$STATUS_CODE_DONE);
    }

    /**
     * Perform action before data show
     * @param $data
     */
    public function beforeShow($data)
    {
    }

    /**
     * Store Record.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        /** @var Model $m */
        $m = self::getModel();
        $rules = self::getValidationRules();
        $message = self::getValidationMessages();

        // $validator = Validator::make($request->all(), $rules, $message);
        $this->validate($request, $rules, $message);
        /*
           if ($validator->fails()) {
               return  response()->json($validator->errors(), self::$STATUS_CODE_NOT_VALID);
           } */
        DB::beginTransaction();

        //try{
        if (!$this->beforeStore($request)) {
            DB::rollback();

            return response()->json(['message' => 'could not create record (Duplicate Record)'], self::$STATUS_CODE_SERVER_ERROR);
        }
        self::doUpload($request);
        $input = $request->input();


        /*unset($input['school']);
        unset($input['staff']);*/

        //dump($input);
        $data = $m::create($input);

        //catch (\Exception $exception){
        //DB::rollback();
        //todo remove Exception message
        //return response()->json( ['message' => 'An error occurred while creating record: '.$exception->getMessage().', Line:'.$exception->getFile().'/'.$exception->getLine()], self::$STATUS_CODE_CONFLICT);
        //}
        if (!$this->afterStore($request, $data)) {
            DB::rollback();

            return response()->json(['message' => 'could not successfully create record'], self::$STATUS_CODE_SERVER_ERROR);
        }

        DB::commit();


        $this->beforeShow($data);
        return response()->json($data, self::$STATUS_CODE_CREATED);
    }

    /**
     * @return array
     */
    public static function getValidationRules(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public static function getValidationMessages(): array
    {
        return [];
    }

    /**
     * Perform action before data store
     * @param Request $request
     * @return bool
     */
    public function beforeStore(Request $request): bool
    {
        return true;
    }

    /**
     * Perform file upload for request
     * @param Request $request
     * @param Model $object
     */
    public static function doUpload(Request $request, $object = null)
    {
        //dd('kdkd');
        $data = $request->all();
        foreach ($data as $key => $val) {

            if ($request->hasFile($key) and $request->file($key)->isValid()) {

                $original = isset($object->$key) ? $object->$key : null;

                $interfaces = class_implements(self::class);
                $base = (isset($interfaces[ISchoolFileUpload::class])) ? self::fileBasePath($request) : '';
                if ($base) {
                    $base = trim($base, '/,\\') . '/';
                }
                $path = $request->$key->store('public/' . $base . $key);
                $path = str_replace('public/', 'storage/', $path);

                $path_url = asset($path);

                $request->files->remove($key);
                $request->merge([$key => $path_url]);

                if (!is_null($original)) {
                    Storage::delete(str_replace('storage/', 'public/', $original));
                }
            }
        }

    }

    /**
     * Perform action after data store
     * @param Request $request
     * @param $data
     * @return bool
     */
    public function afterStore(Request $request, $data): bool
    {
        return true;
    }

    /**
     * Update Record.
     *
     * @param Request $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        /** @var Model $m */
        $m = self::getModel();
        $model = $m::find($id);

        if (is_null($model)) {
            return response()->json(['message' => 'Record was not found'], self::$STATUS_CODE_NOT_FOUND);
        }

        $rules = self::getValidationRulesForUpdate($model);
        $message = self::getValidationMessages();
        $this->validate($request, $rules, $message);
        /*  $validator = Validator::make($request->all(), $rules, $message);
         if ($validator->fails()) {
             return  response()->json($validator->errors(), self::$STATUS_CODE_NOT_VALID);
         } */

        DB::beginTransaction();
        if (!$this->beforeUpdate($request)) {
            DB::rollback();

            return response()->json(['message' => 'could not update record'], self::$STATUS_CODE_SERVER_ERROR);
        }
        self::doUpload($request, $model);
        $fieldsToUpdate = (method_exists($model, 'fieldsToUpdate')
            and !empty(self::fieldsToUpdate())) ?
            $request->only(self::fieldsToUpdate()) : $request->input();

        try {
            $model->update($fieldsToUpdate);
        } catch (\Exception $exception) {
            DB::rollback();
            //todo remove Exception message
            return response()->json(['message' => 'An error occurred while updating record: '], 500);
        }
        if (!$this->afterUpdate($request, $model)) {
            DB::rollback();

            return response()->json(['message' => 'could not successfully update record'], self::$STATUS_CODE_SERVER_ERROR);
        }

        DB::commit();


        return response()->json($model, self::$STATUS_CODE_DONE);
    }

    /**
     * @param Model $model
     * @return array
     */
    public static function getValidationRulesForUpdate(Model $model)
    {
        $id = $model->id;
        $rules = self::getValidationRules();
        $fields = self::getUniqueFields();
        foreach ($fields as $field) {
            if (isset($rules[$field])) {
                $rules[$field] .= ',' . $id;
            }
        }
        return $rules;
    }

    /**
     * Return array of unique field. Used for validation
     * @return array
     */
    public static function getUniqueFields(): array
    {
        return [];
    }

    /**
     * Run before update action
     * @param Request $request
     * @return bool
     */
    public function beforeUpdate(Request $request): bool
    {
        return true;
    }

    /**
     * get fields to be update
     * @return array
     */
    public static function fieldsToUpdate()
    {
        return [];
    }

    /**
     * Run after update action
     * @param Request $request
     * @param $data
     * @return bool
     */
    public function afterUpdate(Request $request, $data): bool
    {
        return true;
    }

    /**
     * Delete Record.
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $m = self::getModel();
        if (is_null($m::find($id))) {
            return response()->json(['record was not found'], self::$STATUS_CODE_NOT_FOUND);
        }
        try {
            $m::destroy($id);
        } catch (\Exception $exception) {

        }

        return response()->json(['message' => 'record was deleted'], self::$STATUS_CODE_DONE);
    }

    /**
     * Perform action before data deletion
     * @param Request $request
     * @return bool
     */
    public function beforeDelete(Request $request): bool
    {
        return true;
    }

    /**
     * @param $status
     * @param array $data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respond($status, $data = [])
    {
        return response()->json($data, $this->statusCodes[$status]);
    }


}
