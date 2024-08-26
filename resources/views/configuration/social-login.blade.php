<!-- Page Heading -->
<h1 class="">
    <?= __tr('Social Login') ?>
</h1>
<!-- /Page Heading -->
<hr>
{{-- @dd(getAppSettings('allow_google_login')) --}}
<!-- User Setting Form -->
<form x-data="{allow_google_login:{{ getAppSettings('allow_google_login') ? 1 : 0 }}}" x-cloak class="lw-ajax-form lw-form" method="post" data-callback="onSocialLoginFormCallback" action="<?= route('manage.configuration.write', ['pageType' => request()->pageType]) ?>">
    <fieldset class="lw-fieldset px-4 d-grid">
        <!-- Google login settings -->
        <!-- Allow google login input radio field -->
        <legend >
            <div class="custom-control custom-checkbox">
                <!-- Enable google login hidden field -->
                <input type="hidden" name="allow_google_login" id="lwEnableGoogleLogin" value="0" />
                <!-- /Enable google login hidden field -->
                <input type="checkbox" class="custom-control-input" id="lwAllowGoogleLogin" :checked="allow_google_login" name="allow_google_login" value="1">
                <label class="custom-control-label" for="lwAllowGoogleLogin">
                   {{ __tr('Allow Google Login') }}
                </label>
            </div>
        </legend>
        <!-- /allow google login input radio field -->
        <div class="mt-3" id="inputFieldShow" >
            <!-- Show after google login allow information -->
            <div class="btn-group mx-4" id="lwIsGoogleKeysExist">
                <button type="button" disabled="true" class="btn btn-success lw-btn lw-payment-mbl-view">
                    {{ __tr('Google keys are installed.') }}
                </button>
                <button type="button" class="btn btn-light lw-btn" id="lwAddGoogleKeys">
                    {{  __tr('Update') }}
                </button>
            </div>
            <!-- Show after google login allow information -->
            <!-- Google key exists hidden field -->
            <input type="hidden" name="google_keys_exist" id="lwGoogleKeysExist" value="<?= $configurationData['google_client_id'] ?>" />
            <!-- Google key exists hidden field -->
            <!-- Enable/Disable check box -->
            <div id="lwGoogleLoginInputField" class="px-1">
                <!-- Google Client ID -->
                <div class="my-3 mx-4">
                    <label for="lwGoogleClientId">
                        <?= __tr('Google Client ID') ?>
                    </label>
                    <input type="text" class="form-control form-control-user" name="google_client_id" placeholder="<?= __tr('Add Your Google Client ID') ?>" id="lwGoogleClientId" >
                </div>
                <!-- / Google Client ID -->

                <!--Google Client Secret -->
                <div class="mx-4">
                    <label for="lwGoogleClientSecret">
                        <?= __tr('Google Client Secret') ?>
                    </label>
                    <input type="text" class="form-control form-control-user" name="google_client_secret" placeholder="<?= __tr('Add Your Google Client Secret') ?>" id="lwGoogleClientSecret" >
                </div>
                <!-- /Google Client Secret -->

                <!-- Google Callback Url -->
                <div class="my-3 mx-4">
                    <label for="lwGoogleCallback Url">
                        <?= __tr('Callback URL') ?>
                    </label>
                    <input type="text" class="form-control form-control-user" data-toggle="tooltip" data-placement="top" title="{{ __tr('Click to copy') }}" id="lwGoogleCallbackUrl" value="{{route('login.google.callback')}}" onclick="myFunctionGoogle()" readonly>
                </div>
                <!-- / Google Callback Url -->
            </div>
        </div>
        <!-- Enable/Disable check box -->
        <!-- / Google login settings -->
        <!-- Update Button -->
        <div class="mt-2">
            <button type="submit" class="btn btn-primary btn-user lw-btn-block-mobile mt-2">
                <?= __tr('Save') ?>
            </button>
        </div>
    </fieldset>
</form>
<form x-data x-cloak class="lw-ajax-form lw-form" method="post" data-callback="onSocialLoginFormCallback" action="<?= route('manage.configuration.write', ['pageType' => request()->pageType]) ?>">
    <!-- facebook login settings -->
    <fieldset class="lw-fieldset px-4 d-grid">
        <!-- Allow facebook login input radio field -->
        <!-- Enable facebook login hidden field -->
        <legend>
            <!-- Facebook Link button -->
        <!-- / Facebook Link button -->
        <input type="hidden" name="allow_facebook_login" id="lwEnableFacebookLogin" value="0" />
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" onclick="allowFacebookLoginFunction()" id="lwAllowFacebookLogin" <?= getAppSettings('allow_facebook_login') ? 'checked' : '' ?> name="allow_facebook_login" value="1">
            <label class="custom-control-label" for="lwAllowFacebookLogin">
                <?= __tr('Allow Facebook Login') ?>
            </label>
        </div>
        </legend>

        <!-- /Allow facebook login input radio field -->
        <div id="lwInputFieldShow" style="display:none ">
            <!-- Show after facebook login allow information -->
            <div class="btn-group mx-4 mt-3" id="lwIsFacebookKeysExist" style="display:none;">
                <button type="button" disabled="true" class="btn btn-success lw-btn lw-payment-mbl-view">
                    <?= __tr('Facebook keys are installed.') ?>
                </button>
                <button type="button" class="btn btn-light lw-btn" id="lwAddFacebookKeys">
                    <?= __tr('Update') ?>
                </button>
            </div>
            <!-- Show after facebook login allow information -->

            <!-- Facebook key exists hidden field -->
            {{-- <input type="hidden" name="facebook_keys_exist" id="lwFacebookKeysExist" value="<?= $configurationData['facebook_client_id'] ?>" /> --}}
            <!-- Facebook key exists hidden field -->

            <!-- Enable/Disable check box -->
            <div id="lwFacebookLoginInputField" class="mx-4 px-1">
                <!--Facebook Client ID -->
                <div class="my-3">
                    <label for="lwFacebookClientId">
                        <?= __tr('Facebook Client ID') ?>
                    </label>
                    <input type="text" class="form-control form-control-user" name="facebook_client_id" placeholder="<?= __tr('Add Your Facebook Client ID') ?>" id="lwFacebookClientId">
                </div>
                <!--/Facebook Client ID -->

                <!--Facebook Client Secret -->
                <div class="">
                    <label for="lwFacebookClientSecret">
                        <?= __tr('Facebook Client Secret') ?>
                    </label>
                    <input type="text" class="form-control form-control-user" name="facebook_client_secret" placeholder="<?= __tr('Add Your Facebook Client Secret') ?>" id="lwFacebookClientSecret" >
                </div>
                <!--/Facebook Client Secret -->

                <!-- Facebook Callback Url -->
                <div class="my-3">
                    <label for="lwFacebookCallbackUrl">
                        <?= __tr('Callback URL') ?>
                    </label>
                    <input type="text" class="form-control form-control-user" data-toggle="tooltip" data-placement="top" title="{{ __tr('Click to copy') }}" id="lwFacebookCallbackUrl" value="{{route('login.facebook.callback')}}" onclick="myFunctionFacebook()" readonly>
                </div>
                <!--/Facebook Callback Url -->

            </div>
            <!--/Facebook login settings -->
        </div>
            <!-- Update Button -->
            <div class="mt-2">
                <button type="submit" class="btn btn-primary btn-user lw-btn-block-mobile mt-2">
                    <?= __tr('Save') ?>
                </button>
            </div>
    </fieldset>
    <!-- Enable/Disable check box -->

    <!-- /Update Button -->
</form>
<!-- /User Setting Form -->

@push('appScripts')
<script>
    "use strict";

    function allowFunction() {
        var checkBox = document.getElementById("lwAllowGoogleLogin");
        var text = document.getElementById("inputFieldShow");
        if (checkBox.checked == true) {
            text.style.display = "block";
        } else {
            text.style.display = "none";
        }
    }

    function allowFacebookLoginFunction() {
        var checkBox1 = document.getElementById("lwAllowFacebookLogin");
        var text1 = document.getElementById("lwInputFieldShow");
        if (checkBox1.checked == true) {
            text1.style.display = "block";
        } else {
            text1.style.display = "none";
        }
    }



    //google login js block start
    $(document).ready(function() {
        var allowGoogleLogin = $("#lwAllowGoogleLogin").is(':checked');
        if (allowGoogleLogin) {
            $("#inputFieldShow").show()
        }

        //is true then disable input field
        if (!allowGoogleLogin) {
            $("#lwGoogleLoginInputField").addClass('lw-disabled-block-content');
            $('#lwAddGoogleKeys').attr("disabled", true);
        }

        //allow google switch on change event
        $("#lwAllowGoogleLogin").on('change', function(e) {
            allowGoogleLogin = $(this).is(":checked");

            //if condition false then add class
            if (!allowGoogleLogin) {
                $("#lwGoogleLoginInputField").addClass('lw-disabled-block-content');
                $('#lwAddGoogleKeys').attr("disabled", true);
            } else {
                $("#lwGoogleLoginInputField").removeClass('lw-disabled-block-content');
                $('#lwAddGoogleKeys').attr("disabled", false);
            }
        });

        /*********** Google Keys setting start here ***********/
        var isGoogleKeysInstalled = "<?= $configurationData['google_client_id'] ?>"
            , lwGoogleLoginInputField = $('#lwGoogleLoginInputField')
            , lwIsGoogleKeysExist = $('#lwIsGoogleKeysExist');

        // Check if test google login keys are installed
        if (isGoogleKeysInstalled) {
            lwGoogleLoginInputField.hide();
            lwIsGoogleKeysExist.show();
        } else {
            lwIsGoogleKeysExist.hide();
        }
        // Update google login checkout testing keys
        $('#lwAddGoogleKeys').click(function() {
            $("#lwGoogleKeysExist").val(0);
            lwGoogleLoginInputField.show();
            lwIsGoogleKeysExist.hide();
        });
        /*********** Google Keys setting end here ***********/
    });
    //google login js block end

    //facebook login js block start
    $(document).ready(function() {
        var allowFacebookLogin = $("#lwAllowFacebookLogin").is(':checked');
        if (allowFacebookLogin) {
            $("#lwInputFieldShow").show()
        }
        //is true then disable input field
        if (!allowFacebookLogin) {
            $("#lwFacebookLoginInputField").addClass('lw-disabled-block-content');
            $('#lwAddFacebookKeys').attr("disabled", true);
        }

        //allow facebook switch on change event
        $("#lwAllowFacebookLogin").on('change', function(e) {
            allowFacebookLogin = $(this).is(":checked");

            //if condition false then add class
            if (!allowFacebookLogin) {
                $("#lwFacebookLoginInputField").addClass('lw-disabled-block-content');
                $('#lwAddFacebookKeys').attr("disabled", true);
            } else {
                $("#lwFacebookLoginInputField").removeClass('lw-disabled-block-content');
                $('#lwAddFacebookKeys').attr("disabled", false);
            }
        });

        /*********** Facebook Keys setting start here ***********/
        var isFacebookKeysInstalled = "<?= $configurationData['facebook_client_id'] ?>"
            , lwFacebookLoginInputField = $('#lwFacebookLoginInputField')
            , lwIsFacebookKeysExist = $('#lwIsFacebookKeysExist');

        // Check if test facebook login keys are installed
        if (isFacebookKeysInstalled) {
            lwFacebookLoginInputField.hide();
            lwIsFacebookKeysExist.show();
        } else {
            lwIsFacebookKeysExist.hide();
        }
        // Update facebook login checkout testing keys
        $('#lwAddFacebookKeys').click(function() {
            $("#lwFacebookKeysExist").val(0);
            lwFacebookLoginInputField.show();
            lwIsFacebookKeysExist.hide();
        });
        /***********Facebook Keys setting end here ***********/
    });
    //facebook login js block end


    //on social login setting success callback function
    function onSocialLoginFormCallback(responseData) {
        //check reaction code is 1 then reload view
        if (responseData.reaction == 1) {
            showConfirmation("{{ __tr('Settings Updated Successfully') }}", function() {
                __Utils.viewReload();
            }, {
                confirmButtonText: "{{ __tr('Reload Page') }}"
                , type: "success"
            });
        }
    };

    function myFunctionGoogle() {
        /* Get the text field */
        var copyText = document.getElementById("lwGoogleCallbackUrl");

        /* Select the text field */
        copyText.select();
        copyText.setSelectionRange(0, 99999); /* For mobile devices */

        /* Copy the text inside the text field */
        navigator.clipboard.writeText(copyText.value);
    }

    function myFunctionFacebook() {
        /* Get the text field */
        var copyText = document.getElementById("lwFacebookCallbackUrl");

        /* Select the text field */
        copyText.select();
        copyText.setSelectionRange(0, 99999); /* For mobile devices */

        /* Copy the text inside the text field */
        navigator.clipboard.writeText(copyText.value);
    }

</script>
@endpush
