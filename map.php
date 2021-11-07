<?php
if (!isset($_POST['streetName']) || !isset($_POST['lga']) || !isset($_POST['state'])) {
    header('Location: /nna/');
}
require_once 'vendor/autoload.php';

use Dotenv\Dotenv;
use OpenCage\Geocoder\Geocoder;

$dotEnv = Dotenv::create(__DIR__);
$dotEnv->load();

define('GOOGLE_API_KEY', getenv('API_KEY'));

$geocoder = new Geocoder(getenv('GEO_CODER_KEY'));

$streetNames = str_replace(',', '', $_POST['streetName']);
$states = $_POST['state'];
$lgas = $_POST['lga'];
$locations = array();
$addresses = array();
$distance_matrix = array();
$sum_of_row_minimum = 0;
$sum_of_column_minimum = 0;

try {
    for ($i = 0; $i < count($streetNames); $i++) {
        $address = str_replace(',', '', $streetNames[$i]) . ' ' . $lgas[$i] . ' ' . $states[$i];
        $result = $geocoder->geocode($address, ['language' => 'en', 'countrycode' => 'ng']);
        if ($result['status']['code'] != 200) {
            ?>
            <script>
                if (window.confirm("You are not connected to the internet or your connection is weak. Please refresh...")) {
                    location.reload();
                } else {
                    window.location.href = 'nna/';
                }
            </script>
            <?php
        }
        $locations[$address] = $result['results'][0]['geometry'];
        array_push($addresses, trim($address));
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

$addresses_ = $addresses;

function calculate_distance($coord1, $coord2): float
{
    $theta = $coord1['lng'] - $coord2['lat'];
    $dist = sin(deg2rad($coord1['lat'])) * sin(deg2rad($coord2['lat'])) + cos(deg2rad($coord1['lat'])) * cos(deg2rad($coord2['lat'])) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    return round($miles * 1.609344, 4); // in km
}

function calculate_distances_from_address($address): array
{
    global $addresses;
    global $locations;
    $row = array();
    foreach ($addresses as $addr) {
        if ($locations[$address] == $locations[$addr])
            $row[trim($addr)] = 'N/A';
        else
            $row[trim($addr)] = calculate_distance($locations[trim($address)], $locations[trim($addr)]);
    }
    return $row;
}

foreach ($addresses as $address) {
    $row = calculate_distances_from_address($address);
    $distance_matrix[$address] = $row;
}

function find_row_minimum($row): float
{
    $new_row = array_filter($row, function ($value) {
        return is_float($value);
    });
    if ($new_row == null) {
        return 0;
    }
    return min($new_row);
}

function find_col_minimum($column): float
{
    $new_col = array_filter($column, function ($value) {
        return is_float($value);
    });
    if ($new_col == null) {
        return 0;
    }
    return min($new_col);
}

function get_row_from_matrix($index, $matrix): array
{
    return $matrix[$index];
}

function get_column_from_matrix($addr, $matrix): array
{
    $column = array();
    foreach ($matrix as $k => $row) {
        foreach ($row as $key => $value) {
            if (trim($addr) == trim($key)) {
                $column[$k] = $value;
            }
        }
    }
    return $column;
}

function substract_minimum_from_row($minimum, $row): array
{
    if ($minimum == 0) {
        return $row;
    }
    $new_row = array();
    foreach ($row as $key => $value) {
        $new_row[$key] = $value == 'N/A' ? 'N/A' : $value - $minimum;
    }
    return $new_row;
}

function assign_value(&$var, $data)
{
    $var = $data;
}

/*Reduce Rows*/
function reduce_row($matrix): array
{
    global $sum_of_row_minimum;
    $total_min = 0;
    $new_matrix = array();
    foreach ($matrix as $k => $row) {
        $minimum = find_row_minimum($row);
        $total_min += $minimum;
        $new_row = substract_minimum_from_row($minimum, $row);
        $new_matrix[$k] = $new_row;
    }
    assign_value($sum_of_row_minimum, $total_min);
    return $new_matrix;
}

function reduce_column($matrix): array
{
    global $sum_of_column_minimum;
    $total_min = 0;
    $new_matrix = array();
    global $addresses_;
    foreach ($addresses_ as $address) {
        $column = get_column_from_matrix($address, $matrix);
        $minimum = find_col_minimum($column);
        $total_min += $minimum;
        $new_column = substract_minimum_from_col($minimum, $column);
        $new_matrix[$address] = $new_column;
    }
    assign_value($sum_of_column_minimum, $total_min);
    return transpose_matrix($new_matrix);
}

function substract_minimum_from_col(float $minimum, array $column): array
{
    $new_row = array();
    foreach ($column as $key => $value) {
        $new_row[$key] = is_float($value) ? $value - $minimum : 'N/A';
    }
    return $new_row;
}

function transpose_matrix(array $array): array
{
    global $addresses_;
    $new_array = array();
    foreach ($addresses_ as $address) {
        $row = get_column_from_matrix($address, $array);
        $new_array[$address] = $row;
    }
    return $new_array;
}

function make_infinity($current_location, $location, $reduced_arr): array
{
    $new_matrix = $reduced_arr;
    $row = get_row_from_matrix($current_location, $new_matrix);
    $infinity_row = set_row_to_infinity($row);
    $new_matrix[$current_location] = $infinity_row;
    $new_matrix[$location][$current_location] = 'N/A';
    return array_map(function ($v) use ($location) {
        $v[$location] = 'N/A';
        return $v;
    }, $new_matrix);
}

function is_reduced(array $arr): bool
{
    $temp_arr = array();
    foreach ($arr as $item) {
        if (!in_array(0, $item)) {
            array_push($temp_arr, 1);
        }
    }

    foreach ($arr as $key => $value) {
        $col = get_column_from_matrix($key, $arr);
        if (!in_array(0, $col)) {
            array_push($temp_arr, 1);
        }
    }
    return in_array(1, $temp_arr);
}

function set_row_to_infinity(array $row): array
{
    $new_row = array();
    foreach ($row as $key => $val) {
        $new_row[$key] = 'N/A';
    }
    return $new_row;
}

function find_minimum_cost($costs, $current_location): array
{
    $min = array();
    $minimum = min($costs[$current_location]);
    $min[array_search($minimum, $costs[$current_location])] = $minimum;
    return $min;
}

$best_path = array();

$reduced_arr = reduce_column(reduce_row($distance_matrix));
$initial_cost = $sum_of_row_minimum + $sum_of_column_minimum;

$t = $addresses;

$current_location = $addresses[0];
array_push($best_path, $current_location);

for ($i = 0; $i < (count($t) - 1); $i++) {
    $other_locations = array_filter($addresses, function ($address) use ($current_location) {
        return $address != $current_location;
    });
    $minimum_cost = array();
    $costs = array();
    $reduced_matrices = array();
    foreach ($other_locations as $location) {
        $arr = make_infinity($current_location, $location, $reduced_arr);
        if (is_reduced($arr)) {
            $cost = $reduced_arr[$current_location][$location] + $initial_cost;
        } else {
            $arr = reduce_column(reduce_row($arr));
            $reduction_cost = $sum_of_column_minimum + $sum_of_row_minimum;
            $cost = $reduced_arr[$current_location][$location] + $initial_cost + $reduction_cost;
        }
        $reduced_matrices[$location] = $arr;
        $costs[$current_location][$location] = $cost;
    }
    $minimum_cost[$current_location] = find_minimum_cost($costs, $current_location);
    $initial_cost = $minimum_cost[$current_location][array_key_first($minimum_cost[$current_location])];
    $current_location = array_key_first($minimum_cost[$current_location]);
    array_push($best_path, $current_location);
    $reduced_arr = $reduced_matrices[$current_location];
    $addresses = $other_locations;
}

include_once('assets/include/layout.php');
include_once('assets/include/header.php');
?>

<div class="container">
    <main class="row min-vh-75 justify-content-center my-5">
        <?php
        $str = '';
        for ($i = 0; $i < count($best_path); $i++) {
            $str .= $best_path[$i];
            if (($i + 1) < count($best_path)) {
                $str .= ' ==> ';
            }
        }

        ?>
        <h2>Best Route</h2>
        <span class="font-weight-bold mb-5"><?php echo $str; ?></span>
        <div id="map" style="height: 100vh; width: 100%;"></div>
    </main>
</div>
<?php
include 'assets/include/js.php';
?>
<script>
    let map;
    let locations = '<?php echo json_encode($locations); ?>';
    let addresses = '<?php echo json_encode($addresses_); ?>';
    let bestPath = '<?php echo json_encode($best_path); ?>';
    locations = JSON.parse(locations);
    addresses = JSON.parse(addresses);
    bestPath = JSON.parse(bestPath);
    const mapCenter = {lat: 9.0845755, lng: 8.674252499999994};
    const options = {
        center: mapCenter,
        zoom: 6,
    };

    function sortLocations(obj) {
        let arr = [];
        for (let i = 0; i < bestPath.length; i++) {
            let addr = bestPath[i];
            let v = Object.keys(obj)
                .filter(key => key === addr)
                .map(value => {
                    return locations[value]
                });
            arr.push(v[0]);
        }
        return arr;
    }

    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), options);
        let sortedLocations = sortLocations(locations);
        for (let i = 0; i < sortedLocations.length - 1; i++) {
            drawLine(sortedLocations[i], sortedLocations[i + 1]);
        }
        for (const address in locations) {
            if (address === Object.keys(locations)[0])
                addMarker({
                    coords: locations[address],
                    content: `<h6>Current Location</h6>${address}<br> <b>lat:</b> ${locations[address].lat} <b>lng:</b> ${locations[address].lng}`,
                    iconImage: ''
                });
            else
                addMarker({
                    coords: locations[address],
                    content: address + `<br> <b>lat:</b> ${locations[address].lat} <b>lng:</b> ${locations[address].lng}`,
                    iconImage: ''
                });
        }

        function addMarker(props) {
            let marker = new google.maps.Marker({
                position: props.coords,
                map: map
            });
            if (props.iconImage) {
                marker.setIcon(props.iconImage)
            }
            if (props.content) {
                let infoWindow = new google.maps.InfoWindow({
                    content: props.content
                });

                marker.addListener('click', () => {
                    infoWindow.open(map, marker);
                });
            }
        }

        function drawLine(pointX, pointY) {
            new google.maps.Polyline({
                path: [
                    new google.maps.LatLng(pointX.lat, pointX.lng),
                    new google.maps.LatLng(pointY.lat, pointY.lng)
                ],
                strokeColor: "#FF0000",
                strokeOpacity: 1.0,
                strokeWeight: 10,
                geodesic: true,
                map: map
            });
        }
    }

</script>
<script
        src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_API_KEY; ?>&callback=initMap&v=weekly&libraries=geometry"
        async
></script>


</body>
</html>
