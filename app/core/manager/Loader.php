<?php
/**
 * This file is part of reliadmin project.
 * @author Stanislav Opletal <info@relisoft.cz>
 */

namespace App\Core\Manager;


use App\Core\Model\iModel;
use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;
use Tracy\Debugger;

class Loader
{
    private $db;

    public function __construct(Context $db)
    {
        $this->db = $db;
    }

    public function loadById(iModel &$model, int $id): ?iModel{
        try{
            $row = $this->db->table($model->table())->get($id);
            return $model->associate($row);
        }catch (\Throwable $e){
            Debugger::log($e->getMessage(),Debugger::EXCEPTION);
        }
        return null;
    }

    public function loadByOne(iModel &$model, array $params): ?iModel {
        try{
            $row = $this->db->table($model->table())->where($params)->fetch();
            if($row){
                return $model->associate($row);
            }
            return null;
        }catch (\Throwable $e){
            Debugger::log($e->getMessage(),Debugger::EXCEPTION);
        }
        return null;
    }

    public function loadBy(iModel $model, array $params): ?iterable {
        try{
            $row = $this->db->table($model->table())->where($params)->fetchAll();
            if($row){
                $returnList = [];
                foreach ($row as $item){
                    $returnList[] = (new $model())->associate($item);
                }
                return $returnList;
            }
            return null;
        }catch (\Throwable $e){
            Debugger::log($e->getMessage(),Debugger::EXCEPTION);
        }
        return null;
    }

    public function save(iModel $model): ?iModel{
        try{
            if($model->getId()){
                //Update
                $modelUpdated = $this->db->table($model->table())->where('id = ?',$model->getId())->update($model->jsonSerialize());
                if($modelUpdated instanceof ActiveRow){
                    $model->associate($modelUpdated);
                    return $model;
                }
            }else{
                //Create new
                $created = $this->db->table($model->table())->insert($model->jsonSerialize());
                if($created instanceof ActiveRow){
                    $model->associate($created);
                    return $model;
                }
            }
            return null;
        }catch (\Throwable $e){
            Debugger::log($e->getMessage(),Debugger::EXCEPTION);
        }
        return null;
    }
}