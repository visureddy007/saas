{{-- datatable queue log--}}
<x-lw.datatable lw-card-classes="border-0" data-page-length="100" id="lwCampaignQueueLog" :url="route('vendor.campaign.queue.log.list.view', ['campaignUid' => $campaignUid])">
    <th data-orderable="true" data-name="full_name">{{ __tr('Name') }}</th>
    {{-- <th data-orderable="true" data-name="last_name">{{ __tr('Last Name') }}</th> --}}
    <th data-orderable="true" data-name="phone_with_country_code">{{ __tr('Phone Number') }}</th>
    <th data-orderable="true" data-order-by="true" data-order-type="desc" data-name="updated_at">{{ __tr('Last Status Updated at') }}</th>
    <th data-template="#campaignActionColumnTemplate" data-name="null">{{ __tr('Messages') }}</th>
</x-lw.datatable>
 <!-- action template -->
 <script type="text/template" id="campaignActionColumnTemplate">
    <!--  status -->
    <% if ((__tData.status != 2) && (__tData.status != 3)) { %>
        <% if (__tData.whatsapp_message_error) { %>
        <span class="text-muted">{{ __tr('requeued and waiting ..') }}</span>
        <small class="text-danger"><%- __tData.whatsapp_message_error %></small>
        <% } else { %>
            <span class="text-muted">{{ __tr('waiting ..') }}</span>
        <% } %>
     <% } else if (__tData.status == 2) { %>
        <span class="text-danger"><%- __tData.whatsapp_message_error %></span>
    <% } else if (__tData.status == 3) { %>
        <span class="text-muted">{{ __tr('processing ..') }}</span>
    <% } %>
</script>
            <!-- / status -->
{{-- /datatable queue log--}}