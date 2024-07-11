<?php


// echo 'gg';

// echo 'e';
require_once __DIR__.'/configuration/connect.php';
$method = $_SERVER['REQUEST_METHOD'];
// if ($connect) {
//         echo 'gg';
//     }else {
//         echo 'badg';
//     }
switch ($_GET['need']) {
    case 'signin':
        switch ($method) {
            case 'POST':
                $data = file_get_contents('php://input')[0]; 

                echo $data;

                $password = mysqli_fetch_all(mysqli_query($connect1, "SELECT * FROM `users` WHERE `email` = '".$data."'"));

                echo $password[0][3];

                break;
            
            default:
                echo 'j';
                break;
        }
        break;






    // ------------------------------------------------------------
    case 'station':
        $station = $_GET['station'];
        switch ($method) {
            case 'GET':
                $id = $_GET['id'];
                $password = mysqli_fetch_all(mysqli_query($connect, "SELECT `password` FROM `amdins` WHERE `id`='$id';"))[0][0];
                
                if ($password==$_GET['password']) {
                    $ns = mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `".$station."`"));
                for ($i=0; $i < count($ns); $i++) {
                    $trainsOnWay = explode(' ',$ns[$i][1]);
                    $CHlocoOnWay = explode(' ',$ns[$i][2]);
                    $NotCHlocoOnWay = explode(' ',$ns[$i][3]);

                        if ($trainsOnWay[0]==0) {
                            $trains[$i] = 0;
                        }else {
                            // print_r($trainsOnWay);
                            for ($j=0; $j < count($trainsOnWay); $j++) { 
                                // echo "SELECT * FROM `trains` WHERE `number` = ". explode(' ',$ns[$i][1])[$j].'<br/>';
                                // echo $ns[$i][1].'<br/>';
                                $trains[$i][$j] = mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `trains` WHERE `number` = ". explode(' ',$ns[$i][1])[$j]))[0];
                                
                            }
                        }

                        if ($NotCHlocoOnWay[0]==0) {
                            $NotCHloco[$i] = 0;
                        }else {
                            // print_r($trainsOnWay);
                            for ($j=0; $j < count($NotCHlocoOnWay); $j++) { 
                                // echo "SELECT * FROM `trains` WHERE `number` = ". explode(' ',$ns[$i][1])[$j].'<br/>';
                                // echo $ns[$i][1].'<br/>';
                                $NotCHloco[$i][$j] = mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `locomotives` WHERE `number` = ". explode(' ',$ns[$i][3])[$j]))[0];
                                
                            }
                        }

                        if ($CHlocoOnWay[0]==0) {
                            $CHloco[$i] = 0;
                        }else {
                            // print_r($trainsOnWay);
                            for ($j=0; $j < count($CHlocoOnWay); $j++) { 
                                // echo "SELECT * FROM `trains` WHERE `number` = ". explode(' ',$ns[$i][1])[$j].'<br/>';
                                // echo $ns[$i][1].'<br/>';
                                $CHloco[$i][$j] = mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `locomotives` WHERE `number` = ". explode(' ',$ns[$i][2])[$j]))[0];
                                
                            }
                        }
                }
                $ans = ['trains' => $trains, 'CH' => $CHloco, 'NotCH' => $NotCHloco];
                echo json_encode($ans);
                }
                break;
            case 'POST':
                $fix = json_decode(file_get_contents('php://input'));
                

                $fixTrain = $fix[0];
                $fixLocoCH = $fix[1][0];
                $fixLocoNotCH = $fix[1][1];
                
                for ($i=0; $i < count($fixTrain); $i++) {
                    if ($fixTrain[$i][1])$StrTrains = $fixTrain[$i][1][0][0];
                    else $StrTrains = 0;

                    if ($fixTrain[$i][1]) {
                        for ($j=1; $j < count($fixTrain[$i][1]); $j++) {
                            $StrTrains = $StrTrains.' '.$fixTrain[$i][1][$j][0];
                        }
                        // $trains = join(' ', $fixTrain[$i][1]);
                        // $trains = join(' ', ['44', '24', '34', '54']);
    
                        echo $StrTrains.'   '.$fixTrain[$i][0];
                        mysqli_query($connect, "UPDATE `".$station."` SET `trains` = '$StrTrains' WHERE ".$station.".`way` = ".$fixTrain[$i][0]);
    
                    }else {
                        mysqli_query($connect, "UPDATE ".$station." SET `trains` = 0 WHERE ".$station.".`way` = ".$fixTrain[$i][0]);
                    }
                    
                }
                // echo json_encode($fixTrain);
                // echo 'gogo';

                for ($i=0; $i < count($fixTrain); $i++) { 
                    for ($j=0; $j < count($fixTrain[$i][1]); $j++) { 
                        mysqli_query($connect, "UPDATE `trains` SET `position` = '".$fixTrain[$i][1][$j][2]."' WHERE `trains`.`number` = ".$fixTrain[$i][1][$j][0]);
                    }
                    
                }


                for ($i=0; $i < count($fixLocoCH); $i++) {
                    if ($fixLocoCH[$i][1])$StrTrains = $fixLocoCH[$i][1][0][0];
                    else $StrTrains = 0;

                    if ($fixLocoCH[$i][1]) {
                        for ($j=1; $j < count($fixLocoCH[$i][1]); $j++) {
                            $StrTrains = $StrTrains.' '.$fixLocoCH[$i][1][$j][0];
                        }
                        // $trains = join(' ', $fixLocoCH[$i][1]);
                        // $trains = join(' ', ['44', '24', '34', '54']);
    
                        echo $StrTrains.'   '.$fixLocoCH[$i][0];
                        mysqli_query($connect, "UPDATE ".$station." SET `CH` = '$StrTrains' WHERE ".$station.".`way` = ".$fixLocoCH[$i][0]);
    
                    }else {
                        mysqli_query($connect, "UPDATE ".$station." SET `CH` = 0 WHERE ".$station.".`way` = ".$fixLocoCH[$i][0]);
                    }
                    
                }

                for ($i=0; $i < count($fixLocoNotCH); $i++) {
                    if ($fixLocoNotCH[$i][1])$StrTrains = $fixLocoNotCH[$i][1][0][0];
                    else $StrTrains = 0;

                    if ($fixLocoNotCH[$i][1]) {
                        for ($j=1; $j < count($fixLocoNotCH[$i][1]); $j++) {
                            $StrTrains = $StrTrains.' '.$fixLocoNotCH[$i][1][$j][0];
                        }
                        // $trains = join(' ', $fixLocoNotCH[$i][1]);
                        // $trains = join(' ', ['44', '24', '34', '54']);
    
                        echo $StrTrains.'   '.$fixLocoNotCH[$i][0];
                        mysqli_query($connect, "UPDATE ".$station." SET `NotCH` = '$StrTrains' WHERE ".$station.".`way` = ".$fixLocoNotCH[$i][0]);
    
                    }else {
                        mysqli_query($connect, "UPDATE ".$station." SET `NotCH` = 0 WHERE ".$station.".`way` = ".$fixLocoNotCH[$i][0]);
                    }
                    
                }

                

                
                
                    
                break;


            default:
                # code...
                break;
        }
        break;
    case 'operation':
        switch ($method) {
            case 'GET':
                $operation = mysqli_fetch_all(mysqli_query($connect, 'SELECT * FROM `operation`'));
                echo json_encode($operation);
                break;
            case 'POST':
                $data = json_decode(file_get_contents('php://input'));
                echo $data;
                mysqli_query($connect, "INSERT INTO `operation` (`id`, `desc`, `type`, `num_Loco_CH`, `num_Loco_NotCH`, `vagon`, `from`, `to`, `later_min`, `start`, `finish`) VALUES (NULL, '$data[0]', '$data[1]', '$data[2]', '$data[3]', '$data[4]', '$data[5]', '$data[6]', '$data[7]', '$data[8]', '$data[9]')");
                break;
            case 'DELETE':
                echo mysqli_query($connect, "DELETE FROM operation WHERE `operation`.`id` = ".file_get_contents('php://input'));
                break;
            default:
                # code...
                break;
        }
        
        break;
    case 'train':
        switch ($method) {
            case 'GET':
                $number = $_GET['train'];
                $ans = mysqli_fetch_all(mysqli_query($connect, 'SELECT * FROM `trains` WHERE number = '.$number.';'))[0];
                echo json_encode([$ans[0], $ans[2]]);
                break;
            
            default:
                # code...
                break;
        }
        break;
    
    case 'authorization':
        $data = json_decode(file_get_contents('php://input'));
        $name = $data[0];
        $password = $data[1];
        $g = mysqli_fetch_all(mysqli_query($connect, "SELECT `password` FROM `amdins` WHERE `name`='$name';"));

        if ($g[0][0]==$password) {
            echo mysqli_fetch_all(mysqli_query($connect, "SELECT `id` FROM `amdins` WHERE `name`='$name';"))[0][0];
        }else {
            echo 0;
        }
        break;


    
          
            
    

          
    
    
  
    
}


// require_once __DIR__.'/configuration/connect.php';
// $method = $_SERVER['REQUEST_METHOD'];
// // if ($connect) {
// //     echo 'gg';
// // }else {
// //     echo 'badg';
// // }
// switch ($_GET['for']) {
//     case 'event':
//         $event = mysqli_fetch_all(mysqli_query($connect, 'SELECT * FROM `event`'));
//         switch ($method) {
//             case 'GET':
//                     echo json_encode($event);
//                     break;
//             case 'POST':
//                     $arr = json_decode(file_get_contents('php://input'));
//                     mysqli_query($connect, "INSERT INTO `event` (`id`, `name`, `description`, `image`) VALUES (NULL, '$arr[0]', '$arr[1]', '$arr[2]')");
//                     break;        
//             case 'DELETE':
//                     mysqli_query($connect, 'DELETE from event where id='.file_get_contents('php://input').';');
//                     break;
//             case 'PATCH':
//                     $fix = json_decode(file_get_contents('php://input'));
//                     mysqli_query($connect, "UPDATE `event` SET `name` = '$fix[1]', `description` = '$fix[2]', `image` = '$fix[3]' WHERE `event`.`id` = ".$fix[0]);
//                     break;
//         }
//         break;
    
//     case 'useful':
//         $useful =  mysqli_fetch_all(mysqli_query($connect, 'SELECT * FROM `useful`'));
//         switch ($method) {
//             case 'GET':
//                 echo json_encode($useful);
//                 break;
            
//             case 'POST':
//                 $arr = json_decode(file_get_contents('php://input'));
//                 if (!mysqli_query($connect, "INSERT INTO `useful` (`id`, `name`, `date`, `desc`, `time`,`number`) VALUES (NULL, '$arr[0]', '$arr[1]', '$arr[2]', '$arr[3]', '$arr[4]')")) {
//                     echo 1;
//                 }
//                 break;
//             case 'DELETE':
//                 $Did = file_get_contents('php://input');
//                 if (!mysqli_query($connect, 'DELETE from useful where id='.$Did.';')) {
//                     echo 1;
//                 }
//                 break;
//             case 'PATCH':
//                 $fix = json_decode(file_get_contents('php://input'));
//                 if (!mysqli_query($connect, "UPDATE `useful` SET `name` = '$fix[1]', `date` = '$fix[2]', `desc` = '$fix[3]', `time` = '$fix[4]', `number` = '$fix[5]' WHERE `useful`.`id` = ".$fix[0])) {
//                     echo 1;
//                 }
//                 break;
//         }
//         break;
//     case 'kitchen':
//         switch ($_GET['type']) {
//             case 'kitchen':
//                 $kitchen = [
//                     mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `restoraunt`.`type_kitchen` WHERE `type` = 'ЗАКУСКИ'")),
//                     mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `restoraunt`.`type_kitchen` WHERE `type` = 'ОСНОВНОЕ БЛЮДО'")),
//                     mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `restoraunt`.`type_kitchen` WHERE `type` = 'САЛАТЫ'")),
//                     mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `restoraunt`.`type_kitchen` WHERE `type` = 'СУПЫ'")),
//                     mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `restoraunt`.`type_kitchen` WHERE `type` = 'ДЕСЕРТ'"))                
//                 ];
//                 break;
//             case 'breakfast':
//                 $kitchen = [
//                     mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `restoraunt`.`type_kitchen` WHERE `type` = 'БАНКЕТНОЕ МЕНЮ'"))  
//                     // mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `restoraunt`.`type_kitchen` WHERE `type` = 'СЛАДКОЕ'")),
//                     // mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `restoraunt`.`type_kitchen` WHERE `type` = 'НЕ СЛАДКОЕ'"))               
//                 ];
//                 break;
//             case 'bar':
//                 $kitchen = [
//                     mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `restoraunt`.`type_kitchen` WHERE `type` = 'КОФЕ'")),
//                     mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `restoraunt`.`type_kitchen` WHERE `type` = 'БЕЗАЛКОГОЛЬНЫЕ НАПИТКИ'")),
//                     mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `restoraunt`.`type_kitchen` WHERE `type` = 'ЧАЙ'")),
//                     mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `restoraunt`.`type_kitchen` WHERE `type` = 'ВИНО'")),
//                     mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `restoraunt`.`type_kitchen` WHERE `type` = 'ВИНО ИГРИСТОЕ'")),
//                     mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `restoraunt`.`type_kitchen` WHERE `type` = 'КОКТЕЙЛИ'")),
//                     mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `restoraunt`.`type_kitchen` WHERE `type` = 'ПИВО'")),
//                     mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `restoraunt`.`type_kitchen` WHERE `type` = 'ВОДКА'")),
//                     mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `restoraunt`.`type_kitchen` WHERE `type` = 'КОНЬЯК'")),
//                     mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `restoraunt`.`type_kitchen` WHERE `type` = 'ВИСКИ'")),
//                     mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `restoraunt`.`type_kitchen` WHERE `type` = 'БРЭНДИ'")),
//                     mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `restoraunt`.`type_kitchen` WHERE `type` = 'ДЖИН'")),
//                     mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `restoraunt`.`type_kitchen` WHERE `type` = 'РОМ'")),          
//                 ];
//                 break;
            
//             default:
//             $kitchen =  mysqli_fetch_all(mysqli_query($connect, "SELECT * FROM `restoraunt`.`type_kitchen` WHERE `type` = '".$_GET['type']."'"));
//                 break;
//         }
        
//         switch ($method) {
//             case 'GET':
//                 echo json_encode($kitchen);
//                 break;
            
//             case 'POST':
//                 $arr = json_decode(file_get_contents('php://input'));
//                 if (!mysqli_query($connect, "INSERT INTO `type_kitchen` (`id`, `type`, `name`, `desc`, `weight`, `price`) VALUES (NULL, '$arr[0]', '$arr[1]', '$arr[2]', '$arr[3]', '$arr[4]')")) {
//                     echo json_encode($arr);
//                 }
//                 break;
//             case 'DELETE':
//                 $Did = file_get_contents('php://input');
//                 if (!mysqli_query($connect, 'DELETE from type_kitchen where id='.$Did.';')) {
//                     echo 1;
//                 }
//                 break;
//             case 'PATCH':
//                 $fix = json_decode(file_get_contents('php://input'));
//                 if (!mysqli_query($connect, "UPDATE `type_kitchen` SET `type` = '$fix[5]', `name` = '$fix[1]', `desc` = '$fix[2]', `weight` = '$fix[3]', `price` = '$fix[4]' WHERE `type_kitchen`.`id` = ".$fix[0])) {
//                     echo 1;
//                 }
//                 break;
//         }
//         break;
//         case 'special':
//             $special = mysqli_fetch_all(mysqli_query($connect, 'SELECT * FROM `special`'));
    
//             switch ($method) {
//                 case 'GET':
//                     echo json_encode($special);
//                     break;
                
//                 case 'POST':
//                     $arr = json_decode(file_get_contents('php://input'));
//                     if (!mysqli_query($connect, "INSERT INTO `special` (`id`, `desc`, `img`, `price`, `weight`) VALUES (NULL, '$arr[0]', '$arr[1]', '$arr[2]', '$arr[3]')")) {
//                         echo json_encode($arr);
//                     }
//                     break;
//                 case 'DELETE':
//                     $Did = file_get_contents('php://input');
//                     if (!mysqli_query($connect, 'DELETE from special where id='.$Did.';')) {
//                         echo 1;
//                     }
//                     break;
//                 case 'PATCH':
//                     $fix = json_decode(file_get_contents('php://input'));
//                     if (!mysqli_query($connect, "UPDATE `special` SET `desc` = '$fix[1]', `img` = '$fix[2]', `price` = '$fix[3]', `weight` = '$fix[4]' WHERE `special`.`id` = ".$fix[0])) {
//                         echo 1;
//                     }
//                     break;
//             }
//             break;
//         case 'reserved':
//             $reserved = mysqli_fetch_all(mysqli_query($connect, 'SELECT * FROM `reserved`'));
//             switch ($method) {
//                 case 'GET':
//                     echo json_encode($reserved);
//                     break;
//                 case 'POST':
//                     $arr = json_decode(file_get_contents('php://input'));
//                     if (mysqli_query($connect, "INSERT INTO `reserved` (`id`, `name`, `surname`, `date`, `time`, `tel`, `kolvo`, `info`) 
//                     VALUES (NULL, '$arr[0]', '$arr[1]', '$arr[2]', '$arr[3]', '$arr[4]', '$arr[5]', '$arr[6]')")) {
//                         echo 1;
//                     }else {
//                         echo 2;
//                     }
//                     break;
                
//                 default:
//                     # code...
//                     break;
//             }
//             break;
// }
?>