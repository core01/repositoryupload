<?php
/**
 * Created by PhpStorm.
 * User: anonymus
 * Date: 22.01.16
 * Time: 13:46
 */

if(isset($_POST['fileupload'])){
    //Получаем файл
    $file = $_FILES['package'];
    //Получаем имя файла
    $name =  $_FILES['package']['name'];
    if($modx->getObject('modResource', array('pagetitle' => $name))){
        return "Релиз компонента ".$name." существует.";
    }
    $object_id = explode(".transport.zip", $name);
    $object_id = $pagetitle = $object_id[0];
    $name = explode("-", $name);
    $title = $name[0];
    $version = $name[1];
    $end = explode(".", $name[2]);
    $release = $end[0];
    $versions = explode(".", $version);
    if(!$package = $modx->getObject('modResource', array('pagetitle:LIKE' => $title.'%'))){
       // return "There is no resource with this pagetitle: ".$title;
        // Получаем id нашего репозитория
       $extras_id = $modx->getOption('modxRepository.handler_doc_id');
        /* @TODO Доделать автоматическое создание пакета и релиза*/

    }
    if(!$parent = $package->get("id")){
        return "Could not get parent of resource";
    }
    //Источник файлов, куда будем загружать транспортные пакеты
    if(!$source = $modx->getObject('sources.modFileMediaSource', '2')){
        return "Couldnt get Media Source number 2";
    }
    $source->initialize();
    if($source->uploadObjectsToContainer('', array($file))){
        $data = array(
            'pagetitle' =>$pagetitle,
            'parent' => $parent,
            'alias' => $pagetitle,
            'template' => 4,
            'published' => 1,
            'tv1' => $object_id,
            'tv3' => $versions[0],
            'tv4' => $versions[1],
            'tv5' => $versions[2],
            'tv6' => $release,
            'tv8' => $pagetitle.".transport.zip",
        );
        $resource = $modx->runProcessor('resource/create', $data);
        //$id = $resource->response['object']['id'];
        return '<p class="lead">Файл успешно загружен</p><p>'.$pagetitle.'</p>';
    }else{
        return 'Ошибка при загрузке файла';
    }
}