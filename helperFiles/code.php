
<?php require_once '../Database/dbconfig.php'; ?>

<?php
    /**
     * Retrieve the values from the database and store it in a Multi-Dimensional Array
     */

    $query="SELECT * FROM forum;";
    $result=$connection->query($query);
    $associates=[];
    if($result->num_rows>0) {
        while ($row = $result->fetch_assoc()) {
            $id=$row['id'];
            $name = $row['name'];
            $lat = $row['latitude'];
            $lng = $row['longitude'];
            $email=$row['email'];
            $assc=array('id'=>$id,'name'=>$name,'email'=>$email,'lat'=>$lat,'lng'=>$lng);
            $associates[]=$assc;
        }
    }


    /**
    * The distanceResult consists of
    * ->id
    * ->name
    * ->distance From the Origin
    */

    $distanceResult=[];

    $destinations=[];

    /**
     * Process the Multi Dimensional array to create a Google Maps API query string.
     * The Origin Co ordinate can be calculated from the HTML5 Browser Geo Location Service
    */

    $origin="27.712136,85.342559";
    $baseURL="https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$origin."&destinations=";
    $key="&key=AIzaSyBeYHkcH0fGv8QOuZ_iBU8gbAv0dB36J8U";
    $destinationQuery="";
    foreach ($associates as $arr) {
        $destinationQuery.=$arr['lat'].",".$arr['lng']."|";
        $destinations["{$arr['id']}"]=array('name'=>$arr['name'],'latlng'=>"{lat:{$arr['lat']}, lng: {$arr['lng']}}");
        $distanceResult[]=array('id'=>$arr['id'],'name'=>$arr['name']);
    }

    $url=$baseURL.$destinationQuery.$key;

    /**
     * The URL is the api query.
    */

    $str_json=file_get_contents($url);
    $json_object=json_decode($str_json);
    $tiny_object = $json_object->rows;
    $count=0;
    foreach ($tiny_object as $objectArray) {
        foreach ($objectArray->elements as $distanceObject) {
            $distanceArray=$distanceObject->distance;
            $distanceResult[$count++]['distance']=$distanceArray->value;
        }
    }

   /**
    *   Here, the co-ordinates are calcualted and preserved.
    *   Next, we create a map.
    *   And, Plot the co ordinates.
    */
?>

<!doctype html>
    <html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <title>myAttorney - Your Legal Helper</title>
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/formCustom.css">
        <link rel="stylesheet" href="../css/styles.css">
        <script src="../js/jquery-3.1.0.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <style>
            /* Always set the map height explicitly to define the size of the div
             * element that contains the map. */
            #map {
                height: 80%;
                width:  80%;
            }
            /* Optional: Makes the sample page fill the window. */
            html, body {
                height: 100%;
                margin: 0;
                padding: 0;
            }
        </style>
    </head>
    <body>
    <div class="container">

        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="localhost/myAttorney">myAttorney</a>
                </div>
                <ul class="nav navbar-nav">
                    <li><a href="index.php">Home</a></li>
                    <li class="active"><a href="findLawyer.php">Find A Lawyer</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Contact Us</a></li>
                </ul>
            </div>
        </nav>

    </div>
    <div class="container">
    <div id="floating-panel">
        <input type="button" value="Get GeoLocation" onclick="">
        <b>Destination: </b>
        <select id="end"> </select>
            <?php
            /**
             * Use PHP to create a dynamic Select Menu
             */
            $menu_items=$destinations;
            die(print_r($menu_items));
            foreach ($menu_items as $option) {
//                echo "<option value=\"{$option['latlng']}\">{$option['name']}</option>";
                print_r($option);
                echo "<hr>";
            }
            ?>
            <!--        <option value="{lat: 27.712136, lng: 85.342559}">DWIT</option>-->
            <!--        <option value="{lat: 27.67355299999999,lng: 85.31725100000001}">Reliance</option>-->
            <!--        <option value="joplin, mo">Joplin, MO</option>-->
            <!--        <option value="oklahoma city, ok">Oklahoma City</option>-->
            <!--        <option value="amarillo, tx">Amarillo</option>-->
            <!--        <option value="gallup, nm">Gallup, NM</option>-->
            <!--        <option value="flagstaff, az">Flagstaff, AZ</option>-->
            <!--        <option value="winona, az">Winona</option>-->
            <!--        <option value="kingman, az">Kingman</option>-->
            <!--        <option value="barstow, ca">Barstow</option>-->
            <!--        <option value="san bernardino, ca">San Bernardino</option>-->
            <!--        <option value="los angeles, ca">Los Angeles</option>-->
<!--        </select>-->
    </div>
    <div id="map">

    </div>
<script>
    function initMap() {
        var directionsService = new google.maps.DirectionsService;
        var directionsDisplay = new google.maps.DirectionsRenderer;

        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 15,
            center: {lat: 27.712136, lng: 85.342559}
        });

        directionsDisplay.setMap(map);

        calculateAndDisplayRoute(directionsService, directionsDisplay);
        var onChangeHandler = function() {
            calculateAndDisplayRoute(directionsService, directionsDisplay);

        };
        document.getElementById('start').addEventListener('change', onChangeHandler);
        document.getElementById('end').addEventListener('change', onChangeHandler);
    }

    function calculateAndDisplayRoute(directionsService, directionsDisplay) {
        directionsService.route({
            destination: document.getElementById('end').value,
//            origin: document.getElementById('start').value,
            origin:     {lat: 27.712136,lng: 85.342559},
//            destination: {lat: 27.691221, lng: 85.31811900000001},
            travelMode: 'DRIVING'
        }, function(response, status) {
            if (status === 'OK') {
                directionsDisplay.setDirections(response);
            } else {
                window.alert('Directions request failed due to ' + status);
            }
        });
    }
</script>
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBeYHkcH0fGv8QOuZ_iBU8gbAv0dB36J8U&callback=initMap">
</script>
        </div>
</body>
</html>