<!DOCTYPE html>
<html lang="en">
<head>
  
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
 
    <title>Live Demo of Google Maps Geocoding Example with PHP</title>
 
    <style>
    /* some custom css */
    #gmap_canvas{
        width:100%;
        height:30em;
    }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
 
</head>
<body>

<div id='address-examples'>
    <div>Address examples:</div>
    <div>1. G/F Makati Cinema Square, Pasong Tamo, Makati City</div>
    <div>2. 80 E.Rodriguez Jr. Ave. Libis Quezon City</div>
</div>


<?php 
// function to geocode address, it will return false if unable to geocode address
function geocode($address){
 
    // url encode the address

    $address = urlencode($address);


    // google map geocode api url
    $url = "https://geocode-maps.yandex.ru/1.x/?format=json&lang=en_US&apikey=9441b877-8ffd-4742-8548-31762b8bec58&geocode={$address}";


    // get the json response
    $resp_json = file_get_contents($url);


    // decode the json
    $resp = json_decode($resp_json, true);
    var_dump($resp['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['text']);
    //var_dump($resp['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['Address']['formatted']);

    // response status will be 'OK', if able to geocode given address 
    if($resp['status']=='OK'){
        var_dump($resp);
        // get the important data
        $lati = isset($resp['response'][0]['geometry']['location']['lat']) ? $resp['response'][0]['geometry']['location']['lat'] : "";
        $longi = isset($resp['response'][0]['geometry']['location']['lng']) ? $resp['response'][0]['geometry']['location']['lng'] : "";
        $formatted_address = isset($resp['response'][0]['formatted_address']) ? $resp['response'][0]['formatted_address'] : "";
         
        // verify if data is complete
        if($lati && $longi && $formatted_address){
         
            // put the data in the array
            $data_arr = array();            
             
            array_push(
                $data_arr, 
                    $lati, 
                    $longi, 
                    $formatted_address
                );
             
            return $data_arr;
             
        }else{
            return false;
        }
         
    }
 
    else{
        echo "<strong>ERROR: {$resp['status']}</strong>";
        return false;
    }
}
?>

<form action="" method="post">
    <input type='text' name='address' placeholder='Enter any address here' />
    <input type='submit' value='Geocode!' />
</form>
</body>
<?php
if($_POST){
 
    // get latitude, longitude and formatted address
    //$data = str_replace('.','', $_POST['address']);

    //$data_arr = geocode($_POST['address']);
    $data_arr = geocode($_POST['address']);


    // if able to geocode the address
    if($data_arr){
         
        $latitude = $data_arr[0];
        $longitude = $data_arr[1];
        $formatted_address = $data_arr[2];


    ?>
 
    <!-- google map will be shown here -->
    <div id="gmap_canvas">Loading map...</div>
    <div id='map-label'>Map shows approximate location.</div>
 
    <!-- JavaScript to show google map -->
    <script type="text/javascript" src="https://maps.google.com/maps/api/js?key=AIzaSyAQGXTYxpWZA2DefCYpd1zthzyqk68jLrU"></script>   
    <script type="text/javascript">
              function init_map() {
            var myOptions = {
                zoom: 14,
                center: new google.maps.LatLng(<?php echo $latitude; ?>, <?php echo $longitude; ?>),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            map = new google.maps.Map(document.getElementById("gmap_canvas"), myOptions);
            marker = new google.maps.Marker({
                map: map,
                position: new google.maps.LatLng(<?php echo $latitude; ?>, <?php echo $longitude; ?>)
            });
            infowindow = new google.maps.InfoWindow({
                content: "<?php echo $formatted_address; ?>"
            });
            google.maps.event.addListener(marker, "click", function () {
                infowindow.open(map, marker);
            });
            infowindow.open(map, marker);
        }
        google.maps.event.addDomListener(window, 'load', init_map);
    </script>
 
    <?php
 
    // if unable to geocode the address
    }else{
        echo "No map found.";
    }
}
?>
</html>
