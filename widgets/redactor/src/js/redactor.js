// Определим Gecko-браузеры, т.к. они отличаются в своей работе от Оперы и IE
var isGecko = navigator.userAgent.toLowerCase().indexOf("gecko") != -1;



function commandHandler(event, editorTag)
{
    var iWin = (isGecko) ? document.getElementById('frame-'+editorTag).contentWindow : frames["frame-"+editorTag].window;

    var tagName = event.target.tagName;

    if(tagName == "IMG")
    {
        var tElement = event.target.parentNode;
    }
    else
    {
        var tElement = event.target;
    }

    console.log("command element tag: "+tagName+", element: "+tElement);

    var uCommand = tElement.getAttribute("data-command");
    console.log("выбрана команда: "+uCommand);

    switch (uCommand){
        case 'link':
            iWin.focus();
            var URL = prompt("Введите ссылку", "http://");
            iWin.document.execCommand("CreateLink", true, URL);
            break;

        case 'source':
            var visible = document.getElementById("source-"+editorTag).style.display;
            if(visible == "none")
            {
                console.log("SOURCE EDITOR SHOW");
                document.getElementById("vr-"+editorTag).style.display = "none";
                document.getElementById("source-"+editorTag).style.display = "block";
            }
            else
            {
                console.log("SOURCE EDITOR HIDE");
                document.getElementById("vr-"+editorTag).style.display = "block";
                document.getElementById("source-"+editorTag).style.display = "none";
            }

            break;

        default:
            iWin.focus();
            iWin.document.execCommand(uCommand, null, "");
            break;
    }

   // XZ: console.log("button status ("+uCommand+") : "+document.queryCommandEnabled (uCommand));
   // XZ: if(document.queryCommandEnabled (uCommand))
   // XZ:     tElement.classList.add("active");
   // XZ: else
   // XZ:     tElement.classList.remove("active");


    liveEdit(editorTag);
}

function createEditor(editorTag)
{
    document.execCommand('defaultParagraphSeparator', false, 'p');

    // Получаем доступ к объектам window & document для ифрейма
    var iFrame = (isGecko) ? document.getElementById('frame-'+editorTag) : frames["frame-"+editorTag];
    var iDoc = (isGecko) ? iFrame.contentDocument : iFrame.document;
    var hInput = document.getElementById(editorTag);

    iDoc.execCommand('defaultParagraphSeparator', false, 'p');

    // Событие обработки всех кнопок
    var commandElements = document.querySelectorAll('.ds-redactor .controls span');
    console.log("Обработка элементов управления "+commandElements.length+" штук");
    for(var i=0; i < commandElements.length; i++) {
        commandElements[i].addEventListener("click", function(event) {commandHandler(event, editorTag); });
    }

    // Событие идентификации тегов
    iDoc.addEventListener();

    var defaultText = hInput.value;

    if(defaultText == "") defaultText = "Text";

    // Формируем HTML-код
    iHTML = "<html><head>";
    // use css here
    iHTML += "<body><div>"+defaultText+"</div></body>";
    iHTML += "</html>";

    // Добавляем его с помощью методов объекта document
    iDoc.open();
    iDoc.write(iHTML);
    iDoc.close();

    if (!iDoc.designMode) alert("Визуальный режим редактирования не поддерживается Вашим браузером");
    else iDoc.designMode = (isGecko) ? "on" : "On";

    liveEdit(editorTag);
    // Событие при изменении фрейма
    iFrame.addEventListener("load", function() { liveEdit(editorTag); });
    iFrame.addEventListener("DOMContentLoaded", function() { liveEdit(editorTag); });
    iDoc.addEventListener("keyup", function() { liveEdit(editorTag); });

    // Событие при изменении исходного кода
    hInput.addEventListener("input", function() { liveEditSource(editorTag); });


}

function liveEdit(editorTag)
{
    console.log("LiveEdit (tag #"+editorTag+")");
    var iDoc = (isGecko) ? document.getElementById("frame-"+editorTag).contentDocument : document.getElementById("frame-"+editorTag).document;
    document.getElementById(editorTag).value = iDoc.body.innerHTML;
}

function liveEditSource(editorTag)
{
    console.log('changed');
    var iDoc = (isGecko) ? document.getElementById("frame-"+editorTag).contentDocument : document.getElementById("frame-"+editorTag).document;
    iDoc.body.innerHTML = document.getElementById(editorTag).value;
}

//function keyPressHandler(e)
//{
//    console.log("Press key: "+e.key);
//    if(e.key == 'Enter')
//    {
//        e.preventDefault();
//    }
//    liveEdit();
//}