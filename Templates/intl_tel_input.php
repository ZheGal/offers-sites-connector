<?php if (!empty($country)):?>

<link rel="stylesheet" href="build/css/intlTelInput.min.css">
<script type="text/javascript" src="build/js/intlTelInput-jquery.min.js"></script>
<script type="text/javascript" src="build/js/intlTelInput.min.js"></script>

<style>
    .iti.iti--allow-dropdown.iti--separate-dial-code {
        width: 100%;
    }

    .iti__country-name,
    .iti__selected-dial-code {
        color: #000;
    }
    .offer_row{
        margin-top: 15px
    }
</style>
<script>
    $("input[type=tel]").intlTelInput({
        autoFormat: true,
        autoPlaceholder: "aggressive",
        defaultCountry: "auto",
        initialCountry: "auto",
        separateDialCode: true,
        geoIpLookup: function(success, failure) {
            var countryCode = "<?=$country?>";
            success(countryCode);
        },
        nationalMode: true,
        hiddenInput: "phone",
        numberType: "MOBILE",
        utilsScript: "build/js/utils.js",
    });
</script>

<?php endif;?>