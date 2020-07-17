<?php

/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */
namespace framework\helpers\validators;

use framework\exceptions\ErrorException;
use framework\helpers\UploadedFile;

class FileValidator implements ValidatorInterface
{
    protected $_errors = [];
    public $options = [];

    public function __construct($options = [])
    {
        $this->options = $options;

        if(!isset($this->options['targetField']))
            throw new ErrorException("Для валидатора File не указано поле хранения файла targetField");
    }

    public function validate($object, $field = 'file') // Return false OR value
    {
        $checkClass = UploadedFile::class;

        $object = $object->{$this->options['targetField']};

        if($object instanceof $checkClass)
        {
            // Расширения
            if(!empty($this->options['extensions']))
            {
                if(!in_array($object->extension, $this->options['extensions']))
                {
                    $this->_errors['__badExtension'] = 'Не верное расширение. К загрузке доступны только файлы с расширениями: '.implode(', ', $this->options['extensions']);
                    return false;
                }

            }

            // Размер файла
            if(isset($this->options['maxsize']))
            {
                if($object->size > $this->options['maxsize'])
                {
                    $this->_errors['__maxSizeError'] = 'Превышен допустимый размер файла. Разрешено не более '.($this->options['maxsize']/1024).'kb';
                    return false;
                }
            }

            //$options = var_export($this->options, true);
            //$this->_errors['__test'] = 'Тестовая ошибка. Опции: '.$options;
            //return false;


            return $object;
        }
        else
        {
            $this->_errors['__instanceOfError'] = 'Переданный объект не является экземпляром класса UploadedFile';
            return false;
        }
    }
    public function getError()
    {
        return implode("\n", $this->_errors);
    }
}