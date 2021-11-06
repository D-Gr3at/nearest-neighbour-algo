<?php
include_once('assets/include/layout.php');
include_once('assets/include/header.php');

abstract class RequestType{
    const POST = 0;
    const GET = 1;
    const PUT = 2;
    const PATCH = 3;
    const DELETE = 4;

    public static function CallAPI($method, $url, $data = false){
        $curl = curl_init();

        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "username:password");

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }
}

echo getenv('DB_USER');
?>

<div class="container">
    <main class="row min-vh-75 justify-content-center my-5">
        <div class="col-md-8 shadow bg-white p-4 rounded">
            <div class="h4 text-center my-2">
                <img src="assets/svgs/regular/address-card.svg" alt="account-icon" width="40" height="40">
                <span> Delivery Locations... </span>
            </div>
            <form action="" id="searchForm" method="post" onsubmit="return false">
                <fieldset>
                    <legend>Location 1</legend>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="streetName1">Address Line <span class="text-danger">*</span></label>
                                <input type="text" name="streetName[]" id="streetName1" class="form-control"
                                    placeholder="23 321 Road" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="state1">State <span class="text-danger">*</span></label>
                                <input type="text" name="state[]" id="state1" class="form-control"
                                    placeholder="Rivers" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="lga1">LGA <span class="text-danger">*</span></label>
                                <input type="text" name="lga[]" id="lga1" class="form-control"
                                    placeholder="PHALGA" required="required">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="postalCode1">Postal Code</label>
                                <input type="text" name="postalCode[]" id="postalCode1" class="form-control">
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Location 2</legend>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="streetName2">Address Line <span class="text-danger">*</span></label>
                                <input type="text" name="streetName[]" id="streetName2" class="form-control"
                                    placeholder="23 321 Road" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="state2">State <span class="text-danger">*</span></label>
                                <input type="text" name="state[]" id="state2" class="form-control"
                                    placeholder="Rivers" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="lga2">LGA <span class="text-danger">*</span></label>
                                <input type="text" name="lga[]" id="lga2" class="form-control"
                                    placeholder="PHALGA" required="required">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="postalCode2">Postal Code</label>
                                <input type="text" name="postalCode[]" id="postalCode2" class="form-control">
                            </div>
                        </div>
                    </div>
                </fieldset>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary ">
                        <i class="fa fa-search"></i>
                        Search Locations
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/all.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/jquery-ui.min.js"></script>
<script>
    $(document).ready(function () {
        console.log('here');
        $('#dateOfBirth').datepicker({
            showAnim: "slide",
            beforeShow: function (input, inst) {
                setTimeout(function () {
                    inst.dpDiv.css({
                        top: $('#dateOfBirth').offset().top + 35,
                        left: $('#dateOfBirth').offset().left
                    });
                }, 0);
            },
            maxDate: new Date()
        });
    })
</script>
<script src="assets/js/main.js"></script>

</body>
</html>


<?php
/**
 * @project - ${PROJECT}
 * @author - Bille Ibinabo <billeibinabo@gmail.com>
 * @date - ${date}
 **/