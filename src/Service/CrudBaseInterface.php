<?php
/**
 * Created by PhpStorm.
 * User: andela
 * Date: 25/12/2019
 * Time: 3:50 AM
 */

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
