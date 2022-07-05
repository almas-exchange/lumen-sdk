<?php

namespace Exchange\Repositories;

use Illuminate\Support\Facades\DB;

class MongoBaseRepository
{
    public function getRecord($table, $where = [], $selection = ['*'])
    {
        $selection = $selection == [] ? ['*'] : $selection;

        return DB::connection('mongodb')->table($table)->select($selection)->where($where)->first();
    }

    public function getRecords($table, $where = [], $orderBy = 'created_at', $selection = ['*'])
    {
        $selection = $selection == [] ? ['*'] : $selection;
        return DB::connection('mongodb')->table($table)->select($selection)->where($where)->orderBy($orderBy)->get();
    }

    public function getPaginate($table, $where = [], $orderBy = 'created_at', $selection = ['*'],$paginate=10)
    {
        $selection = $selection == [] ? ['*'] : $selection;
        return DB::connection('mongodb')->table($table)->select($selection)->where($where)->orderBy($orderBy)->paginate($paginate);
    }

    public function storeRecord($table, $data)
    {
        return DB::connection('mongodb')->table($table)->insert($data);
    }

    public function updateRecord($table, $where, $data)
    {
        return DB::connection('mongodb')->table($table)->where($where)->update($data);
    }

    public function deleteRecords($table, $where)
    {
        return DB::connection('mongodb')->table($table)->where($where)->delete();
    }

}
