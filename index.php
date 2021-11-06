<?php

require 'assets/lib/database.php';

use Database\Database;

$conn = Database::getConnectionInstance();

$query = 'SELECT * FROM state';

$result = mysqli_query($conn, $query);

$states = mysqli_fetch_all($result, MYSQLI_ASSOC);

include_once('assets/include/layout.php');
include_once('assets/include/header.php');
?>

<div class="container">
    <main class="row min-vh-75 justify-content-center my-5">
        <div class="col-md-8 shadow bg-white p-4 rounded">
            <div class="h4 text-center my-2">
                <img src="assets/svgs/regular/address-card.svg" alt="account-icon" width="40" height="40">
                <span> Delivery Locations... </span>
            </div>
            <form action="map/" id="searchForm" method="post">
                <fieldset>
                    <div class="d-flex">
                        <legend class="w-75 mr-5">Current Location</legend>
                        <button type="button" id="addLocation" class="btn btn-outline-primary btn-sm">
                            Add Location
                            <i class="fa fa-plus-circle"></i>
                        </button>
                    </div>
                    <div class="row" id="model">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="address">Address Line <span class="text-danger">*</span></label>
                                <input type="text" name="streetName[]" id="address" class="form-control streetName"
                                    placeholder="23 321 Road" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="state">State <span class="text-danger">*</span></label>
                                <input type="hidden" name="state[]">
                                <select type="text" id="state"
                                        class="form-control text-capitalize state" required>
                                    <option value="">:: SELECT STATE ::</option>
                                    <?php
                                    foreach ($states as $state){
                                        echo '<option value="'.$state['id'].'">'.$state['name'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="lga">LGA <span class="text-danger">*</span></label>
                                <select type="text" name="lga[]" id="lga" class="form-control lga" required="required" disabled>
                                    <option value="">:: SELECT LGA ::</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="postalCode">Postal Code</label>
                                <input type="text" name="postalCode[]" id="postalCode" class="form-control postalCode">
                            </div>
                        </div>
                    </div>
                </fieldset>
                <div class="text-right">
                    <button type="submit" id="search" class="btn btn-primary">
                        <i class="fa fa-search"></i>
                        Search Locations
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
<?php
include 'assets/include/js.php';
?>

</body>
</html>


<?php
/**
 * @project - ${PROJECT}
 * @author - Bille Ibinabo <billeibinabo@gmail.com>
 * @date - ${date}
 **/