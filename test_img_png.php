<?php

function getColorDiagram ($im, $index){
    // Зададим цвета элементов для диаграмм

    $colorsDiagramArray = array(
        0 => array(255, 203, 3),
        1 => array(220, 101, 29),
        2 => array(189, 24, 51),
        3 => array(214, 0, 127),
        4 => array(98, 1, 96),
        5 => array(0, 62, 136),
        6 => array(0, 102, 179),
        7 => array(0, 145, 195),
        8 => array(0, 115, 106),
        9 => array(178, 210, 52),
        10 => array(137, 91, 74),
        11 => array(82, 56, 47)
    );

    return imagecolorallocate($im, $colorsDiagramArray[$index][0], $colorsDiagramArray[$index][1], $colorsDiagramArray[$index][2]);
}

function getShadowDiagram ($im, $index){
    // Зададим цвета теней элементов для диаграмм

    $shadowsDiagramArray = array(
        0 => array(205, 153, 0),
        1 => array(170, 51, 0),
        2 => array(139, 0, 1),
        3 => array(164, 0, 77),
        4 => array(48, 0, 46),
        5 => array(0, 12, 86),
        6 => array(0, 52, 129),
        7 => array(0, 95, 145),
        8 => array(0, 65, 56),
        9 => array(128, 160, 2),
        10 => array(87, 41, 24),
        11 => array(32, 6, 0),
    );

    return imagecolorallocate($im, $shadowsDiagramArray[$index][0], $shadowsDiagramArray[$index][1], $shadowsDiagramArray[$index][2]);
}

function Diagramm($im, $VALUES, $LEGEND) {
    // $im - идентификатор изображения
    // $VALUES - массив со значениями
    // $LEGEND - массив с подписями

    //GLOBAL $COLORS, $SHADOWS;

    $black = ImageColorAllocate($im, 0, 0, 0);

    // Получим размеры изображения
    $W = ImageSX($im);
    $H = ImageSY($im);

    // Вывод легенды #####################################

    // Посчитаем количество пунктов, от этого зависит высота легенды
    $legend_count = count($LEGEND);

    // Посчитаем максимальную длину пункта, от этого зависит ширина легенды
    $max_length = 0;
    foreach($LEGEND as $v){
        if ($max_length < strlen($v)) {
            $max_length = strlen($v);
        }
    }

    // Номер шрифта, котором мы будем выводить легенду
    $FONT = 2;
    $font_w = ImageFontWidth($FONT);
    $font_h = ImageFontHeight($FONT);

    // Вывод прямоугольника - границы легенды ----------------------------

    $l_width = ($font_w * $max_length) + $font_h + 10 + 5 + 10;
    $l_height = $font_h * $legend_count + 10 + 10;


    // Получим координаты верхнего левого угла прямоугольника - границы легенды
    $l_x1 = $W - 10 - $l_width;
    $l_y1 = ($H - $l_height) / 2;

    // Выводя прямоугольника - границы легенды
    ImageRectangle($im, $l_x1, $l_y1, $l_x1 + $l_width, $l_y1 + $l_height, $black);

    // Вывод текст легенды и цветных квадратиков
    $text_x = $l_x1 + 10 + 5 + $font_h;
    $square_x = $l_x1 + 10;
    $y = $l_y1 + 10;

    $i = 0;
    foreach($LEGEND as $v) {
        $dy = $y + ($i*$font_h);
        ImageString($im, $FONT, $text_x, $dy, $v, $black);
        ImageFilledRectangle($im,
            $square_x+1, $dy+1, $square_x+$font_h-1, $dy+$font_h-1,
            getColorDiagram($im, $i));
        ImageRectangle($im,
            $square_x+1, $dy+1, $square_x+$font_h-1, $dy+$font_h-1,
            $black);
        $i++;
    }

    // Вывод круговой диаграммы ----------------------------------------

    $total = array_sum($VALUES);
    $anglesum = $angle = Array(0);
    $i = 1;

    // Расчет углов
    while ($i < count($VALUES)) {
        $part = $VALUES[$i-1] / $total;
        $angle[$i] = floor($part*360);
        $anglesum[$i] = array_sum($angle);
        $i++;
    }
    $anglesum[] = $anglesum[0];

    // Расчет диаметра
    $diametr = $l_x1-10-10;

    // Расчет координат центра эллипса
    $circle_x = ($diametr/2) + 10;
    $circle_y = $H/2-10;

    // Поправка диаметра, если эллипс не помещается по высоте
    if ($diametr > ($H*2)-10-10) {
        $diametr = ($H*2)-20-20-40;
    }

    // Вывод тени
    for ($j=20; $j>0; $j--) {
        for ($i = 0; $i < count($anglesum) - 1; $i++) {
            ImageFilledArc($im, $circle_x, $circle_y + $j,
                $diametr, $diametr / 2,
                $anglesum[$i], $anglesum[$i + 1],
                getShadowDiagram($im, $i), IMG_ARC_PIE);
        }
    }

    // Вывод круговой диаграммы
    for ($i=0; $i<count($anglesum)-1; $i++){
        ImageFilledArc($im, $circle_x, $circle_y,
            $diametr, $diametr/2,
            $anglesum[$i], $anglesum[$i+1],
            getColorDiagram($im, $i), IMG_ARC_PIE);
    }
}

/*function drawDiagram (){

}*/

// Создадим изображения
header("Content-Type: image/png");
$im = ImageCreate(500, 500);

// Зададим цвет фона. Немного желтоватый, для того, чтобы было
// видно границы изображения на белом фоне.
$bgcolor = ImageColorAllocate($im, 255, 255, 200);

// Зададим значение и подписи
$VALUES = Array(100, 200, 300, 400, 500, 400, 300);
$LEGEND = Array("John", "Bob", "Alex", "Mike", "Andrew", "Greg");

// Вызов функции рисования диаграммы
Diagramm($im, $VALUES, $LEGEND);

// Генерация изображения
ImagePNG($im);

?>