<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\helpers\captcha;


use framework\core\Application;
use framework\exceptions\ErrorException;

class Captcha
{
    // Свойства
    public $fonts_path;
    public $image_dir;
    protected $verifyCodeName;


    // Конструктор
    public function __construct($options = [])
    {
        //exit(__DIR__);
        if(!empty($options['image_dir']))       $this->image_dir        = $options['image_dir'];
        if(!empty($options['fonts_path']))      $this->fonts_path       = $options['fonts_path'];
        if(!empty($options['verifyCodeName']))  $this->verifyCodeName   = $options['verifyCodeName'];
    }

    // Возвращает ключ для массива $_COOKIE
    public function getVerifyCodeName()
    {
        return $this->verifyCodeName;
    }


    // Вносим в куки хэш капчи.
    public function setVerifyCode()
    {
        $sourceCode = $this->generate_code();
        $cookie = md5($sourceCode);
        $cookietime = time()+(24*60*60); // Куки будет жить +X секунд.
        setcookie($this->verifyCodeName, $cookie, $cookietime);

        return $sourceCode;
    }

    // Получаем код
    public function getVerifyCode()
    {
        return $_COOKIE[$this->verifyCodeName];
    }

    // проверка
    public function check($code)
    {
        $userCode = trim(md5($code));
        $verify = trim($this->getVerifyCode());
        if($userCode == $verify)
            return true;
        else
            return false;
    }


    // Функция генерации капчи
    public function generate_code()
    {
        $chars = 'abcdefhknrstyz23456789'; // Задаем символы, используемые в капче. Разделитель использовать не надо.
        $length = rand(4, 7); // Задаем длину капчи, в нашем случае - от 4 до 7
        $numChars = strlen($chars); // Узнаем, сколько у нас задано символов
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, rand(1, $numChars) - 1, 1);
        } // Генерируем код

        // Перемешиваем, на всякий случай
        $array_mix = preg_split('//', $str, -1, PREG_SPLIT_NO_EMPTY);
        srand ((float)microtime()*1000000);
        shuffle ($array_mix);
        // Возвращаем полученный код
        return implode("", $array_mix);
    }

    public function captcha(){
        // Устанавливаем каптчу
        $code = $this->setVerifyCode();

        // Количество линий. Обратите внимание, что они накладываться будут дважды (за текстом и на текст). Поставим рандомное значение, от 3 до 7.
        $linenum = rand(3, 7);
        // Задаем фоны для капчи. Можете нарисовать свой и загрузить его в папку /img. Рекомендуемый размер - 150х70. Фонов может быть сколько угодно
        $img_arr = array(
            "1.png"
        );
        // Шрифты для капчи. Задавать можно сколько угодно, они будут выбираться случайно
        $font_arr = array();
        $font_arr[0]["fname"] = "DroidSans.ttf";	// Имя шрифта. Я выбрал Droid Sans, он тонкий, плохо выделяется среди линий.
        $font_arr[0]["size"] = rand(20, 30);				// Размер в pt

        // Генерируем "подстилку" для капчи со случайным фоном
        $n = rand(0,sizeof($font_arr)-1);
        $img_fn = $img_arr[rand(0, sizeof($img_arr)-1)];

        $image_phone = Application::getRealPath($this->image_dir .'/'. $img_fn);

        if(!is_file($image_phone)) throw new ErrorException("Captcha::captcha Не найдено фоновое изображение в ".$image_phone);

        $im = imagecreatefrompng ($image_phone);
        // Рисуем линии на подстилке
        for ($i=0; $i<$linenum; $i++)
        {
            $color = imagecolorallocate($im, rand(0, 150), rand(0, 100), rand(0, 150)); // Случайный цвет c изображения
            imageline($im, rand(0, 20), rand(1, 50), rand(150, 180), rand(1, 50), $color);
        }
        $color = imagecolorallocate($im, rand(0, 200), 0, rand(0, 200)); // Опять случайный цвет. Уже для текста.

        // Накладываем текст капчи
        $x = rand(0, 35);
        for($i = 0; $i < strlen($code); $i++) {
            $x+=15;
            $letter = substr($code, $i, 1);

            $font_path = Application::getRealPath($this->fonts_path.'/'.$font_arr[$n]["fname"]);

            if(!is_file($font_path)) throw new ErrorException("Captcha::captcha Не найден шрифт в ".$font_path);

            imagettftext ($im, $font_arr[$n]["size"], rand(2, 4), $x, rand(50, 55), $color, $font_path, $letter);
        }

        // Опять линии, уже сверху текста
        for ($i=0; $i<$linenum; $i++)
        {
            $color = imagecolorallocate($im, rand(0, 255), rand(0, 200), rand(0, 255));
            imageline($im, rand(0, 20), rand(1, 50), rand(150, 180), rand(1, 50), $color);
        }

        // Отправляем браузеру Header'ы
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s", 10000) . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Content-Type:image/png");

        // Возвращаем получившееся изображение
        ImagePNG ($im);
        ImageDestroy ($im);
    }
}