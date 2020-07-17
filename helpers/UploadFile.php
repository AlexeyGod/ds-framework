<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\helpers;


use framework\exceptions\ErrorException;

class UploadFile
{
    protected $_attachments = [];
    protected $_errors = [];

    public function __construct(array $options = [])
    {

    }


    public function loadFieldFromModel($model, $field, $options = [])
    {
        //exit(var_dump($_FILES));
        if(!isset($_FILES[$model->modelName()])) return false;
        else
        {
            $file = $_FILES[$model->modelName()];
            $fileName = $file['name'][$field];
            $fileType = $file['type'][$field];
            $fileSize = $file['size'][$field];
            $fileTmp = $file['tmp_name'][$field];

            if($file == '' OR $fileTmp == '')
                return false;

            $extension = pathinfo($fileName);
            $extension = strtolower($extension['extension']);

            if(!isset($options['savePath'])) throw new ErrorException("В функции loadFileFromModel не передана опция savePath");
            if(!is_dir($options['savePath'])) throw new ErrorException("В функции loadFileFromModel не корректно передана опция savePath: ".$options['savePath']);

            $fileNewName = $model->modelName().'-'.date("dmyhis").mt_rand(0,999).'-'.substr(self::translit($fileName),0, (strlen($extension)+1)*-1).'.'.$extension;
            //$fileNewName = 'user_id_'.$model->id.'.'.$extension;

            $finalPath = $options['savePath'].str_replace("//", "/", '/'.$fileNewName);

            //if(!is_uploaded_file($fileTmp)) throw new ErrorException("Файл не загружен на сервер. Передан несуществующий адрес: ".$fileTmp);

            $result = move_uploaded_file($fileTmp, $finalPath);



            if ($result)
            {
                return $fileNewName;
            }
            else
                {

                    return false;
            }
        }
    }
    /**
     * uploadMultiple($model, ['photo' =>
     * 'extensions' => ['gif', 'png'],
     * 'max_size' => 1024, // киллобайт
     * 'skipOnError' => true, // Все ошибки будут в ->getErrors() , если false - будет выброшено исключение
     * ])
     *
     * in HTML:
     *
     * <input name="Attachments['photo']>
     *
     * @param $model
     * @param array $rules
     * @return bool
     * @throws ErrorException
     */
    public function uploadMultiple($model, $rules = [])
    {
        //start breakpoint
        //exit(var_dump($_FILES));

        $filesVar = $_FILES[$model->modelName()];

        if (empty($filesVar)) return false;
        // массив для пропускаемых файлов
        $skip = [];
        // прогон
        // Имя
        $i = 0;
        foreach ($filesVar['name'] as $key => $files) {
            foreach ($files as $filename) {
                // Пропуск пустых
                if($filename == '')
                {
                    //exit('breakpoint empty file name');
                    $skip[$key][] = $i;
                    continue;
                }

                $info = pathinfo($filename);
                // Если есть проверка по расширению
                if (isset($rules[$key]['extensions']))
                {
                    // Проверяем
                    if (!in_array($info['extension'], $rules[$key]['extensions'])) {
                        $error = 'Не верное расширение файла ' . $filename . ' в поле ' . $key . ', допустимы: ' . implode(", ", $rules[$key]['extensions']);
                        $this->_errors[] = $error;

                        // Проверяем уровень ошибки
                        if (!$rules['_skipOnError'])
                            throw new ErrorException($error);

                        //exit('e:'.$error);

                        $skip[$key][] = $i;
                        continue;
                    }
                }

                $this->_attachments[$key][$i]['real_name'] = $filename;
                $this->_attachments[$key][$i]['extension'] = strtolower($info['extension']);
                $this->_attachments[$key][$i]['new_name'] = $model->modelName().'_'.substr(time(),5).'_'.self::translit(substr($filename,0, (strlen($this->_attachments[$key][$i]['extension'])+1)*-1)).'.'. $this->_attachments[$key][$i]['extension'];

                $i++;
            }
        }
        // Хранилище, размер, миме-тип
        $i = 0;
        foreach ($filesVar['tmp_name'] as $key => $files) {
            foreach ($files as $filename) {
                // Проверяем прошел ли файл пердидущие проверки
                if (isset($skip[$key]) AND in_array($i, $skip[$key])) {
                    //exit('breakpoint skiped var '.$i.' in array skiped: '.implode(', ',$skip));
                    continue;
                }

                $this->_attachments[$key][$i]['tmp'] = $filename;
                $this->_attachments[$key][$i]['size'] = ceil(filesize($filename) / 1024);

                // Если есть проверка по размеру
                if (isset($rules[$key]['max_size'])) {
                    // Проверяем
                    if ($this->_attachments[$key][$i]['size'] > $rules[$key]['max_size']) {
                        $error = 'Размер файла ' . $filename . ' в поле ' . $key . ' больше допустимого: ' . $rules[$key]['max_size'] . 'kb';
                        $this->_errors[] = $error;

                        // Проверяем уровень ошибки
                        if (!$rules['_skipOnError'])
                            throw new ErrorException($error);

                        $skip[$key][] = $i;
                        continue;
                    }
                }

                $this->_attachments[$key][$i]['mime'] = mime_content_type($filename);

                $i++;
            }
        }

        //exit(var_dump($this->getRegisterFiles()));

        return $this->getErrors();
    }

    public function getRegisterFiles()
    {
        return $this->_attachments;
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    public static function translit($title)
    {
        $alf = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => '', 'ы' => 'y', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',

            'А' => 'A', 'Б' => 'B', 'В' => 'V',
            'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
            'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U',
            'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
            'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        );
        $title = strtr($title, $alf);
        $title = mb_strtolower($title);
        $title = mb_ereg_replace('[^-0-9a-z]', '-', $title);
        $title = mb_ereg_replace('[-]+', '-', $title);
        $title = trim($title, '-');
        return $title;
    }

    public function saveAllto($path)
    {

        if(empty($this->_attachments)) return false;
        //throw  new ErrorException("UploadFile: вызов функции сохранения без предварительно загруженных объектов");
        if(!is_dir($path)) throw  new ErrorException("UploadFile: указана не существующаяя директория для сохранения файлов ".$path);

        if(substr($path, -1) != '/')
            $path .= '/';

        foreach ($this->_attachments as $field => $attachmentsArray)
            foreach ($attachmentsArray as $key => $attachment)
            {
                $newname = $path.$attachment['new_name'];
                if(!move_uploaded_file($attachment['tmp'], $newname))
                    $this->_errors[] = 'Ошибка сохранения '.$newname;
                else
                    $return[$field][] = $attachment['new_name'];
            }

            return $return;
    }
}