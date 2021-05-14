<?php
namespace Kwasett\LaravelCommon\Service;

interface CrudBaseInterface
{
    function create($data);
    function update($id, $data);
    function search($data);
    function find($id);
    function delete($id);
    function validate($data);
    function rules();
}
