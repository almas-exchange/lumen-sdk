<?php

namespace Exchange\Facades;

/**
 * @class \Milyoonex\Facades\MongoBaseRepositoryFacade
 *
 * @method static getRecord($table, $where = [], $selection = ['*'])
 * @method static getRecords($table, $where = [], $orderBy = 'created_at', $selection = ['*'])
 * @method static getPaginate($table, $where = [], $orderBy = 'created_at', $selection = ['*'], $paginate=10)
 * @method static storeRecord($table, $data)
 * @method static updateRecord($table, $where, $data)
 * @method static deleteRecords($table, $where)
 *
 * @see \Exchange\Repositories\MongoBaseRepository
 */

class MongoBaseRepositoryFacade extends BaseFacade
{
    //
}