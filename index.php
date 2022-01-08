<?php // стили взяты с ВКонтакте, чтоб визуально было похоже на стену ВК ?>
<style>
    .bp_post {
        color:#363636;
        padding: 15px 0;
        margin-top: -1px;
        border: solid #e7e8ec;
        border-width: 1px 0;
    }
    .bp_thumb {
        float: left;
    }
    .bp_thumb, .bp_img {
        width: 50px;
        height: 50px;
    }
    .bp_img {
        border-radius: 50%;
        overflow: hidden;
    }
    .bp_info {
        margin-left: 62px;
    }
    .bp_author_wrap {
        line-height: 16px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .bp_author {
        font-weight: 700;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        color: #42648b;
    }
    a.bp_date {
        color: #939393;
        font-size:11px;
        
    }
    a.img.imgvk, img.imgvk {
        height:150px;
        margin:5px;    
    }
    
@media only screen and (max-width: 479px) {
    a.img.imgvk, img.imgvk {
        max-height:90px;
        max-width:120px;
        margin:3px;    
    }
    
    .bp_thumb, .bp_img {
        width: 40px;
        height: 40px;
    } 
    .bp_info {
        margin-left: 50px;
    }    
}
</style>
<?php

// https://vk.com/dev/board.getComments - Документация

$token = ""; // access_token - Как получить токен описано в документации ВК
$group_id = 1; // ID Группы
$topic_id = 35487172; // ID Топика

$count = 100; // кол-во нужных записей. Положительное число, по умолчанию 20, максимальное значение 100
$sort = 'asc'; // asc — хронологический / desc — антихронологический

// раскомментировать по необходимости
// $get_count = curl('https://api.vk.com/method/board.getComments?group_id='.$group_id.'&topic_id='.$topic_id.'&v=5.60&access_token='.$token);
// $jsonGetCount = json_decode($get_count,true);
// $count = $jsonGetCount['response']['count']; // данные по кол-ву записей, если вдруг нужно будет больше 100

$get = curl('https://api.vk.com/method/board.getComments?group_id='.$group_id.'&topic_id='.$topic_id.'&extended=1'.'&count='.$count.'&sort='.$sort.'&v=5.95&lang=ru&access_token='.$token);
$jsonGet = json_decode($get,true);

if(!empty($jsonGet['response']['groups'][0])) {
    $groupsId = $jsonGet['response']['groups'][0]['id']; // id группы
    $groupsName = $jsonGet['response']['groups'][0]['name']; // название группы
    $groupsPhoto = $jsonGet['response']['groups'][0]['photo_50']; // фото
}
?>

<div class="wall_module">
    <?php
    foreach ($jsonGet['response']['items'] as $value) :
        $userId = (string)$value['from_id']; // ID Автора
        $date = $value['date']; // Дата в unixtime
        $text = $value['text']; // Текст

        $user = false;
        $attachments = array();
        unset($textFormated);

        if ($userId[0] != '-') {
            $user = true;
            $profile = array_search($userId, array_column($jsonGet['response']['profiles'], 'id')); // Ищем автора в массиве profiles
            $text = $value['text']; // Текст

            $photo = $jsonGet['response']['profiles'][$profile]['photo_50']; // Фото Автора (50,100 и т.д.)
            $fname = $jsonGet['response']['profiles'][$profile]['first_name']; // Имя Автора
            $lname = $jsonGet['response']['profiles'][$profile]['last_name']; // Фамилия Автора
        }

        // Прикрепленные фото
        if(!empty($value['attachments'])) {
            $attachments = $value['attachments'];
        }

        // ответы на комментарии
        if (strripos($text, "|")) {
            $textArray = explode("|", $text);
            $textFormated = str_replace("]","",$textArray[1]);
        }
    ?>
    <div class="bp_post clear_fix ">
        <?php if ($user) : ?>
            <a class="bp_thumb" href="https://vk.com/id<?php echo $userId; ?>" target="_blank">
                <img class="bp_img" alt="<?php echo $fname." ".$lname; ?>" src="<?php echo $photo ; ?>">
            </a>
        <?php else :?>
            <a class="bp_thumb" href="https://vk.com/club<?php echo $groupsId; ?>" target="_blank">
                <img class="bp_img" alt="<?php echo $groupsName; ?>" src="<?php echo $groupsPhoto ; ?>">
            </a>
        <?php endif; ?>
        <div class="bp_info">
            <div class="bp_author_wrap">
                <?php if ($user) : ?>
                    <a class="bp_author" href="https://vk.com/id<?php echo $userId; ?>" target="_blank"><?php echo $fname." ".$lname; ?></a>
                    <a class="bp_date" ><?php echo gmdate("d.m.Y", $date); ?></a>
                <?php else :?>
                    <a class="bp_author" href="https://vk.com/club<?php echo $groupsId; ?>" target="_blank"><?php echo $groupsName; ?></a>
                    <a class="bp_date" ><?php echo gmdate("d.m.Y", $date); ?></a>
                <?php endif; ?>
                <span class="bp_topic"></span>
            </div>
            <div class="bp_content" id="">
                <?php if(isset($textFormated)) :?>
                    <div class="bp_text sl"><?php echo $textFormated; ?></div>
                <?php else :?>
                    <div class="bp_text"><?php echo $text; ?></div>
                <?php endif;?>
            <div>
                <?php
                // проверяем, есть ли прикрепленые файлы в записи, далее берем только изображения, можно вывести видео, опрос и т.д.
                if (!empty($attachments)) {
                    foreach ($attachments as $attach) {
                        if($attach['type'] == 'photo') {
                            echo '<a href="'.$attach['photo']['photo_604'].'">';
                            echo '<img class="imgvk" src="'. $attach['photo']['photo_604'].'" >';
                            echo '</a>';
                        }
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>
    <?php endforeach; ?>
</div>

<?php
// внимание! проверьте, что у вас на сервере есть curl
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