<html>
  <head>
    <title>Writing to my Database</title>
  </head>
  <body>
    <?php
      //before any script run this script
      //connecting PHP to postgre SQL database server
      $conn = pg_connect("host=10.100.71.21 dbname=201401058 user=201401058 password=ADpatel3697@");
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

      $script_file = fopen("station_insert.sql","a");
      //fwrite($script_file,$query);
      //read in given Range
      //each file have 100 station's details
      for( $i = 3401; $i < 10402; $i+= 100){

        $file = fopen($i.'',"r");
        if($file == null)
          break;

        while(!feof($file)){
            echo "File : $i";

            $data = fgets($file);

            $station_details = explode("%",$data);
            //If count is less than 3 then data parsed from the file is invalid so continue to next station's details
            if(count($station_details) < 3)
              continue;

            //Removing extra details from station's name
            $station_name = explode("Railway Station - Train Departure Timings",$station_details[1]);
            $station_details[1] = trim($station_name[0]);

            //If platform details are not available then set it to 2
            if($station_details[2] == "n/a")
              $station_details[2] = 2;

            //Formatting station name and address to be inserted into database
            $station_details[3] = mb_convert_encoding($station_details[3], 'UTF-8', 'UTF-8');
            $station_details[3] = str_replace("'","",$station_details[3]);
            $station_details[3] = str_replace("\n","",$station_details[3]);
            $station_details[3] = trim($station_details[3]);

            $pno = (int)$station_details[2];
            $station_details[0] = trim(strtoupper($station_details[0]),"\n");
            $station_details[1] = trim(strtoupper($station_details[1]),"\n");
            $station_details[3] = trim(strtoupper($station_details[3]),"\n");
            $query = "INSERT INTO station VALUES ('$station_details[0]','$station_details[1]',$pno,'$station_details[3]');\n";
            echo $query."<br>";
            fwrite($script_file, $query);
            //Inserting station details in station table of database
            $result = pg_query($conn,$query);
        }
      }
     ?>
  </body>
</html>
