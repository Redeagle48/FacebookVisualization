<?php
//ini_set('display_errors', 1);
require './FacebookSDK/facebook.php';

$facebook = new Facebook(array(
    'appId' => '',
    'secret' => '',
        ));

// See if there is a user from a cookie
$user = $facebook->getUser();

if ($user) {
    try {
        // Proceed knowing you have a logged in user who's authenticated.
        $user_profile = $facebook->api('/me');
        print_r($user_profile);
        echo '<br/>';
        echo '<br/>';
        //For example: your facebook friend's page is http://www.facebook.com/users/1002020300010
        $myFriend = $facebook->api('/ana.almeida.372');
        //Print information like your friend's name:
        echo $myFriend['name'];
        echo '<br/>';
        echo '<br/>';
        //Print all information captured:
        print_r($myFriend);
        echo '<br/>';
        echo '<br/>';
        $myFriends = $facebook->api('/me/friends');
        $statuses = $facebook->api('/me/statuses?limit=10000');
        $statuses = $facebook->api('/me/posts');
        //$statuses = $facebook->api('/me/posts?access_token=xxx&limit=5000&since=2+years+ago&until=now');
        //print_r($myFriends);
        echo '<br/>';
        echo '<br/>';

        $friendArray = array();
        $cont = 0;
        while (/*count($statuses["data"]) != 0*/ $cont <= 500) {

            foreach ($statuses['data'] as $status) {
                // processing likes array for calculating fanbase. 

                foreach ($status['likes']['data'] as $likesData) {
                    $frid = $likesData['id'];
                    $frname = $likesData['name'];
                    //print_r($frname); echo '<br/>'; echo '<br/>';
                    if (isset($friendArray[$frid]) | array_key_exists($friendArray[$frid], $friendArray)) {
                        $friendArray[$frid]["count"] = $friendArray[$frid]["count"] + 1;
                    } else {
                        $friendArray[$frid]["name"] = $frname;
                        $friendArray[$frid]["count"] = 1;
                    }
                }

                foreach ($status['comments']['data'] as $comArray) {
                    // processing comments array for calculating fanbase
                    $frid = $comArray['from']['id'];
                    $frname = $comArray['from']['name'];
                    if (isset($friendArray[$frid]) | array_key_exists($friendArray[$frid], $friendArray)) {
                        $friendArray[$frid]["count"] = $friendArray[$frid]["count"] + 1;
                    } else {
                        $friendArray[$frid]["name"] = $frname;
                        $friendArray[$frid]["count"] = 1;
                    }
                }
            }
            $res = explode("/posts", $statuses["paging"]["next"]);
            $url = '/me/posts' . $res[1];
            $statuses = $facebook->api($url);
            $cont = $cont+1;
            //print_r($friendArray); echo '<br/>'; echo '<br/>';
            //$logoutUrl = $facebook->getLogoutUrl();
        }
        //print_r($friendArray);
        echo '<br/>';
        echo '<br/>';
    } catch (FacebookApiException $e) {
        $user = null;
    }

    function aasort(&$array, $key) {
        $sorter = array();
        $ret = array();
        reset($array);
        foreach ($array as $ii => $va) {
            $sorter[$ii] = $va[$key];
        }
        arsort($sorter);
        foreach ($sorter as $ii => $va) {
            $ret[$ii] = $array[$ii];
        }
        $array = $ret;
    }

    aasort($friendArray, "count");
    print_r($friendArray);
    echo '<br/>';
    echo '<br/>';

    
    $friendArray = array_slice($friendArray, 1, 5);  // retirar o meu nome
    print_r($friendArray);
    echo '<br/>';
    echo '<br/>';

    $total = 0;
    foreach ($friendArray as $key => $value) {
        $total = $total + $value["count"];
    }
    print_r($total);
} else {
    $loginUrl = $facebook->getLoginUrl();
}

$data2 = array(
    array(
        "value" => $friendArray[0]["count"] / $total * 100,
        "color" => "#878BB6",
    ),
    array(
        "value" => $friendArray[1]["count"] / $total * 100,
        "color" => "#4ACAB4"
    ),
    array(
        "value" => $friendArray[2]["count"] / $total * 100,
        "color" => "#FF8153"
    ),
    array(
        "value" => $friendArray[3]["count"] / $total * 100,
        "color" => "#FFEA88"
    ),
    array(
        "value" => $friendArray[4]["count"] / $total * 100,
        "color" => "#001100"
    )
);
$data2 = json_encode($data2);
//print_r($data2);        echo '<br/><br/>';
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Facebook PHP SDK</title>
    </head>
    <body>
        <fb:login-button size="small" onlogin="after_login_button()" scope="email, user_about_me, user_birthday, user_status, publish_stream, user_photos, read_stream, friends_likes">Login with facebook</fb:login-button>
        <div id="fb-root"></div>

        <script>
            window.fbAsyncInit = function() {
                FB.init({
                    appId: '<?php echo $facebook->getAppID() ?>',
                    cookie: true,
                    xfbml: true,
                    oauth: true
                });

                /* This is used with facebook button */
                FB.Event.subscribe('auth.login', function(response) {
                    if (response.authResponse) {
                        // Specify the login page (the page in which your fb login button is situated)
                        window.location = 'main.php';
                    }
                });
                FB.Event.subscribe('auth.logout', function(response) {
                    window.location = 'logout.php';
                });
            };
            (function() {
                var e = document.createElement('script');
                e.async = true;
                e.src = document.location.protocol +
                        '//connect.facebook.net/en_US/all.js';
                document.getElementById('fb-root').appendChild(e);
            }());

            function after_login_button() {
                FB.getLoginStatus(function(response) {
                    if (response.status == "connected") {
                        // If user is connected, redirect to this page
                        window.location = 'main.php';
                    }
                }, true);
            }
        </script>

        <!--<img src="https://graph.facebook.com/ana.almeida.372/picture">-->
        <canvas id="myChart" width="400" height="400"></canvas>


    </body>
</html>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Chart.js demo</title>
        <!-- import plugin script -->
        <script src='Chart.js-master/Chart.min.js'></script>
    </head>
    <body>
        <!-- line chart canvas element -->
        <!--<canvas id="buyers" width="600" height="400"></canvas>-->
        <!-- pie chart canvas element -->
        <canvas id="countries" width="600" height="400"></canvas>
        <!-- bar chart canvas element -->
        <!--<canvas id="income" width="600" height="400"></canvas>-->
        <script>
            /*
             // line chart data
             var buyerData = {
             labels : ["January","February","March","April","May","June"],
             datasets : [
             {
             fillColor : "rgba(172,194,132,0.4)",
             strokeColor : "#ACC26D",
             pointColor : "#fff",
             pointStrokeColor : "#9DB86D",
             data : [203,156,99,251,305,247]
             }
             ]
             }
             // get line chart canvas
             var buyers = document.getElementById('buyers').getContext('2d');
             // draw line chart
             new Chart(buyers).Line(buyerData);
             */
            // pie chart data

            var pieData = <?php echo $data2; ?>;

            /*
             var pieData = [
             {
             value: 20,
             color: "#878BB6"
             },
             {
             value: 40,
             color: "#4ACAB4"
             },
             {
             value: 10,
             color: "#FF8153"
             },
             {
             value: 30,
             color: "#FFEA88"
             }
             ];
             */

            // pie chart options
            var pieOptions = {
                segmentShowStroke: false,
                animateScale: true,
            }
            // get pie chart canvas
            var countries = document.getElementById("countries").getContext("2d");
            // draw pie chart
            new Chart(countries).Pie(pieData, pieOptions);


            /*
             // bar chart data
             var barData = {
             labels : ["January","February","March","April","May","June"],
             datasets : [
             {
             fillColor : "#48A497",
             strokeColor : "#48A4D1",
             data : [456,479,324,569,702,600]
             },
             {
             fillColor : "rgba(73,188,170,0.4)",
             strokeColor : "rgba(72,174,209,0.4)",
             data : [364,504,605,400,345,320]
             }
             ]
             }
             
             // get bar chart canvas
             var income = document.getElementById("income").getContext("2d");
             // draw bar chart
             new Chart(income).Bar(barData);
             */
        </script>
    </body>
</html>

<?php
echo '<br/>';
foreach ($friendArray as $value) {
    echo $value["name"] . " -> " . $value["count"] . " ";
}
?>