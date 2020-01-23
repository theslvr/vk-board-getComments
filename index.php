<?php // стили взяты с ВКонтакте, чтоб визуально было похоже на стену ВК ?>
<style>
    .bp_post {
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
    }
</style>
<?php
$token = ""; // access_token - Как получить токен, описано в документации ВК
$group_id = 12345; // ID Группы
$topic_id = 12345; // ID Топика

$get_count = curl('https://api.vk.com/method/board.getComments?group_id='.$group_id.'&topic_id='.$topic_id.'&v=5.60&access_token='.$token);
$jsonGetCount = json_decode($get_count,true);
$count = $jsonGetCount['response']['count'];
?>
<div class="wall_module">
    <?php
    for ($i = 1; $i <= $count; $i++) {
        $get = curl('https://api.vk.com/method/board.getComments?group_id='.$group_id.'&topic_id='.$topic_id.'&extended=1'.'&offset='.$i.'&count=1&v=5.60&access_token='.$token);
        $jsonGet = json_decode($get,true);

        $user_id = $jsonGet['response']['items'][0]['from_id']; // ID Автора
        $date = $jsonGet['response']['items'][0]['date']; // Дата в unixtime
        $text = $jsonGet['response']['items'][0]['text']; // Текст
        $attachments = $jsonGet['response']['items'][0]['attachments']; // Прикрепленные файлы к записи
        $photo = $jsonGet['response']['profiles'][0]['photo_50']; // Фото Автора (50,100 и т.д.)
        $fname = $jsonGet['response']['profiles'][0]['first_name']; // Имя Автора
        $lname = $jsonGet['response']['profiles'][0]['last_name']; // Фамилия Автора
        ?>
        <div class="bp_post clear_fix ">
            <a class="bp_thumb" href="https://vk.com/id<?php echo $user_id; ?>">
                <img class="bp_img" alt="<?php echo $fname." ".$lname; ?>" src="<?php echo $photo ; ?>">
            </a>
            <div class="bp_info">
                <div class="bp_author_wrap">
                    <a class="bp_author" href="https://vk.com/id<?php echo $user_id; ?>"><?php echo $fname." ".$lname; ?></a>
                    <a class="bp_date" ><?php echo gmdate("Y-m-d", $date); ?></a>
                    <span class="bp_topic"></span>
                </div>
                <div class="bp_content" id="">
                    <div class="bp_text"><?php echo $text; ?></div>
                    <div>
                        <?php
                        // проверяем, есть ли прикрепленые файлы в записи, далее берем только изображения, можно вывести опрос и т.п.
                        if ($attachments) {
                            foreach ($attachments as $attach) {
                                echo '<a href="'.$attach['photo']['photo_1280'].'" target="_blank">';
                                echo '<img src="'. $attach['photo']['photo_604'].'"height="223" style="margin:5px;">';
                                echo '</a>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
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
</div>