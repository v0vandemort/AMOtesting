<?php

require_once __DIR__ . '/src/AmoCrmV4Client.php';

define('SUB_DOMAIN', 'mamyshevvovan');
define('CLIENT_ID', '48fd41e7-0f56-4bfe-b5d9-5930df2eb055');
define('CLIENT_SECRET', 'OO6PZWAR1Ruip91PAiwv8aDvtldGEz1Scei1as3YNfW6B00AJ0AQBL6x9VYFCCzn');
define('CODE', 'def5020052e21a57133444e826499e6f30b8c75ef386c82c612d4afa33bfbcba8c32982249e22261b699aaccdc8c4dbea3d31202c7a08b40e77e080853569dd2dccf943c27eae01494713097bb87e809bcb36329f0dbc12236bbaf7c509c96bfb80456267dd064a7d1c24ff4fc0403f9f5e6cadf7d82da94c4bf7de0be86b014d7b0b24f142102fc8ad6cc554795da9a761d5e31f7d0d15d1eced62d5f9d071a52b80e3a558316a3a8936b6fb1a2623dcec94c856d881fe2253294538ff4d27e2a28b7081b11cc90024aae013a2a88541c791b501f73bc6bac8a9199ab9325894418bc75865c8e17fd2040b26231c4adaef226f5eab03973f45d0421060b098d69ed14f28a67affa929c96f4db31df88c92d8337dda0c2f9b86d0a787702442c630809041d6d34630d42031803e358db70f519b01493428a3bfa3b23c05282c1acde9a67c757dcdb55def68128d9d2e027b01fa2cb8cf9fbf3ae4ed609f1b3cc49c2ca954dfce67c64bda87ec6c362c358081c75d6c2246f320ba1062c067f945d9122c3f52435a4182231f6839fe0f3b2d90fc766e2c46b265d0c48d293a42cd67d95230ddb7f0e2e6675a127375e8c40189a696e336f93365e648a29b423ef876b99ebe501713b40f874f966bc246488e056c6981ef490b2dba7c4b14bcd3a83032c003a6728d5cac6cb67c71e6e40');
define('REDIRECT_URL', 'https://mamyshevvovan.amocrm.ru');


try {
    $amoV4Client = new AmoCrmV4Client(SUB_DOMAIN, CLIENT_ID, CLIENT_SECRET, CODE, REDIRECT_URL);

    //Смена статуса на следующий при цене больше >5000
    $leads = $amoV4Client->GETRequestApi("leads", [
        "filter[statuses][0][pipeline_id]" => 7597210,
        "filter[statuses][0][status_id]" => 62884266
    ]);

    foreach ($leads['_embedded']['leads'] as $key => &$lead) {
        if ($lead['price'] > 5000) {
            $leadId = $lead['id'];
            $leads = $amoV4Client->POSTRequestApi("leads", [
                ["id" => $leadId,
                    "status_id" => 62884270,]
            ], "PATCH");
        };
    }

    //Создание дубликата сделки с ценой 4999 на предыдущий этап со всеми значениями, примечаниями и задачами
    $leads = $amoV4Client->GETRequestApi("leads", [
        "filter[statuses][0][pipeline_id]" => 7597210,
        "filter[statuses][0][status_id]" => 62884278,
        "filter[price]" => 4999
    ]);

    $checkLeads = $amoV4Client->GETRequestApi("leads", [
        "filter[statuses][0][pipeline_id]" => 7597210,
        "filter[statuses][0][status_id]" => 62884274,
        "filter[price]" => 4999
    ]);


    foreach ($leads['_embedded']['leads'] as $key => &$lead) {
        $leadId = $lead['id'];
        $newLead = [$lead];

        unset($lead['id']);
        unset($lead['updated_at']);
        unset($lead['_links']);
        unset($lead['status_id']);
        unset($lead['closest_task_at']);
        $copyExists = false;

        //Проверка наличия созданных копий
        if (!empty($checkLeads['_embedded']['leads'])) {
            $copyExists = false;


            foreach ($checkLeads['_embedded']['leads'] as $checkKey => &$checkLead) {

                unset($checkLead['id']);
                unset($checkLead['_links']);
                unset($checkLead['updated_at']);
                unset($checkLead['status_id']);
                unset($checkLead['closest_task_at']);

                if ($lead == $checkLead) {
                    $copyExists = true;
                    break;

                }
            }

        }
        //Если копии текущей сделки нет, то создаем
        if (!$copyExists) {

            //Чистим данные для дублирования
            unset($newLead[$key]['id']);
            unset($newLead[$key]['_links']);
            unset($newLead[$key]['_embedded']);
            $newLead[0]['status_id'] = 62884274;//Перенос статуса копии сделки

            //чистка пустых элементов элементов в сделке
            foreach ($newLead[0] as $subKey => $subValue) {
                if (empty($newLead[0][$subKey])) {
                    unset($newLead[0][$subKey]);
                }
            }

            //вносим очищенные данные
            $copiedLead = $amoV4Client->POSTRequestApi("leads", $newLead, "POST");


            $tasks = $amoV4Client->GETRequestApi("tasks", [
                "filter[entity_id]" => $leadId
            ]);

            foreach ($tasks['_embedded']['tasks'] as $key => &$task) {
                $newTask = $task;
                unset($newTask['id']);
                unset($newTask['_links']);
                $newTask['entity_id']=$copiedLead['_embedded']['leads'][0]['id'];
                $taskToCopy=[$newTask];
                $copiedTask = $amoV4Client->POSTRequestApi("tasks", $taskToCopy, "POST");

            }
        } else {
            continue;
        }
        unset($copyExists);


    }
} catch (Exception $ex) {
    var_dump($ex);
    file_put_contents("ERROR_LOG.txt", 'Ошибка: ' . $ex->getMessage() . PHP_EOL . 'Код ошибки:' . $ex->getCode());
}
