<!DOCTYPE html>
<?php
echo '
<html>
<body>
            <div class="captcha">
                <form method="POST" id="recaptcha-form" action="look.php">
                    <input type="hidden" name="_csrf"
                           value=""/>
                    <div class="g-recaptcha" data-sitekey="6LdPO70aAAAAAPLTFBiLkiyTlzco6VNnD0Y6jP3b"
                         data-callback="captchaPassed"></div>
                </form>
            </div>
            <p class="text text-bottom">Thank you and enjoy your free unlimited watching</p>
        </div>

    </div>
</div>

<script>
    function captchaPassed(token) {
        document.querySelector("#recaptcha-form").submit();
    }
</script>
</div>
<script src="https://www.google.com/recaptcha/api.js?'.time().'"  async defer>
</script>
</body>
</html>
';
?>
