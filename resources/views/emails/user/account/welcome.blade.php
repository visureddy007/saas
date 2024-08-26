
<?php
    $webSiteName = __tr(getAppSettings('name'));
?>

<table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td align="center">
            <table class="email-content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                <!-- Email Body -->
                <tr>
                    <td class="email-body" width="570" cellpadding="0" cellspacing="0">
                        <table class="email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0"
                            role="presentation">
                            <!-- Body content -->
                            <tr>
                                <td class="content-cell">
                                    <p>
                                        @if($welcomeEmailContent !=null)
                                        {!! strtr($welcomeEmailContent, [
                                            '__fullName__' => $fullName
                                        ]) !!}
                                        @else
                                          <!-- Body content -->
                                    <div class="f-fallback">
                                        <?= __tr('Dear') ?> {{$fullName}},
                                        <br><br>
                                        <?= __tr('Welcome to ') ?>{{$webSiteName}} !
                                        <br> <br>
                                        <?=__tr('Thank you for choosing us. We look forward to helping you achieve your goals and enjoy your time with us.') ?>
                                        <br> <br>
                                    </div>
                                    <p class="f-fallback sub align-center">
                                        <br><?= __tr('Best regards,') ?>
                                        <br><?= __tr('The') ?> {{$webSiteName}} <?= __tr('Team.') ?>
                                    </p>
                            <!--/Body content--->
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>


