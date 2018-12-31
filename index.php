<?php
$token = ""; // access_token
$group_id = 123; // ID Группы
$topic_id = 123; // ID Топика

$get_count = curl('https://api.vk.com/method/board.getComments?group_id='.$group_id.'&topic_id='.$topic_id.'&v=5.60&access_token='.$token);
$jsonGetCount = json_decode($get_count,true);
$count = $jsonGetCount['response']['count'];

for ($i = 1; $i <= $count; $i++) {

$get = curl('https://api.vk.com/method/board.getComments?group_id='.$group_id.'&topic_id='.$topic_id.'&extended=1'.'&offset='.$i.'&count=1&v=5.60&access_token='.$token);
$jsonGet = json_decode($get,true);
$user_id = $jsonGet['response']['items'][0]['from_id']; // ID Автора
$date = $jsonGet['response']['items'][0]['date']; // Дата в unixtime
$text = $jsonGet['response']['items'][0]['text']; // Текст

$photo = $jsonGet['response']['profiles'][0]['photo_50']; // Фото Автора (50,100 и т.д.)
$fname = $jsonGet['response']['profiles'][0]['first_name']; // Имя Автора
$lname = $jsonGet['response']['profiles'][0]['last_name']; // Фамилия Автора

echo '<img src="'.$photo.'">';
echo $fname." ".$lname."</br>".gmdate("Y-m-d", $date)."</br>".$text."</br></br>";


}

function curl($url) {
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);
return $response;
}
?>
