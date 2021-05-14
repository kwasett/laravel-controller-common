<?php
namespace Kwasett\LaravelCommon\Service;

use Illuminate\Database\Eloquent\Model;
use Kwasett\LaravelCommon\Utils\ResponseItem;
use Kwasett\LaravelCommon\Utils\ProjectUtils;

class CrudBaseService
{
    public $model;
    public $modelClass;

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->modelClass = get_class($this->model);
    }

    function find($id): Model
    {
        return $this->model->find($id);
    }

    function create($data, array $createData,array $rules)
    {
        $res = $this->validate($data, $rules);

        if ($res->isError) {
            ProjectLog::debug("Failed validation for creating ".$this->modelClass);
            ProjectLog::debug("validation failed ". json_encode($res->errors));
            return $res;
        }
        ProjectLog::debug("Validation Passed for creating ".$this->modelClass);
        ProjectLog::debug($this->modelClass." creating");
        $modelCreated = $this->model->create($createData);

        ProjectLog::debug($this->modelClass." created {$modelCreated->id}");
        return $modelCreated;
    }

    function validate($data, $rules): ResponseItem
    {
        return ProjectUtils::validateInput($rules, $data);
    }

    function update($id, $data,array $updateItem, array $rules): ResponseItem
    {
        ProjectLog::debug("Now about to update");
        $res = $this->validate($data, $rules);

        if ($res->isError) {
            ProjectLog::debug("Failed validation for updating :: $id [".$this->modelClass);
            return $res;
        }

        $item = $this->find($id);

        if (!$item) {
            return ResponseItem::notFoundResponse($this->modelClass." not found");
        }

        ProjectLog::debug("Item found $id");
        $updated = $item->update($updateItem);

        return ResponseItem::successResponse(get_class($this->model)." Updated", ["item" => $item]);
    }


    public function delete($id)
    {
        return $this->model->delete($id);
    }
}
//bokgolf5_wp_website
//bokgolf5_website
//xzmRT8wSAv

