<?php
/**
 * Created by PhpStorm.
 * User: Алексей-дом
 * Date: 28.07.2019
 * Time: 20:31
 */

namespace framework\helpers;

class Console
{
    protected $stdIn = false;

    public $colors = [
    'black' => '0;30',
    'dark_gray' => '1;30',
    'blue' => '0;34',
    'light_blue' => '1;34',
    'green' => '0;32',
    'light_green' => '1;32',
    'cyan' => '0;36',
    'light_cyan' => '1;36',
    'red' => '0;31',
    'light_red' => '1;31',
    'purple' => '0;35',
    'light_purple' => '1;35',
    'brown' => '0;33',
    'yellow' => '1;33',
    'light_gray' => '0;37',
    'white' => '1;37'
    ];

    public $bgColors = [
        'black' => '40',
        'red' => '41',
        'green' => '42',
        'yellow' => '43',
        'blue' => '44',
        'magenta' => '45',
        'cyan' => '46',
        'light_gray' => '47'
    ];

    public function __construct()
    {
        $this->startDialog();
    }

    protected function startDialog() {
        $this->stdIn = fopen("php://stdin","r");
    }

    protected function closeDialog(){
        fclose($this->stdIn);
    }

    public function setColor ($color = 'white', $bgColor = 'black')
    {
        if(!isset($this->colors[$color]))
            $sColor = $this->colors['white'];
        else
            $sColor = $this->colors[$color];


        if(!isset($this->bgColors[$bgColor]))
            $sBgColor = $this->bgColors['black'];
        else
            $sBgColor = $this->bgColors[$bgColor];

        echo " \033[".$sColor."m"."\033[".$sBgColor."m ";

        return $this;
    }

    public function showAllColors()
    {
        echo "Text colors:\r\n";
        foreach ($this->colors as $colorLabel => $colorCode)
        {
            $this->setColor($colorLabel);
            $this->writeLn($colorLabel);
        }

        $this->setColor();

        echo "- - - - - - - \r\n"
            ."Background colors:\r\n";
        foreach ($this->bgColors as $colorLabel => $colorCode)
        {
            $this->setColor('', $colorLabel);
            $this->writeLn($colorLabel);
        }

        // Set Default font
        $this->setColor();

        return $this;
    }

    public function write($str = '')
    {
        echo $str;

        return $this;
    }

    public function writeLn($str = '')
    {
        echo $str."\r\n";

        return $this;
    }

    public function hr()
    {
        echo "- - - - - - - \r\n";
        return $this;
    }

    public function question($question = '?'){
        $this->writeLn($question);

        $answer = '';
        while(true)
        {
            $strChar = stream_get_contents(STDIN, 1);
            if($strChar === chr(10))
            {
                break;
            }
            $answer .= $strChar;
        }

        return $answer;
    }
}