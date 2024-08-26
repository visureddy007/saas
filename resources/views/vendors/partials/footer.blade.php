<footer class="footer mt-auto py-3 lw-store-front-footer text-light">
    <div class="footer-text container text-center">
        &copy;<span class="footer-store-name">{{ $title }}</span> {{ __tr(date('Y')) }}  -  <span>{!!  __tr('Powered by __serviceName__', [
            '__serviceName__' => '<a class="text-white" href="'.url('/').'">' . getAppSettings("name") .'</a>']) !!}</span>
        @if (getVendorSettings('info_terms_and_conditions'))
            |  <a class="text-white" href="{{ route('vendor.info_page', [
                'vendorSlug' => getVendorSettings('slug'),
                'pageSlug' => 'info-terms-and-conditions'
            ]) }}">{{  __tr('Terms and Conditions') }}</a>
        @endif
        @if (getVendorSettings('info_refund_policy'))
            |  <a class="text-white" href="{{ route('vendor.info_page', [
                'vendorSlug' => getVendorSettings('slug'),
                'pageSlug' => 'info-refund-policy'
            ]) }}">{{  __tr('Refund Policy') }}</a>
        @endif
        @if (!isset($noGotoButton) or !$noGotoButton)
            <a href="#" id="lwGotoTop" class="go-to-top fr float-right btn btn-light"> <i class="fa fa-arrow-up"></i> {{ __tr('Go to Top') }}</a>
        @endif
    </div>
</footer>