<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework\widgets\redactor;

class RedactorCollection
{
    public static $buttons = [
        'bold' => [
            'name' => 'B',
            'title' => 'Жирный',
            'command' => 'bold',
            'type' => 'button'
        ],
        'underline' => [
            'name' => 'U',
            'title' => 'Подчеркнутый',
            'command' => 'underline',
            'type' => 'button'
        ],
        'italic' => [
            'name' => 'I',
            'title' => 'Курсив',
            'command' => 'italic',
            'type' => 'button'
        ],
        'outdent' => [
            'name' => '',
            'title' => 'Удалить отступ',
            'command' => 'outdent',
            'type' => 'image',
            'img' => 'data:image/gif;base64,R0lGODlhFgAWAMIHAAAAADljwliE35GjuaezxtDV3NHa7P///yH5BAEAAAcALAAAAAAWABYAAAM2eLrc/jDKCQG9F2i7u8agQgyK1z2EIBil+TWqEMxhMczsYVJ3e4ahk+sFnAgtxSQDqWw6n5cEADs='
        ],
        'indent' => [
            'name' => '',
            'title' => 'Вставить отступ',
            'command' => 'indent',
            'type' => 'image',
            'img' => 'data:image/gif;base64,R0lGODlhFgAWAOMIAAAAADljwl9vj1iE35GjuaezxtDV3NHa7P///////////////////////////////yH5BAEAAAgALAAAAAAWABYAAAQ7EMlJq704650B/x8gemMpgugwHJNZXodKsO5oqUOgo5KhBwWESyMQsCRDHu9VOyk5TM9zSpFSr9gsJwIAOw=='
        ],
        'link' => [
            'name' => '',
            'title' => 'Вставить ссылку',
            'command' => 'link', //'insertLink',
            'type' => 'image',
            'img' => 'data:image/gif;base64,R0lGODlhFgAWAOMKAB1ChDRLY19vj3mOrpGjuaezxrCztb/I19Ha7Pv8/f///////////////////////yH5BAEKAA8ALAAAAAAWABYAAARY8MlJq7046827/2BYIQVhHg9pEgVGIklyDEUBy/RlE4FQF4dCj2AQXAiJQDCWQCAEBwIioEMQBgSAFhDAGghGi9XgHAhMNoSZgJkJei33UESv2+/4vD4TAQA7'
        ],
        'unlink' => [
            'name' => '',
            'title' => 'Удалить ссылку',
            'command' => 'unlink', //'insertLink',
            'type' => 'image',
            'img' => 'data:image/gif;base64,R0lGODlhFgAWAOMKAB1ChDRLY19vj3mOrpGjuaezxrCztb/I19Ha7Pv8/f///////////////////////yH5BAEKAA8ALAAAAAAWABYAAARY8MlJq7046827/2BYIQVhHg9pEgVGIklyDEUBy/RlE4FQF4dCj2AQXAiJQDCWQCAEBwIioEMQBgSAFhDAGghGi9XgHAhMNoSZgJkJei33UESv2+/4vD4TAQA7'
        ],

        'source' => [
            'name' => '',
            'title' => 'Исходный код',
            'command' => 'source',
            'type' => 'image',
            'img' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAB6UlEQVR4Xu2Z227EIAxEvV/e9stb8UAVRdw8HhMIRtpHbM/xQJzNRw5fn8P1SwAIBxxOII7A4QaISzCOQByBwwnEESAb4FtE0s9zUXMwHZAK+xJxf7L8isgPCzQLQBafOs+KWXNRApAWBQKj2Kv4mQAoEKwA7uJnAzBDsAAoiX8CgAkCCqAm/ikAMAQEQEv8kwAgCFoAPfGUm7kzRFBr0ACgJjZOSrRaRgHQEhqFX7dTahoBQElEFE6F0AMwWzySD9nzD7EFwBQY6LglH7y3BgAOCAhPWxj5oBglAFAgUDhLfE6vrv0OQB3AIJwtHoJwBfAG8WoIGcCbxKsgJABvFD8MYTaA5WDPPALLiU/un3UJLim+9O7uUahHzNbTV5XPexBSFWOcKaC5wnMUXl587+8riwDLXsQIcD6P12G4GES5dY7pAYDOFSgE2WaGPQJgVQhm8b074N4RSkKkzYU9tFpGHTA8W7O+2jZA0cRrHTAKQQtVa4r8dbi0T/1dAi221QU05iiIGgC1eNQBPSc8AQASbwVQezrMBgCLZwAoQZgJwCSeBeAOYRYAs3gmgCuEGQAo4tkAMoT0hPBcKT4th3e3PEFQYgcACsaNg4QDNm4epfRwAAXjxkHCARs3j1J6OICCceMgxzvgD8gGrUFnCXrbAAAAAElFTkSuQmCC'
        ],



    ];



    public function template()
    {
        $html = "<!-- JS Editor begin -->\n";

        $html .= "<div class=\"ds-redactor\">\n";
        $html .= "<label for=\"".$this->activeForm->getFieldName()."\">".$this->model->label($this->field)."</label>\n";

        $html .= "<div class=\"controls\">\n";
        $html .= '<div class="format">';

        $html .= '<select class="s-format">';
        $html .= '<option selected>- формат -</option>';
        $html .= '<option value="h1">Заголовок 1 &lt;h1&gt;</option>';
        $html .= '<option value="h2">Заголовок 2 &lt;h2&gt;</option>';
        $html .= '<option value="h3">Заголовок 3 &lt;h3&gt;</option>';
        $html .= '<option value="h4">Заголовок 4 &lt;h4&gt;</option>';
        $html .= '<option value="h5">Заголовок 5 &lt;h5&gt;</option>';
        $html .= '<option value="h6">Подзаголовок &lt;h6&gt;</option>';
        $html .= '<option value="p">Параграф &lt;p&gt;</option>';
        $html .= '<option value="pre">Отф.текст &lt;pre&gt;</option>';
        $html .= '</select> ';

        $html .= '<select class="s-font-family">';
        $html .= '<option selected>- шрифт -</option>';
        $html .= '<option>Arial</option>';
        $html .= '<option>Arial Black</option>';
        $html .= '<option>Courier New</option>';
        $html .= '<option>Times New Roman</option>';
        $html .= '</select> ';

        $html .= '<select class="s-font-size">';
        $html .= '<option selected>- размер -</option>';
        $html .= '<option value="1">Малюсенький</option>';
        $html .= '<option value="2">Маленький</option>';
        $html .= '<option value="3">Нормальный</option>';
        $html .= '<option value="4">Большеват</option>';
        $html .= '<option value="5">Большой</option>';
        $html .= '<option value="6">Большущий</option>';
        $html .= '<option value="7">Огромный</option>';
        $html .= '</select> ';

        $html .= '<select>';
        $html .= '<option selected>- цвет -</option>';
        $html .= '<option value="red">Красный</option>';
        $html .= '<option value="blue">Синий</option>';
        $html .= '<option value="green">Зеленый</option>';
        $html .= '<option value="black">Чёрный</option>';
        $html .= '</select> ';

        $html .= '<select>';
        $html .= '<option selected>- фон -</option>';
        $html .= '<option value="#faa">Красень</option>';
        $html .= '<option value="#afa">Зелень</option>';
        $html .= '<option value="#aaf">Синь</option> ';
        $html .= '</select> ';

        $html .= '</div>';
        //$html .= ''<div id="toolBar2">';
        //$html .= '<img class="intLink" title="Чистка" onclick="if(validateMode()&&confirm('Вы уверены?')){oDoc.innerHTML=sDefTxt};" src="data:image/gif;base64,R0lGODlhFgAWAIQbAD04KTRLYzFRjlldZl9vj1dusY14WYODhpWIbbSVFY6O7IOXw5qbms+wUbCztca0ccS4kdDQjdTLtMrL1O3YitHa7OPcsd/f4PfvrvDv8Pv5xv///////////////////yH5BAEKAB8ALAAAAAAWABYAAAV84CeOZGmeaKqubMteyzK547QoBcFWTm/jgsHq4rhMLoxFIehQQSAWR+Z4IAyaJ0kEgtFoLIzLwRE4oCQWrxoTOTAIhMCZ0tVgMBQKZHAYyFEWEV14eQ8IflhnEHmFDQkAiSkQCI2PDC4QBg+OAJc0ewadNCOgo6anqKkoIQA7" />';
        //$html .= '<img class="intLink" title="Печать" onclick="printDoc();" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAABGdBTUEAALGPC/xhBQAAAAZiS0dEAP8A/wD/oL2nkwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB9oEBxcZFmGboiwAAAAIdEVYdENvbW1lbnQA9syWvwAAAuFJREFUOMvtlUtsjFEUx//n3nn0YdpBh1abRpt4LFqtqkc3jRKkNEIsiIRIBBEhJJpKlIVo4m1RRMKKjQiRMJRUqUdKPT71qpIpiRKPaqdF55tv5vvusZjQTjOlseUkd3Xu/3dPzusC/22wtu2wRn+jG5So/OCDh8ycMJDflehMlkJkVK7KUYN+ufzA/RttH76zaVocDptRxzQtNi3mRWuPc+6cKtlXZ/sddP2uu9uXlmYXZ6Qm8v4Tz8lhF1H+zDQXt7S8oLMXtbF4e8QaFHjj3kbP2MzkktHpiTjp9VH6iHiA+whtAsX5brpwueMGdONdf/2A4M7ukDs1JW662+XkqTkeUoqjKtOjm2h53YFL15pSJ04Zc94wdtibr26fXlC2mzRvBccEbz2kiRFD414tKMlEZbVGT33+qCoHgha81SWYsew0r1uzfNylmtpx80pngQQ91LwVk2JGvGnfvZG6YcYRAT16GFtW5kKKfo1EQLtfh5Q2etT0BIWF+aitq4fDbk+ImYo1OxvGF03waFJQvBCkvDffRyEtxQiFFYgAZTHS0zwAGD7fG5TNnYNTp8/FzvGwJOfmgG7GOx0SAKKgQgDMgKBI0NJGMEImpGDk5+WACEwEd0ywblhGUZ4Hw5OdUekRBLT7DTgdEgxACsIznx8zpmWh7k4rkpJcuHDxCul6MDsmmBXDlWCH2+XozSgBnzsNCEE4euYV4pwCpsWYPW0UHDYBKSWu1NYjENDReqtKjwn2+zvtTc1vMSTB/mvev/WEYSlASsLimcOhOBJxw+N3aP/SjefNL5GePZmpu4kG7OPr1+tOfPyUu3BecWYKcwQcDFmwFKAUo90fhKDInBCAmvqnyMgqUEagQwCoHBDc1rjv9pIlD8IbVkz6qYViIBQGTJPx4k0XpIgEZoRN1Da0cij4VfR0ta3WvBXH/rjdCufv6R2zPgPH/e4pxSBCpeatqPrjNiso203/5s/zA171Mv8+w1LOAAAAAElFTkSuQmCC">';
        //$html .= '<img class="intLink" title="Назад" onclick="formatDoc('undo');" src="data:image/gif;base64,R0lGODlhFgAWAOMKADljwliE33mOrpGjuYKl8aezxqPD+7/I19DV3NHa7P///////////////////////yH5BAEKAA8ALAAAAAAWABYAAARR8MlJq7046807TkaYeJJBnES4EeUJvIGapWYAC0CsocQ7SDlWJkAkCA6ToMYWIARGQF3mRQVIEjkkSVLIbSfEwhdRIH4fh/DZMICe3/C4nBQBADs=" />';
        //$html .= '<img class="intLink" title="Вперёд" onclick="formatDoc('redo');" src="data:image/gif;base64,R0lGODlhFgAWAMIHAB1ChDljwl9vj1iE34Kl8aPD+7/I1////yH5BAEKAAcALAAAAAAWABYAAANKeLrc/jDKSesyphi7SiEgsVXZEATDICqBVJjpqWZt9NaEDNbQK1wCQsxlYnxMAImhyDoFAElJasRRvAZVRqqQXUy7Cgx4TC6bswkAOw==" />';
        //$html .= '<img class="intLink" title="Удалить форматирование" onclick="formatDoc('removeFormat')" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAABGdBTUEAALGPC/xhBQAAAAZiS0dEAP8A/wD/oL2nkwAAAAlwSFlzAAAOxAAADsQBlSsOGwAAAAd0SU1FB9oECQMCKPI8CIIAAAAIdEVYdENvbW1lbnQA9syWvwAAAuhJREFUOMtjYBgFxAB501ZWBvVaL2nHnlmk6mXCJbF69zU+Hz/9fB5O1lx+bg45qhl8/fYr5it3XrP/YWTUvvvk3VeqGXz70TvbJy8+Wv39+2/Hz19/mGwjZzuTYjALuoBv9jImaXHeyD3H7kU8fPj2ICML8z92dlbtMzdeiG3fco7J08foH1kurkm3E9iw54YvKwuTuom+LPt/BgbWf3//sf37/1/c02cCG1lB8f//f95DZx74MTMzshhoSm6szrQ/a6Ir/Z2RkfEjBxuLYFpDiDi6Af///2ckaHBp7+7wmavP5n76+P2ClrLIYl8H9W36auJCbCxM4szMTJac7Kza////R3H1w2cfWAgafPbqs5g7D95++/P1B4+ECK8tAwMDw/1H7159+/7r7ZcvPz4fOHbzEwMDwx8GBgaGnNatfHZx8zqrJ+4VJBh5CQEGOySEua/v3n7hXmqI8WUGBgYGL3vVG7fuPK3i5GD9/fja7ZsMDAzMG/Ze52mZeSj4yu1XEq/ff7W5dvfVAS1lsXc4Db7z8C3r8p7Qjf///2dnZGxlqJuyr3rPqQd/Hhyu7oSpYWScylDQsd3kzvnH738wMDzj5GBN1VIWW4c3KDon7VOvm7S3paB9u5qsU5/x5KUnlY+eexQbkLNsErK61+++VnAJcfkyMTIwffj0QwZbJDKjcETs1Y8evyd48toz8y/ffzv//vPP4veffxpX77z6l5JewHPu8MqTDAwMDLzyrjb/mZm0JcT5Lj+89+Ybm6zz95oMh7s4XbygN3Sluq4Mj5K8iKMgP4f0////fv77//8nLy+7MCcXmyYDAwODS9jM9tcvPypd35pne3ljdjvj26+H2dhYpuENikgfvQeXNmSl3tqepxXsqhXPyc666s+fv1fMdKR3TK72zpix8nTc7bdfhfkEeVbC9KhbK/9iYWHiErbu6MWbY/7//8/4//9/pgOnH6jGVazvFDRtq2VgiBIZrUTIBgCk+ivHvuEKwAAAAABJRU5ErkJggg==">';
        //$html .= '<img class="intLink" title="Жирный" onclick="formatDoc('bold');" src="data:image/gif;base64,R0lGODlhFgAWAID/AMDAwAAAACH5BAEAAAAALAAAAAAWABYAQAInhI+pa+H9mJy0LhdgtrxzDG5WGFVk6aXqyk6Y9kXvKKNuLbb6zgMFADs=" />';
        //$html .= '<img class="intLink" title="Italic" onclick="formatDoc('italic');" src="data:image/gif;base64,R0lGODlhFgAWAKEDAAAAAF9vj5WIbf///yH5BAEAAAMALAAAAAAWABYAAAIjnI+py+0Po5x0gXvruEKHrF2BB1YiCWgbMFIYpsbyTNd2UwAAOw==" />';
        //$html .= '<img class="intLink" title="Подчеркивание" onclick="formatDoc('underline');" src="data:image/gif;base64,R0lGODlhFgAWAKECAAAAAF9vj////////yH5BAEAAAIALAAAAAAWABYAAAIrlI+py+0Po5zUgAsEzvEeL4Ea15EiJJ5PSqJmuwKBEKgxVuXWtun+DwxCCgA7" />';
        //$html .= '<img class="intLink" title="Выровнять слева" onclick="formatDoc('justifyleft');" src="data:image/gif;base64,R0lGODlhFgAWAID/AMDAwAAAACH5BAEAAAAALAAAAAAWABYAQAIghI+py+0Po5y02ouz3jL4D4JMGELkGYxo+qzl4nKyXAAAOw==" />';
        //$html .= '<img class="intLink" title="Выровнять центр" onclick="formatDoc('justifycenter');" src="data:image/gif;base64,R0lGODlhFgAWAID/AMDAwAAAACH5BAEAAAAALAAAAAAWABYAQAIfhI+py+0Po5y02ouz3jL4D4JOGI7kaZ5Bqn4sycVbAQA7" />';
        //$html .= '<img class="intLink" title="Выровнять справа" onclick="formatDoc('justifyright');" src="data:image/gif;base64,R0lGODlhFgAWAID/AMDAwAAAACH5BAEAAAAALAAAAAAWABYAQAIghI+py+0Po5y02ouz3jL4D4JQGDLkGYxouqzl43JyVgAAOw==" />';
        //$html .= '<img class="intLink" title="Нумерованный список" onclick="formatDoc('insertorderedlist');" src="data:image/gif;base64,R0lGODlhFgAWAMIGAAAAADljwliE35GjuaezxtHa7P///////yH5BAEAAAcALAAAAAAWABYAAAM2eLrc/jDKSespwjoRFvggCBUBoTFBeq6QIAysQnRHaEOzyaZ07Lu9lUBnC0UGQU1K52s6n5oEADs=" />';
        //$html .= '<img class="intLink" title="Пунктирный список" onclick="formatDoc('insertunorderedlist');" src="data:image/gif;base64,R0lGODlhFgAWAMIGAAAAAB1ChF9vj1iE33mOrqezxv///////yH5BAEAAAcALAAAAAAWABYAAAMyeLrc/jDKSesppNhGRlBAKIZRERBbqm6YtnbfMY7lud64UwiuKnigGQliQuWOyKQykgAAOw==" />';
        //$html .= '<img class="intLink" title="Цитата" onclick="formatDoc('formatblock','blockquote');" src="data:image/gif;base64,R0lGODlhFgAWAIQXAC1NqjFRjkBgmT9nqUJnsk9xrFJ7u2R9qmKBt1iGzHmOrm6Sz4OXw3Odz4Cl2ZSnw6KxyqO306K63bG70bTB0rDI3bvI4P///////////////////////////////////yH5BAEKAB8ALAAAAAAWABYAAAVP4CeOZGmeaKqubEs2CekkErvEI1zZuOgYFlakECEZFi0GgTGKEBATFmJAVXweVOoKEQgABB9IQDCmrLpjETrQQlhHjINrTq/b7/i8fp8PAQA7" />';
        //$html .= '<img class="intLink" title="Удалить отступ" onclick="formatDoc('outdent');" src="data:image/gif;base64,R0lGODlhFgAWAMIHAAAAADljwliE35GjuaezxtDV3NHa7P///yH5BAEAAAcALAAAAAAWABYAAAM2eLrc/jDKCQG9F2i7u8agQgyK1z2EIBil+TWqEMxhMczsYVJ3e4ahk+sFnAgtxSQDqWw6n5cEADs=" />';
        //$html .= '<img class="intLink" title="Добавить отступ" onclick="formatDoc('indent');" src="data:image/gif;base64,R0lGODlhFgAWAOMIAAAAADljwl9vj1iE35GjuaezxtDV3NHa7P///////////////////////////////yH5BAEAAAgALAAAAAAWABYAAAQ7EMlJq704650B/x8gemMpgugwHJNZXodKsO5oqUOgo5KhBwWESyMQsCRDHu9VOyk5TM9zSpFSr9gsJwIAOw==" />';
        //$html .= '<img class="intLink" title="Гиперссылка" onclick="var sLnk=prompt('Введите ваш URL','http:\/\/');if(sLnk&&sLnk!=''&&sLnk!='http://'){formatDoc('createlink',sLnk)}" src="data:image/gif;base64,R0lGODlhFgAWAOMKAB1ChDRLY19vj3mOrpGjuaezxrCztb/I19Ha7Pv8/f///////////////////////yH5BAEKAA8ALAAAAAAWABYAAARY8MlJq7046827/2BYIQVhHg9pEgVGIklyDEUBy/RlE4FQF4dCj2AQXAiJQDCWQCAEBwIioEMQBgSAFhDAGghGi9XgHAhMNoSZgJkJei33UESv2+/4vD4TAQA7" />';
        //$html .= '<img class="intLink" title="Вырезать" onclick="formatDoc('cut');" src="data:image/gif;base64,R0lGODlhFgAWAIQSAB1ChBFNsRJTySJYwjljwkxwl19vj1dusYODhl6MnHmOrpqbmpGjuaezxrCztcDCxL/I18rL1P///////////////////////////////////////////////////////yH5BAEAAB8ALAAAAAAWABYAAAVu4CeOZGmeaKqubDs6TNnEbGNApNG0kbGMi5trwcA9GArXh+FAfBAw5UexUDAQESkRsfhJPwaH4YsEGAAJGisRGAQY7UCC9ZAXBB+74LGCRxIEHwAHdWooDgGJcwpxDisQBQRjIgkDCVlfmZqbmiEAOw==" />';
        //$html .= '<img class="intLink" title="Копировать" onclick="formatDoc('copy');" src="data:image/gif;base64,R0lGODlhFgAWAIQcAB1ChBFNsTRLYyJYwjljwl9vj1iE31iGzF6MnHWX9HOdz5GjuYCl2YKl8ZOt4qezxqK63aK/9KPD+7DI3b/I17LM/MrL1MLY9NHa7OPs++bx/Pv8/f///////////////yH5BAEAAB8ALAAAAAAWABYAAAWG4CeOZGmeaKqubOum1SQ/kPVOW749BeVSus2CgrCxHptLBbOQxCSNCCaF1GUqwQbBd0JGJAyGJJiobE+LnCaDcXAaEoxhQACgNw0FQx9kP+wmaRgYFBQNeAoGihCAJQsCkJAKOhgXEw8BLQYciooHf5o7EA+kC40qBKkAAAGrpy+wsbKzIiEAOw==" />';
        //$html .= '<img class="intLink" title="Вставить" onclick="formatDoc('paste');" src="data:image/gif;base64,R0lGODlhFgAWAIQUAD04KTRLY2tXQF9vj414WZWIbXmOrpqbmpGjudClFaezxsa0cb/I1+3YitHa7PrkIPHvbuPs+/fvrvv8/f///////////////////////////////////////////////yH5BAEAAB8ALAAAAAAWABYAAAWN4CeOZGmeaKqubGsusPvBSyFJjVDs6nJLB0khR4AkBCmfsCGBQAoCwjF5gwquVykSFbwZE+AwIBV0GhFog2EwIDchjwRiQo9E2Fx4XD5R+B0DDAEnBXBhBhN2DgwDAQFjJYVhCQYRfgoIDGiQJAWTCQMRiwwMfgicnVcAAAMOaK+bLAOrtLUyt7i5uiUhADs=" />';
        //$html .= '</div>';

        $html .= '<span data-command="bold">B</span>'."\n";
        $html .= '<span data-command="underline">U</span>'."\n";
        $html .= '<span data-command="italic">I</span>'."\n";
        //$html .= '<span data-command="">Ссылка</span>'."\n";
        $html .= '<span data-command="outdent"><img src="data:image/gif;base64,R0lGODlhFgAWAMIHAAAAADljwliE35GjuaezxtDV3NHa7P///yH5BAEAAAcALAAAAAAWABYAAAM2eLrc/jDKCQG9F2i7u8agQgyK1z2EIBil+TWqEMxhMczsYVJ3e4ahk+sFnAgtxSQDqWw6n5cEADs=" alt="Удалить отступ"/></span>'."\n";
        $html .= '<span data-command="indent"><img src="data:image/gif;base64,R0lGODlhFgAWAOMIAAAAADljwl9vj1iE35GjuaezxtDV3NHa7P///////////////////////////////yH5BAEAAAgALAAAAAAWABYAAAQ7EMlJq704650B/x8gemMpgugwHJNZXodKsO5oqUOgo5KhBwWESyMQsCRDHu9VOyk5TM9zSpFSr9gsJwIAOw==" /></span>'."\n";
        $html .= '<span data-command="link"><img src="data:image/gif;base64,R0lGODlhFgAWAOMKAB1ChDRLY19vj3mOrpGjuaezxrCztb/I19Ha7Pv8/f///////////////////////yH5BAEKAA8ALAAAAAAWABYAAARY8MlJq7046827/2BYIQVhHg9pEgVGIklyDEUBy/RlE4FQF4dCj2AQXAiJQDCWQCAEBwIioEMQBgSAFhDAGghGi9XgHAhMNoSZgJkJei33UESv2+/4vD4TAQA7" /></span>'."\n";

        $html .= "</div>\n";

        $html .= Html::smartTag('iframe', [
            'id' => $this->frameId,
            'class' => 'ds-redactor-frame'
        ])."\n"
        .$this->activeForm->textarea();//->hidden();

        $html .= "</div>\n";

        return $html;
    }


    public function run()
    {
        $defaultText = ($this->model->{$this->field} == '' ? 'Текст' : $this->model->{$this->field});
        $css = 'iHTML += "<link rel=\'stylesheet\' href=\'/assets/ca4c50b905/css/bootstrap.min.css\'/>";'."\n";
        $css .= 'iHTML += "<link rel=\'stylesheet\' href=\'/assets/f17aaabc20/css/style.css\'/>";';
        $jsCode = <<<JSCODE
console.log("DS-Redactor Active (id: #{$this->frameId})");
// Обработчики
document.addEventListener("DOMContentLoaded", createEditor);

// Контейнеры
var hInput, iFrame, iDoc, iWin;

// Определим Gecko-браузеры, т.к. они отличаются в своей работе от Оперы и IE
var isGecko = navigator.userAgent.toLowerCase().indexOf("gecko") != -1;

function createEditor()
{
    document.execCommand("defaultParagraphSeparator", false, "div");
    // Получаем доступ к объектам window & document для ифрейма
    iFrame = (isGecko) ? document.getElementById("{$this->frameId}") : frames["{$this->frameId}"];
    iDoc = (isGecko) ? iFrame.contentDocument : iFrame.document;
    iWin = (isGecko) ? iFrame.contentWindow : iFrame.window;
    hInput = document.getElementById("{$this->htmlId}");

    // Формируем HTML-код
    iHTML = "<html><head>";
    {$css}
    iHTML += "<style>";
    //iHTML += "body, div, p, td {font-size:12px; font-family:tahoma; margin:0px; padding:0px;}";
    iHTML += "body {margin:5px;}";
    iHTML += "</style>";
    iHTML += "<body><div>{$defaultText}</div></body>";
    iHTML += "</html>";
    // Добавляем его с помощью методов объекта document
    iDoc.open();
    iDoc.write(iHTML);
    iDoc.close();

    if (!iDoc.designMode) alert("Визуальный режим редактирования не поддерживается Вашим браузером");
    else iDoc.designMode = (isGecko) ? "on" : "On";

    // Событие при изменении фрейма
    iFrame.addEventListener("load", liveEdit);
    iFrame.addEventListener("DOMContentLoaded", liveEdit);

    //iDoc.addEventListener("onkeyup", liveEdit);
    iDoc.addEventListener("keyup", keyPressHandler);
    //iDoc.addEventListener("keypress", liveEdit);
    //iDoc.addEventListener("onkeypress", liveEdit);
    //iDoc.addEventListener("onkeypress", liveEdit);

}

function liveEdit()
{
    console.log("live reload active");
    hInput.value = iDoc.body.innerHTML;
}

function keyPressHandler(e)
{
    console.log("Press key: "+e.key);
    if(e.key == 'Enter')
    {
        e.preventDefault();
    }
     liveEdit();
}

JSCODE;
        Application::app()->assetManager->setJsCode($jsCode);
        return $this->template();
    }


}