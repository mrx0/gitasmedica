/**
	drag drop script
 	https://habrahabr.ru/post/187582/
**/

//dragStart: пользователь начинает перетаскивание элемента.
function dragStart(ev) {
	//console.log(ev.target.getAttribute('id'));

    ev.dataTransfer.effectAllowed='move';
    ev.dataTransfer.setData("Text", ev.target.getAttribute('id'));
    ev.dataTransfer.setDragImage(ev.target,1,1);
    return true;
}
//dragEnter: перетаскиваемый элемент достигает конечного элемента.
function dragEnter(ev) {
    event.preventDefault();
    return true;
}
//dragOver: курсор мыши наведен на элемент при перетаскивании.
function dragOver(ev) {
    event.preventDefault();
}

function dragDrop(ev) {
    var data = ev.dataTransfer.getData("Text");
    //ev.target.appendChild(document.getElementById(data));
    ev.stopPropagation();

    var target = ev.target;

    // цикл двигается вверх от target к родителям до table
    while (target.getAttribute('invoice_attrib') != 'true') {

        target = target.parentNode;
    }

    //если перетаскивали availableBalance
    if (ev.dataTransfer.getData("Text") == 'availableBalance') {
        //console.log(target.getAttribute('invoice_id'));
        //console.log(ev.dataTransfer.getData("Text"));
    }

    return false;
}