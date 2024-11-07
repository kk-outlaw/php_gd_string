<?php
$font_file_name = realpath('./') . '/NotoSerifJP-Regular.ttf'; //フォントのパス：絶対パスで指定
$font_size = 32; //フォントサイズ
$text = "XYZxyzテキスト文字列";
//テキストの角度：GETパラメータより取得
$text_angle = empty($_GET['angle']) ? 0 : intval($_GET['angle']);

//描画領域の座標を取得し、画像サイズの計算に使用する
$bounding_box = imagettfbbox(
    $font_size,
    $text_angle,
    $font_file_name,
    $text
    //PHP8.0では引数にarray $options = []が追加されている
);

$max_x = max($bounding_box[0], $bounding_box[2], $bounding_box[4], $bounding_box[6]);
$min_x = min($bounding_box[0], $bounding_box[2], $bounding_box[4], $bounding_box[6]);
$max_y = max($bounding_box[1], $bounding_box[3], $bounding_box[5], $bounding_box[7]);
$min_y = min($bounding_box[1], $bounding_box[3], $bounding_box[5], $bounding_box[7]);

/*/
echo '(' . $bounding_box[0] . ', ' . $bounding_box[1] . ')' . "\n";
echo '(' . $bounding_box[2] . ', ' . $bounding_box[3] . ')' . "\n";
echo '(' . $bounding_box[4] . ', ' . $bounding_box[5] . ')' . "\n";
echo '(' . $bounding_box[6] . ', ' . $bounding_box[7] . ')' . "\n";
//*/

//画像サイズ
$canvas_width = $max_x - $min_x;
$canvas_height = $max_y - $min_y;

//キャンバスとなる空のイメージ
$image = imagecreatetruecolor($canvas_width, $canvas_height);

$background_color = imagecolorallocate($image, 255, 255, 255); //背景色: FFFFFF
$foreground_color = imagecolorallocate($image, 0, 0, 255); //前景色：: 0000FF

//*
//画像を背景色で塗りつぶす
imagefilledrectangle($image, 0, 0, $canvas_width, $canvas_height, $background_color);

//文字列を書き込む
imagettftext(
    $image,
    $font_size,
    $text_angle,
    $min_x * -1,
    $min_y * -1,
    $foreground_color,
    $font_file_name,
    $text
);

//画像を出力する前に明示的にレスポンスヘッダを出力する
header("Content-Type: image/png");
header('Content-Disposition: inline; filename="logo.png"');

//PNGイメージでストリーム出力
imagepng($image, null, -1, -1);

//後処理
imagedestroy($image);
/*/

/*
例：$text_angle = 100, $font_size = 32, フォントをNotoSerifJP-Regular.ttfとした場合のバウンディングボックスの座標は
($bounding_box[0], $bounding_box[1]) = (12, -2)
($bounding_box[2], $bounding_box[3]) = (-66, -449)
($bounding_box[4], $bounding_box[5]) = (-115, -440)
($bounding_box[6], $bounding_box[7]) = (-36, 7)

この中でx座標、y座標の最小値(-115, -449) = ($min_x, $min_y)がキャンバスの起点となるように調整する
すなわちimagettftext()の開始位置は($min_x * -1, $min_y * -1)となる
*/
?>