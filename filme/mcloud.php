<!DOCTYPE html>
<?php
$id=$_GET['id'];
$host="https://".parse_url($id)['host'];
if (preg_match("/mcloud/",$host))
$host="https://mcloud.to/assets/mcloud/cache/";
else
$host=$host."/assets/vidstream/cache/";
echo '
<html>
<head>
<meta charset="utf-8">


</head>
<body>


<div id="player-wrapper"

    >
     <div class="servers">
        <div class="toggle"></div>
        <div class="items">

        </div>
    </div>

            <div id="player">
            <div class="loading"><div></div><div></div><div></div><div></div><div></div></div>
        </div>



</div>





<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>



<!-- inject:js -->
<!--https://mcloud.to/assets/mcloud/cache/-->
<!--<script type="text/javascript" src="scripts2.js?v=1676"></script>-->
<script src="'.$host.'scripts.js?v='.time().'" type="text/javascript"></script>

<!-- endinject -->

</body>
</html>
';
?>
