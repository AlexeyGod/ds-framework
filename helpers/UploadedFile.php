<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\helpers;


use framework\exceptions\ErrorException;

class UploadedFile
{
    public $model;
    public $tmp_name;
    public $name;
    public $type;
    public $size;
    public $extension;

    public function __construct(array $options = [])
    {

    }


    public static function get($model, $fieldName)
    {
        if(is_uploaded_file($_FILES[$model->modelName()]['tmp_name'][$fieldName]))
        {
            $file = new static();
            $file->model = $model->modelName();

            $file->tmp_name = $_FILES[$file->model]['tmp_name'][$fieldName];
            $file->name     = $_FILES[$file->model]['name'][$fieldName];
            $file->type     = $_FILES[$file->model]['type'][$fieldName];
            $file->size     = $_FILES[$file->model]['size'][$fieldName];

            $parts = pathinfo($file->name);

            $file->extension = strtolower($parts['extension']);

            return $file;
        }
        else
            return false;
    }

    public function saveAs($destinationPath)
    {
        if(empty($destinationPath)) throw new ErrorException("UploadedFile.saveAs не указан destinationPath");

        return move_uploaded_file($this->tmp_name, $destinationPath);
    }
}