<?php

namespace Crystoline\LaraRestApi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
interface IRestApiAble
{
    public static function getModel() : string ;

    public function index(Request $request);

    /**
     * Paginate Data
     * @param Request $request
     * @param Builder $data
     * @param int     $pages
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|Builder
     */
    public  static function paginate(Request $request, $data, $pages = 50);

    /**
     * Filter data using request
     * @param Request $request
     * @param $query
     */
    static function filter(Request $request, $query);

    /**
     * Perform wild-card search
     * @param Request $request
     * @param Builder $builder
     * @param $searchables
     * return none, Builder passed by reference
     */
    public static function doSearch(Request $request, Builder $builder, $searchables) ;

    /**
     * Order Data
     * @param Request $request
     * @param Builder $builder
     * @param array $orderBy
     */
    public static function doOrderBy(Request $request, Builder $builder,array $orderBy);

    /**
     * Perform action before data list
     * @param $data
     */
    public function beforeList($data);

    /**
     * Show records.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id);

    /**
     * Perform action before data show
     * @param $data
     */
    public function beforeShow($data);

    /**
     * Store Record.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request);

    /**
     * Perform action after data store
     * @param Request $request
     * @param $data
     * @return bool
     */
    public function afterStore(Request $request, $data): bool;

    /**
     * Perform action before data store
     * @param Request $request
     * @return bool
     */
    public function beforeStore(Request $request): bool;

    /**
     * Perform action before data deletion
     * @param Request $request
     * @return bool
     */
    public function beforeDelete(Request $request): bool;

    /**
     * Update Record.
     *
     * @param Request $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id);

    /**
     * Run after update action
     * @param Request $request
     * @param $data
     * @return bool
     */
    public function afterUpdate(Request $request, $data): bool;

    /**
     * Run before update action
     * @param Request $request
     * @return bool
     */
    public function beforeUpdate(Request $request): bool;

    /**
     * Delete Record.
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id);



    /**
     * Perform file upload for request
     * @param Request $request
     * @param Model $object
     */
    public static function doUpload(Request $request, $object = null);

}