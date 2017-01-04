<?php
  function updateString($data){
    return strtoupper(trim(trim(mb_convert_encoding($data,'UTF-8','UTF-8')),"\n"));
  }

  function traintype($data){
    $data = updateString($data);
    $tdata = explode("-",$data);
    $tdata[0] = updateString($tdata[0]);
    switch ($tdata[0]) {
      case 'MEMU':
        return 'MEMU';
        break;
      case 'EMU':
        return 'EMU';
      case 'PASSENGER':
        return 'PASS';
      case 'SUVIDHA':
        return 'SUV';
      case 'MAIL/EXPRESS':
        return 'EXP';
      case 'DEMU':
        return 'DEMU';
      case 'SUPERFAST':
        return 'SF';
      case 'AC SUPERFAST':
        return 'ACSF';
      case 'AC EXPRESS':
        return 'ACEXP';
      case 'SHATABDI':
        return 'SHTB';
      case 'GARIB RATH':
        return 'GRB';
      case 'DOUBLE DECKER':
        return 'DD';
      case 'JAN SHATABDI':
        return 'JSHTB';
      case 'DURANTO':
        return 'DRNT';
      case 'RAJDHANI':
        return 'RAJ';
      case 'HILL TRAIN':
        return 'HT';
      case 'SPECIAL':
        return 'SPCL';
      case 'SAMPARK KRANTI':
        return 'SMPK';
      case 'HUMSAFAR':
        return 'HMS';
      default:
        return 'EXP';
        break;
    }
  }


        $conn = pg_connect("host=10.100.71.21 dbname=201401058 user=201401058 password=1234567890");
        if (!$conn) {
          echo "An error occurred.\n";
          exit;
        }
        $query = "SET search_path TO railway_reservation_system;\n";
        echo $query."<br>";
        //Setting search path to railwayreservationsystem schema where all table of interest exists
        $result = pg_query($conn,$query);
        if (!$result) {
          echo "An error occured.\n";
          exit;
        }

  $file = fopen("trlist.rtf","r");
  $trainfile = fopen("train_insert.sql","w");
  $stopfile = fopen("trainstops_insert.sql","w");
  while(!feof($file)){
      $line = fgets($file);
      $trno = $line;
      $datafile = fopen(trim($line),"r");
      $line = fgets($datafile);
      echo "<br>".$line."<br>";
      $traindata = explode("|",$line);
      for($i = 0, $j = count($traindata); $i < $j; $i++){
        $traindata[$i] = updateString($traindata[$i]);
      }
      $line1 = fgets($datafile);
      $rakeinfo = explode("|",$line1);
      for($i = 0, $j = count($rakeinfo); $i < $j; $i++){
        $rakeinfo[$i] = updateString($rakeinfo[$i]);
      }
      if(count(explode(" ",$trno[0])) > 1)
        continue;
      $trainno = $traindata[0]; //ddddddddddddddddddddd
      echo $trainno."<br>";
      $temp = explode("-",$trainno);
      if(count($temp) > 1){
        continue;
      }
      $trainname = $traindata[1]; //ddddddddddddddddddddddddddd
      echo $traindata[2]."<br><br>";
      $ttype = traintype(trim(trim(trim($traindata[2]),"\n"),"\t")); //dddddddddddddddddd
      $start = $traindata[4]; //ddddddddddddddddddddddddd
      $end = $traindata[5]; //ddddddddddddddddddddddddddddddddddd
      $freqfile = fopen(".\\alltrain\\".trim($trno),"r");
      $line2 = fgets($freqfile);
      $fdata = explode("|",$line2);
      $freq = array(); //ddddddddddddddddddddddddd
      for($i = 0; $i < 7; $i++){
        if(trim($fdata[$i]) == ""){
          $freq[$i] = 'FALSE';
        }else{
          $freq[$i] = 'TRUE';
        }
      }
      $zone = $traindata[3]; //ddddddddddddddddddddddddd
      $rinfo = array();
      $rinfo['GEN'] = 0;
      $rinfo['S2'] = 0;
      $rinfo['SL'] = 0;
      $rinfo['A1'] = 0;
      $rinfo['A2'] = 0;
      $rinfo['A3'] = 0;
      $rinfo['CC'] = 0;
      $rinfo['EX'] = 0;
      $rinfo['FC'] = 0;

      $food = 'FALSE';

      foreach ($rakeinfo as $key => $value) {
        $rakeinfo[$key] = strtoupper($rakeinfo[$key]);
        $d = explode("-",$rakeinfo[$key]);
        $d[0] = trim($d[0]);
        if($d[0] == 'LARR' || $d[0] == 'DARR' || $d[0] == 'EOG')
          $d[0] = 'GEN';
        if($d[0] == 'PC'){
          $food =  'TRUE';
          continue;
        }
        if(!isset($rinfo[$d[0]]))
          continue;
        $rinfo[$d[0]]++;
      }


      $trainq = "INSERT INTO TRAIN VALUES ('$trainno','$trainname','$start','$end','$ttype','$zone',$food,($freq[1],$freq[2],$freq[3],$freq[4],$freq[5],$freq[6],$freq[0]),$rinfo[GEN],$rinfo[EX],$rinfo[S2],$rinfo[FC],$rinfo[SL],$rinfo[A3],$rinfo[A2],$rinfo[A1],$rinfo[CC]);\n";

      $result = pg_query($conn, $trainq);

      echo $trainq."<br><br>";
      $lastzone = '';

      $count = 0;
      $query = "";
      $queryp = "";
      while(!feof($datafile)){
        $line = fgets($datafile);
        $sinfo = explode("|",$line);
        if(count($sinfo) < 9)
          continue;
        $ststation = $sinfo[0];
        $arrtime = $sinfo[2];
        $deptime = $sinfo[3];
        $jday = $sinfo[4];
        $pf = $sinfo[5];
        $distance = $sinfo[6];
        $lastzone = $sinfo[8];
        if($pf == '--')
          $pf = 'NULL';
        if($count == 0){
          $arrtime = $deptime;
        }
        if($deptime == '')
          $deptime = $arrtime;
          if(!is_int($jday))
            $jday = 0;
        $pf = explode(",",$pf)[0];
        $query = "INSERT INTO TRAINSTOPS VALUES ('$trainno','$ststation','$arrtime','$deptime',$pf,$jday,$distance);";
        $queryp += $query."\n";
        $result = pg_query($conn, $query);
        echo $query."<br>";
        $count++;
      }

      if($zone == '???'){
        $zone = $lastzone;
        pg_query("UPDATE TRAIN SET RZONE = '$zone' WHERE TRAINNO = '$trainno'");
        echo "<hr>UPDATE TRAIN SET RZONE = '$zone 'WHERE TRAINNO = '$trainno'<hr>";
      }
      fwrite($trainfile,$trainq);
      fwrite($stopfile,$queryp);
  }
?>
